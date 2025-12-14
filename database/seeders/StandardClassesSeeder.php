<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StandardClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating standard Ugandan school classes...');

        // Ugandan Education System Classes (Form 1-6)
        $standardClasses = [
            // O Level (Ordinary Level) - Forms 1-4
            [
                'name' => 'Form 1',
                'grade_level' => 1,
                'level' => 'O Level',
                'description' => 'First year of secondary education (Form 1)',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_active' => true,
                'school_id' => null, // System-wide class
            ],
            [
                'name' => 'Form 2',
                'grade_level' => 2,
                'level' => 'O Level',
                'description' => 'Second year of secondary education (Form 2)',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_active' => true,
                'school_id' => null,
            ],
            [
                'name' => 'Form 3',
                'grade_level' => 3,
                'level' => 'O Level',
                'description' => 'Third year of secondary education (Form 3)',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_active' => true,
                'school_id' => null,
            ],
            [
                'name' => 'Form 4',
                'grade_level' => 4,
                'level' => 'O Level',
                'description' => 'Fourth year of secondary education (Form 4) - UCE Examinations',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_active' => true,
                'school_id' => null,
            ],

            // A Level (Advanced Level) - Forms 5-6
            [
                'name' => 'Form 5',
                'grade_level' => 5,
                'level' => 'A Level',
                'description' => 'First year of advanced secondary education (Form 5)',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_active' => true,
                'school_id' => null,
            ],
            [
                'name' => 'Form 6',
                'grade_level' => 6,
                'level' => 'A Level',
                'description' => 'Final year of secondary education (Form 6) - UACE Examinations',
                'term' => 'First Term',
                'start_date' => Carbon::createFromFormat('m-d', '01-15')->startOfDay(),
                'end_date' => Carbon::createFromFormat('m-d', '04-15')->endOfDay(),
                'is_active' => true,
                'school_id' => null,
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($standardClasses as $classData) {
            // Check if class already exists
            $existingClass = SchoolClass::where('name', $classData['name'])
                                      ->where('school_id', null)
                                      ->first();

            if (!$existingClass) {
                $classData['slug'] = Str::slug($classData['name']);
                $classData['created_by'] = 1; // Assuming admin user ID 1

                SchoolClass::create($classData);
                $this->command->info("✅ Created: {$classData['name']} ({$classData['level']})");
                $created++;
            } else {
                $this->command->warn("⏭️  Skipped: {$classData['name']} (already exists)");
                $skipped++;
            }
        }

        $this->command->info("🎉 Standard classes seeding completed!");
        $this->command->info("📊 Created: {$created} classes");
        $this->command->info("⏭️  Skipped: {$skipped} classes (already existed)");

        $this->command->info("");
        $this->command->info("📚 Standard Ugandan Classes Available:");
        $this->command->info("   • Form 1-4 (O Level) - Secondary education years 1-4");
        $this->command->info("   • Form 5-6 (A Level) - Advanced secondary education years 1-2");
        $this->command->info("");
        $this->command->info("💡 These classes are available system-wide for all schools to use.");
    }
}