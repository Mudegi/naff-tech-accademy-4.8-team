<?php
require_once 'vendor/autoload.php';
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate the controller logic
$user = DB::table('users')->where('name', 'Wanasolo  Saul')->first();
if (!$user) {
    echo "Student not found\n";
    exit;
}

echo "=== SIMULATING CONTROLLER LOGIC ===\n\n";
echo "Student: {$user->name} (ID: {$user->id}, School ID: {$user->school_id})\n";

$student = DB::table('students')->where('user_id', $user->id)->first();
if (!$student) {
    echo "Student record not found\n";
    exit;
}

$gradeLevel = ($student->level === 'A Level' || in_array($student->class, ['Form 5', 'Form 6'])) ? 'A Level' : 'O Level';
echo "Grade Level: {$gradeLevel}\n\n";

// Check if user is a school student
$isSchoolStudent = $user->account_type === 'student' && !is_null($user->school_id) && $user->school_id > 0;
echo "Is School Student: " . ($isSchoolStudent ? 'Yes' : 'No') . "\n\n";

if ($isSchoolStudent) {
    echo "=== BUILDING QUERY ===\n";

    // Build the exact query from controller
    $query = \App\Models\Resource::with(['subject', 'term', 'topic', 'classRoom'])
        ->where('is_active', true)
        ->where('grade_level', $gradeLevel)
        ->whereNotNull('google_drive_link')
        ->where('google_drive_link', '!=', '')
        ->where(function($q) use ($user) {
            $q->where('school_id', $user->school_id)
              ->orWhereHas('schools', function($subQuery) use ($user) {
                  $subQuery->where('schools.id', $user->school_id);
              });
        });

    echo "Query SQL: " . $query->toSql() . "\n";
    echo "Query bindings: " . json_encode($query->getBindings()) . "\n\n";

    // Get count
    $count = $query->count();
    echo "Total resources found: {$count}\n\n";

    // Get paginated results (like controller does)
    $resources = $query->latest()->paginate(12);
    echo "Paginated results - Current page: {$resources->currentPage()}\n";
    echo "Paginated results - Per page: {$resources->perPage()}\n";
    echo "Paginated results - Total: {$resources->total()}\n";
    echo "Paginated results - Count on this page: {$resources->count()}\n\n";

    // Show first few resources
    echo "First 5 resources on page 1:\n";
    foreach ($resources->take(5) as $resource) {
        echo "  ID: {$resource->id}, Title: {$resource->title}, School_ID: " . ($resource->school_id ?: 'null') . "\n";
    }

    echo "\n=== CHECKING FOR EMPTY LINKS ===\n";
    $emptyLinks = DB::table('resources')
        ->where('is_active', true)
        ->where('grade_level', $gradeLevel)
        ->where(function($q) use ($user) {
            $q->where('school_id', $user->school_id)
              ->orWhereExists(function($subQ) use ($user) {
                  $subQ->select(DB::raw(1))
                       ->from('resource_school')
                       ->whereRaw('resource_school.resource_id = resources.id')
                       ->where('resource_school.school_id', $user->school_id);
              });
        })
        ->where(function($q) {
            $q->whereNull('google_drive_link')
              ->orWhere('google_drive_link', '');
        })
        ->count();

    echo "Resources with empty/null Google Drive links: {$emptyLinks}\n";

    $validLinks = DB::table('resources')
        ->where('is_active', true)
        ->where('grade_level', $gradeLevel)
        ->where(function($q) use ($user) {
            $q->where('school_id', $user->school_id)
              ->orWhereExists(function($subQ) use ($user) {
                  $subQ->select(DB::raw(1))
                       ->from('resource_school')
                       ->whereRaw('resource_school.resource_id = resources.id')
                       ->where('resource_school.school_id', $user->school_id);
              });
        })
        ->whereNotNull('google_drive_link')
        ->where('google_drive_link', '!=', '')
        ->count();

    echo "Resources with valid Google Drive links: {$validLinks}\n";
}