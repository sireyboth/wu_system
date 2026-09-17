<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamCategoryRequest;
use App\Http\Resources\ExamCategoryResource;
use App\Models\ExamCategory;
use Illuminate\Http\Request;

class ExamCategoryController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Exam Category';
        $this->model    = ExamCategory::class;
        $this->resource = ExamCategoryResource::class;
    }

    public function index(Request $request)
    {
        return $this->list($request);
    }

    public function store(ExamCategoryRequest $request)
    {
        return $this->save($request);
    }

    public function show(ExamCategory $examCategory)
    {
        return $this->view($examCategory);
    }

    public function update(ExamCategoryRequest $request, ExamCategory $examCategory)
    {
        return $this->release($request, $examCategory);
    }

    public function destroy(ExamCategory $examCategory)
    {
        return $this->disable($examCategory);
    }

    public function restore(ExamCategory $examCategory)
    {
        return $this->enable($examCategory);
    }

    public function force_destroy(ExamCategory $examCategory)
    {
        return $this->clear($examCategory);
    }
}
