<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Resource;

// Test the query
$userSchoolId = 3;
$studentGradeLevel = 'O Level';

$query = Resource::query();
$query->withoutGlobalScope('school');
$query->with(['subject', 'term', 'topic', 'classRoom'])
    ->where('is_active', true)
    ->where('grade_level', $studentGradeLevel)
    ->whereNotNull('google_drive_link')
    ->where('google_drive_link', '!=', '')
    ->where(function($q) use ($userSchoolId) {
        $q->where('school_id', $userSchoolId)
          ->orWhereRaw('id in (select resource_id from resource_school where school_id = ?)', [$userSchoolId]);
    });

$results = $query->get();

echo "Results found: " . $results->count() . "\n";

$direct = $results->where('school_id', $userSchoolId)->count();
$pivot = $results->where('school_id', '!=', $userSchoolId)->count();

echo "Direct: $direct\n";
echo "Pivot: $pivot\n";