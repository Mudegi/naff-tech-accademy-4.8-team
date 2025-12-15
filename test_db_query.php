<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Test the new query
$userSchoolId = 3;
$studentGradeLevel = 'O Level';

$query = DB::table('resources')
    ->leftJoin('subjects', 'resources.subject_id', '=', 'subjects.id')
    ->leftJoin('terms', 'resources.term_id', '=', 'terms.id')
    ->leftJoin('topics', 'resources.topic_id', '=', 'topics.id')
    ->leftJoin('classes', 'resources.class_id', '=', 'classes.id')
    ->select('resources.*', 'subjects.name as subject_name', 'terms.name as term_name', 'topics.name as topic_name', 'classes.name as class_name')
    ->where('resources.is_active', true)
    ->where('resources.grade_level', $studentGradeLevel)
    ->whereNotNull('resources.google_drive_link')
    ->where('resources.google_drive_link', '!=', '')
    ->where(function($q) use ($userSchoolId) {
        $q->where('resources.school_id', $userSchoolId)
          ->orWhereRaw('resources.id in (select resource_id from resource_school where school_id = ?)', [$userSchoolId]);
    });

echo "SQL: " . $query->toSql() . "\n";
echo "Bindings: " . json_encode($query->getBindings()) . "\n";

$results = $query->get();

echo "Results found: " . $results->count() . "\n";

$direct = $results->where('school_id', $userSchoolId)->count();
$pivot = $results->where('school_id', '!=', $userSchoolId)->count();

echo "Direct: $direct\n";
echo "Pivot: $pivot\n";

// Check the pivot subquery
$pivotIds = DB::table('resource_school')->where('school_id', $userSchoolId)->pluck('resource_id')->toArray();
echo "Pivot resource_ids for school 3: " . count($pivotIds) . "\n";

// Check how many of those meet the criteria
$validPivot = DB::table('resources')
    ->whereIn('id', $pivotIds)
    ->where('grade_level', $studentGradeLevel)
    ->where('is_active', true)
    ->whereNotNull('google_drive_link')
    ->where('google_drive_link', '!=', '')
    ->count();
echo "Valid pivot resources: $validPivot\n";