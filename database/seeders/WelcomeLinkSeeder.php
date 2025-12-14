<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WelcomeLink;

class WelcomeLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create records if none exist
        if (WelcomeLink::count() === 0) {
            WelcomeLink::create([
                'hero_title' => 'Welcome to Naf Academy',
                'hero_subtitle' => 'Empowering the next generation of tech leaders',
                'hero_description' => 'Through quality education and hands-on learning experiences.',
                'hero_image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'hero_image_2' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'hero_image_3' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'hero_image_4' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'hero_image_5' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'hero_image_6' => 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'hero_image_7' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'hero_image_8' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'hero_image_9' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'hero_image_10' => 'https://images.unsplash.com/photo-1513258496099-48168024aec0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'about_title' => 'About Naf Academy',
                'about_description' => 'Empowering the next generation of tech leaders through innovative education and hands-on learning experiences.',
                'about_image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'features_title' => 'Why Choose Us',
                'features_description' => 'Excellence in Tech Education',
                'features_image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'testimonials_title' => 'What Our Students Say',
                'testimonials_description' => 'Hear from our successful graduates',
                'testimonials_image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'cta_title' => 'Ready to start your journey?',
                'cta_description' => 'Join Naf Academy today.',
                'cta_image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                'login_image' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80',
                'register_image' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80'
            ]);
        }
    }
}
