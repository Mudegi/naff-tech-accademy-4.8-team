<?php

namespace App\Console\Commands;

use App\Models\WelcomeLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckWelcomeImages extends Command
{
    protected $signature = 'check:welcome-images';
    protected $description = 'Check current welcome page images status';

    public function handle()
    {
        $welcomePage = WelcomeLink::first();
        
        if (!$welcomePage) {
            $this->error('No welcome_links record found in database!');
            return;
        }

        $this->info('Welcome Page Images Status:');
        $this->line('');

        $imageFields = [
            'hero_image_1', 'hero_image_2', 'hero_image_3', 'hero_image_4', 'hero_image_5',
            'hero_image_6', 'hero_image_7', 'hero_image_8', 'hero_image_9', 'hero_image_10',
            'about_image', 'features_image', 'testimonials_image', 'cta_image',
            'login_image', 'register_image', 'og_image', 'twitter_image',
            'mission_image', 'team_image', 'values_image', 'vision_image'
        ];

        $totalImages = 0;
        $existingFiles = 0;
        $missingFiles = 0;

        foreach ($imageFields as $field) {
            $path = $welcomePage->$field;
            if ($path) {
                $totalImages++;
                $fullPath = 'public/' . $path;
                $exists = Storage::exists($fullPath);
                
                $status = $exists ? '✓ EXISTS' : '✗ MISSING';
                $this->line(sprintf('%-20s: %-40s [%s]', $field, $path, $status));
                
                if ($exists) {
                    $existingFiles++;
                } else {
                    $missingFiles++;
                }
            }
        }

        $this->line('');
        $this->info("Summary:");
        $this->line("Images in database: $totalImages");
        $this->line("Files exist: $existingFiles");
        $this->line("Files missing: $missingFiles");
        
        $this->line('');
        $this->info("Storage path: " . storage_path('app/public/welcome-images'));
        $this->info("Public symlink: " . public_path('storage'));
        
        // Check if symlink exists
        if (!file_exists(public_path('storage'))) {
            $this->warn('WARNING: Public storage symlink does not exist!');
            $this->line('Run: php artisan storage:link');
        }
    }
}
