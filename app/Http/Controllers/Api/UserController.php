<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->name     = 'User';
        $this->model    = User::class;
        $this->resource = UserResource::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit    = $request->integer('per_page', 10);
        $response = $this->model::query()->with('roles');

        if ($sort = $request->input('sort')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $column    = ltrim($sort, '-');
            $response->orderBy($column, $direction);
        } else {
            $response->latest();
        }

        return $this->resource::collection($response->paginate($limit)->onEachSide(1));
    }

    /**
     * Reassigns a user's role(s) — the only thing this admin surface edits
     * about another account. A user's own profile page handles name/password.
     */
    public function updateRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles'   => 'array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return new UserResource($user->fresh('roles'));
    }
}
