<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FullRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Fetch existing roles
        $superAdminRoleId = DB::table('roles')->where('name', 'Super Admin')->value('id');
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');

        // 2. Define modules and actions
        $modules = [
            'subject', 'topic', 'settings', 'user', 'video', 'faq', 'message', 'payment', 'payment_settings',
            'partner', 'resource', 'class', 'subscription_package', 'term', 'student'
        ];
        $actions = ['view', 'create', 'edit', 'delete'];

        // 3. Insert or update Permissions
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                DB::table('permissions')->updateOrInsert(
                    [
                        'name' => "{$action}_{$module}"
                    ],
                    [
                        'description' => ucfirst($action) . " " . str_replace('_', ' ', ucfirst($module)),
                        'created_by' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        // 4. Assign all permissions to Super Admin and Admin roles
        $permissionIds = DB::table('permissions')->pluck('id')->toArray();
        foreach ($permissionIds as $pid) {
            // Assign to Super Admin
            DB::table('permission_role')->updateOrInsert(
                [
                    'permission_id' => $pid,
                    'role_id' => $superAdminRoleId
                ],
                [
                    'created_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            // Assign to Admin
            DB::table('permission_role')->updateOrInsert(
                [
                    'permission_id' => $pid,
                    'role_id' => $adminRoleId
                ],
                [
                    'created_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // 5. Assign Super Admin and Admin roles to user with id = 1
        DB::table('role_user')->updateOrInsert([
            'role_id' => $superAdminRoleId,
            'user_id' => 1,
        ], [
            'created_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('role_user')->updateOrInsert([
            'role_id' => $adminRoleId,
            'user_id' => 1,
        ], [
            'created_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
} 