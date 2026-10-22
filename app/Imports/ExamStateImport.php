<?php
namespace App\Imports;

use App\Models\ExamState;
use App\Models\ExamTerm;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Bulk-fix companion to ExamStateExport — same column shape, round-tripped.
 * Scoped to a single ExamTerm (chosen by the admin before importing, same
 * as the room-creation modal always requires a term) so a plain room name
 * like "101" never collides across unrelated terms.
 *
 * Long format: one row per major within a room. A room with several
 * majors is several consecutive rows sharing the same Room value — that's
 * how someone fills this in by hand (no JSON, no separate breakdown
 * column) and how it's read back here: rows are grouped by consecutive
 * Room value into one ExamState each, with majors[] and invigilators[]
 * built positionally from each group's Major/Time/Total/Invigilator
 * cells, same order the sheet has them in. Room, Shift, Degree and Exam
 * Date only need to be filled on a group's first row — blank on the
 * continuation rows is fine and expected.
 *
 * Matched by Room (case-insensitive, trimmed) within that term:
 * updateOrCreate — an existing room's fields are updated in place, a
 * new Room value creates one. This writes ExamState directly, no
 * approval step, same as every other admin edit to this table.
 *
 * Blank cells on an UPDATE are left untouched (never used to clear an
 * existing value) — this is a bulk fix/setup tool, not a full overwrite.
 * A CREATE still requires Room, Major and Degree (from its group's first
 * non-blank cell); missing any of those skips the whole group (reported,
 * not silently dropped).
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
        foreach ($this->groupByRoom($rows) as $group) {
            // One bad group (e.g. a cell that doesn't match its column —
            // this table has caught real files with "Morning" typed into
            // Exam Date) must not abort every other room in the file, so
            // any unexpected failure here is caught and reported per-room
            // instead of bubbling up and failing the whole import.
            try {
                $this->processGroup($group);
            } catch (\Throwable $e) {
                $this->skipped[] = [
                    'row' => $group['first_row'], 'room' => $group['room'],
                    'reason' => "Could not save this room: {$e->getMessage()}",
                ];
            }
        }
    }

    /**
     * Splits the sheet into consecutive runs belonging to the same room. A
     * blank Room continues the previous group (so only the first row of a
     * room needs Room/Shift/Degree/Exam Date filled in) — and so does a
     * Room that repeats the same value as the current group (the natural
     * way most people fill this in: re-typing/dragging the Room down every
     * row rather than leaving it blank). Only a Room that's actually
     * different starts a fresh group.
     */
    protected function groupByRoom(Collection $rows): array
    {
        $groups = [];
        $currentRoomKey = null; // trimmed + lowercased, for continuation matching

        foreach ($rows as $i => $row) {
            $rowNumber = $i + 2;
            $room = trim((string) ($row['room'] ?? ''));
            $roomKey = $room === '' ? null : mb_strtolower($room);

            if ($roomKey !== null && $roomKey !== $currentRoomKey) {
                $currentRoomKey = $roomKey;
                $groups[] = ['room' => $room, 'first_row' => $rowNumber, 'rows' => []];
            } elseif ($roomKey === null && $currentRoomKey === null) {
                $this->skipped[] = ['row' => $rowNumber, 'reason' => 'Missing Room — row left blank.'];
                continue;
            }

            $groups[count($groups) - 1]['rows'][] = $row;
        }

        return $groups;
    }

    protected function processGroup(array $group): void
    {
        $room = $group['room'];
        $rows = collect($group['rows']);

        $existing = ExamState::query()
            ->where('exam_term_id', $this->term->id)
            ->whereRaw('LOWER(TRIM(room)) = ?', [mb_strtolower($room)])
            ->first();

        // Room-level fields only need to be on the group's first row.
        $head    = $rows->first();
        $degree  = $this->nullableCell($head, 'degree');
        $shift   = $this->nullableCell($head, 'shift');
        $examDate = $this->parseDateCell($head, 'exam_date', $room);

        $majors = [];
        $invigilators = [];
        foreach ($rows as $row) {
            $major = $this->nullableCell($row, 'major');
            if ($major === null) {
                continue;
            }
            $majors[] = [
                'major' => $major,
                'time'  => $this->nullableCell($row, 'time') ?? '',
                'total' => (int) ($this->nullableCell($row, 'total') ?? 0),
            ];
            $invigilators[] = $this->nullableCell($row, 'invigilator') ?? '';
        }

        if (! $existing && ($degree === null || empty($majors))) {
            $this->skipped[] = [
                'row' => $group['first_row'], 'room' => $room,
                'reason' => 'New room needs at least one Major row and a Degree filled in to be created.',
            ];
            return;
        }

        $attributes = ['exam_term_id' => $this->term->id, 'room' => $room];

        if ($degree !== null) {
            $attributes['degree'] = $degree;
        }
        if ($shift !== null) {
            $attributes['shift'] = $shift;
        }
        if ($examDate !== null) {
            $attributes['exam_date'] = $examDate;
        }
        if (! empty($majors)) {
            $attributes['majors'] = $majors;
            $attributes['major'] = $majors[0]['major'];
            $attributes['student_total'] = array_sum(array_column($majors, 'total'));
            // Kept positional with $majors (blank = no invigilator on that
            // row), not filtered — the edit modal pairs invigilators[i]
            // with majors[i] by index, and dropping blanks would shift
            // every invigilator after a gap onto the wrong major.
            $attributes['invigilators'] = $invigilators;
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

    /**
     * Same as nullableCell(), but for the Exam Date column specifically:
     * validates the value parses as a real date instead of letting a
     * stray value (seen in practice: "Morning" typed into this column)
     * reach the model's date cast, where it throws and used to take the
     * whole import down with it. An unparseable value is reported and
     * treated as blank (left untouched on an UPDATE, omitted on a CREATE)
     * rather than blocking the row.
     */
    private function parseDateCell(Collection $row, string $key, string $room): ?string
    {
        $value = $this->nullableCell($row, $key);
        if ($value === null) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            $this->skipped[] = [
                'row' => null, 'room' => $room,
                'reason' => "Exam Date \"{$value}\" isn't a valid date — left unchanged.",
            ];
            return null;
        }
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
