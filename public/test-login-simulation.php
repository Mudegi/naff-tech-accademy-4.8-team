<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "=== Testing Login Credentials ===\n\n";

$email = 'adonai_parent@gmail.com';
$password = '0786765326';

echo "Testing login with:\n";
echo "  Email: $email\n";
echo "  Password: $password\n\n";

// Find user by email
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found with email: $email\n";
    exit;
}

echo "User found:\n";
echo "  ID: {$user->id}\n";
echo "  Name: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  Account Type: {$user->account_type}\n";
echo "  is_active: " . ($user->is_active ? 'TRUE' : 'FALSE') . "\n";
echo "  School ID: " . ($user->school_id ?? 'NULL') . "\n";

// Test password
echo "\nPassword check:\n";
if (Hash::check($password, $user->password)) {
    echo "  ✓ Password matches!\n";
} else {
    echo "  ✗ Password does NOT match!\n";
    echo "  Hash in DB: {$user->password}\n";
    echo "  Testing hash: " . Hash::make($password) . "\n";
    exit;
}

// Check credentials array as AuthController does
echo "\nTesting Auth::attempt simulation:\n";
$credentials = [
    'email' => $email,
    'password' => $password,
    'is_active' => true,
];

echo "  Credentials array:\n";
echo "    email: {$credentials['email']}\n";
echo "    password: {$credentials['password']}\n";
echo "    is_active: " . ($credentials['is_active'] ? 'true' : 'false') . "\n";

// Check if user matches all credentials
$matchesEmail = ($user->email === $credentials['email']);
$matchesPassword = Hash::check($credentials['password'], $user->password);
$matchesActive = ($user->is_active == $credentials['is_active']);

echo "\n  Credential checks:\n";
echo "    Email matches: " . ($matchesEmail ? '✓ YES' : '✗ NO') . "\n";
echo "    Password matches: " . ($matchesPassword ? '✓ YES' : '✗ NO') . "\n";
echo "    is_active matches: " . ($matchesActive ? '✓ YES' : '✗ NO') . "\n";

if ($matchesEmail && $matchesPassword && $matchesActive) {
    echo "\n✓✓✓ ALL CHECKS PASS! Login should work!\n";
    
    // Check if school exists (for school members)
    if ($user->school_id) {
        $school = \App\Models\School::find($user->school_id);
        if ($school) {
            echo "\n  School check: ✓ School exists (ID: {$school->id}, Name: {$school->name})\n";
        } else {
            echo "\n  School check: ✗ School NOT FOUND (ID: {$user->school_id})\n";
            echo "  This would block login!\n";
        }
    } else {
        echo "\n  School check: N/A (not school member)\n";
    }
} else {
    echo "\n❌ Some checks FAILED! Login would not work.\n";
}

echo "\n=== Test Complete ===\n";
