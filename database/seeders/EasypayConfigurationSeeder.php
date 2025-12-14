<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EasypayConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\EasypayConfiguration::create([
            'client_id' => 'ce27add4a499b53e',
            'secret' => '52fe49c24496d196',
            'website_url' => 'https://nafftechacademy.xyz',
            'ipn_url' => 'https://nafftechacademy.xyz/callback',
            'hits' => 13,
            'is_active' => true
        ]);
    }
}
