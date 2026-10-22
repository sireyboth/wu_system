<?php
namespace App\Imports;

use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\CourseEnrollment;
use App\Models\SessionRoster;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

/**
 * Bulk-fix companion to AttendanceHistoryExport — same column shape,
 * round-tripped. A lecturer exports the grid, edits statuses offline
 * (faster than clicking dropdowns one by one), re-uploads.
 *
 * Deliberately NOT WithHeadingRow — Maatwebsite would slugify a date
 * heading like "2026-09-15 (S2)" into something unrecognizable, so this
 * reads row 0 as headings manually and matches each one back to a real
 * class_session via ClassSession::historyColumnLabel(), by exact string,
 * not by position — a lecturer re-ordering or deleting columns still
 * resolves correctly, since a column that no longer matches any real
 * session's label for this class is just skipped, not misapplied to the
 * wrong date.
 *
 * Writes directly to attendance_records, same no-approval behavior as
 * the grid's own dropdowns (see attendance schema doc §5's audit-gap
 * callout) — this is one more door into that same gap, not a new one.
 * A blank cell is left untouched, never used to clear an existing mark —
 * this is a bulk *fix*, not a full overwrite.
 */
class AttendanceHistoryImport implements ToCollection
{
    private const STATUS_MAP = ['P' => 'present', 'A' => 'absent', 'L' => 'late', 'E' => 'excused'];

    private int $updatedCount = 0;
    private array $skipped = [];

    public function __construct(private int $classId)
    {
    }

    public function collection(Collection $rows): void
    {
        if ($rows->count() < 2) {
            return;
        }

        $headingRow = $rows->first();
        $dataRows   = $rows->slice(1)->values();

        $sessionsByLabel = ClassSession::where('class_id', $this->classId)
            ->get(['id', 'session_date', 'session_number'])
            ->keyBy(fn (ClassSession $s) => ClassSession::historyColumnLabel($s->session_date->format('Y-m-d'), $s->session_number));

        $columnSessionIds = [];
        foreach ($headingRow as $col => $heading) {
            if ($col < 2) {
                continue;
            }
            $columnSessionIds[$col] = $sessionsByLabel->get(trim((string) $heading))?->id;
        }

        foreach ($dataRows as $rowIndex => $row) {
            $code = trim((string) ($row[0] ?? ''));
            if ($code === '') {
                continue;
            }

            $student = Student::where('code', $code)->first();
            if (! $student) {
                $this->skipped[] = ['row' => $rowIndex + 2, 'code' => $code, 'reason' => 'Student code not found.'];
                continue;
            }

            $enrollment = CourseEnrollment::where('class_id', $this->classId)
                ->whereHas('studentAcademicHistory', fn ($q) => $q->where('student_id', $student->id))
                ->first();
            if (! $enrollment) {
                $this->skipped[] = ['row' => $rowIndex + 2, 'code' => $code, 'reason' => 'Not enrolled in this class.'];
                continue;
            }

            foreach ($columnSessionIds as $col => $sessionId) {
                if (! $sessionId) {
                    continue;
                }

                $raw = strtoupper(trim((string) ($row[$col] ?? '')));
                if ($raw === '') {
                    continue;
                }

                $status = self::STATUS_MAP[$raw] ?? null;
                if (! $status) {
                    $this->skipped[] = ['row' => $rowIndex + 2, 'code' => $code, 'reason' => "Unrecognized status \"{$raw}\" (use P/A/L/E)."];
                    continue;
                }

                $roster = SessionRoster::where('class_session_id', $sessionId)
                    ->where('course_enrollment_id', $enrollment->id)
                    ->first();
                if (! $roster) {
                    continue;
                }

                AttendanceRecord::updateOrCreate(
                    ['class_session_id' => $sessionId, 'student_id' => $student->id],
                    ['session_roster_id' => $roster->id, 'status' => $status, 'method' => 'manual', 'marked_at' => now()]
                );
                $this->updatedCount++;
            }
        }
    }

    public function report(): array
    {
        return ['updated_count' => $this->updatedCount, 'skipped' => $this->skipped];
    }
}
