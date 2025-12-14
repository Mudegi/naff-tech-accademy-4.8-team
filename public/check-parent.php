<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Checking for Adonai Ray's Parent Account ===\n\n";

// Check for parent with email
echo "1. Checking parent with email: adonai_parent@gmail.com\n";
$parent = User::where('email', 'adonai_parent@gmail.com')->first();
if ($parent) {
    echo "   FOUND: ID={$parent->id}, Name={$parent->name}, Account Type={$parent->account_type}\n";
} else {
    echo "   NOT FOUND\n";
}

echo "\n2. Checking student named 'Adonai Ray'\n";
$student = User::where('account_type', 'student')
    ->where('name', 'like', '%Adonai%')
    ->first();

if ($student) {
    echo "   Student ID: {$student->id}\n";
    echo "   Name: {$student->name}\n";
    echo "   Email: {$student->email}\n";
    echo "   Phone: {$student->phone_number}\n";
    
    echo "\n3. Checking for parent account of this student\n";
    $parents = $student->parents;
    if ($parents->count() > 0) {
        foreach ($parents as $p) {
            echo "   Parent ID: {$p->id}\n";
            echo "   Parent Name: {$p->name}\n";
            echo "   Parent Email: {$p->email}\n";
            echo "   Parent Phone: {$p->phone_number}\n";
            
            // Try to verify password
            echo "\n4. Testing password: 0786765326\n";
            if (Hash::check('0786765326', $p->password)) {
                echo "   ✓ Password matches!\n";
            } else {
                echo "   ✗ Password does NOT match\n";
            }
        }
    } else {
        echo "   NO parent account found for this student\n";
        echo "   You can generate one using 'Generate Missing Parents' button\n";
    }
} else {
    echo "   Student NOT FOUND\n";
}

echo "\n=== Check Complete ===\n";
