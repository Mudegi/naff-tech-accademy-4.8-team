<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_settings')->updateOrInsert(
            ['id' => 1],
            [
                'company_name' => 'Naf Academy',
                'company_email' => 'info@nafftechacademy.com',
                'company_phone' => '+256 123 456 789',
                'company_address' => 'Plot 123, Main Street, Kampala, Uganda',
                'company_website' => 'https://nafftechacademy.com',
                'company_description' => 'Naff Tech Academy is a premier educational institution dedicated to providing quality technology education and training. We offer comprehensive courses in various tech disciplines, helping students build successful careers in the digital world.',
                'tax_number' => 'TAX123456789',
                'currency' => 'UGX',
                'timezone' => 'Africa/Kampala',
                'bank_name' => 'Centenary Bank',
                'account_name' => 'Naf Academy Ltd',
                'account_number' => '1234567890',
                'mtn_mobile_number' => '+256 700 000 000',
                'mtn_registered_name' => 'Naf Academy MTN',
                'airtel_mobile_number' => '+256 750 000 000',
                'airtel_registered_name' => 'Naf Academy Airtel',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
