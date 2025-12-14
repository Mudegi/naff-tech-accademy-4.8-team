<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>All Departments</h2>";
$allDepts = DB::table('departments')->get();
echo "Total: " . $allDepts->count() . "<br><br>";
foreach ($allDepts as $dept) {
    $schoolInfo = $dept->school_id ? "(school_id: {$dept->school_id})" : "(system-wide)";
    echo "- {$dept->code}: {$dept->name} {$schoolInfo}<br>";
}

echo "<br><h2>System Departments (school_id IS NULL)</h2>";
$departments = DB::table('departments')->whereNull('school_id')->get();
echo "Total: " . $departments->count() . "<br><br>";
foreach ($departments as $dept) {
    echo "- {$dept->code}: {$dept->name}<br>";
}

echo "<br><h2>All Subjects</h2>";
$allSubjects = DB::table('subjects')->get();
echo "Total: " . $allSubjects->count() . "<br><br>";
foreach ($allSubjects as $subject) {
    $schoolInfo = $subject->school_id ? "(school_id: {$subject->school_id})" : "(system-wide)";
    $level = isset($subject->level) ? $subject->level : 'N/A';
    echo "- {$subject->name} ({$level}) {$schoolInfo}<br>";
}

echo "<br><h2>System Subjects (school_id IS NULL)</h2>";
$subjects = DB::table('subjects')->whereNull('school_id')->get();
echo "Total: " . $subjects->count() . "<br><br>";
foreach ($subjects as $subject) {
    $level = isset($subject->level) ? $subject->level : 'N/A';
    echo "- {$subject->name} ({$level})<br>";
}

echo "<br><h2>Run Seeder</h2>";
echo "<a href='run-seeder.php' target='_blank'>Click here to run seeder</a>";
