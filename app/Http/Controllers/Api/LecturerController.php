<?php
namespace App\Http\Controllers\Api;

use App\Exports\LecturerExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\LecturerRequest;
use App\Http\Resources\LecturerResource;
use App\Imports\LecturerImport;
use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class LecturerController extends Controller
{
    public function __construct()
    {
        $this->name     = 'Lecturer';
        $this->model    = Lecturer::class;
        $this->resource = LecturerResource::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->list($request);
    }

    public function exportList(Request $request)
    {
        return $this->export(
            new LecturerExport($request->only(['search'])),
            'lecturers'
        );
    }

    /**
     * Bulk lecturer import — see LecturerImport's docblock for the exact
     * column contract and what gets skipped vs created.
     */
    public function importFile(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new LecturerImport();

        try {
            Excel::import($import, $validated['file']);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lecturer import failed', ['error' => $e->getMessage()]);
            return no_data('The file could not be processed. Please check it is a valid, correctly formatted spreadsheet.', 422);
        }

        return has_data(['report' => $import->report()], 'Import complete.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LecturerRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lecturer $lecturer)
    {
        return $this->view($lecturer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LecturerRequest $request, Lecturer $lecturer)
    {
        return $this->release($request, $lecturer);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Lecturer $lecturer)
    {
        return $this->disable($lecturer);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Lecturer $lecturer)
    {
        return $this->enable($lecturer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Lecturer $lecturer)
    {
        return $this->clear($lecturer);
    }

    /**
     * Creates a login for this lecturer and grants the Lecturer role —
     * deliberately its own endpoint rather than reusing /register, which
     * calls Auth::login() on the new account and would log the acting
     * registrar out of their own session.
     */
    public function createAccount(Request $request, Lecturer $lecturer)
    {
        if ($lecturer->user_id) {
            return no_data('This lecturer already has a login account.', 422);
        }

        $validated = $request->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        return execute(function () use ($validated, $lecturer) {
            $user = User::create([
                'name'     => trim("{$lecturer->name_en}") ?: $lecturer->code,
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            $user->assignRole('Lecturer');
            $lecturer->update(['user_id' => $user->id]);

            return has_data(['user_id' => $user->id, 'email' => $user->email], "Login created for {$lecturer->code}.");
        });
    }

    /**
     * Permanently delete multiple lecturers in one request — same
     * hard-delete behavior as force_destroy() above, just batched. Pass
     * {"all": true} to wipe every lecturer instead of listing ids
     * individually. Deletes via a raw DB::table() query rather than
     * looping per-model, same reasoning as SubjectController::bulkDestroy.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'all'   => 'sometimes|boolean',
            'ids'   => 'sometimes|array|min:1',
            'ids.*' => 'integer|exists:lecturers,id',
        ]);

        $all = $validated['all'] ?? false;
        if (! $all && empty($validated['ids'])) {
            return no_data('Either "ids" (non-empty array) or "all": true is required.', 422);
        }

        return execute(function () use ($validated, $all) {
            $query = Lecturer::withTrashed();

            if (! $all) {
                $query->whereIn('id', $validated['ids']);
            }

            $ids   = $query->pluck('id');
            $count = $ids->count();

            \Illuminate\Support\Facades\DB::table('lecturers')->whereIn('id', $ids)->delete();

            return has_data(null, "{$count} lecturer(s) permanently deleted.");
        });
    }
}
