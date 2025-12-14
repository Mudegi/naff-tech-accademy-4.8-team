<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Testing Parent Creation Logic ===\n\n";

// Find Adonai Ray
$student = User::where('name', 'like', '%Adonai%')
    ->where('account_type', 'student')
    ->first();

if (!$student) {
    echo "❌ Student 'Adonai Ray' not found\n";
    exit;
}

echo "✓ Found student:\n";
echo "  ID: {$student->id}\n";
echo "  Name: {$student->name}\n";
echo "  Email: {$student->email}\n";
echo "  Phone: {$student->phone_number}\n";
echo "  School ID: {$student->school_id}\n";

// Check if parent exists
$parents = $student->parents;
echo "\n📋 Parent accounts linked to this student: " . $parents->count() . "\n";

if ($parents->count() > 0) {
    foreach ($parents as $parent) {
        echo "\n  Parent ID: {$parent->id}\n";
        echo "  Name: {$parent->name}\n";
        echo "  Email: {$parent->email}\n";
        echo "  Phone: {$parent->phone_number}\n";
        echo "  Account Type: {$parent->account_type}\n";
        echo "  Is Active: " . ($parent->is_active ? 'YES' : 'NO') . "\n";
        
        // Test password
        echo "\n  Testing passwords:\n";
        if (Hash::check('0786765326', $parent->password)) {
            echo "    ✓ Password '0786765326' works!\n";
        } else {
            echo "    ✗ Password '0786765326' doesn't work\n";
        }
        
        if (Hash::check('parent123', $parent->password)) {
            echo "    ✓ Password 'parent123' works!\n";
        } else {
            echo "    ✗ Password 'parent123' doesn't work\n";
        }
        
        // Show actual password hash
        echo "  Current password hash: {$parent->password}\n";
    }
} else {
    echo "  ⚠ No parent account found!\n";
    echo "\n💡 This student was created BEFORE the auto-creation feature was added.\n";
    echo "   Solution: Click 'Generate Missing Parents' button on the admin panel.\n";
}

echo "\n=== Test Complete ===\n";
