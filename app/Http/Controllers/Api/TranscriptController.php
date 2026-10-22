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
        $this->relationships = ['major', 'subjects'];
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

            return new TranscriptResource($this->reload($transcript));
        });
    }

    public function destroy(Transcript $transcript)
    {
        return $this->disable($transcript);
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
     * transcripts, and breakdowns by major and degree. All filters optional.
     * "printable" / "blocked" = final with / without the entrance exam passed.
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
            ->with('major:id,name_en,name_kh')
            ->when($filters['major_id'] ?? null, fn ($q, $id) => $q->where('major_id', $id))
            ->when($filters['doc_type'] ?? null, fn ($q, $type) => $q->where('doc_type', $type))
            ->when($filters['degree'] ?? null, fn ($q, $degree) => $q->where('degree', $degree))
            ->when($filters['date_from'] ?? null, fn ($q, $date) => $q->whereDate('issue_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($q, $date) => $q->whereDate('issue_date', '<=', $date))
            ->when($filters['status'] ?? null, fn ($q, $status) => match ($status) {
                'draft'     => $q->where('status', Transcript::DRAFT),
                'final'     => $q->where('status', Transcript::FINAL),
                'printable' => $q->where('status', Transcript::FINAL)->where('entrance_exam', true),
                'blocked'   => $q->where('status', Transcript::FINAL)->where('entrance_exam', false),
            })
            ->get(['id', 'status', 'entrance_exam', 'degree', 'major_id', 'total_gpa']);

        $isFinal = fn ($t) => $t->status === Transcript::FINAL;
        $final   = $rows->filter($isFinal);
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
            'printable'   => $final->where('entrance_exam', true)->count(),
            'blocked'     => $final->where('entrance_exam', false)->count(),
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
                'blocked'     => $group->filter($isFinal)->where('entrance_exam', false)->count(),
                'average_gpa' => $average($group->filter($isFinal)),
            ])->sortByDesc('total')->values(),
        ]);
    }

    /** Header columns from the validated input; the draft flag becomes the status. */
    protected function header(array $data): array
    {
        return [
            ...collect($data)->except(['subjects', 'draft'])->all(),
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
}
