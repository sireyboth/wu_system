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
        return $this->list($request);
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
