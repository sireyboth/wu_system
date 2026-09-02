<?php
namespace App\Http\Controllers;

class StateExamController extends Controller
{
    public function index()
    {
        return view('state-exam.index');
    }

    /**
     * Public landing page: 3 session cards. No auth — on-site staff use this.
     */
    public function attendance()
    {
        return view('state-exam.attendance.index', ['rounds' => $this->rounds]);
    }

    /**
     * Public search dashboard for a single session/round.
     */
    public function attendanceSearch(int $round)
    {
        abort_unless($round >= 1 && $round <= count($this->rounds), 404);

        return view('state-exam.attendance.search', [
            'round'      => $round,
            'roundLabel' => $this->rounds[$round - 1],
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
