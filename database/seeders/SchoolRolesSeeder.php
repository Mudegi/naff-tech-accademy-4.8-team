<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchoolRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $superAdminId = DB::table('users')->where('email', 'admin@nafftechacademy.com')->value('id') ?? 1;

        // Define school roles with hierarchy
        $roles = [
            [
                'name' => 'School Admin',
                'description' => 'Full administrative access to school account. Can manage all staff, students, and school settings.',
                'level' => 4,
            ],
            [
                'name' => 'Director of Studies',
                'description' => 'Manages academic programs, heads of departments, and subject teachers. Reports to School Admin.',
                'level' => 3,
            ],
            [
                'name' => 'Head of Department',
                'description' => 'Manages subject teachers within their department. Reports to Director of Studies.',
                'level' => 2,
            ],
            [
                'name' => 'Subject Teacher',
                'description' => 'Teaches subjects and manages class resources. Reports to Head of Department.',
                'level' => 1,
            ],
        ];

        foreach ($roles as $role) {
            $existingRole = DB::table('roles')->where('name', $role['name'])->first();
            
            if (!$existingRole) {
                DB::table('roles')->insert([
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'created_by' => $superAdminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info('School roles created successfully!');
    }
}
