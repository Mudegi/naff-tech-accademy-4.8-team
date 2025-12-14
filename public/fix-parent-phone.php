<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Fixing Parent Phone Number ===\n\n";

$parent = User::where('email', 'adonai_parent@gmail.com')->first();

if (!$parent) {
    echo "❌ Parent not found\n";
    exit;
}

echo "Before:\n";
echo "  Email: {$parent->email}\n";
echo "  Phone: {$parent->phone_number}\n";

$parent->phone_number = '0786765326';
$parent->save();

echo "\nAfter:\n";
echo "  Email: {$parent->email}\n";
echo "  Phone: {$parent->phone_number}\n";

echo "\n✓ Parent phone number updated successfully!\n";
echo "\nYou can now login with:\n";
echo "  Email: {$parent->email}\n";
echo "  Password: 0786765326\n";
echo "\nOR:\n";
echo "  Phone: {$parent->phone_number}\n";
echo "  Password: 0786765326\n";
