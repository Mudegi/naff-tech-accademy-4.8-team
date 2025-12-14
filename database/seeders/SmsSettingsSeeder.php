<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sms_settings')->updateOrInsert(
            ['username' => 'info@weafmall.com'], // Unique identifier
            [
                'password' => '^4GdyTqO^Gr=',
                'sender_id' => 'Egosms',
                'api_url' => 'https://www.egosms.co/api/v1/plain/',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
