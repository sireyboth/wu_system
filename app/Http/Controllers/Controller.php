<?php
namespace App\Http\Controllers;

use function App\Helpers\handle;
use function App\Helpers\no_data;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class Controller
{
    protected string $name;
    protected string $model;
    protected string $resource;
    protected array $relationships = [];

    protected function not_found(string $id = '', string $message = 'not found'): JsonResponse
    {
        return no_data("{$this->name} {$message} with ID: {$id}", 404);
    }

    protected function list(Request $request)
    {
        $per_page = $request->integer('per_page', 10);
        $response = $this->model::query()
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->when($request->filled('with'), fn($q) => $q->with(explode(',', $request->with)))
            ->paginate($per_page);
        $this->related($response);

        return $this->resource::collection($response);
    }

    protected function save(FormRequest $request)
    {
        return handle(function () use ($request) {
            $data     = $request->validated();
            $response = $this->model::create($data);
            $this->related($response);

            return new $this->resource($response->fresh());
        }, "{$this->name} created successfully", 201);
    }

    protected function view(string $id)
    {
        $response = $this->model::find($id);
        if (! $response) {
            return $this->not_found($id);
        }
        $this->related($response);

        return new $this->resource($response);
    }

    protected function release(FormRequest $request, string $id)
    {
        $response = $this->model::find($id);
        if (! $response) {
            return $this->not_found($id, 'not matched');
        }

        return handle(function () use ($request, $response) {
            $response->update($request->validated());
            $this->related($response);

            return new $this->resource($response->fresh());
        }, "{$this->name} updated successfully.");
    }

    protected function disable(string $id)
    {
        $response = $this->model::find($id);
        if (! $response) {
            return $this->not_found($id, 'not exist');
        }

        return handle(function () use ($id) {
            $this->model::withTrashed()->findOrFail($id)->delete();

            return null;
        }, "{$this->name} deleted successfully.");
    }

    protected function enable(string $id)
    {
        $response = $this->model::find($id);
        if (! $response) {
            return $this->not_found($id, 'not exist');
        }

        return handle(function () use ($id) {
            $response = $this->model::withTrashed()->findOrFail($id);
            $response->restore();
            $this->related($response);

            return new $this->resource($response->fresh());
        }, "{$this->name} restored successfully.");
    }

    protected function clear(string $id)
    {
        $response = $this->model::find($id);
        if (! $response) {
            return $this->not_found($id, 'not exist');
        }

        return handle(function () use ($id) {
            $response = $this->model::withTrashed()->findOrFail($id);
            $response->forceDelete();

            return $response;
        }, "{$this->name} permanently deleted.");
    }

    private function related(mixed $data)
    {
        return $this->relationships ? $data->loadMissing($this->relationships) : [];
    }
}
