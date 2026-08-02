<?php
namespace App\Http\Controllers;

use App\Helpers\Generic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

abstract class Controller
{
    use Generic;

    protected string $name;
    protected string $model;
    protected string $resource;
    protected array|string $relationships = [];

    protected function not_found(string $id = '', string $message = 'not found'): JsonResponse
    {
        return no_data("{$this->name} {$message} with ID: {$id}", 404);
    }

    protected function list(Request $request,  ? callable $fn = null)
    {
        $limit    = $request->integer('per_page', 10);
        $response = $this->model::query();

        //     ->when($request->filled('search'), fn($q) => $q->search($request->search))
        //     ->when($request->filled('with'), fn($q) => $q->with(explode(',', $request->with)))
        //     ->orderByDesc('created_at')
        //     ->paginate($per_page);

        if ($fn) {
            $response = $fn($response);
        }
        $response = $response->search($request->search)->with($this->relationships);

        return $this->resource::collection($response->latest()->paginate($limit));
    }

    protected function save(FormRequest $request, ?array $columns = [])
    {
        $response = ($this->model)::create(array_merge($request->validated(), $columns));
        return new $this->resource($this->reload($response));
    }

    protected function view(Model $response)
    {
        return new $this->resource($this->reload($response));
    }

    protected function release(FormRequest $request, Model $response, ?array $columns = [])
    {
        $response->update(array_merge($request->validated(), $columns));
        return new $this->resource($this->reload($response));
    }

    protected function disable(Model $response)
    {
        $response->delete();
        return has_data(null, 'Moved to trash.');
    }

    protected function enable(Model $response)
    {
        $response->restore();
        return new $this->resource($this->reload($response));
    }

    protected function clear(Model $response)
    {
        $response->forceDelete();
        return has_data(null, 'Permanently deleted.');
    }

    protected function reload(object $data)
    {
        return $data->fresh()->load($this->relationships ?? []);
    }

    protected function export(object $data, string $file_name = 'student')
    {
        $time = now()->format('Y_m_d_His');
        return Excel::download($data, "{$file_name}_{$time}.xlsx");
    }

    protected function import(object $data, UploadedFile $file) : array
    {
        Excel::import($data, $file);
        return [
            'imported' => true,
            'failures' => $data->failures(), // rows that failed validation
        ];
    }

    protected function withStudent(string $key = 'student')
    {
        return array_map(fn($s) => "{$key}.{$s}",
            array_merge(['person', 'guardians'], $this->withPerson())
        );
    }

    protected function withPerson(string $key = 'person', string $otherKey = 'addresses')
    {
        return array_map(fn($p) => "{$key}.{$p}",
            array_merge(['nationality', 'addresses'],
                array_map(fn($a) => "{$otherKey}.$a", ['province', 'district', 'commune', 'village'])
            )
        );
    }
}
