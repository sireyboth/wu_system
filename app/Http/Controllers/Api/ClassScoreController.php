<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassScoreRequest;
use App\Http\Resources\ClassScoreResource;
use App\Models\ClassScore;
use Illuminate\Http\Request;

class ClassScoreController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Class Score';
        $this->model         = ClassScore::class;
        $this->resource      = ClassScoreResource::class;
        $this->relationships = ['courseEnrollment.studentAcademicHistory.student.person'];
    }

    public function index(Request $request)
    {
        return $this->list($request, fn($query) => $query->when(
            $request->filled('course_enrollment_id'),
            fn($q) => $q->where('course_enrollment_id', $request->input('course_enrollment_id'))
        ));
    }

    /**
     * Upsert, not a plain create — the roster UI re-posts to this endpoint
     * whenever a score cell changes, including cells that already have a
     * value, so a second save for the same student+component must update
     * rather than collide with the (course_enrollment_id, component)
     * unique constraint.
     */
    public function store(ClassScoreRequest $request)
    {
        return execute(function () use ($request) {
            $score = ClassScore::updateOrCreate(
                [
                    'course_enrollment_id' => $request->input('course_enrollment_id'),
                    'component'            => $request->input('component'),
                ],
                [
                    'points'      => $request->input('points'),
                    'recorded_by' => auth()->id(),
                ]
            );

            return new ClassScoreResource($this->reload($score));
        });
    }

    public function show(ClassScore $class_score)
    {
        return $this->view($class_score);
    }

    public function update(ClassScoreRequest $request, ClassScore $class_score)
    {
        return $this->release($request, $class_score, ['recorded_by' => auth()->id()]);
    }

    public function destroy(ClassScore $class_score)
    {
        return $this->disable($class_score);
    }

    public function restore(ClassScore $class_score)
    {
        return $this->enable($class_score);
    }

    public function force_destroy(ClassScore $class_score)
    {
        return $this->clear($class_score);
    }
}
