<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Disable caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

echo "<h2>Resource Verification</h2>";
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
</style>";

// Get all resources
$resources = DB::table('resources')
    ->orderBy('created_at', 'desc')
    ->get();

echo "<p><strong>Total Resources:</strong> " . $resources->count() . "</p>";

if ($resources->count() > 0) {
    echo "<table>";
    echo "<tr>
            <th>ID</th>
            <th>Title</th>
            <th>Teacher</th>
            <th>Class ID</th>
            <th>Class Name</th>
            <th>School ID</th>
            <th>Created At</th>
          </tr>";
    
    foreach ($resources as $resource) {
        // Get teacher name
        $teacher = DB::table('users')->where('id', $resource->teacher_id)->first();
        $teacherName = $teacher ? $teacher->name : 'Unknown';
        
        // Get class name
        $class = DB::table('classes')->where('id', $resource->class_id)->first();
        $className = $class ? $class->name : 'Unknown';
        
        echo "<tr>";
        echo "<td>{$resource->id}</td>";
        echo "<td>{$resource->title}</td>";
        echo "<td><span class='badge badge-blue'>{$teacherName}</span></td>";
        echo "<td>{$resource->class_id}</td>";
        echo "<td><span class='badge badge-green'>{$className}</span></td>";
        echo "<td>{$resource->school_id}</td>";
        echo "<td>{$resource->created_at}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p><em>No resources found in the system.</em></p>";
}

echo "<br><br><a href='verify-teacher-assignments.php'>← Back to teacher verification</a>";
