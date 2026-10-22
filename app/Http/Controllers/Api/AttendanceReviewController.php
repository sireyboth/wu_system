<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceVerification;
use Illuminate\Http\Request;

/**
 * Registrar's queue: flagged scans (risk-scored, never blocked at scan
 * time) and pending corrections (a lecturer asking to change a locked
 * record). Both are read-only until a human here acts on them — nothing
 * in this controller runs automatically. See attendance schema doc §6/§7.
 */
class AttendanceReviewController extends Controller
{
    public function index(Request $request)
    {
        $verifications = AttendanceVerification::whereNull('reviewed_at')
            ->with(['attendanceRecord.student.person', 'attendanceRecord.classSession.classSection.subject'])
            ->latest()
            ->paginate($request->integer('per_page', 20), ['*'], 'verifications_page');

        $corrections = AttendanceCorrection::where('status', 'pending')
            ->with([
                'attendanceRecord.student.person',
                'attendanceRecord.classSession.classSection.subject',
                'requester',
            ])
            ->latest()
            ->paginate($request->integer('per_page', 20), ['*'], 'corrections_page');

        return has_data([
            'flagged_scans'      => $verifications->through(fn($v) => $this->verificationPayload($v)),
            'pending_corrections' => $corrections->through(fn($c) => $this->correctionPayload($c)),
        ]);
    }

    private function studentLabel($record): string
    {
        $person = $record->student->person ?? null;
        $name   = trim(($person->first_name_kh ?? '') . ' ' . ($person->last_name_kh ?? ''))
            ?: trim(($person->first_name ?? '') . ' ' . ($person->last_name ?? ''));

        return trim(($record->student->code ?? '') . ' — ' . $name, ' —');
    }

    private function classLabel($record): string
    {
        $class = $record->classSession->classSection;
        return trim(($class->code ?? '') . ' (' . ($class->subject->code ?? '') . ')');
    }

    private function verificationPayload(AttendanceVerification $verification): array
    {
        $record = $verification->attendanceRecord;
        return [
            'id'         => $verification->id,
            'risk_score' => $verification->risk_score,
            'signals'    => $verification->signals,
            'student'    => $this->studentLabel($record),
            'class'      => $this->classLabel($record),
            'marked_at'  => $record->marked_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function correctionPayload(AttendanceCorrection $correction): array
    {
        $record = $correction->attendanceRecord;
        return [
            'id'           => $correction->id,
            'student'      => $this->studentLabel($record),
            'class'        => $this->classLabel($record),
            'old_status'   => $correction->old_status,
            'new_status'   => $correction->new_status,
            'reason'       => $correction->reason,
            'requested_by' => $correction->requester?->name,
            'created_at'   => $correction->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Dismisses a flag. Two outcomes: plain "dismiss" (the flag was a
     * false positive — e.g. a legitimate borrowed phone — record stays
     * present, nothing else changes) or "mark_absent" (the flag was
     * right — this was buddy-punching — so besides marking the flag
     * reviewed, this is the one path that also corrects the record
     * itself, since a flagged scan is by definition not locked yet, so
     * the normal correction-request/approval flow doesn't apply here.
     */
    public function reviewVerification(Request $request, AttendanceVerification $verification)
    {
        $validated = $request->validate(['decision' => 'nullable|in:dismiss,mark_absent']);
        $decision  = $validated['decision'] ?? 'dismiss';

        return execute(function () use ($verification, $decision) {
            if ($decision === 'mark_absent') {
                $verification->attendanceRecord->update(['status' => 'absent']);
            }

            $verification->update(['reviewed_by' => auth()->id(), 'reviewed_at' => now()]);

            return has_data(null, $decision === 'mark_absent' ? 'Marked absent and flag reviewed.' : 'Marked reviewed.');
        });
    }

    /**
     * Approving is the one place that actually writes to attendance_records
     * — a correction request by itself never does.
     */
    public function decideCorrection(Request $request, AttendanceCorrection $correction)
    {
        $validated = $request->validate(['decision' => 'required|in:approve,reject']);

        if ($correction->status !== 'pending') {
            return no_data('This correction has already been decided.', 422);
        }

        return execute(function () use ($validated, $correction) {
            if ($validated['decision'] === 'approve') {
                $correction->attendanceRecord->update(['status' => $correction->new_status]);
            }

            $correction->update([
                'status'      => $validated['decision'] === 'approve' ? 'approved' : 'rejected',
                'approved_by' => auth()->id(),
            ]);

            return has_data(null, $validated['decision'] === 'approve' ? 'Correction approved and applied.' : 'Correction rejected.');
        });
    }
}
