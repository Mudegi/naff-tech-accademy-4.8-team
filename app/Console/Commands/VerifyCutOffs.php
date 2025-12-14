<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UniversityCutOff;

class VerifyCutOffs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cutoffs:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify university cut-offs in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verifying University Cut-Offs Database');
        $this->newLine();
        
        // Total count
        $total = UniversityCutOff::count();
        $this->info("📊 Total Courses: {$total}");
        $this->newLine();
        
        // Breakdown by university
        $universities = UniversityCutOff::select('university_name')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('university_name')
            ->orderBy('count', 'desc')
            ->get();
        
        $this->info('University Breakdown:');
        foreach ($universities as $uni) {
            $this->line("  • {$uni->university_name}: {$uni->count} courses");
        }
        $this->newLine();
        
        // With essential subjects
        $withEssential = UniversityCutOff::whereNotNull('essential_subjects')
            ->whereRaw('JSON_LENGTH(essential_subjects) > 0')
            ->count();
        $percentage = $total > 0 ? round(($withEssential / $total) * 100, 1) : 0;
        $this->info("✅ Courses with Essential Subjects: {$withEssential} ({$percentage}%)");
        
        // Without essential subjects
        $withoutEssential = $total - $withEssential;
        $this->warn("⚠️  Courses without Essential Subjects: {$withoutEssential}");
        $this->newLine();
        
        // Active vs Inactive
        $active = UniversityCutOff::where('is_active', true)->count();
        $inactive = UniversityCutOff::where('is_active', false)->count();
        $this->info("Status:");
        $this->line("  • Active: {$active}");
        $this->line("  • Inactive: {$inactive}");
        $this->newLine();
        
        // Academic years
        $years = UniversityCutOff::select('academic_year')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'desc')
            ->get();
        
        $this->info('Academic Years:');
        foreach ($years as $year) {
            $this->line("  • {$year->academic_year}: {$year->count} courses");
        }
        $this->newLine();
        
        // Sample courses
        $this->info('Sample Courses (First 5):');
        $samples = UniversityCutOff::limit(5)->get();
        foreach ($samples as $sample) {
            $essentialCount = is_array($sample->essential_subjects) ? count($sample->essential_subjects) : 0;
            $this->line("  • {$sample->course_name} ({$sample->university_name}) - {$essentialCount} essential subjects");
        }
        
        $this->newLine();
        $this->info('✅ Verification Complete');
        
        return 0;
    }
}
