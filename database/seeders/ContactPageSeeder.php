<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactPage;

class ContactPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create if no records exist
        if (ContactPage::count() === 0) {
            ContactPage::create([
                // Meta Tags
                'meta_title' => 'Contact Naf Academy - Get in Touch With Us',
                'meta_description' => 'Have questions about our tech courses? Contact Naf Academy for expert guidance on programming, web development, and more. Reach us via phone, email, or visit our campus.',
                'meta_keywords' => 'contact Naf Academy, tech education contact, programming courses contact, web development training contact, tech academy location',
                'meta_author' => 'Naf Academy',
                'meta_robots' => 'index, follow',
                'meta_language' => 'English',
                'meta_revisit_after' => '7 days',
                
                // Open Graph / Facebook
                'og_title' => 'Contact Naf Academy - Get in Touch With Us',
                'og_description' => 'Have questions about our tech courses? Contact Naf Academy for expert guidance on programming, web development, and more.',
                'og_image' => 'images/naff-tech-academy-contact.jpg',
                
                // Twitter
                'twitter_title' => 'Contact Naf Academy - Get in Touch With Us',
                'twitter_description' => 'Have questions about our tech courses? Contact Naf Academy for expert guidance on programming, web development, and more.',
                'twitter_image' => 'images/naff-tech-academy-contact.jpg',
                
                // Schema.org
                'schema_name' => 'Naf Academy',
                'schema_description' => 'Premier tech education institution offering comprehensive programming and web development courses',
                'schema_street_address' => '123 Tech Street',
                'schema_address_locality' => 'San Francisco',
                'schema_address_region' => 'CA',
                'schema_postal_code' => '94105',
                'schema_address_country' => 'US',
                'schema_telephone' => '+1 (555) 123-4567',
                'schema_email' => 'support@nafftechacademy.com',
                'schema_opening_hours' => 'Mo,Tu,We,Th,Fr 09:00-17:00',
                
                // Contact Information
                'contact_title' => 'Contact Information',
                'contact_description' => 'Have questions about our courses or programs? We\'re here to help. Reach out to us through any of the following channels.',
                'contact_phone' => '+1 (555) 123-4567',
                'contact_phone_hours' => 'Mon-Fri 8am to 6pm PST',
                'contact_email' => 'support@nafftechacademy.com',
                'contact_address' => '123 Tech Street, San Francisco, CA 94105',
                
                // Map Section
                'map_title' => 'Visit Our Campus',
                'map_description' => 'Come visit our state-of-the-art facilities and meet our team in person.',
                'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.097827987877!2d-122.4194!3d37.7749!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzfCsDQ2JzI5LjYiTiAxMjLCsDI1JzA5LjgiVw!5e0!3m2!1sen!2sus!4v1234567890',
                'map_opening_hours_monday_friday' => 'Monday - Friday: 9am - 5pm',
                'map_opening_hours_saturday' => 'Saturday: 10am - 2pm',
                'map_opening_hours_sunday' => 'Sunday: Closed',
            ]);
        }
    }
}
