<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WelcomeLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_image_1',
        'hero_image_2',
        'hero_image_3',
        'hero_image_4',
        'hero_image_5',
        'hero_image_6',
        'hero_image_7',
        'hero_image_8',
        'hero_image_9',
        'hero_image_10',
        'about_title',
        'about_description',
        'about_image',
        'features_title',
        'features_description',
        'features_image',
        'testimonials_title',
        'testimonials_description',
        'testimonials_image',
        'cta_title',
        'cta_description',
        'cta_image',
        'login_image',
        'register_image',
        // Meta Tags
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        // Mission Section
        'mission_title',
        'mission_description',
        'mission_image',
        // Team Section
        'team_title',
        'team_description',
        'team_image',
        // Values Section
        'values_title',
        'values_description',
        'values_image',
        // Vision Section
        'vision_title',
        'vision_description',
        'vision_image',
    ];

    /**
     * Get the full URL for an image field
     */
    public function getImageUrl($field)
    {
        if ($this && $this->$field) {
            return Storage::url($this->$field);
        }
        return null;
    }

    /**
     * Get all hero images as URLs
     */
    public function getHeroImages()
    {
        $images = [];
        for ($i = 1; $i <= 10; $i++) {
            $field = 'hero_image_' . $i;
            if ($this->$field) {
                $images[] = $this->getImageUrl($field);
            }
        }
        return $images;
    }

    /**
     * Check if an image field has a value
     */
    public function hasImage($field)
    {
        return !empty($this->$field);
    }

    /**
     * Get image dimensions info for display
     */
    public function getImageInfo($field)
    {
        if (!$this->$field) {
            return null;
        }

        $path = storage_path('app/public/' . $this->$field);
        if (file_exists($path)) {
            $size = getimagesize($path);
            return [
                'width' => $size[0] ?? 0,
                'height' => $size[1] ?? 0,
                'size' => filesize($path),
                'url' => $this->getImageUrl($field)
            ];
        }

        return null;
    }
}
