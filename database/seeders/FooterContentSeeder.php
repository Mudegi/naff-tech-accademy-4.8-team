<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FooterContent;

class FooterContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!FooterContent::exists()) {
            FooterContent::create([
                'about_title' => 'About Naf Academy',
                'about_description' => 'Welcome to Naf Academy, your premier destination for quality education and learning resources. We are dedicated to empowering students with knowledge and skills for a brighter future.',
                'contact_email' => 'info@nafftechacademy.com',
                'contact_phone' => '+234 123 456 7890',
                'contact_address' => '123 Education Street, Learning City, Nigeria',
                'facebook_url' => 'https://facebook.com/nafftechacademy',
                'twitter_url' => 'https://twitter.com/nafftechacademy',
                'instagram_url' => 'https://instagram.com/nafftechacademy',
                'linkedin_url' => 'https://linkedin.com/company/nafftechacademy'
            ]);
        }
    }
}
