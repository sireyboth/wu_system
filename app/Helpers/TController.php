<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

trait TController
{
    protected string $model;
    protected string $resource;
    protected array|string $relationships = [];
    protected array $rounds                 = ['ម៉ោងទី១', 'ម៉ោងទី២', 'ម៉ោងទី៣'];

    protected function list(Request $request,  ? callable $queryModifier = null)
    {
        $limit = min(max($request->integer('per_page', 10), 1), 100);
        $query = $this->model::query();

        if ($queryModifier) {
            $query = $queryModifier($query) ?? $query;
        }

        if ($search = $request->input('search')) {
            $fields = $request->input('by');
            $fields = $fields ? explode(',', $fields) : null;

            $query->search($search, $fields);
        }

        if ($sort = $request->input('sort')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $column    = ltrim($sort, '-');

            $allowedSorts = $query->getModel()->getFillable();

            if (in_array($column, $allowedSorts, true)) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->latest();
        }

        $query->with($this->relationships);

        return $this->resource::collection($query->paginate($limit)->onEachSide(1));
    }

    protected function save(FormRequest $request, ?Model $model = null, ?array $columns = [])
    {
        $data = array_merge($request->validated(), $columns ?? []);

        // If model not passed, but id exists in request → find it
        if (! $model && ! empty($data['id'])) {
            $model = ($this->model)::findOrFail($data['id']);
        }

        $response = $model
            ? tap($model)->update($data)
            : ($this->model)::create($data);

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

    public function summarize(?string $from = null, ?string $to = null) : array
    {
        $rooms = $this->model::query()
            ->when($from, fn($q) => $q->whereDate('exam_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('exam_date', '<=', $to))
            ->get(['student_total', 'absences']);

        $summary = array_fill(0, count($this->rounds), ['total' => 0, 'absent' => 0]);

        // absences is one entry per session/round, positionally: absences[0] = round 1, etc.
        foreach ($rooms as $room) {
            foreach ($this->rounds as $round => $label) {
                $summary[$round]['total']  += $room->student_total ?? 0;
                $summary[$round]['absent'] += $room->absences[$round]['total'] ?? 0;
            }
        }

        return collect($summary)->map(function ($data, $i) use ($label) {
            $present = $data['total'] - $data['absent'];
            return [
                'label'   => $this->rounds[$i] ?? $label,
                'total'   => $data['total'],
                'present' => $present,
                'absent'  => $data['absent'],
                'percent' => $data['total'] > 0 ? round($present / $data['total'] * 100) : 0,
            ];
        })->all();
    }
}
