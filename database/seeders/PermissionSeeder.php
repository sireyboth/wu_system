<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Module => label. Alerts is intentionally excluded — every logged-in
     * user can manage alerts regardless of role, so it's never gated.
     */
    protected const MODULES = [
        'student'     => 'Student',
        'batch'       => 'Batch',
        'group'       => 'Group',
        'major'       => 'Major',
        'faculty'     => 'Faculty',
        'campus'      => 'Campus',
        'shift'       => 'Shift',
        'app-status'  => 'Student Status Options',
        'lecturer'    => 'Lecturer',
        'subject'     => 'Subject',
        'state-exam'  => 'State Exam',
        'certificate' => 'Certificate',
        'role'        => 'Roles & Permissions',
    ];

    protected const ACTIONS = ['view', 'create', 'edit', 'delete'];

    protected const ROLE_MODULES = [
        'Enrollment Officer'         => ['student', 'batch', 'group', 'major', 'faculty', 'campus', 'shift', 'app-status'],
        'Exam Officer'               => ['state-exam', 'lecturer', 'subject'],
        'Score/Certificate Officer'  => ['certificate'],
    ];

    public function run(): void
    {
        $allPermissions = [];

        foreach (array_keys(self::MODULES) as $module) {
            foreach (self::ACTIONS as $action) {
                $allPermissions[] = Permission::firstOrCreate(['name' => "{$module}.{$action}"]);
            }
        }

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions($allPermissions);

        foreach (self::ROLE_MODULES as $roleName => $modules) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $permissions = Permission::whereIn(
                'name',
                collect($modules)->flatMap(fn ($m) => collect(self::ACTIONS)->map(fn ($a) => "{$m}.{$a}"))->all()
            )->get();
            $role->syncPermissions($permissions);
        }

        $adminUser = User::where('email', 'admin@system.me')->first();
        $adminUser?->syncRoles([$admin]);
    }
}
