<?php
namespace App\Http\Controllers;

use App\Helpers\Generic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    protected function list(Request $request)
    {
        $per_page = $request->integer('per_page', 10);
        // $response = $this->model::query()
        //     ->when($request->filled('search'), fn($q) => $q->search($request->search))
        //     ->when($request->filled('with'), fn($q) => $q->with(explode(',', $request->with)))
        //     ->orderByDesc('created_at')
        //     ->paginate($per_page);
        $response = $this->model::query()->search($request->search)
            ->with($this->relationships)->orderByDesc('created_at')->paginate($per_page);
        $this->related($response);

        return $this->resource::collection($response);
    }

    protected function save(FormRequest $request, ?array $columns = [])
    {
        $response = $this->model::create([ ...$request->validated(), ...$columns]);
        $this->related($response);

        return new $this->resource($response->fresh()->load($this->relationships));
    }

    protected function view(Model $response)
    {
        $this->related($response);
        return new $this->resource($response->fresh()->load($this->relationships));
    }

    protected function release(FormRequest $request, Model $response, ?array $columns = [])
    {
        $response->update([ ...$request->validated(), ...$columns]);
        $this->related($response);

        return new $this->resource($response->fresh()->load($this->relationships));
    }

    protected function disable(Model $response)
    {
        $response->delete();
        return has_data(null, 'Moved to trash.');
    }

    protected function enable(Model $response)
    {
        $response->restore();
        $this->related($response);

        return new $this->resource($response->fresh()->load($this->relationships));
    }

    protected function clear(Model $response)
    {
        $response->forceDelete();
        return has_data(null, 'Permanently deleted.');
    }

    private function related(mixed $data)
    {
        return $this->relationships ? $data->load($this->relationships) : [];
    }
}
