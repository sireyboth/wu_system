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
        $this->model    = User::class;
        $this->resource = UserResource::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->integer('per_page', 10);
        $query = $this->model::query();

        if ($search = $request->input('search')) {
            $fields = $request->input('by');
            $fields = $fields ? explode(',', $fields) : null;

            $query->search($search, $fields); // scopeSearch from IModel
        }

        if ($sort = $request->input('sort')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $column    = ltrim($sort, '-');
            $query->orderBy($column, $direction);
        } else {
            $query->latest();
        }

        return $this->resource::collection($query->paginate($limit)->onEachSide(1));
    }
}
