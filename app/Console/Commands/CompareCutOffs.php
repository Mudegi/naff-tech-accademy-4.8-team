<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UniversityCutOff;

class CompareCutOffs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cutoffs:compare {expected=307 : Expected number of courses}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare current database with expected number of courses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expected = (int) $this->argument('expected');
        $actual = UniversityCutOff::count();
        
        $this->info('📊 Cut-Offs Comparison Report');
        $this->newLine();
        
        // Overall comparison
        $this->line("Expected Courses: {$expected}");
        $this->line("Actual Courses:   {$actual}");
        
        $difference = $actual - $expected;
        if ($difference > 0) {
            $this->info("Difference:       +{$difference} (You have MORE courses)");
        } elseif ($difference < 0) {
            $this->error("Difference:       {$difference} (You have FEWER courses) ⚠️");
        } else {
            $this->info("Difference:       0 (Perfect match! ✅)");
        }
        
        $this->newLine();
        
        // Expected breakdown (based on your development database)
        $expectedBreakdown = [
            'Makerere University' => 158,
            'Kyambogo University' => 149,
        ];
        
        $this->info('University Comparison:');
        $this->newLine();
        
        $allMatch = true;
        foreach ($expectedBreakdown as $university => $expectedCount) {
            $actualCount = UniversityCutOff::where('university_name', $university)->count();
            $diff = $actualCount - $expectedCount;
            
            $status = $diff === 0 ? '✅' : ($diff > 0 ? '⚠️' : '❌');
            $diffText = $diff === 0 ? 'Match' : ($diff > 0 ? "+{$diff}" : "{$diff}");
            
            $this->line(sprintf(
                "  %s %-30s Expected: %3d | Actual: %3d | %s",
                $status,
                $university . ':',
                $expectedCount,
                $actualCount,
                $diffText
            ));
            
            if ($diff !== 0) {
                $allMatch = false;
            }
        }
        
        $this->newLine();
        
        // Check for unexpected universities
        $actualUniversities = UniversityCutOff::select('university_name')
            ->distinct()
            ->pluck('university_name')
            ->toArray();
        
        $expectedUniversities = array_keys($expectedBreakdown);
        $unexpected = array_diff($actualUniversities, $expectedUniversities);
        
        if (!empty($unexpected)) {
            $this->warn('⚠️  Unexpected Universities Found:');
            foreach ($unexpected as $uni) {
                $count = UniversityCutOff::where('university_name', $uni)->count();
                $this->line("  • {$uni}: {$count} courses");
            }
            $this->newLine();
        }
        
        // Missing universities
        $missing = array_diff($expectedUniversities, $actualUniversities);
        if (!empty($missing)) {
            $this->error('❌ Missing Universities:');
            foreach ($missing as $uni) {
                $this->line("  • {$uni} (Expected: {$expectedBreakdown[$uni]} courses)");
            }
            $this->newLine();
        }
        
        // Final status
        if ($actual === $expected && $allMatch && empty($unexpected) && empty($missing)) {
            $this->info('🎉 PERFECT MATCH! Your database has exactly the expected courses.');
            $this->newLine();
            $this->info('Next steps:');
            $this->line('  1. Verify essential subjects are populated');
            $this->line('  2. Test course recommendations');
            $this->line('  3. Check admin panel displays correctly');
            return 0;
        } elseif ($actual === $expected) {
            $this->warn('⚠️  Total matches but distribution differs.');
            $this->info('You may want to review the specific courses.');
            return 1;
        } else {
            $this->error('❌ Database does not match expected state.');
            $this->newLine();
            $this->info('Recommended actions:');
            if ($actual < $expected) {
                $this->line('  1. Backup your current database');
                $this->line('  2. Run: php artisan db:seed --class=ProductionUniversityCutOffsSeeder');
                $this->line('  3. Run: php artisan cutoffs:compare');
            } else {
                $this->line('  1. Review unexpected courses');
                $this->line('  2. Decide if they should be kept or removed');
                $this->line('  3. Update accordingly');
            }
            return 1;
        }
    }
}
