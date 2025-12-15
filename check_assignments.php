<?php
require_once 'vendor/autoload.php';
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SCHOOL-RESOURCE ASSIGNMENT ANALYSIS ===\n\n";

// Get Green Valley High School
$school = DB::table('schools')->where('name', 'Green Valley High School')->first();
if (!$school) {
    echo "Green Valley High School not found\n";
    exit;
}

echo "School: {$school->name} (ID: {$school->id})\n\n";

// Check direct assignments (resources.school_id)
$directAssignments = DB::table('resources')
    ->where('school_id', $school->id)
    ->where('is_active', true)
    ->count();

echo "Direct assignments (resources.school_id = {$school->id}): {$directAssignments}\n";

// Check pivot table assignments
$pivotAssignments = DB::table('resource_school')
    ->where('school_id', $school->id)
    ->count();

echo "Pivot table assignments (resource_school): {$pivotAssignments}\n\n";

// Check total resources that should be accessible
$totalAccessible = DB::table('resources')
    ->where('is_active', true)
    ->where(function($q) use ($school) {
        $q->where('school_id', $school->id)
          ->orWhereExists(function($subQ) use ($school) {
              $subQ->select(DB::raw(1))
                   ->from('resource_school')
                   ->whereRaw('resource_school.resource_id = resources.id')
                   ->where('resource_school.school_id', $school->id);
          });
    })
    ->count();

echo "Total resources accessible to school: {$totalAccessible}\n\n";

// Check grade level distribution
$gradeDistribution = DB::table('resources')
    ->where('is_active', true)
    ->where(function($q) use ($school) {
        $q->where('school_id', $school->id)
          ->orWhereExists(function($subQ) use ($school) {
              $subQ->select(DB::raw(1))
                   ->from('resource_school')
                   ->whereRaw('resource_school.resource_id = resources.id')
                   ->where('resource_school.school_id', $school->id);
          });
    })
    ->select('grade_level', DB::raw('count(*) as count'))
    ->groupBy('grade_level')
    ->get();

echo "Grade level distribution:\n";
foreach ($gradeDistribution as $grade) {
    echo "  {$grade->grade_level}: {$grade->count}\n";
}
echo "\n";

// Check for Wanasolo Saul
$user = DB::table('users')->where('name', 'Wanasolo  Saul')->first();
if ($user) {
    echo "Student: {$user->name} (ID: {$user->id}, School ID: {$user->school_id})\n";

    $student = DB::table('students')->where('user_id', $user->id)->first();
    if ($student) {
        $gradeLevel = ($student->level === 'A Level' || in_array($student->class, ['Form 5', 'Form 6'])) ? 'A Level' : 'O Level';
        echo "Grade Level: {$gradeLevel}\n\n";

        // Check what this student should see
        $studentResources = DB::table('resources')
            ->where('is_active', true)
            ->where('grade_level', $gradeLevel)
            ->whereNotNull('google_drive_link')
            ->where('google_drive_link', '!=', '')
            ->where(function($q) use ($user) {
                $q->where('school_id', $user->school_id)
                  ->orWhereExists(function($subQ) use ($user) {
                      $subQ->select(DB::raw(1))
                           ->from('resource_school')
                           ->whereRaw('resource_school.resource_id = resources.id')
                           ->where('resource_school.school_id', $user->school_id);
                  });
            })
            ->count();

        echo "Resources {$gradeLevel} student should see: {$studentResources}\n\n";

        // Show sample resources
        $samples = DB::table('resources')
            ->where('is_active', true)
            ->where('grade_level', $gradeLevel)
            ->whereNotNull('google_drive_link')
            ->where('google_drive_link', '!=', '')
            ->where(function($q) use ($user) {
                $q->where('school_id', $user->school_id)
                  ->orWhereExists(function($subQ) use ($user) {
                      $subQ->select(DB::raw(1))
                           ->from('resource_school')
                           ->whereRaw('resource_school.resource_id = resources.id')
                           ->where('resource_school.school_id', $user->school_id);
                  });
            })
            ->select('id', 'title', 'school_id', 'grade_level')
            ->limit(5)
            ->get();

        echo "Sample resources:\n";
        foreach ($samples as $resource) {
            echo "  ID: {$resource->id}, Title: {$resource->title}, School_ID: " . ($resource->school_id ?: 'null') . ", Grade: {$resource->grade_level}\n";
        }
    }
}