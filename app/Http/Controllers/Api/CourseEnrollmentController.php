<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseEnrollmentRequest;
use App\Http\Resources\CourseEnrollmentResource;
use App\Models\ClassScoreConfig;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Course Enrollment';
        $this->model         = CourseEnrollment::class;
        $this->resource      = CourseEnrollmentResource::class;
        $this->relationships = ['classSection.subject', 'studentAcademicHistory.student.person', 'classScores'];
    }

    public function index(Request $request)
    {
        $response = $this->list($request, fn($query) => $query->when(
            $request->filled('class_id'),
            fn($q) => $q->where('class_id', $request->input('class_id'))
        ));

        // Attendance score only makes sense scoped to one class — attach
        // it when the roster view (always class_id-filtered) asks for it.
        if ($request->filled('class_id')) {
            $enrollments = $response->collection;
            $studentIds  = $enrollments->pluck('studentAcademicHistory.student_id');
            $scores      = ClassScoreConfig::attendanceScoresFor((int) $request->input('class_id'), $studentIds);
            foreach ($enrollments as $enrollment) {
                $enrollment->attendance_score = $scores[$enrollment->studentAcademicHistory->student_id] ?? null;
            }
        }

        return $response;
    }

    public function store(CourseEnrollmentRequest $request)
    {
        return $this->save($request);
    }

    public function show(CourseEnrollment $course_enrollment)
    {
        return $this->view($course_enrollment);
    }

    public function update(CourseEnrollmentRequest $request, CourseEnrollment $course_enrollment)
    {
        return $this->release($request, $course_enrollment);
    }

    public function destroy(CourseEnrollment $course_enrollment)
    {
        return $this->disable($course_enrollment);
    }

    public function restore(CourseEnrollment $course_enrollment)
    {
        return $this->enable($course_enrollment);
    }

    public function force_destroy(CourseEnrollment $course_enrollment)
    {
        return $this->clear($course_enrollment);
    }
}
