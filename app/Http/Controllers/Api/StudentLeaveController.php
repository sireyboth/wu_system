<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentLeaveRequest;
use App\Http\Resources\StudentLeaveResource;
use App\Models\StudentLeave;
use Illuminate\Http\Request;

class StudentLeaveController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Student Leave';
        $this->model         = StudentLeave::class;
        $this->resource      = StudentLeaveResource::class;
        $this->relationships = ['student.person'];
    }

    public function index(Request $request)
    {
        return $this->list($request);
    }

    public function store(StudentLeaveRequest $request)
    {
        return $this->save($request);
    }

    public function show(StudentLeave $student_leave)
    {
        return $this->view($student_leave);
    }

    public function update(StudentLeaveRequest $request, StudentLeave $student_leave)
    {
        return $this->release($request, $student_leave);
    }

    public function destroy(StudentLeave $student_leave)
    {
        return $this->disable($student_leave);
    }

    public function restore(StudentLeave $student_leave)
    {
        return $this->enable($student_leave);
    }

    public function force_destroy(StudentLeave $student_leave)
    {
        return $this->clear($student_leave);
    }

    /**
     * Registrar approve/reject action — separate from the generic update
     * so the approved_by stamp always reflects who actually clicked
     * approve, not whoever last edited any field on the leave request.
     */
    public function decide(Request $request, StudentLeave $student_leave)
    {
        $validated = $request->validate(['status' => 'required|in:approved,rejected']);

        return execute(function () use ($validated, $student_leave) {
            $student_leave->update([
                'status'      => $validated['status'],
                'approved_by' => auth()->id(),
            ]);

            return new StudentLeaveResource($student_leave->fresh()->load($this->relationships));
        });
    }
}
