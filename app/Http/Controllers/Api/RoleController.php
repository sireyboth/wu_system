<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->get();
        return has_data($roles->map(fn (Role $role) => $this->toRoleArray($role)));
    }

    /**
     * Full permission catalog grouped by module, for building the
     * checkbox grid when creating/editing a role.
     */
    public function permissionCatalog()
    {
        $grouped = Permission::all()
            ->groupBy(fn (Permission $p) => explode('.', $p->name)[0])
            ->map(fn ($perms) => $perms->pluck('name')->values());

        return has_data($grouped);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100|unique:roles,name',
            'permissions'     => 'array',
            'permissions.*'   => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return has_data($this->toRoleArray($role->fresh('permissions')), 'Role created.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions'     => 'array',
            'permissions.*'   => 'string|exists:permissions,name',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return has_data($this->toRoleArray($role->fresh('permissions')), 'Role updated.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Admin') {
            return no_data('The Admin role cannot be deleted.', 422);
        }

        if ($role->users()->exists()) {
            return no_data('Cannot delete a role that is still assigned to users.', 422);
        }

        $role->delete();
        return has_data(null, 'Role deleted.');
    }

    protected function toRoleArray(Role $role): array
    {
        return [
            'id'          => $role->id,
            'name'        => $role->name,
            'permissions' => $role->permissions->pluck('name'),
            'users_count' => $role->users_count ?? $role->users()->count(),
        ];
    }
}
