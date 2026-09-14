<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassScoreConfigRequest;
use App\Http\Requests\ClassScoreRequest;
use App\Http\Resources\ClassScoreConfigResource;
use App\Http\Resources\ClassScoreResource;
use App\Http\Resources\ClassSectionResource;
use App\Http\Resources\CourseEnrollmentResource;
use App\Models\ClassScore;
use App\Models\ClassScoreConfig;
use App\Models\ClassSection;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Everything a lecturer can see/do about their OWN classes only — every
 * method here checks ownership first (via TeacherAssignment, not the
 * registrar's `class.*` permissions), because "which classes" isn't a
 * fixed permission, it's per-row and changes as classes get assigned.
 * Admin still bypasses via AppServiceProvider's Gate::before, same as
 * everywhere else, so an admin testing this sees every class.
 */
class LecturerPortalController extends Controller
{
    /**
     * Aborts if the authenticated user isn't the lecturer on this class
     * (as any teacher_assignment row — primary or substitute).
     */
    private function assertOwnsClass(ClassSection $class): void
    {
        if (auth()->user()->hasRole('Admin')) {
            return;
        }

        $lecturerId = auth()->user()->lecturer?->id;
        $owns = $lecturerId && TeacherAssignment::where('class_id', $class->id)
            ->where('lecturer_id', $lecturerId)
            ->exists();

        if (! $owns) {
            abort(403, "You aren't assigned to this class.");
        }
    }

    /**
     * Every class this lecturer is assigned to (primary or substitute).
     * Admin sees every class, same reasoning as assertOwnsClass().
     */
    public function classes(Request $request)
    {
        $lecturerId = auth()->user()->lecturer?->id;

        $query = ClassSection::query()
            ->with(['subject', 'term', 'campus', 'shift', 'scoreConfig'])
            ->withCount('courseEnrollments');

        if (! auth()->user()->hasRole('Admin')) {
            if (! $lecturerId) {
                return has_data(['data' => []], 'No lecturer profile linked to your account — ask a registrar to link one.');
            }
            $query->whereHas('teacherAssignments', fn($q) => $q->where('lecturer_id', $lecturerId));
        }

        return ClassSectionResource::collection($query->latest()->paginate($request->integer('per_page', 20)));
    }

    public function scoreConfig(ClassSection $class)
    {
        $this->assertOwnsClass($class);
        return new ClassScoreConfigResource(ClassScoreConfig::where('class_id', $class->id)->first());
    }

    public function updateScoreConfig(ClassScoreConfigRequest $request, ClassSection $class)
    {
        $this->assertOwnsClass($class);

        return execute(function () use ($request, $class) {
            $config = ClassScoreConfig::updateOrCreate(
                ['class_id' => $class->id],
                array_merge($request->validated(), ['set_by' => auth()->id()])
            );

            return new ClassScoreConfigResource($config->fresh());
        });
    }

    public function roster(Request $request, ClassSection $class)
    {
        $this->assertOwnsClass($class);

        $enrollments = $class->courseEnrollments()
            ->with(['classSection.subject', 'studentAcademicHistory.student.person', 'classScores'])
            ->paginate($request->integer('per_page', 200));

        return CourseEnrollmentResource::collection($enrollments);
    }

    /**
     * Records one score component for one student — same validation
     * (per-component cap from this class's own config) as the registrar
     * endpoint, plus the ownership check the registrar one doesn't need.
     */
    public function storeScore(ClassScoreRequest $request)
    {
        $enrollment = \App\Models\CourseEnrollment::findOrFail($request->input('course_enrollment_id'));
        $this->assertOwnsClass($enrollment->classSection);

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

            return new ClassScoreResource($score->fresh());
        });
    }
}
