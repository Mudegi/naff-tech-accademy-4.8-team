<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = [
            [
                'name' => 'First Term',
                'slug' => 'first-term-2024',
                'description' => 'First academic term of the year',
                'start_date' => '2024-02-05',
                'end_date' => '2024-05-03',
            ],
            [
                'name' => 'Second Term',
                'slug' => 'second-term-2024',
                'description' => 'Second academic term of the year',
                'start_date' => '2024-05-27',
                'end_date' => '2024-08-23',
            ],
            [
                'name' => 'Third Term',
                'slug' => 'third-term-2024',
                'description' => 'Third academic term of the year',
                'start_date' => '2024-09-16',
                'end_date' => '2024-12-06',
            ],
        ];

        foreach ($terms as $term) {
            DB::table('terms')->updateOrInsert(
                ['slug' => $term['slug']],
                [
                    'name' => $term['name'],
                    'description' => $term['description'],
                    'start_date' => $term['start_date'],
                    'end_date' => $term['end_date'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
