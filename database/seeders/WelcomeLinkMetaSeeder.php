<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WelcomeLink;

class WelcomeLinkMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $welcomeLink = WelcomeLink::first();

        if ($welcomeLink) {
            // Only update fields that are null or empty
            $updates = [];

            // Meta Tags
            if (empty($welcomeLink->meta_title)) {
                $updates['meta_title'] = 'About Us - Naf Academy';
            }
            if (empty($welcomeLink->meta_description)) {
                $updates['meta_description'] = 'Learn about Naf Academy\'s mission to provide quality education and empower the next generation of tech leaders in Uganda.';
            }
            if (empty($welcomeLink->meta_keywords)) {
                $updates['meta_keywords'] = 'about Naf Academy, Uganda education, tech education, online learning, academic resources';
            }
            if (empty($welcomeLink->og_title)) {
                $updates['og_title'] = 'About Us - Naf Academy';
            }
            if (empty($welcomeLink->og_description)) {
                $updates['og_description'] = 'Learn about Naf Academy\'s mission to provide quality education and empower the next generation of tech leaders in Uganda.';
            }
            if (empty($welcomeLink->og_image)) {
                $updates['og_image'] = 'images/og-image.jpg';
            }
            if (empty($welcomeLink->twitter_title)) {
                $updates['twitter_title'] = 'About Us - Naf Academy';
            }
            if (empty($welcomeLink->twitter_description)) {
                $updates['twitter_description'] = 'Learn about Naf Academy\'s mission to provide quality education and empower the next generation of tech leaders in Uganda.';
            }
            if (empty($welcomeLink->twitter_image)) {
                $updates['twitter_image'] = 'images/og-image.jpg';
            }

            // Mission Section
            if (empty($welcomeLink->mission_title)) {
                $updates['mission_title'] = 'Empowering Through Education';
            }
            if (empty($welcomeLink->mission_description)) {
                $updates['mission_description'] = 'We are committed to providing high-quality education that prepares students for success in the digital age.';
            }
            if (empty($welcomeLink->mission_image)) {
                $updates['mission_image'] = 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80';
            }

            // Team Section
            if (empty($welcomeLink->team_title)) {
                $updates['team_title'] = 'Meet Our Expert Instructors';
            }
            if (empty($welcomeLink->team_description)) {
                $updates['team_description'] = 'Our team of experienced educators is dedicated to providing the best learning experience for our students.';
            }
            if (empty($welcomeLink->team_image)) {
                $updates['team_image'] = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80';
            }

            // Values Section
            if (empty($welcomeLink->values_title)) {
                $updates['values_title'] = 'Our Core Values';
            }
            if (empty($welcomeLink->values_description)) {
                $updates['values_description'] = 'Excellence, Innovation, Integrity, and Student Success are the core values that guide everything we do.';
            }
            if (empty($welcomeLink->values_image)) {
                $updates['values_image'] = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80';
            }

            // Vision Section
            if (empty($welcomeLink->vision_title)) {
                $updates['vision_title'] = 'Our Vision';
            }
            if (empty($welcomeLink->vision_description)) {
                $updates['vision_description'] = 'To be the leading provider of quality education in Uganda, empowering students with the skills and knowledge needed for success in the digital age.';
            }
            if (empty($welcomeLink->vision_image)) {
                $updates['vision_image'] = 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80';
            }

            // Only update if there are fields to update
            if (!empty($updates)) {
                $welcomeLink->update($updates);
            }
        }
    }
}
