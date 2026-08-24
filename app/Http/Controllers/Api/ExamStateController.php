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
        $this->model    = ExamState::class;
        $this->resource = ExamStateResource::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->list($request, fn($query) => $request->boolean('trashed') ? $query->onlyTrashed() : $query);
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
        return $this->save($request, $examState);
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
    public function empty(ExamState $examState)
    {
        return $this->clear($examState);
    }

    /**
     * Move multiple resources to trash in one request.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:exam_states,id',
        ]);

        $count = ExamState::whereIn('id', $validated['ids'])->count();
        ExamState::whereIn('id', $validated['ids'])->delete();

        return has_data(null, "{$count} room(s) moved to trash.");
    }

    public function report(Request $request)
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        return $this->summarize($validated['from'] ?? null, $validated['to'] ?? null);
    }
}
