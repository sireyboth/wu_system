<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvigilatorRequest;
use App\Http\Resources\InvigilatorResource;
use App\Models\Invigilator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvigilatorController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Invigilator';
        $this->model         = Invigilator::class;
        $this->resource      = InvigilatorResource::class;
        $this->relationships = ['histories'];
    }

    public function index(Request $request)
    {
        return $this->list($request);
    }

    /**
     * Profile + its history rows in one transaction — the form saves both
     * together, so a bad history row never leaves a half-saved invigilator.
     */
    public function store(InvigilatorRequest $request)
    {
        return execute(function () use ($request) {
            $data        = $request->validated();
            $invigilator = Invigilator::create(collect($data)->except('histories')->all());

            $this->syncHistories($invigilator, $data['histories'] ?? []);

            return new InvigilatorResource($this->reload($invigilator));
        });
    }

    public function show(Invigilator $invigilator)
    {
        return $this->view($invigilator);
    }

    public function update(InvigilatorRequest $request, Invigilator $invigilator)
    {
        return execute(function () use ($request, $invigilator) {
            $data = $request->validated();
            $invigilator->update(collect($data)->except('histories')->all());

            // Only touch history when the form actually sent the block, so
            // a profile-only update can't wipe it by omission.
            if (array_key_exists('histories', $data)) {
                $this->syncHistories($invigilator, $data['histories']);
            }

            return new InvigilatorResource($this->reload($invigilator));
        });
    }

    public function destroy(Invigilator $invigilator)
    {
        return $this->disable($invigilator);
    }

    public function restore(Invigilator $invigilator)
    {
        return $this->enable($invigilator);
    }

    public function force_destroy(Invigilator $invigilator)
    {
        $path = $invigilator->photo_path;
        $response = $this->clear($invigilator);
        if ($path) {
            Storage::disk('public')->delete($path);
        }

        return $response;
    }

    /**
     * Its own multipart endpoint rather than part of store/update, so the
     * main form stays plain JSON (nested histories) — the page saves the
     * profile first, then uploads the photo against the returned id.
     */
    public function uploadPhoto(Request $request, Invigilator $invigilator)
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $old = $invigilator->photo_path;
        $invigilator->forceFill([
            'photo_path' => $validated['photo']->store('invigilator-photos', 'public'),
        ])->save();

        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return new InvigilatorResource($this->reload($invigilator));
    }

    public function deletePhoto(Invigilator $invigilator)
    {
        if ($invigilator->photo_path) {
            Storage::disk('public')->delete($invigilator->photo_path);
            $invigilator->forceFill(['photo_path' => null])->save();
        }

        return new InvigilatorResource($this->reload($invigilator));
    }

    /**
     * Makes the saved history match the submitted list: rows with an id
     * (that belong to THIS invigilator) are updated, rows without one are
     * created, and saved rows left out of the list are deleted. An id from
     * another invigilator is treated as a new row, never updated in place.
     */
    protected function syncHistories(Invigilator $invigilator, array $rows): void
    {
        $existing = $invigilator->histories()->get()->keyBy('id');
        $keep     = [];

        foreach ($rows as $row) {
            $fields = collect($row)->only(['description', 'date', 'rating', 'remark'])->all();
            $model  = isset($row['id']) ? $existing->get($row['id']) : null;

            if ($model) {
                $model->update($fields);
            } else {
                $model = $invigilator->histories()->create($fields);
            }

            $keep[] = $model->id;
        }

        $existing->except($keep)->each->delete();
    }
}
