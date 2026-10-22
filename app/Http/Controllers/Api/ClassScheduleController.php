<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassScheduleRequest;
use App\Http\Resources\ClassScheduleResource;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Class Schedule';
        $this->model         = ClassSchedule::class;
        $this->resource      = ClassScheduleResource::class;
        $this->relationships = ['classSection.subject', 'room'];
    }

    public function index(Request $request)
    {
        return $this->list($request);
    }

    public function store(ClassScheduleRequest $request)
    {
        return $this->save($request);
    }

    public function show(ClassSchedule $class_schedule)
    {
        return $this->view($class_schedule);
    }

    public function update(ClassScheduleRequest $request, ClassSchedule $class_schedule)
    {
        return $this->release($request, $class_schedule);
    }

    public function destroy(ClassSchedule $class_schedule)
    {
        return $this->disable($class_schedule);
    }

    public function restore(ClassSchedule $class_schedule)
    {
        return $this->enable($class_schedule);
    }

    public function force_destroy(ClassSchedule $class_schedule)
    {
        return $this->clear($class_schedule);
    }
}
