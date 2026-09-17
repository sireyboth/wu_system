<?php
namespace App\Exports;

use App\Models\ClassSession;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Mirrors ClassSession::attendanceHistoryFor() exactly — one row per
 * student, one column per session date — so what's on screen in the
 * Attendance History grid is exactly what comes out of the file, and
 * AttendanceHistoryImport can round-trip it back in by matching these
 * same column headings to real sessions.
 */
class AttendanceHistoryExport implements FromArray, WithHeadings
{
    private array $sessionHeadings = [];

    public function __construct(private array $history)
    {
        $this->sessionHeadings = collect($this->history['sessions'] ?? [])
            ->map(fn ($s) => ClassSession::historyColumnLabel($s['date'], $s['session_number']))
            ->all();
    }

    public function headings(): array
    {
        return ['Student Code', 'Student Name', ...$this->sessionHeadings];
    }

    public function array(): array
    {
        $sessions = $this->history['sessions'] ?? [];

        return collect($this->history['students'] ?? [])->map(function ($student) use ($sessions) {
            $row = [$student['code'], $student['name']];

            foreach ($sessions as $session) {
                $cell = $student['statuses'][$session['id']] ?? null;
                $status = $cell['status'] ?? null;
                $row[] = $status ? strtoupper($status[0]) : '';
            }

            return $row;
        })->all();
    }
}
