<?php
namespace App\Http\Controllers\Api;

use App\Helpers\Degree;
use App\Helpers\Grade;
use App\Http\Controllers\Controller;
use App\Http\Requests\TranscriptRequest;
use App\Http\Resources\TranscriptResource;
use App\Models\Subject;
use App\Models\Transcript;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TranscriptController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Transcript';
        $this->model         = Transcript::class;
        $this->resource      = TranscriptResource::class;
        $this->relationships = ['major', 'subjects', 'payments.verifier:id,name', 'prints.printer:id,name'];
    }

    public function index(Request $request)
    {
        return $this->list($request);
    }

    /** Header + course rows in one transaction, totals recomputed from the rows. */
    public function store(TranscriptRequest $request)
    {
        return execute(function () use ($request) {
            $data       = $request->validated();
            $transcript = Transcript::create($this->header($data));

            $this->syncSubjects($transcript, $data['subjects'] ?? []);
            $this->syncPayments($transcript, $data['payments'] ?? []);

            return new TranscriptResource($this->reload($transcript));
        });
    }

    public function show(Transcript $transcript)
    {
        return $this->view($transcript);
    }

    public function update(TranscriptRequest $request, Transcript $transcript)
    {
        return execute(function () use ($request, $transcript) {
            $data = $request->validated();
            $transcript->update($this->header($data));

            if (array_key_exists('subjects', $data)) {
                $this->syncSubjects($transcript, $data['subjects']);
            }
            if (array_key_exists('payments', $data)) {
                $this->syncPayments($transcript, $data['payments']);
            }

            return new TranscriptResource($this->reload($transcript));
        });
    }

    public function destroy(Transcript $transcript)
    {
        return $this->disable($transcript);
    }

    /**
     * Logs one print of the chosen layout and hands back its URL. The
     * client asks for confirmation first when that layout was printed
     * before; the print page itself only re-checks the gates.
     */
    public function print(Request $request, Transcript $transcript)
    {
        $data = $request->validate([
            'doc_type' => ['required', Rule::in(array_keys(Transcript::DOC_TYPES))],
        ]);

        $transcript->load(['subjects', 'payments']);
        abort_unless(
            $transcript->canPrint($data['doc_type']),
            422,
            'Cannot print ' . Transcript::DOC_TYPES[$data['doc_type']] . ' — ' . implode(' · ', $transcript->printBlockers($data['doc_type']))
        );

        $transcript->prints()->create(['doc_type' => $data['doc_type'], 'printed_by' => auth()->id()]);

        return response()->json([
            'print_url' => route('transcript.print', ['transcript' => $transcript->id, 'type' => $data['doc_type']]),
        ]);
    }

    /**
     * Earlier requests under the same student ID, newest first — the form
     * warns with these before saving another one (still allowed). "Who"
     * comes from the activity log's created entry.
     */
    public function studentRequests(Request $request)
    {
        $data = $request->validate([
            'student_code' => 'required|string|max:50',
            'except'       => 'nullable|integer',
        ]);

        $rows = Transcript::query()
            ->where('student_code', trim($data['student_code']))
            ->when($data['except'] ?? null, fn ($q, $id) => $q->whereKeyNot($id))
            ->with(['activities' => fn ($q) => $q->where('event', 'created')->with('causer')])
            ->withCount('prints')
            ->latest('id')
            ->get();

        return response()->json($rows->map(fn ($t) => [
            'id'           => $t->id,
            'issue_no'     => $t->issue_no,
            'name_kh'      => $t->name_kh,
            'request_type' => Transcript::REQUEST_TYPES[$t->request_type] ?? $t->request_type,
            'is_draft'     => $t->isDraft(),
            'prints'       => $t->prints_count,
            'requested_at' => $t->created_at?->format('Y-m-d H:i'),
            'requested_by' => $t->activities->first()?->causer?->name,
        ]));
    }

    public function restore(Transcript $transcript)
    {
        return $this->enable($transcript);
    }

    public function force_destroy(Transcript $transcript)
    {
        return $this->clear($transcript);
    }

    /**
     * Summary for the Report modal: status counts, GPA spread of the final
     * transcripts, and breakdowns by major and degree. All filters optional;
     * doc_type keeps the records that have been printed as that layout.
     * "printable" / "blocked" = final records that do / don't pass the
     * print gates of the document they requested (Transcript::canPrint) —
     * worked out on the loaded rows.
     */
    public function report(Request $request)
    {
        $filters = $request->validate([
            'major_id'  => 'nullable|integer',
            'doc_type'  => ['nullable', Rule::in(array_keys(Transcript::DOC_TYPES))],
            'degree'    => ['nullable', Rule::enum(Degree::class)],
            'status'    => 'nullable|in:final,draft,printable,blocked',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ]);

        $rows = Transcript::query()
            ->with(['major:id,name_en,name_kh', 'subjects:id,transcript_id,year,semester,score,grade', 'payments:id,transcript_id,year,semester'])
            ->when($filters['major_id'] ?? null, fn ($q, $id) => $q->where('major_id', $id))
            ->when($filters['doc_type'] ?? null, fn ($q, $type) => $q->whereHas('prints', fn ($p) => $p->where('doc_type', $type)))
            ->when($filters['degree'] ?? null, fn ($q, $degree) => $q->where('degree', $degree))
            ->when($filters['date_from'] ?? null, fn ($q, $date) => $q->whereDate('issue_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($q, $date) => $q->whereDate('issue_date', '<=', $date))
            ->when($filters['status'] ?? null, fn ($q, $status) => match ($status) {
                'draft'  => $q->where('status', Transcript::DRAFT),
                default  => $q->where('status', Transcript::FINAL),
            })
            ->get(['id', 'status', 'request_type', 'entrance_exam', 'exam_date', 'comprehensive_exam', 'degree', 'major_id', 'total_gpa']);

        $rows = match ($filters['status'] ?? null) {
            'printable' => $rows->filter(fn ($t) => $t->canPrint())->values(),
            'blocked'   => $rows->reject(fn ($t) => $t->canPrint())->values(),
            default     => $rows,
        };

        $isFinal   = fn ($t) => $t->status === Transcript::FINAL;
        $isBlocked = fn ($t) => ! $t->canPrint();
        $final     = $rows->filter($isFinal);
        $average = fn ($items) => $items->isEmpty() ? null : round((float) $items->avg('total_gpa'), 2);

        $bands = [
            '3.50 – 4.00' => [3.5, 4.01],
            '3.00 – 3.49' => [3.0, 3.5],
            '2.00 – 2.99' => [2.0, 3.0],
            'Below 2.00'  => [0, 2.0],
        ];

        return response()->json([
            'total'       => $rows->count(),
            'final'       => $final->count(),
            'draft'       => $rows->count() - $final->count(),
            'printable'   => $final->reject($isBlocked)->count(),
            'blocked'     => $final->filter($isBlocked)->count(),
            'average_gpa' => $average($final),
            'gpa_bands'   => collect($bands)->map(fn ($range, $label) => [
                'label' => $label,
                'total' => $final->filter(fn ($t) => (float) $t->total_gpa >= $range[0] && (float) $t->total_gpa < $range[1])->count(),
            ])->values(),
            'by_degree'   => $rows->groupBy('degree')->map(fn ($group, $degree) => [
                'label' => Degree::tryFrom((string) $degree)?->labelEn() ?? ucfirst((string) $degree),
                'total' => $group->count(),
            ])->sortByDesc('total')->values(),
            'by_major'    => $rows->groupBy(fn ($t) => $t->major_id ?? 0)->map(fn ($group) => [
                'name'        => $group->first()->major?->name_en ?? '— No major —',
                'total'       => $group->count(),
                'final'       => $group->filter($isFinal)->count(),
                'draft'       => $group->reject($isFinal)->count(),
                'blocked'     => $group->filter($isFinal)->filter($isBlocked)->count(),
                'average_gpa' => $average($group->filter($isFinal)),
            ])->sortByDesc('total')->values(),
        ]);
    }

    /** Header columns from the validated input; the draft flag becomes the status. */
    protected function header(array $data): array
    {
        return [
            ...collect($data)->except(['subjects', 'payments', 'draft'])->all(),
            'status' => ($data['draft'] ?? false) ? Transcript::DRAFT : Transcript::FINAL,
        ];
    }

    /**
     * Replaces the course rows with the submitted list (order within a
     * semester = submitted order) and recomputes the totals. Grades,
     * code and title are derived here — the client's values are display only.
     * A row without a score is "IP": no grade, credit not counted.
     */
    protected function syncSubjects(Transcript $transcript, array $rows): void
    {
        $subjects = Subject::query()
            ->whereIn('id', collect($rows)->pluck('subject_id')->filter()->unique())
            ->get(['id', 'code', 'name_en'])
            ->keyBy('id');

        $transcript->subjects()->delete();

        $positions = [];
        $credits   = 0;
        $points    = [];

        foreach ($rows as $row) {
            $block   = "{$row['year']}-{$row['semester']}";
            // A draft row may not have its subject or credit picked yet.
            $subject = $subjects->get($row['subject_id'] ?? null);
            $credit  = (int) ($row['credit'] ?? 0);
            $score   = isset($row['score']) && $row['score'] !== '' ? (float) $row['score'] : null;

            [$point, $letter] = $score === null ? [null, null] : Grade::of($score);

            $transcript->subjects()->create([
                'subject_id'  => $subject?->id,
                'year'        => $row['year'],
                'semester'    => $row['semester'],
                'position'    => $positions[$block] = ($positions[$block] ?? 0) + 1,
                'code'        => $subject?->code,
                'title'       => $subject?->name_en,
                'credit'      => $credit,
                'score'       => $score,
                'grade_point' => $point,
                'grade'       => $letter,
            ]);

            if ($score !== null) {
                $credits += $credit;
                $points[] = [$point, $credit];
            }
        }

        $transcript->forceFill([
            'total_credits' => $credits,
            'total_gpa'     => Grade::gpa($points),
        ])->save();
    }

    /**
     * Makes the paid semesters match the submitted checkboxes. Rows that
     * stay checked are left alone so their verified_by/verified_at keep
     * pointing at whoever first ticked them.
     */
    protected function syncPayments(Transcript $transcript, array $rows): void
    {
        $checked = collect($rows)->map(fn ($row) => "{$row['year']}-{$row['semester']}")->unique();
        $current = $transcript->payments()->get()->keyBy(fn ($p) => "{$p->year}-{$p->semester}");

        // reject(), not except(): an Eloquent collection's except() matches model ids, not these keys.
        $unchecked = $current->reject(fn ($payment, $block) => $checked->contains($block));
        $transcript->payments()->whereKey($unchecked->modelKeys())->delete();

        foreach ($checked->diff($current->keys()) as $block) {
            [$year, $semester] = explode('-', $block);
            $transcript->payments()->create([
                'year'        => $year,
                'semester'    => $semester,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
        }
    }
}
