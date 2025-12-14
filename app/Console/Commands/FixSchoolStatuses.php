<?php

namespace App\Console\Commands;

use App\Models\School;
use Illuminate\Console\Command;

class FixSchoolStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schools:fix-statuses {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix school statuses based on subscription approval status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        // Find schools that are active but don't have approved subscriptions
        $schoolsToDeactivate = School::where('status', 'active')
            ->whereDoesntHave('subscriptions', function($q) {
                $q->where('payment_status', 'completed')
                  ->where('is_active', true)
                  ->where('end_date', '>=', now());
            })
            ->get();

        if ($schoolsToDeactivate->count() > 0) {
            $this->info("📋 Found {$schoolsToDeactivate->count()} schools that should be inactive:");
            foreach ($schoolsToDeactivate as $school) {
                $this->line("  - {$school->name} (ID: {$school->id})");

                if (!$dryRun) {
                    $school->update(['status' => 'inactive']);
                    $this->line("    ✅ Set to inactive");
                }
            }
        } else {
            $this->info("✅ All active schools have approved subscriptions");
        }

        // Find schools that are inactive but have approved active subscriptions
        $schoolsToActivate = School::where('status', 'inactive')
            ->whereHas('subscriptions', function($q) {
                $q->where('payment_status', 'completed')
                  ->where('is_active', true)
                  ->where('end_date', '>=', now());
            })
            ->get();

        if ($schoolsToActivate->count() > 0) {
            $this->info("📋 Found {$schoolsToActivate->count()} schools that should be active:");
            foreach ($schoolsToActivate as $school) {
                $this->line("  - {$school->name} (ID: {$school->id})");

                if (!$dryRun) {
                    $school->update(['status' => 'active']);
                    $this->line("    ✅ Set to active");
                }
            }
        } else {
            $this->info("✅ All inactive schools correctly don't have approved subscriptions");
        }

        // Check for schools with expired subscriptions that should be deactivated
        $schoolsWithExpiredSubs = School::where('status', 'active')
            ->whereHas('subscriptions', function($q) {
                $q->where('payment_status', 'completed')
                  ->where('is_active', true)
                  ->where('end_date', '<', now());
            })
            ->get();

        if ($schoolsWithExpiredSubs->count() > 0) {
            $this->info("📋 Found {$schoolsWithExpiredSubs->count()} schools with expired subscriptions:");
            foreach ($schoolsWithExpiredSubs as $school) {
                $this->line("  - {$school->name} (ID: {$school->id}) - Subscription expired");

                if (!$dryRun) {
                    $school->deactivateSubscription();
                    $this->line("    ✅ Deactivated due to expired subscription");
                }
            }
        } else {
            $this->info("✅ No schools found with expired subscriptions");
        }

        if ($dryRun) {
            $this->info('💡 Run without --dry-run to apply these changes');
        } else {
            $this->info('🎉 School statuses have been fixed!');
        }

        return Command::SUCCESS;
    }
}