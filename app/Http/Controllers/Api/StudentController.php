<?php
namespace App\Http\Controllers\Api;

use App\Exports\StudentExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Imports\StudentImport;
use App\Models\Person;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /**
     * Fields that define a student's academic snapshot — a change to any
     * of these means "advance to a new term," not "correct a typo," so
     * each change gets its own student_academic_histories row instead of
     * silently overwriting the last one (see advanceAcademicHistory()).
     */
    private const ACADEMIC_FIELDS = [
        'batch_id',
        'major_id',
        'group_id',
        'shift_id',
        'campus_id',
        'status_id',
        'year_level',
        'semester',
    ];

    public function __construct()
    {
        $this->name          = 'Student';
        $this->model         = Student::class;
        $this->resource      = StudentResource::class;
        $this->relationships = array_merge([
            'person',
            'batch',
            'major',
            'shift',
            'campus',
            'major.faculty',
            'group',
            'status',
            'guardians',
        ], $this->withPerson());
    }

    /**
     * Filterable by any of these academic fields via query string (e.g.
     * ?major_id=3) — what lets the student list narrow down to "whole
     * major" or "whole batch" before a bulk advance-semester action.
     */
    private const FILTERABLE_FIELDS = [
        'major_id',
        'batch_id',
        'shift_id',
        'group_id',
        'campus_id',
        'status_id',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->list($request, function ($query) use ($request) {
            $this->applyFilters($query, $request);

            if ($request->payment === Student::YEARLY) {
                return $query->yearly();
            }

            if ($request->payment === Student::SEMESTER) {
                return $query->semester();
            }

            return $query;
        });
    }

    /**
     * Exports whatever the list is currently filtered to (search/payment) —
     * same filter contract as index() above. Named exportList, not export,
     * since export(object, string) is a reserved method name on the base
     * Controller (see RetakeBatchController::importFile()'s docblock for
     * the same reasoning — reusing a base method name with an
     * incompatible signature is a fatal error, not a warning).
     */
    public function exportList(Request $request)
    {
        return $this->export(
            new StudentExport($request->only(['search', 'payment'])),
            'students'
        );
    }

    /**
     * Bulk enrollment import — see StudentImport's docblock for the exact
     * column contract and what gets skipped vs created.
     */
    public function importFile(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new StudentImport();

        try {
            Excel::import($import, $validated['file']);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Student import failed', ['error' => $e->getMessage()]);
            return no_data('The file could not be processed. Please check it is a valid, correctly formatted spreadsheet.', 422);
        }

        return has_data(['report' => $import->report()], 'Import complete.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentRequest $request)
    {
        return execute(function () use ($request) {
            $data    = $request->validated();
            $person  = Person::create($data);
            $student = $person->student()->create($data);

            $person->addresses()->createMany($data['addresses']);
            $student->guardians()->createMany($data['guardians']);
            $student->academicHistories()->create($this->academicSnapshot($student) + ['is_current' => true]);

            return new StudentResource($student->load($this->relationships));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return new StudentResource($student->load($this->relationships));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentRequest $request, Student $student)
    {
        return execute(function () use ($request, $student) {
            $data   = $request->validated();
            $person = $student->person;

            $person->update($data);
            $student->update($data);

            $this->sync_addresses($person, $data['addresses'] ?? []);
            $this->sync_guardians($student, $data['guardians'] ?? []);
            $this->advanceAcademicHistory($student);

            return new StudentResource($student->load($this->relationships));
        });
    }

    /**
     * Dedicated "advance to a new semester" action — a focused form with
     * just the academic fields (defaults to the student's current values,
     * see the frontend modal), instead of the full edit-student form.
     * Goes through the same advanceAcademicHistory() as a normal edit, so
     * a no-op submission (nothing actually changed) still doesn't create
     * a redundant history row.
     */
    public function advanceSemester(Request $request, Student $student)
    {
        $data = $request->validate(array_merge(
            check_exist('batch_id', 'batches'),
            check_exist('major_id', 'majors'),
            check_exist('group_id', 'groups'),
            check_exist('shift_id', 'shifts'),
            check_exist('campus_id', 'campuses', required: false),
            check_exist('status_id', 'statuses'),
            [
                'year_level' => 'required|integer|min:1|max:10',
                'semester'   => 'nullable|integer|in:1,2',
            ],
        ));

        return execute(function () use ($data, $student) {
            $student->update($data);
            $this->advanceAcademicHistory($student, forTermChangeToo: true);

            return new StudentResource($student->load($this->relationships));
        });
    }

    /**
     * Advance a student to a new academic history row when their batch,
     * major, shift, group, campus, status, or year_level actually changed
     * on this update — the previous row is left untouched (just flipped to
     * is_current = false) so past terms keep reading exactly what was true
     * at the time.
     *
     * $forTermChangeToo additionally advances when NONE of those fields
     * changed but the active Term has moved on since the student's current
     * snapshot — e.g. Year 1 Semester 1 -> Year 1 Semester 2 touches
     * nothing on the student themselves, only which term is now active, so
     * a field-only diff would see this as a no-op. Only the two dedicated
     * "advance semester" actions opt into this; a plain profile edit
     * (fixing a phone number, say) must never silently advance a student
     * just because time has passed and the active term changed underneath
     * them.
     */
    private function advanceAcademicHistory(Student $student, bool $forTermChangeToo = false): void
    {
        $current = $student->currentAcademicHistory;
        $activeTermId = Term::active()->value('id');
        $termAdvanced = $forTermChangeToo && $activeTermId && (int) $current?->term_id !== (int) $activeTermId;

        if (! $student->wasChanged(self::ACADEMIC_FIELDS) && ! $termAdvanced) {
            return;
        }

        $current?->update(['is_current' => false]);
        $student->academicHistories()->create($this->academicSnapshot($student) + ['is_current' => true]);
    }

    private function academicSnapshot(Student $student): array
    {
        return [
            ...$student->only(self::ACADEMIC_FIELDS),
            'term_id'        => Term::active()->value('id'),
            'effective_date' => now(),
        ];
    }

    private function applyFilters($query, Request $request): void
    {
        foreach (self::FILTERABLE_FIELDS as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }
    }

    /**
     * Bulk "advance to a new semester" — the group version of
     * advanceSemester() above. Scoped either to an explicit list of ids
     * or to "every student matching these filters" (so the frontend can
     * filter down to one major/batch and advance the whole thing without
     * listing every id). Only the fields present in `changes` are ever
     * touched — a student's other academic fields are left exactly as
     * they were, same "only touch what's provided" contract as a normal
     * edit, just applied to many students at once.
     */
    public function bulkAdvanceSemester(Request $request)
    {
        $validated = $request->validate([
            'all'                  => 'sometimes|boolean',
            'ids'                  => 'sometimes|array|min:1',
            'ids.*'                => 'integer|exists:students,id',
            'filters'              => 'sometimes|array',
            'filters.search'       => 'nullable|string',
            'filters.major_id'     => 'nullable|integer|exists:majors,id',
            'filters.batch_id'     => 'nullable|integer|exists:batches,id',
            'filters.shift_id'     => 'nullable|integer|exists:shifts,id',
            'filters.group_id'     => 'nullable|integer|exists:groups,id',
            'filters.campus_id'    => 'nullable|integer|exists:campuses,id',
            'filters.status_id'    => 'nullable|integer|exists:statuses,id',
            'changes'              => 'sometimes|array',
            'changes.major_id'     => 'nullable|integer|exists:majors,id',
            'changes.batch_id'     => 'nullable|integer|exists:batches,id',
            'changes.shift_id'     => 'nullable|integer|exists:shifts,id',
            'changes.group_id'     => 'nullable|integer|exists:groups,id',
            'changes.campus_id'    => 'nullable|integer|exists:campuses,id',
            'changes.status_id'    => 'nullable|integer|exists:statuses,id',
            'changes.year_level'   => 'nullable|integer|min:1|max:10',
            'changes.semester'     => 'nullable|integer|in:1,2',
        ]);

        // Empty is valid on its own — "advance everyone in this filtered
        // group to the current semester" with no other field changes is a
        // real case (e.g. Year 1 Semester 1 -> Year 1 Semester 2, nothing
        // about the student themselves changes). advanceAcademicHistory()
        // still records a new snapshot for anyone whose term actually
        // moved on, and is a no-op for anyone already on the active term.
        $changes = array_filter(
            $validated['changes'] ?? [],
            fn($value) => $value !== null && $value !== ''
        );

        $all = $validated['all'] ?? false;
        if (! $all && empty($validated['ids'])) {
            return no_data('Either "ids" (non-empty array) or "all": true with filters is required.', 422);
        }

        // "All matching filters" can silently touch far more students than
        // intended if the list isn't narrowed down first — an explicit ids
        // list doesn't have this risk (each one was picked by hand), so
        // this only gates the filter-scoped path.
        if ($all && (empty($validated['filters']['batch_id']) || empty($validated['filters']['campus_id']))) {
            return no_data('Filter by both Batch and Campus before bulk-advancing by filter.', 422);
        }

        return execute(function () use ($validated, $changes, $all) {
            $query = Student::query();

            if (! $all) {
                $query->whereIn('id', $validated['ids']);
            } else {
                if (! empty($validated['filters']['search'])) {
                    $query->search($validated['filters']['search']);
                }
                foreach (self::FILTERABLE_FIELDS as $field) {
                    if (! empty($validated['filters'][$field])) {
                        $query->where($field, $validated['filters'][$field]);
                    }
                }
            }

            $count = 0;
            $query->each(function (Student $student) use ($changes, &$count) {
                $student->update($changes);
                $this->advanceAcademicHistory($student, forTermChangeToo: true);
                $count++;
            });

            return has_data(['advanced' => $count], "{$count} student(s) advanced to the new semester.");
        });
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        return execute(function () use ($student) {
            $student->person->addresses()->forceDelete(); // if addresses relation is via person, adjust accordingly
            $student->guardians()->forceDelete();
            $student->person->forceDelete();
            $student->forceDelete();

            return has_data(null, 'Permanently deleted.');
        });
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Student $student)
    {
        return execute(function () use ($student) {
            $student->restore();
            $student->person()->withTrashed()->first()?->restore();

            return new StudentResource($student->load($this->relationships));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function trash(Student $student)
    {
        return execute(function () use ($student) {
            $student->delete();
            $student->person->delete();

            return has_data(null, 'Moved to trash.');
        });
    }

    /**
     * Permanently delete multiple students in one request (same hard-delete
     * behavior as destroy() above). Pass {"all": true} to wipe every student
     * instead of listing ids individually.
     *
     * Deletes via the "people" table rather than looping per-student: people
     * -> students, and students -> guardians, are both cascadeOnDelete at
     * the DB level (see create_students_table / create_guardians_table
     * migrations), and addresses cascade on person_id — so one raw delete
     * on people cascades through all of it. This bypasses Eloquent's
     * SoftDeletes on Person (its ->delete() would only set deleted_at, which
     * does NOT fire the FK cascade), which is required to actually trigger
     * removal of the dependent rows.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'all'   => 'sometimes|boolean',
            'ids'   => 'sometimes|array|min:1',
            'ids.*' => 'integer|exists:students,id',
        ]);

        $all = $validated['all'] ?? false;
        if (! $all && empty($validated['ids'])) {
            return no_data('Either "ids" (non-empty array) or "all": true is required.', 422);
        }

        return execute(function () use ($validated, $all) {
            $query = Student::withTrashed();

            if (! $all) {
                $query->whereIn('id', $validated['ids']);
            }

            $personIds = $query->pluck('person_id');
            $count     = $personIds->count();

            \Illuminate\Support\Facades\DB::table('people')->whereIn('id', $personIds)->delete();

            return has_data(null, "{$count} student(s) permanently deleted.");
        });
    }
}
