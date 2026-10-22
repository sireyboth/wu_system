<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamTermRequest;
use App\Http\Resources\ExamTermResource;
use App\Models\ExamTerm;
use Illuminate\Http\Request;

class ExamTermController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Exam Term';
        $this->model         = ExamTerm::class;
        $this->resource      = ExamTermResource::class;
        $this->relationships = ['campus', 'category'];
    }

    public function index(Request $request)
    {
        return $this->list($request, fn($query) => $request->boolean('active') ? $query->active() : $query);
    }

    public function store(ExamTermRequest $request)
    {
        return $this->save($request);
    }

    public function show(ExamTerm $examTerm)
    {
        return $this->view($examTerm);
    }

    public function update(ExamTermRequest $request, ExamTerm $examTerm)
    {
        return $this->release($request, $examTerm);
    }

    public function destroy(ExamTerm $examTerm)
    {
        return $this->disable($examTerm);
    }

    public function restore(ExamTerm $examTerm)
    {
        return $this->enable($examTerm);
    }

    public function force_destroy(ExamTerm $examTerm)
    {
        return $this->clear($examTerm);
    }
}
