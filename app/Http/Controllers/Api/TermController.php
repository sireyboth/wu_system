<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TermRequest;
use App\Http\Resources\TermResource;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Term';
        $this->model    = Term::class;
        $this->resource = TermResource::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->list($request, fn($query) => $query->orderByDesc('year')->orderByDesc('semester'));
    }

    /**
     * Store a newly created resource in storage. Multiple terms can be
     * active at once (different batches genuinely run on different
     * calendars at the same time), so this no longer deactivates anything
     * else — see Term::resolveDefault() for how "which term did you mean"
     * gets picked when a caller doesn't say explicitly.
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
     * One-click "mark this term active" — flips just this term on, without
     * touching any other term's active flag. The complement, deactivate(),
     * turns it back off.
     */
    public function activate(Term $term)
    {
        return execute(function () use ($term) {
            $term->update(['is_active' => true]);

            return new TermResource($this->reload($term));
        });
    }

    public function deactivate(Term $term)
    {
        return execute(function () use ($term) {
            $term->update(['is_active' => false]);

            return new TermResource($this->reload($term));
        });
    }

    /**
     * Disable the specified resource from storage.
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
     * Remove the specified resource from storage.
     */
    public function force_destroy(Term $term)
    {
        return $this->clear($term);
    }
}
