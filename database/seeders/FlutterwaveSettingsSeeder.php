<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlutterwaveSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('flutterwave_settings')->updateOrInsert(
            ['public_key' => 'FLWPUBK-326a3a632a12683cd720c24827d9c3e3-X'], // Unique identifier
            [
                'secret_key' => 'FLWSECK-5c7596e4ee21d6b176f560f2b82f6dc5-X',
                'encryption_key' => '5c7596e4ee21e6488c4d75b3',
                'test_mode' => false, // Production mode
                'webhook_secret' => '5c7596e4ee21e6488c4d75b3',
                'currency_code' => 'UGX',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
