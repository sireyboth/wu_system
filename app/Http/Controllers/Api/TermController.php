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
     * Store a newly created resource in storage. Only one term is ever
     * active — student_academic_histories rows are stamped with whatever
     * term is active at the moment a student advances, so a brand new
     * term created as active must be the only one, same reasoning as
     * update() below.
     */
    public function store(TermRequest $request)
    {
        return execute(function () use ($request) {
            $data = $request->validated();

            if ($data['is_active'] ?? false) {
                Term::query()->update(['is_active' => false]);
            }

            return $this->save($request);
        });
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
        return execute(function () use ($request, $term) {
            $data = $request->validated();

            if ($data['is_active'] ?? false) {
                Term::where('id', '!=', $term->id)->update(['is_active' => false]);
            }

            return $this->release($request, $term);
        });
    }

    /**
     * One-click "make this the active term" — the primary way a registrar
     * moves the whole school forward a semester, rather than editing the
     * full term form just to flip a checkbox.
     */
    public function activate(Term $term)
    {
        return execute(function () use ($term) {
            Term::where('id', '!=', $term->id)->update(['is_active' => false]);
            $term->update(['is_active' => true]);

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
