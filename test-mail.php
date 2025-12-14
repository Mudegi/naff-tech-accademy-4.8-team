<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

try {
    Mail::raw(
        "This is a test email from Naf Academy.\n\n" .
        "If you receive this, the mail configuration is working correctly.",
        function (Message $message) {
            $message->to('emmanuelwandera8@gmail.com')
                ->subject('Test Email from Naf Academy');
        }
    );
    
    echo "Test email sent successfully!\n";
} catch (\Exception $e) {
    echo "Failed to send test email: " . $e->getMessage() . "\n";
    echo "Error trace:\n" . $e->getTraceAsString() . "\n";
} 