<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Testing Phone-Based Parent Login ===\n\n";

$phoneInput = '0786765326';
$passwordInput = '0786765326';

echo "Login Input:\n";
echo "  Phone: $phoneInput\n";
echo "  Password: $passwordInput\n\n";

// Step 1: Check exact phone
echo "Step 1: Looking for exact phone match...\n";
$user = User::where('phone_number', $phoneInput)
    ->where('is_active', true)
    ->first();

if ($user) {
    echo "  ✓ Found: {$user->name} ({$user->account_type})\n";
    if (Hash::check($passwordInput, $user->password)) {
        echo "  ✓ Password matches!\n";
        echo "  🎉 LOGIN WILL SUCCEED AS {$user->account_type}!\n\n";
    } else {
        echo "  ✗ Password doesn't match\n\n";
    }
} else {
    echo "  ✗ No exact match found\n\n";
}

// Step 2: Check parent phone format
echo "Step 2: Looking for parent phone format...\n";
$parentPhoneFormat = '+parent_' . $phoneInput;
echo "  Searching for: $parentPhoneFormat\n";

$user = User::where('phone_number', $parentPhoneFormat)
    ->where('is_active', true)
    ->where('account_type', 'parent')
    ->first();

if ($user) {
    echo "  ✓ Found parent: {$user->name}\n";
    echo "    Email: {$user->email}\n";
    echo "    Phone in DB: {$user->phone_number}\n";
    echo "    Is Active: " . ($user->is_active ? "YES" : "NO") . "\n";
    
    if (Hash::check($passwordInput, $user->password)) {
        echo "  ✓ Password matches!\n";
        echo "  🎉 LOGIN WILL SUCCEED AS PARENT!\n\n";
    } else {
        echo "  ✗ Password doesn't match\n\n";
    }
} else {
    echo "  ✗ No parent found with prefixed phone\n\n";
}

echo "=== Conclusion ===\n";
echo "With the updated AuthController logic:\n";
echo "- Phone '0786765326' will first try exact match (student)\n";
echo "- If not found or password fails, tries '+parent_0786765326' (parent)\n";
echo "- Parents can now login using just their child's phone number!\n";
