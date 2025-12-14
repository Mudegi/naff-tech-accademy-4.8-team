<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPage extends Model
{
    use HasFactory;

    protected $fillable = [
        // Meta Tags
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_author',
        'meta_robots',
        'meta_language',
        'meta_revisit_after',
        
        // Open Graph / Facebook
        'og_title',
        'og_description',
        'og_image',
        
        // Twitter
        'twitter_title',
        'twitter_description',
        'twitter_image',
        
        // Schema.org
        'schema_name',
        'schema_description',
        'schema_street_address',
        'schema_address_locality',
        'schema_address_region',
        'schema_postal_code',
        'schema_address_country',
        'schema_telephone',
        'schema_email',
        'schema_opening_hours',
        
        // Contact Information
        'contact_title',
        'contact_description',
        'contact_phone',
        'contact_phone_hours',
        'contact_email',
        'contact_address',
        
        // Map Section
        'map_title',
        'map_description',
        'map_embed_url',
        'map_opening_hours_monday_friday',
        'map_opening_hours_saturday',
        'map_opening_hours_sunday',
    ];
}
