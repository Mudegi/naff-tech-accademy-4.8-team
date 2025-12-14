<?php

namespace App\Console\Commands;

use App\Models\SchoolClass;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RefreshSystemClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classes:refresh-system {--force : Force refresh even if classes exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh or create standard Ugandan system classes (Form 1-6)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        if (!$force) {
            $existingCount = SchoolClass::where('is_system_class', true)->count();
            if ($existingCount > 0) {
                $this->warn("⚠️  {$existingCount} system classes already exist.");
                if (!$this->confirm('Do you want to refresh them? This will update existing classes.')) {
                    $this->info('Operation cancelled.');
                    return Command::SUCCESS;
                }
            }
        }

        $this->info('🔄 Refreshing standard Ugandan school classes...');

        $classes = [
            // O Level (Ordinary Level) - Forms 1-4
            [
                'name' => 'Form 1',
                'slug' => 'form-1',
                'description' => 'First year of secondary education (Form 1)',
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
                'description' => 'Second year of secondary education (Form 2)',
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
                'description' => 'Third year of secondary education (Form 3)',
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
                'description' => 'Fourth year of secondary education (Form 4) - UCE Examinations',
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
                'description' => 'First year of advanced secondary education (Form 5)',
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
                'description' => 'Final year of secondary education (Form 6) - UACE Examinations',
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
                    'is_system_class' => $class['is_system_class'],
                    'school_id' => null, // System-wide classes
                    'updated_at' => now(),
                ]);
                $this->info("📝 Updated: {$class['name']} ({$class['level']})");
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
                    'is_system_class' => $class['is_system_class'],
                    'school_id' => null, // System-wide classes
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info("✅ Created: {$class['name']} ({$class['level']})");
                $created++;
            }
        }

        $this->info("🎉 System classes refresh completed!");
        $this->info("📊 Created: {$created} classes");
        $this->info("📝 Updated: {$updated} classes");

        $this->info("");
        $this->info("📚 Standard Ugandan Classes Available:");
        $this->info("   • Form 1-4 (O Level) - Secondary education years 1-4");
        $this->info("   • Form 5-6 (A Level) - Advanced secondary education years 1-2");
        $this->info("");
        $this->info("💡 These classes are available system-wide for all schools to use.");
        $this->info("🔧 School admins can assign subjects to these classes but cannot modify basic info.");

        return Command::SUCCESS;
    }
}