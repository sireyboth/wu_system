<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StateExamController extends Controller
{
    public function index()
    {
       return view('stateExam.index');
    }

    /**
     * Public landing page: 3 session cards. No auth — on-site staff use this.
     */
    public function attendance()
    {
        return view('stateExam.attendance.index', ['rounds' => $this->rounds]);
    }

    /**
     * Public search dashboard for a single session/round.
     */
    public function attendanceSearch(int $round)
    {
        abort_unless($round >= 1 && $round <= count($this->rounds), 404);

        return view('stateExam.attendance.search', [
            'round'      => $round,
            'roundLabel' => $this->rounds[$round - 1],
        ]);
    }

    /**
     * Admin-only attendance report page (charts + KPIs). No modal — a full page.
     */
    public function report()
    {
        return view('stateExam.report.index');
    }
}
