<?php
require_once 'vendor/autoload.php';
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::find(23);
echo 'User ID: ' . $user->id . PHP_EOL;
echo 'Account Type: ' . $user->account_type . PHP_EOL;
echo 'School ID: ' . $user->school_id . PHP_EOL;
echo 'Is School Student: ' . ($user->account_type === 'student' && !is_null($user->school_id) && $user->school_id > 0 ? 'Yes' : 'No') . PHP_EOL;

$student = \App\Models\Student::where('user_id', 23)->first();
if ($student) {
    echo 'Student Level: ' . $student->level . PHP_EOL;
    echo 'Student Class: ' . $student->class . PHP_EOL;
    $gradeLevel = ($student->level === 'A Level' || in_array($student->class, ['Form 5', 'Form 6'])) ? 'A Level' : 'O Level';
    echo 'Grade Level: ' . $gradeLevel . PHP_EOL;
}