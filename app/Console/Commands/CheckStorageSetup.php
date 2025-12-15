<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckStorageSetup extends Command
{
    protected $signature = 'check:storage';
    protected $description = 'Check storage configuration and permissions';

    public function handle()
    {
        $this->info('Storage Configuration Check');
        $this->line('');

        // Check storage path
        $storagePath = storage_path('app/public');
        $this->line("Storage path: $storagePath");
        $this->line("Exists: " . (file_exists($storagePath) ? '✓ YES' : '✗ NO'));
        $this->line("Writable: " . (is_writable($storagePath) ? '✓ YES' : '✗ NO'));
        
        if (!file_exists($storagePath)) {
            $this->warn("Creating storage/app/public directory...");
            mkdir($storagePath, 0755, true);
        }

        // Check public symlink
        $this->line('');
        $publicLink = public_path('storage');
        $this->line("Public symlink: $publicLink");
        $this->line("Exists: " . (file_exists($publicLink) ? '✓ YES' : '✗ NO'));
        $this->line("Is link: " . (is_link($publicLink) ? '✓ YES' : '✗ NO'));
        
        if (file_exists($publicLink)) {
            $this->line("Points to: " . (is_link($publicLink) ? readlink($publicLink) : 'Not a symlink'));
        }

        // Check welcome-images directory
        $this->line('');
        $welcomePath = storage_path('app/public/welcome-images');
        $this->line("Welcome images path: $welcomePath");
        $this->line("Exists: " . (file_exists($welcomePath) ? '✓ YES' : '✗ NO'));
        $this->line("Writable: " . (is_writable($welcomePath) ? '✓ YES' : '✗ NO'));

        if (!file_exists($welcomePath)) {
            $this->warn("Creating welcome-images directory...");
            mkdir($welcomePath, 0755, true);
            $this->info("✓ Created");
        }

        // Test write
        $this->line('');
        $this->info("Testing file write...");
        try {
            $testFile = 'welcome-images/test-' . time() . '.txt';
            Storage::disk('public')->put($testFile, 'test content');
            
            if (Storage::disk('public')->exists($testFile)) {
                $this->info("✓ Write test SUCCESSFUL");
                Storage::disk('public')->delete($testFile);
                $this->info("✓ Delete test SUCCESSFUL");
            } else {
                $this->error("✗ Write test FAILED - File not found after write");
            }
        } catch (\Exception $e) {
            $this->error("✗ Write test FAILED: " . $e->getMessage());
        }

        // Recommendations
        $this->line('');
        $this->info("Recommendations:");
        if (!file_exists($publicLink) || !is_link($publicLink)) {
            $this->warn("→ Run: php artisan storage:link");
        }
        if (!is_writable($storagePath)) {
            $this->warn("→ Fix permissions: chmod -R 775 storage/app/public");
            $this->warn("→ Or: chown -R www-data:www-data storage/");
        }
    }
}
