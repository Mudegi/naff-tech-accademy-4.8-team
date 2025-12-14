<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UniversityCutOff;
use Illuminate\Support\Facades\File;

class ExportCutOffsToSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cutoffs:export-seeder {--file=database/seeders/ProductionUniversityCutOffsSeeder.php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export current university cut-offs to a seeder file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Exporting university cut-offs to seeder...');
        
        // Get all cut-offs
        $cutOffs = UniversityCutOff::orderBy('university_name')
            ->orderBy('course_name')
            ->get();
        
        if ($cutOffs->isEmpty()) {
            $this->error('❌ No cut-offs found in database!');
            return 1;
        }
        
        $this->info("📊 Found {$cutOffs->count()} courses");
        
        // Generate seeder code
        $seederCode = $this->generateSeederCode($cutOffs);
        
        // Write to file
        $filePath = base_path($this->option('file'));
        File::put($filePath, $seederCode);
        
        $this->info("✅ Seeder exported successfully to: {$filePath}");
        $this->info("📝 Total courses exported: {$cutOffs->count()}");
        
        // Show breakdown
        $breakdown = $cutOffs->groupBy('university_name')->map->count();
        $this->newLine();
        $this->info('University Breakdown:');
        foreach ($breakdown as $university => $count) {
            $this->line("  • {$university}: {$count} courses");
        }
        
        $this->newLine();
        $this->info('🚀 To use this seeder on production:');
        $this->line('   1. Copy the file to production server');
        $this->line('   2. Run: php artisan db:seed --class=ProductionUniversityCutOffsSeeder');
        
        return 0;
    }
    
    /**
     * Generate the seeder file code
     */
    protected function generateSeederCode($cutOffs)
    {
        $coursesArray = [];
        
        foreach ($cutOffs as $cutOff) {
            $coursesArray[] = [
                'university_name' => $cutOff->university_name,
                'university_code' => $cutOff->university_code,
                'course_name' => $cutOff->course_name,
                'course_code' => $cutOff->course_code,
                'faculty' => $cutOff->faculty,
                'department' => $cutOff->department,
                'degree_type' => $cutOff->degree_type,
                'minimum_principal_passes' => $cutOff->minimum_principal_passes,
                'minimum_aggregate_points' => $cutOff->minimum_aggregate_points,
                'cut_off_points' => $cutOff->cut_off_points,
                'cut_off_points_male' => $cutOff->cut_off_points_male,
                'cut_off_points_female' => $cutOff->cut_off_points_female,
                'essential_subjects' => $cutOff->essential_subjects,
                'relevant_subjects' => $cutOff->relevant_subjects,
                'desirable_subjects' => $cutOff->desirable_subjects,
                'duration_years' => $cutOff->duration_years,
                'academic_year' => $cutOff->academic_year,
                'is_active' => $cutOff->is_active,
            ];
        }
        
        // Convert to PHP code
        $coursesPhp = $this->arrayToPhpCode($coursesArray);
        
        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UniversityCutOff;
use Illuminate\Support\Facades\DB;

class ProductionUniversityCutOffsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder contains all university cut-offs exported from development database.
     * It uses updateOrCreate to prevent duplicates and can be run multiple times safely.
     * 
     * Total Courses: {$cutOffs->count()}
     * Generated: {now()->format('Y-m-d H:i:s')}
     */
    public function run(): void
    {
        \$this->command->info('🔄 Seeding university cut-offs...');
        
        \$courses = {$coursesPhp};
        
        \$created = 0;
        \$updated = 0;
        \$errors = 0;
        
        DB::beginTransaction();
        
        try {
            foreach (\$courses as \$course) {
                try {
                    \$cutOff = UniversityCutOff::updateOrCreate(
                        [
                            'university_name' => \$course['university_name'],
                            'course_name' => \$course['course_name'],
                            'academic_year' => \$course['academic_year'],
                        ],
                        \$course
                    );
                    
                    if (\$cutOff->wasRecentlyCreated) {
                        \$created++;
                    } else {
                        \$updated++;
                    }
                } catch (\Exception \$e) {
                    \$errors++;
                    \$this->command->error("Error with course: {\$course['course_name']} - " . \$e->getMessage());
                }
            }
            
            DB::commit();
            
            \$this->command->newLine();
            \$this->command->info('✅ Seeding completed successfully!');
            \$this->command->info("📊 Results:");
            \$this->command->line("   • Created: {\$created} new courses");
            \$this->command->line("   • Updated: {\$updated} existing courses");
            if (\$errors > 0) {
                \$this->command->error("   • Errors: {\$errors} courses failed");
            }
            \$this->command->line("   • Total processed: " . count(\$courses));
            
            // Show breakdown by university
            \$breakdown = collect(\$courses)->groupBy('university_name')->map->count();
            \$this->command->newLine();
            \$this->command->info('University Breakdown:');
            foreach (\$breakdown as \$university => \$count) {
                \$this->command->line("   • {\$university}: {\$count} courses");
            }
            
        } catch (\Exception \$e) {
            DB::rollBack();
            \$this->command->error('❌ Seeding failed: ' . \$e->getMessage());
            throw \$e;
        }
    }
}

PHP;
    }
    
    /**
     * Convert array to formatted PHP code
     */
    protected function arrayToPhpCode($array, $indent = 2)
    {
        $indentStr = str_repeat(' ', $indent);
        $lines = ["["];
        
        foreach ($array as $item) {
            $lines[] = $indentStr . "[";
            foreach ($item as $key => $value) {
                $lines[] = $indentStr . "    '{$key}' => " . $this->valueToPhp($value) . ",";
            }
            $lines[] = $indentStr . "],";
        }
        
        $lines[] = "]";
        
        return implode("\n", $lines);
    }
    
    /**
     * Convert value to PHP code representation
     */
    protected function valueToPhp($value)
    {
        if (is_null($value)) {
            return 'null';
        }
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        if (is_numeric($value)) {
            return $value;
        }
        
        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }
            $items = array_map(function($v) {
                return "'{$v}'";
            }, $value);
            return '[' . implode(', ', $items) . ']';
        }
        
        // String - escape single quotes
        $escaped = str_replace("'", "\\'", $value);
        return "'{$escaped}'";
    }
}
