<?php
namespace App\Imports;

use App\Models\ExamState;
use App\Models\ExamTerm;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Bulk-fix companion to ExamStateExport — same column shape, round-tripped.
 * Scoped to a single ExamTerm (chosen by the admin before importing, same
 * as the room-creation modal always requires a term) so a plain room name
 * like "101" never collides across unrelated terms.
 *
 * Matched by Room (case-insensitive, trimmed) within that term:
 * updateOrCreate — an existing room's fields are updated in place, a
 * new Room value creates one. This writes ExamState directly, no
 * approval step, same as every other admin edit to this table.
 *
 * Blank cells on an UPDATE are left untouched (never used to clear an
 * existing value) — this is a bulk fix/setup tool, not a full overwrite.
 * A CREATE still requires Room, Major and Degree; missing any of those
 * skips the row (reported, not silently dropped).
 */
class ExamStateImport implements ToCollection, WithHeadingRow
{
    private int $updatedCount = 0;
    private int $createdCount = 0;
    private array $skipped = [];

    public function __construct(private ExamTerm $term)
    {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $i => $row) {
            $this->processRow($i + 2, $row);
        }
    }

    protected function processRow(int $rowNumber, Collection $row): void
    {
        $room = trim((string) ($row['room'] ?? ''));

        if ($room === '') {
            $this->skipped[] = ['row' => $rowNumber, 'reason' => 'Missing Room — row left blank.'];
            return;
        }

        $existing = ExamState::query()
            ->where('exam_term_id', $this->term->id)
            ->whereRaw('LOWER(TRIM(room)) = ?', [mb_strtolower($room)])
            ->first();

        $major   = $this->nullableCell($row, 'major');
        $degree  = $this->nullableCell($row, 'degree');

        if (! $existing && ($major === null || $degree === null)) {
            $this->skipped[] = [
                'row' => $rowNumber, 'room' => $room,
                'reason' => 'New room needs Major and Degree filled in to be created.',
            ];
            return;
        }

        $attributes = ['exam_term_id' => $this->term->id, 'room' => $room];

        if ($major !== null) {
            $attributes['major'] = $major;
        }
        if ($degree !== null) {
            $attributes['degree'] = $degree;
        }
        if (($shift = $this->nullableCell($row, 'shift')) !== null) {
            $attributes['shift'] = $shift;
        }
        if (($total = $this->nullableCell($row, 'student_total')) !== null) {
            $attributes['student_total'] = (int) $total;
        }
        if (($date = $this->nullableCell($row, 'exam_date')) !== null) {
            $attributes['exam_date'] = $date;
        }
        if (($invigilators = $this->nullableCell($row, 'invigilators')) !== null) {
            $attributes['invigilators'] = array_values(array_filter(array_map('trim', explode(',', $invigilators))));
        }

        if ($existing) {
            $existing->update($attributes);
            $this->updatedCount++;
        } else {
            ExamState::create($attributes);
            $this->createdCount++;
        }
    }

    private function nullableCell(Collection $row, string $key): ?string
    {
        $value = trim((string) ($row[$key] ?? ''));
        return $value === '' ? null : $value;
    }

    public function report(): array
    {
        return [
            'created_count' => $this->createdCount,
            'updated_count' => $this->updatedCount,
            'skipped'       => $this->skipped,
        ];
    }
}
