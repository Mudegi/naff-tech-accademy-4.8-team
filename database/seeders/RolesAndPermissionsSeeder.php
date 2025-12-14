<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Create super admin user first if not exists
        $superAdmin = User::where('email', 'admin@nafftechacademy.com')->first();
        
        if (!$superAdmin) {
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@nafftechacademy.com',
                'email_verified_at' => $now,
                'password' => Hash::make('password'),
                'account_type' => 'admin',
                'remember_token' => \Str::random(10),
            ]);
        }

        // 2. Create roles if not exist
        $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
        if (!$superAdminRole) {
            $superAdminRoleId = DB::table('roles')->insertGetId([
                'name' => 'Super Admin',
                'description' => 'Super Administrator with full access',
                'created_by' => $superAdmin->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $superAdminRoleId = $superAdminRole->id;
        }

        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        if (!$adminRole) {
            $adminRoleId = DB::table('roles')->insertGetId([
                'name' => 'Admin',
                'description' => 'Administrator with limited access',
                'created_by' => $superAdmin->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $adminRoleId = $adminRole->id;
        }

        // 3. Insert Permissions if not exist
        $entities = ['subject', 'topic', 'class'];
        $actions = ['view', 'create', 'edit', 'delete'];
        
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $permissionName = "{$action}_{$entity}";
                $permission = DB::table('permissions')->where('name', $permissionName)->first();
                
                if (!$permission) {
                    DB::table('permissions')->insert([
                        'name' => $permissionName,
                        'description' => ucfirst($action) . " " . ucfirst($entity),
                        'created_by' => $superAdmin->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // 4. Get all permission IDs
        $permissionIds = DB::table('permissions')->pluck('id')->toArray();

        // 5. Assign permissions to roles if not already assigned
        foreach ($permissionIds as $pid) {
            // Check if permission is already assigned to Super Admin role
            $superAdminPermission = DB::table('permission_role')
                ->where('permission_id', $pid)
                ->where('role_id', $superAdminRoleId)
                ->first();

            if (!$superAdminPermission) {
                DB::table('permission_role')->insert([
                    'permission_id' => $pid,
                    'role_id' => $superAdminRoleId,
                    'created_by' => $superAdmin->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Check if permission is already assigned to Admin role
            $adminPermission = DB::table('permission_role')
                ->where('permission_id', $pid)
                ->where('role_id', $adminRoleId)
                ->first();

            if (!$adminPermission) {
                DB::table('permission_role')->insert([
                    'permission_id' => $pid,
                    'role_id' => $adminRoleId,
                    'created_by' => $superAdmin->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 6. Assign Super Admin role to the super admin user if not already assigned
        $userRole = DB::table('role_user')
            ->where('role_id', $superAdminRoleId)
            ->where('user_id', $superAdmin->id)
            ->first();

        if (!$userRole) {
            DB::table('role_user')->insert([
                'role_id' => $superAdminRoleId,
                'user_id' => $superAdmin->id,
                'created_by' => $superAdmin->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
