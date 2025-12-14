<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeScaleSeeder extends Seeder
{
    /**
     * Seed the default O-Level and A-Level grade scales.
     */
    public function run(): void
    {
        // Default O-Level Grading Scale (Uganda UCE)
        $oLevelGrades = [
            ['grade' => 'A', 'min_percentage' => 80.00, 'max_percentage' => 100.00, 'points' => 6],
            ['grade' => 'B', 'min_percentage' => 70.00, 'max_percentage' => 79.99, 'points' => 5],
            ['grade' => 'C', 'min_percentage' => 60.00, 'max_percentage' => 69.99, 'points' => 4],
            ['grade' => 'D', 'min_percentage' => 50.00, 'max_percentage' => 59.99, 'points' => 3],
            ['grade' => 'E', 'min_percentage' => 40.00, 'max_percentage' => 49.99, 'points' => 2],
            ['grade' => 'O', 'min_percentage' => 35.00, 'max_percentage' => 39.99, 'points' => 1],
            ['grade' => 'F', 'min_percentage' => 0.00, 'max_percentage' => 34.99, 'points' => 0],
        ];

        foreach ($oLevelGrades as $grade) {
            DB::table('grade_scales')->insert([
                'grade' => $grade['grade'],
                'min_percentage' => $grade['min_percentage'],
                'max_percentage' => $grade['max_percentage'],
                'points' => $grade['points'],
                'academic_level' => 'O-Level',
                'school_id' => null, // NULL = default system-wide scale
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Default A-Level Grading Scale (Uganda UACE - Principal Pass system)
        // For A-Level we use the existing points system:
        // A=6, B=5, C=4, D=3, E=2, O=1, F=0
        $aLevelGrades = [
            ['grade' => 'A', 'min_percentage' => 75.00, 'max_percentage' => 100.00, 'points' => 6],
            ['grade' => 'B', 'min_percentage' => 65.00, 'max_percentage' => 74.99, 'points' => 5],
            ['grade' => 'C', 'min_percentage' => 55.00, 'max_percentage' => 64.99, 'points' => 4],
            ['grade' => 'D', 'min_percentage' => 45.00, 'max_percentage' => 54.99, 'points' => 3],
            ['grade' => 'E', 'min_percentage' => 40.00, 'max_percentage' => 44.99, 'points' => 2],
            ['grade' => 'O', 'min_percentage' => 35.00, 'max_percentage' => 39.99, 'points' => 1],
            ['grade' => 'F', 'min_percentage' => 0.00, 'max_percentage' => 34.99, 'points' => 0],
        ];

        foreach ($aLevelGrades as $grade) {
            DB::table('grade_scales')->insert([
                'grade' => $grade['grade'],
                'min_percentage' => $grade['min_percentage'],
                'max_percentage' => $grade['max_percentage'],
                'points' => $grade['points'],
                'academic_level' => 'A-Level',
                'school_id' => null, // NULL = default system-wide scale
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Default grade scales seeded successfully!');
        $this->command->info('   - O-Level: 7 grades (A=6 to F=0)');
        $this->command->info('   - A-Level: 7 grades (A=6 to F=0)');
    }
}
