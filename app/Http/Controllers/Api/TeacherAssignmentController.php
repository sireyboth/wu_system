<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherAssignmentRequest;
use App\Http\Resources\TeacherAssignmentResource;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;

class TeacherAssignmentController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Teacher Assignment';
        $this->model         = TeacherAssignment::class;
        $this->resource      = TeacherAssignmentResource::class;
        $this->relationships = ['lecturer', 'classSection.subject'];
    }

    public function index(Request $request)
    {
        return $this->list($request);
    }

    public function store(TeacherAssignmentRequest $request)
    {
        return $this->save($request);
    }

    public function show(TeacherAssignment $teacher_assignment)
    {
        return $this->view($teacher_assignment);
    }

    public function update(TeacherAssignmentRequest $request, TeacherAssignment $teacher_assignment)
    {
        return $this->release($request, $teacher_assignment);
    }

    public function destroy(TeacherAssignment $teacher_assignment)
    {
        return $this->disable($teacher_assignment);
    }

    public function restore(TeacherAssignment $teacher_assignment)
    {
        return $this->enable($teacher_assignment);
    }

    public function force_destroy(TeacherAssignment $teacher_assignment)
    {
        return $this->clear($teacher_assignment);
    }
}
