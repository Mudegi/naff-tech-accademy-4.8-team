<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Disable caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

echo "<h2>Teacher Assignments Verification</h2>";
echo "<p style='color: #666; font-size: 12px;'>Last Updated: " . date('Y-m-d H:i:s') . "</p>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .badge { padding: 4px 8px; border-radius: 4px; margin: 2px; display: inline-block; font-size: 12px; }
    .badge-blue { background-color: #2196F3; color: white; }
    .badge-green { background-color: #4CAF50; color: white; }
    .badge-orange { background-color: #FF9800; color: white; }
    .debug { background-color: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #2196F3; }
</style>";

// Get all teachers (subject_teacher, teacher, head_of_department)
$teachers = DB::table('users')
    ->whereIn('account_type', ['subject_teacher', 'teacher', 'head_of_department'])
    ->where('is_active', true)
    ->get();

echo "<p><strong>Total Teachers Found:</strong> " . $teachers->count() . "</p>";

// Debug: Show total class assignments in database
$totalClassAssignments = DB::table('class_user')->count();
echo "<div class='debug'>";
echo "<strong>Debug Info:</strong><br>";
echo "Total class_user records: {$totalClassAssignments}<br>";
echo "Total subject_user records: " . DB::table('subject_user')->count();
echo "</div>";

if ($teachers->count() > 0) {
    echo "<table>";
    echo "<tr>
            <th>Name</th>
            <th>Account Type</th>
            <th>Department</th>
            <th>Assigned Classes</th>
            <th>Assigned Subjects</th>
          </tr>";
    
    foreach ($teachers as $teacher) {
        // Get department name
        $department = DB::table('departments')
            ->where('id', $teacher->department_id)
            ->first();
        $deptName = $department ? $department->name : '<em>None</em>';
        
        // Get assigned classes - use direct query without join to avoid scope issues
        $classIds = DB::table('class_user')
            ->where('user_id', $teacher->id)
            ->pluck('class_id')
            ->toArray();
        
        $classes = [];
        if (!empty($classIds)) {
            $classes = DB::table('classes')
                ->whereIn('id', $classIds)
                ->pluck('name')
                ->toArray();
        }
        
        // Get assigned subjects - use direct query without join to avoid scope issues
        $subjectIds = DB::table('subject_user')
            ->where('user_id', $teacher->id)
            ->pluck('subject_id')
            ->toArray();
        
        $subjects = [];
        if (!empty($subjectIds)) {
            $subjects = DB::table('subjects')
                ->whereIn('id', $subjectIds)
                ->pluck('name')
                ->toArray();
        }
        
        echo "<tr>";
        echo "<td><strong>{$teacher->name}</strong><br><small>{$teacher->email}</small></td>";
        echo "<td><span class='badge badge-blue'>{$teacher->account_type}</span></td>";
        echo "<td>{$deptName}</td>";
        echo "<td>";
        if (count($classes) > 0) {
            foreach ($classes as $class) {
                echo "<span class='badge badge-green'>{$class}</span> ";
            }
        } else {
            echo "<em>No classes assigned</em>";
        }
        echo "</td>";
        echo "<td>";
        if (count($subjects) > 0) {
            foreach ($subjects as $subject) {
                echo "<span class='badge badge-orange'>{$subject}</span> ";
            }
        } else {
            echo "<em>No subjects assigned</em>";
        }
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Debug: Show all class assignments
    echo "<div class='debug' style='margin-top: 20px;'>";
    echo "<strong>All Class Assignments (Raw Data):</strong><br>";
    $allAssignments = DB::table('class_user')
        ->join('users', 'class_user.user_id', '=', 'users.id')
        ->join('classes', 'class_user.class_id', '=', 'classes.id')
        ->select('users.name as user_name', 'users.account_type', 'classes.name as class_name', 'class_user.user_id', 'class_user.class_id')
        ->get();
    
    if ($allAssignments->count() > 0) {
        echo "<table style='font-size: 12px;'>";
        echo "<tr><th>User ID</th><th>User Name</th><th>Account Type</th><th>Class ID</th><th>Class Name</th></tr>";
        foreach ($allAssignments as $assignment) {
            echo "<tr>";
            echo "<td>{$assignment->user_id}</td>";
            echo "<td>{$assignment->user_name}</td>";
            echo "<td>{$assignment->account_type}</td>";
            echo "<td>{$assignment->class_id}</td>";
            echo "<td>{$assignment->class_name}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<em>No class assignments found in database</em>";
    }
    echo "</div>";
} else {
    echo "<p><em>No teachers found in the system.</em></p>";
}

echo "<br><br><a href='test-seeder.php'>← Back to test page</a>";
