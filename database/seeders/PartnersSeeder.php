<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'name' => 'Tuple',
                'logo' => 'https://tailwindui.com/img/logos/tuple-logo-gray-400.svg',
                'website' => 'https://tuple.app',
                'description' => 'Tuple is a remote pair programming app for macOS and Windows.',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Mirage',
                'logo' => 'https://tailwindui.com/img/logos/mirage-logo-gray-400.svg',
                'website' => 'https://miragejs.com',
                'description' => 'Mirage is a library that helps frontend developers mock API responses.',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'StaticKit',
                'logo' => 'https://tailwindui.com/img/logos/statickit-logo-gray-400.svg',
                'website' => 'https://statickit.com',
                'description' => 'StaticKit is a form backend for static sites.',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Transistor',
                'logo' => 'https://tailwindui.com/img/logos/transistor-logo-gray-400.svg',
                'website' => 'https://transistor.fm',
                'description' => 'Transistor is a podcast hosting and analytics platform.',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Workcation',
                'logo' => 'https://tailwindui.com/img/logos/workcation-logo-gray-400.svg',
                'website' => 'https://workcation.com',
                'description' => 'Workcation is a platform for remote workers to find accommodations.',
                'is_active' => true,
                'order' => 5,
            ],
        ];

        foreach ($partners as $partner) {
            Partner::firstOrCreate(
                ['name' => $partner['name']],
                $partner
            );
        }
    }
}
