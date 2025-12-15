<?php

namespace App\Console\Commands;

use App\Models\WelcomeLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanWelcomeImages extends Command
{
    protected $signature = 'clean:welcome-images {--force : Actually delete, not just preview}';
    protected $description = 'Clean up missing welcome page images from database';

    public function handle()
    {
        $welcomePage = WelcomeLink::first();
        
        if (!$welcomePage) {
            $this->error('No welcome_links record found!');
            return;
        }

        $imageFields = [
            'hero_image_1', 'hero_image_2', 'hero_image_3', 'hero_image_4', 'hero_image_5',
            'hero_image_6', 'hero_image_7', 'hero_image_8', 'hero_image_9', 'hero_image_10',
            'about_image', 'features_image', 'testimonials_image', 'cta_image',
            'login_image', 'register_image', 'og_image', 'twitter_image',
            'mission_image', 'team_image', 'values_image', 'vision_image'
        ];

        $cleaned = 0;
        $force = $this->option('force');

        if (!$force) {
            $this->warn('PREVIEW MODE - Add --force to actually clean');
            $this->line('');
        }

        foreach ($imageFields as $field) {
            $path = $welcomePage->$field;
            if ($path) {
                $isUrl = str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
                $exists = !$isUrl && Storage::exists('public/' . $path);
                
                if (!$exists) {
                    $this->line("$field: $path [WILL BE CLEARED]");
                    $cleaned++;
                    
                    if ($force) {
                        $welcomePage->$field = null;
                    }
                }
            }
        }

        if ($force && $cleaned > 0) {
            $welcomePage->save();
            $this->info("\n✓ Cleaned $cleaned missing/external images from database");
        } else if ($cleaned > 0) {
            $this->info("\n$cleaned images would be cleaned. Run with --force to execute.");
        } else {
            $this->info("\n✓ All image paths are valid!");
        }
    }
}
