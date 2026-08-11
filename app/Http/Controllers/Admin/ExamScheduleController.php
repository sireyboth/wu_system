<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamStateResource;
use App\Models\ExamState;
use Illuminate\Http\Request;

class ExamScheduleController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Exam State';
        $this->model    = ExamState::class;
        $this->resource = ExamStateResource::class;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('admin.exam-schedule.index');
    }
}
