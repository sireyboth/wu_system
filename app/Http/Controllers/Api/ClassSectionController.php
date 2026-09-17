<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassScoreConfigRequest;
use App\Http\Requests\ClassSectionRequest;
use App\Http\Resources\ClassScoreConfigResource;
use App\Http\Resources\ClassSectionResource;
use App\Models\ClassScoreConfig;
use App\Models\ClassSection;
use App\Models\CourseEnrollment;
use App\Models\Student;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;

class ClassSectionController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Class';
        $this->model         = ClassSection::class;
        $this->resource      = ClassSectionResource::class;
        $this->relationships = ['subject', 'term', 'campus', 'shift', 'scoreConfig', 'teacherAssignments.lecturer'];
    }

    public function index(Request $request)
    {
        return $this->list($request, fn($query) => $query->withCount('courseEnrollments'));
    }

    /**
     * lecturer_id is accepted here only to create the initial primary
     * teacher_assignment in the same step as class creation — it isn't a
     * column on classes itself (see TeacherAssignment, which is where a
     * class's actual lecturer history lives, including substitutions).
     */
    public function store(ClassSectionRequest $request)
    {
        return execute(function () use ($request) {
            $validated  = $request->validated();
            $lecturerId = $validated['lecturer_id'] ?? null;
            unset($validated['lecturer_id']);

            $class = ClassSection::create($validated);

            if ($lecturerId) {
                TeacherAssignment::create([
                    'lecturer_id'   => $lecturerId,
                    'class_id'      => $class->id,
                    'role'          => 'primary',
                    'assigned_from' => now()->toDateString(),
                ]);
            }

            return new ClassSectionResource($this->reload($class));
        });
    }

    public function show(ClassSection $class)
    {
        return $this->view($class);
    }

    /**
     * lecturer_id is deliberately ignored on update — reassigning a
     * lecturer is its own action (TeacherAssignmentController) with its
     * own history, not a side effect of editing a class's other fields.
     */
    public function update(ClassSectionRequest $request, ClassSection $class)
    {
        $validated = $request->validated();
        unset($validated['lecturer_id']);

        return execute(function () use ($request, $class, $validated) {
            $class->update($validated);
            return new ClassSectionResource($this->reload($class));
        });
    }

    public function destroy(ClassSection $class)
    {
        return $this->disable($class);
    }

    public function restore(ClassSection $class)
    {
        return $this->enable($class);
    }

    public function force_destroy(ClassSection $class)
    {
        return $this->clear($class);
    }

    /**
     * A lecturer's own point split for this class. One row per class —
     * class_id is unique, so this is a get-or-empty rather than a list.
     */
    public function scoreConfig(ClassSection $class)
    {
        return new ClassScoreConfigResource(ClassScoreConfig::where('class_id', $class->id)->first());
    }

    /**
     * Set (or replace) this class's own point split. Deliberately an
     * upsert keyed by class_id, not a generic CRUD endpoint with its own
     * id — matches the "one config per class" shape from the attendance
     * schema doc: Lecturer A adjusting class_id=1 can never touch
     * class_id=2's row, because there's exactly one row per class.
     */
    public function updateScoreConfig(ClassScoreConfigRequest $request, ClassSection $class)
    {
        return execute(function () use ($request, $class) {
            $config = ClassScoreConfig::updateOrCreate(
                ['class_id' => $class->id],
                array_merge($request->validated(), ['set_by' => auth()->id()])
            );

            return new ClassScoreConfigResource($config->fresh());
        });
    }

    /**
     * Automatic rostering — "attendance will take automatic from main
     * list when we create attendance for teacher, and some terms 2-3
     * majors study mixed each other." Enrolls every student whose
     * *current* academic history matches the given filters into this
     * class, skipping anyone already enrolled. A class has no major_id of
     * its own (see ClassSection), so mixed-major classes are just
     * whatever this filter set matches — e.g. major_id left blank enrolls
     * every major that matches the rest of the filters.
     */
    public function autoEnroll(Request $request, ClassSection $class)
    {
        $validated = $request->validate([
            'filters'             => 'required|array',
            'filters.batch_id'    => 'nullable|integer|exists:batches,id',
            'filters.major_id'    => 'nullable|array',
            'filters.major_id.*'  => 'integer|exists:majors,id',
            'filters.group_id'    => 'nullable|integer|exists:groups,id',
            'filters.shift_id'    => 'nullable|integer|exists:shifts,id',
            'filters.campus_id'   => 'nullable|integer|exists:campuses,id',
            'filters.status_id'   => 'nullable|integer|exists:statuses,id',
            'filters.semester'    => 'nullable|integer|in:1,2',
            'filters.year_level'  => 'nullable|integer|min:1',
        ]);

        // major_id is the one filter that can be several values at once
        // (mixed-major rostering across 2-3 specific majors) — every other
        // filter stays single-value, matched with a plain where().
        $majorIds = array_filter($validated['filters']['major_id'] ?? []);
        $filters  = array_filter(
            \Illuminate\Support\Arr::except($validated['filters'], 'major_id'),
            fn($v) => $v !== null && $v !== ''
        );

        if (empty($filters) && empty($majorIds)) {
            return no_data('At least one filter is required — enrolling every student in the school into one class is almost certainly a mistake.', 422);
        }

        return execute(function () use ($filters, $majorIds, $class) {
            $historyIds = Student::query()
                ->whereHas('currentAcademicHistory', function ($query) use ($filters, $majorIds) {
                    foreach ($filters as $field => $value) {
                        $query->where($field, $value);
                    }
                    if (! empty($majorIds)) {
                        $query->whereIn('major_id', $majorIds);
                    }
                })
                ->with('currentAcademicHistory:id,student_id')
                ->get()
                ->pluck('currentAcademicHistory.id')
                ->filter()
                ->values();

            $alreadyEnrolled = CourseEnrollment::where('class_id', $class->id)
                ->whereIn('student_academic_history_id', $historyIds)
                ->pluck('student_academic_history_id');

            $toEnroll = $historyIds->diff($alreadyEnrolled);

            foreach ($toEnroll as $historyId) {
                CourseEnrollment::create([
                    'student_academic_history_id' => $historyId,
                    'class_id'                    => $class->id,
                    'status'                      => 'enrolled',
                ]);
            }

            return has_data([
                'matched'         => $historyIds->count(),
                'already_enrolled' => $alreadyEnrolled->count(),
                'newly_enrolled'  => $toEnroll->count(),
            ], "{$toEnroll->count()} student(s) enrolled into {$class->code}.");
        });
    }

    /**
     * A student search scoped to the `class` permission — Auto-Enroll
     * covers "a whole batch/major", but not "just these 1-3 specific
     * students, possibly from a totally different batch" (a retake, an
     * add-subject case, a student borrowed from another cohort for one
     * elective). This is the search behind that manual add. Deliberately
     * not the main /students endpoint — that requires `student.view`,
     * which a class-managing role (e.g. Exam Officer) doesn't have.
     */
    public function searchStudents(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        if (mb_strlen($term) < 2) {
            return has_data([]);
        }

        $students = Student::query()
            ->whereHas('person', fn($q) => $q
                ->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('first_name_kh', 'like', "%{$term}%")
                ->orWhere('last_name_kh', 'like', "%{$term}%"))
            ->orWhere('code', 'like', "%{$term}%")
            ->with(['person', 'major', 'batch', 'shift', 'campus', 'status', 'currentAcademicHistory'])
            ->limit(15)
            ->get();

        return has_data($students->map(fn(Student $s) => [
            'id'                          => $s->id,
            'code'                        => $s->code,
            'name'                        => trim(($s->person?->first_name_kh ?? '') . ' ' . ($s->person?->last_name_kh ?? '')) ?: trim(($s->person?->first_name ?? '') . ' ' . ($s->person?->last_name ?? '')),
            'major'                       => $s->major?->name_en,
            'batch'                       => $s->batch?->name_en,
            'shift'                       => $s->shift?->name_en,
            'campus'                      => $s->campus?->name_en,
            'status'                      => $s->status?->name_en,
            'can_attend'                  => (bool) ($s->status?->can_attend ?? false),
            'year_level'                  => $s->year_level,
            'semester'                    => $s->semester,
            'student_academic_history_id' => $s->currentAcademicHistory?->id,
        ])->filter(fn($s) => $s['student_academic_history_id'])->values());
    }

    /**
     * Manually enroll one specific student — no filter, no batch/major
     * match required. Covers the case Auto-Enroll can't: a handful of
     * individual students, possibly from a different batch entirely
     * (retake, add-subject, borrowed for one elective).
     */
    public function addStudent(Request $request, ClassSection $class)
    {
        $validated = $request->validate([
            'student_academic_history_id' => 'required|integer|exists:student_academic_histories,id',
        ]);

        $exists = CourseEnrollment::where('class_id', $class->id)
            ->where('student_academic_history_id', $validated['student_academic_history_id'])
            ->exists();

        if ($exists) {
            return no_data('This student is already enrolled in this class.', 422);
        }

        return execute(function () use ($validated, $class) {
            CourseEnrollment::create([
                'student_academic_history_id' => $validated['student_academic_history_id'],
                'class_id'                    => $class->id,
                'status'                      => 'enrolled',
            ]);

            return has_data(null, 'Student added to the class.');
        });
    }

    public function attendanceHistory(ClassSection $class)
    {
        return has_data(\App\Models\ClassSession::attendanceHistoryFor($class->id));
    }

    public function exportAttendanceHistory(ClassSection $class)
    {
        $history = \App\Models\ClassSession::attendanceHistoryFor($class->id);
        return $this->export(new \App\Exports\AttendanceHistoryExport($history), "attendance-history-{$class->code}");
    }
}
