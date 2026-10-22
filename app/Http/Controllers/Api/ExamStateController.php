<?php
namespace App\Http\Controllers\Api;

use App\Exports\ExamStateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExamStateRequest;
use App\Http\Resources\ExamStateResource;
use App\Imports\ExamStateImport;
use App\Models\ExamState;
use App\Models\ExamTerm;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExamStateController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Exam State';
        $this->model         = ExamState::class;
        $this->resource      = ExamStateResource::class;
        $this->relationships = ['examTerm.category'];
    }

    /**
     * Display a listing of the resource. Supports two independent
     * scoping params: `exam_term_id` (an explicit term, used once a
     * public flow or an admin filter has picked one) and
     * `active_terms_only` (used by pages with no term-picker step, e.g.
     * the invigilator search box, so a deactivated term's rooms simply
     * never show up there — the admin index deliberately never passes
     * either, so registrars keep seeing every room regardless of term
     * status).
     */
    public function index(Request $request)
    {
        return $this->list($request, function ($query) use ($request) {
            $query = $request->boolean('trashed') ? $query->onlyTrashed() : $query;

            if ($request->filled('exam_term_id')) {
                $query->where('exam_term_id', $request->input('exam_term_id'));
            }

            if ($request->boolean('active_terms_only')) {
                $query->whereHas('examTerm', fn($q) => $q->where('is_active', true));
            }

            return $query;
        });
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
            'from'         => 'nullable|date',
            'to'           => 'nullable|date|after_or_equal:from',
            'exam_term_id' => 'nullable|integer|exists:exam_terms,id',
        ]);

        return $this->summarize($validated['from'] ?? null, $validated['to'] ?? null, $validated['exam_term_id'] ?? null);
    }

    /**
     * Excel download matching whatever the grid is currently filtered to
     * (same term filter + search as index()) — see ExamStateExport.
     */
    public function exportList(Request $request)
    {
        return $this->export(new ExamStateExport($request->only(['exam_term_id', 'search', 'trashed'])), 'exam-rooms');
    }

    /**
     * Bulk create/fix rooms for a single term at once — see
     * ExamStateImport's docblock for the exact matching/column contract.
     */
    public function importFile(Request $request)
    {
        $validated = $request->validate([
            'file'         => 'required|file|mimes:xlsx,xls,csv',
            'exam_term_id' => 'required|integer|exists:exam_terms,id',
        ]);

        $term = ExamTerm::findOrFail($validated['exam_term_id']);

        $import = new ExamStateImport($term);

        try {
            Excel::import($import, $validated['file']);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Exam room import failed', ['error' => $e->getMessage()]);
            return no_data('The file could not be processed. Please check it is a valid, correctly formatted spreadsheet.', 422);
        }

        return has_data(['report' => $import->report()], 'Import complete.');
    }
}
