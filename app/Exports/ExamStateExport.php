<?php
namespace App\Exports;

use App\Models\ExamState;

/**
 * Admin's Exam Rooms export — mirrors whatever the grid is currently
 * showing (same term filter + search as ExamStateController::index()),
 * so what you export is what you were just looking at. Round-tripped by
 * ExamStateImport for bulk setup/fixes of many rooms in one term at once.
 */
class ExamStateExport extends IExport
{
    protected string $model = ExamState::class;

    protected array $relationships = ['examTerm'];

    protected array $headings = [
        'No', 'Room', 'Major', 'Degree', 'Shift', 'Student Total', 'Exam Date', 'Invigilators', 'Term',
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

    public function map(mixed $data): array
    {
        $this->numRow++;

        return [
            $this->numRow,
            $data->room,
            $data->major,
            $data->degree,
            $data->shift,
            $data->student_total,
            $data->exam_date?->format('Y-m-d'),
            implode(', ', (array) ($data->invigilators ?? [])),
            $data->examTerm?->title,
        ];
    }
}
