<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlertRequest;
use App\Http\Resources\AlertResource;
use App\Models\Alert;
use App\Services\TelegramNotifier;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Alert';
        $this->model    = Alert::class;
        $this->resource = AlertResource::class;
    }

    public function index(Request $request)
    {
        return $this->list($request, function ($query) use ($request) {
            if ($request->boolean('trashed')) {
                $query->onlyTrashed();
            }
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }
            return $query;
        });
    }

    /**
     * Un-paginated board data for the dashboard cards — pending only,
     * bucketed into High/Medium/Later client-side using the resource's
     * server-computed `days_until_start` (single source of truth for the
     * date math, not duplicated in JS). Completed alerts can pile up
     * indefinitely, so they're not part of this un-paginated payload — see
     * index(?status=completed) for the paginated Done table instead.
     */
    public function dashboard()
    {
        $pending = Alert::query()
            ->where('status', 'pending')
            ->orderBy('start_date')
            ->get();

        return has_data(AlertResource::collection($pending));
    }

    public function store(AlertRequest $request)
    {
        $response = $this->save($request);
        $response->resource->log('created', 'Alert created');
        return $response;
    }

    public function show(Alert $alert)
    {
        return $this->view($alert);
    }

    public function update(AlertRequest $request, Alert $alert)
    {
        return $this->release($request, $alert);
    }

    public function destroy(Alert $alert)
    {
        return $this->disable($alert);
    }

    public function restore(Alert $alert)
    {
        return $this->enable($alert);
    }

    public function force_destroy(Alert $alert)
    {
        return $this->clear($alert);
    }

    /**
     * Marks an alert done — or, if it repeats, silently re-arms it to its
     * next occurrence instead (see Alert::completeOrAdvance()).
     */
    public function complete(Alert $alert)
    {
        $wasRepeating = $alert->repeat_type !== 'none';
        $alert->completeOrAdvance();
        $alert->refresh();

        $message = $alert->telegramMessage('completed', $wasRepeating ? ['next_start' => $alert->start_date] : []);

        $ok = TelegramNotifier::send($message);
        $alert->log('completed', $message, $ok);

        return new AlertResource($this->reload($alert));
    }

    /**
     * Pauses reminders until a chosen time. Accepts either `minutes` (e.g.
     * 60, 180) for a relative snooze, or an explicit `until` datetime.
     */
    public function snooze(Request $request, Alert $alert)
    {
        $validated = $request->validate([
            'minutes' => 'nullable|integer|min:1',
            'until'   => 'nullable|date|after:now',
        ]);

        $until = isset($validated['until'])
            ? now()->parse($validated['until'])
            : now()->addMinutes($validated['minutes'] ?? 60);

        $alert->update(['snoozed_until' => $until]);
        $alert->log('snoozed', "Snoozed until {$until->format('Y-m-d H:i')}");

        return new AlertResource($this->reload($alert));
    }

    public function logs(Alert $alert)
    {
        return has_data($alert->logs()->limit(50)->get());
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:alerts,id',
        ]);

        $count = Alert::whereIn('id', $validated['ids'])->count();
        Alert::whereIn('id', $validated['ids'])->delete();

        return has_data(null, "{$count} alert(s) moved to trash.");
    }
}
