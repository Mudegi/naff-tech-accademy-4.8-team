<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Checking is_active Status ===\n\n";

$parent = User::where('email', 'adonai_parent@gmail.com')->first();

if ($parent) {
    echo "Parent account found:\n";
    echo "  ID: {$parent->id}\n";
    echo "  Name: {$parent->name}\n";
    echo "  Email: {$parent->email}\n";
    echo "  Account Type: {$parent->account_type}\n";
    echo "  is_active: " . ($parent->is_active ? 'TRUE ✓' : 'FALSE ✗') . "\n";
    echo "  Raw value: " . var_export($parent->is_active, true) . "\n";
    
    if (!$parent->is_active) {
        echo "\n❌ PROBLEM FOUND: is_active is FALSE!\n";
        echo "   The login requires is_active = true\n";
        echo "\n   Fixing now...\n";
        
        $parent->is_active = true;
        $parent->save();
        
        echo "   ✓ Updated! is_active is now TRUE\n";
    } else {
        echo "\n✓ is_active is correctly set to TRUE\n";
    }
} else {
    echo "❌ Parent account not found!\n";
}

// Check all parent accounts
echo "\n\n=== All Parent Accounts ===\n";
$allParents = User::where('account_type', 'parent')->get(['id', 'name', 'email', 'is_active']);
foreach ($allParents as $p) {
    $status = $p->is_active ? '✓' : '✗';
    echo "{$status} ID:{$p->id} | {$p->name} | {$p->email} | is_active: " . ($p->is_active ? 'TRUE' : 'FALSE') . "\n";
}

echo "\n=== Check Complete ===\n";
