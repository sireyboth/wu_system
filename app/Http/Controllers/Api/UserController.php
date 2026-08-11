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
        $response = $this->model::query();

        if ($sort = $request->input('sort')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $column    = ltrim($sort, '-');
            $response->orderBy($column, $direction);
        } else {
            $response->latest();
        }

        return $this->resource::collection($response->paginate($limit)->onEachSide(1));
    }
}
