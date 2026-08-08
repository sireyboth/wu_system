<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamStateRequest;
use App\Http\Resources\ExamStateResource;
use App\Models\ExamState;
use Illuminate\Http\Request;

class ExamStateController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Exam State';
        $this->model    = ExamState::class;
        $this->resource = ExamStateResource::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->list($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExamStateRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamState $examState)
    {
        return $this->view($examState);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExamStateRequest $request, ExamState $examState)
    {
        return $this->release($request, $examState);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(ExamState $examState)
    {
        return $this->disable($examState);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(ExamState $examState)
    {
        return $this->enable($examState);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(ExamState $examState)
    {
        return $this->clear($examState);
    }
}
