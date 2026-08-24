<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TermRequest;
use App\Http\Resources\TermResource;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function __construct()
    {
        $this->model    = Term::class;
        $this->resource = TermResource::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->list($request, function (Builder $query) use ($request) {
            if ($request->active == true) {
                return $query->active();
            }

            if ($request->year === date('Y')) {
                return $query->currentYear();
            }

            return $query;
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TermRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Term $term)
    {
        return $this->view($term);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TermRequest $request, Term $term)
    {
        return $this->release($request, $term);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Term $term)
    {
        return $this->disable($term);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Term $term)
    {
        return $this->enable($term);
    }

    /**
     * Trash the specified resource from storage.
     */
    public function empty(Term $term)
    {
        return $this->clear($term);
    }
}
