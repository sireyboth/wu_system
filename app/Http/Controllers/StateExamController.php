<?php
namespace App\Http\Controllers;

use App\Models\ExamTerm;

class StateExamController extends Controller
{
    public function index()
    {
        return view('state-exam.index');
    }

    /**
     * Public landing page — lists every ACTIVE exam term (State Exam,
     * Scholarship, whatever's currently live) as a card. A deactivated
     * term simply never appears here; on-site staff pick the exam
     * they're actually working today, then that term's own real time
     * slots on the next screen.
     */
    public function attendance()
    {
        $examTerms = ExamTerm::active()->with('category')->orderByDesc('exam_date')->get();

        return view('state-exam.attendance.index', compact('examTerms'));
    }

    /**
     * Public — this exam term's own time slots, however many it has.
     */
    public function attendanceTerm(ExamTerm $examTerm)
    {
        abort_unless($examTerm->is_active, 404);

        return view('state-exam.attendance.term', compact('examTerm'));
    }

    /**
     * Public search dashboard for one specific term + time slot.
     */
    public function attendanceSearch(ExamTerm $examTerm, int $slot)
    {
        abort_unless($examTerm->is_active, 404);

        $slots = $examTerm->time_slots ?? [];
        abort_unless($slot >= 1 && $slot <= count($slots), 404);

        return view('state-exam.attendance.search', [
            'examTerm'  => $examTerm,
            'slot'      => $slot,
            'slotLabel' => $slots[$slot - 1],
        ]);
    }

    /**
     * Admin-only attendance report page (charts + KPIs). No modal — a full page.
     */
    public function report()
    {
        return view('state-exam.report.index');
    }

    /**
     * Public invigilator duty-card lookup. No auth, no geofence — meant to be
     * shared with invigilators ahead of time so they can look up their own
     * room / floor / shift before arriving on campus.
     */
    public function invigilators()
    {
        return view('state-exam.invigilators.index');
    }
}
