<?php
namespace App\Exports;

use App\Models\ExamState;

/**
 * Admin's Exam Rooms export — mirrors whatever the grid is currently
 * showing (same term filter + search as ExamStateController::index()),
 * so what you export is what you were just looking at. Round-tripped by
 * ExamStateImport for bulk setup/fixes of many rooms in one term at once.
 *
 * Long format, one row per major within a room: a room with 3 majors
 * becomes 3 rows sharing the same Room (Shift/Degree/Exam Date repeated
 * on each). This is what makes the file editable by hand — no JSON, no
 * hidden per-index pairing — and it's how the import reads it back:
 * consecutive rows with the same Room are grouped back into one room's
 * majors[]/invigilators[] arrays, in the order they appear.
 */
class ExamStateExport extends IExport
{
    protected string $model = ExamState::class;

    protected array $relationships = ['examTerm'];

    protected array $headings = [
        'No', 'Room', 'Shift', 'Degree', 'Exam Date', 'Major', 'Time', 'Total', 'Invigilator', 'Term',
    ];

    public function __construct(protected array $filters = [])
    {
    }

    public function query()
    {
        $query = ExamState::query()->with($this->relationships);

        $query = ($this->filters['trashed'] ?? false) ? $query->onlyTrashed() : $query;

        if ($examTermId = $this->filters['exam_term_id'] ?? null) {
            $query->where('exam_term_id', $examTermId);
        }

        if ($search = $this->filters['search'] ?? null) {
            $query->search($search);
        }

        return $query;
    }

    /**
     * Returns an array of rows (Laravel Excel supports multiple output
     * rows per model from WithMapping) — one per major breakdown entry,
     * falling back to a single row from the flat major/shift/student_total
     * fields for older rooms that never got a majors[] breakdown.
     */
    public function map(mixed $data): array
    {
        $this->numRow++;
        $no = $this->numRow;

        $majors = (array) ($data->majors ?? []);
        if (empty($majors)) {
            $majors = [['major' => $data->major, 'time' => $data->shift, 'total' => $data->student_total]];
        }

        $invigilators = (array) ($data->invigilators ?? []);

        $rows = [];
        foreach (array_values($majors) as $i => $entry) {
            $rows[] = [
                $no,
                $data->room,
                $data->shift,
                $data->degree,
                $data->exam_date?->format('Y-m-d'),
                $entry['major'] ?? '',
                $entry['time'] ?? '',
                $entry['total'] ?? '',
                $invigilators[$i] ?? '',
                $data->examTerm?->title,
            ];
        }

        return $rows;
    }
}
