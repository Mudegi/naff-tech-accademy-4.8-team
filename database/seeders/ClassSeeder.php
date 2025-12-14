<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating standard Ugandan school classes (Form 1-6)...');

        $classes = [
            // O Level (Ordinary Level) - Forms 1-4
            [
                'name' => 'Form 1',
                'slug' => 'form-1',
                'description' => 'First year of secondary education (Senior 1)',
                'grade_level' => 1,
                'level' => 'O Level',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_system_class' => true,
            ],
            [
                'name' => 'Form 2',
                'slug' => 'form-2',
                'description' => 'Second year of secondary education (Senior 2)',
                'grade_level' => 2,
                'level' => 'O Level',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_system_class' => true,
            ],
            [
                'name' => 'Form 3',
                'slug' => 'form-3',
                'description' => 'Third year of secondary education (Senior 3)',
                'grade_level' => 3,
                'level' => 'O Level',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_system_class' => true,
            ],
            [
                'name' => 'Form 4',
                'slug' => 'form-4',
                'description' => 'Fourth year of secondary education (Senior 4) - UCE Examinations',
                'grade_level' => 4,
                'level' => 'O Level',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_system_class' => true,
            ],

            // A Level (Advanced Level) - Forms 5-6
            [
                'name' => 'Form 5',
                'slug' => 'form-5',
                'description' => 'First year of advanced secondary education (Senior 5)',
                'grade_level' => 5,
                'level' => 'A Level',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_system_class' => true,
            ],
            [
                'name' => 'Form 6',
                'slug' => 'form-6',
                'description' => 'Final year of secondary education (Senior 6) - UACE Examinations',
                'grade_level' => 6,
                'level' => 'A Level',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_system_class' => true,
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($classes as $class) {
            $existing = DB::table('classes')->where('slug', $class['slug'])->first();

            if ($existing) {
                DB::table('classes')->where('slug', $class['slug'])->update([
                    'name' => $class['name'],
                    'description' => $class['description'],
                    'grade_level' => $class['grade_level'],
                    'level' => $class['level'],
                    'term' => $class['term'],
                    'start_date' => $class['start_date'],
                    'end_date' => $class['end_date'],
                    'is_active' => true,
                    'is_system_class' => $class['is_system_class'] ?? false,
                    'school_id' => null, // System-wide classes
                    'updated_at' => now(),
                ]);
                $this->command->info("📝 Updated: {$class['name']} ({$class['level']})");
                $updated++;
            } else {
                DB::table('classes')->insert([
                    'name' => $class['name'],
                    'slug' => $class['slug'],
                    'description' => $class['description'],
                    'grade_level' => $class['grade_level'],
                    'level' => $class['level'],
                    'term' => $class['term'],
                    'start_date' => $class['start_date'],
                    'end_date' => $class['end_date'],
                    'is_active' => true,
                    'is_system_class' => $class['is_system_class'] ?? false,
                    'school_id' => null, // System-wide classes
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("✅ Created: {$class['name']} ({$class['level']})");
                $created++;
            }
        }

        $this->command->info("🎉 Standard classes seeding completed!");
        $this->command->info("📊 Created: {$created} classes");
        $this->command->info("📝 Updated: {$updated} classes");

        $this->command->info("");
        $this->command->info("📚 Standard Ugandan Classes Available:");
        $this->command->info("   • Form 1-4 (O Level) - Secondary education years 1-4");
        $this->command->info("   • Form 5-6 (A Level) - Advanced secondary education years 1-2");
        $this->command->info("");
        $this->command->info("💡 These classes are available system-wide for all schools to use.");
    }
}
