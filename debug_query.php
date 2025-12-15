<?php
require_once 'vendor/autoload.php';
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING RESOURCE QUERY LOGIC ===\n\n";

// Get the student
$user = DB::table('users')->where('name', 'Wanasolo  Saul')->first();
if (!$user) {
    echo "Student not found\n";
    exit;
}

echo "Student: {$user->name} (ID: {$user->id}, School ID: {$user->school_id})\n";

$student = DB::table('students')->where('user_id', $user->id)->first();
if (!$student) {
    echo "Student record not found\n";
    exit;
}

$gradeLevel = ($student->level === 'A Level' || in_array($student->class, ['Form 5', 'Form 6'])) ? 'A Level' : 'O Level';
echo "Grade Level: {$gradeLevel}\n\n";

// Test the exact query from the controller
$query = DB::table('resources')
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
    });

$count = $query->count();
echo "Query result count: {$count}\n\n";

// Show the actual SQL query
$querySql = $query->toSql();
$bindings = $query->getBindings();
echo "SQL Query: {$querySql}\n";
echo "Bindings: " . json_encode($bindings) . "\n\n";

// Get sample results
$results = $query->select('id', 'title', 'school_id', 'grade_level')->limit(10)->get();
echo "Sample results:\n";
foreach ($results as $resource) {
    echo "  ID: {$resource->id}, Title: {$resource->title}, School_ID: " . ($resource->school_id ?: 'null') . ", Grade: {$resource->grade_level}\n";
}

echo "\n=== CHECKING PIVOT TABLE DIRECTLY ===\n";
$pivotCount = DB::table('resource_school')->where('school_id', $user->school_id)->count();
echo "Pivot table entries for school {$user->school_id}: {$pivotCount}\n";

$pivotSample = DB::table('resource_school')
    ->where('school_id', $user->school_id)
    ->join('resources', 'resource_school.resource_id', '=', 'resources.id')
    ->where('resources.grade_level', $gradeLevel)
    ->where('resources.is_active', true)
    ->whereNotNull('resources.google_drive_link')
    ->where('resources.google_drive_link', '!=', '')
    ->select('resources.id', 'resources.title', 'resources.school_id', 'resources.grade_level')
    ->limit(5)
    ->get();

echo "Pivot table sample (grade level filtered):\n";
foreach ($pivotSample as $resource) {
    echo "  ID: {$resource->id}, Title: {$resource->title}, School_ID: " . ($resource->school_id ?: 'null') . ", Grade: {$resource->grade_level}\n";
}