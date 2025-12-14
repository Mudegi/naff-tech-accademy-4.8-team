<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "<h2>Running UgandanSubjectsAndDepartmentsSeeder...</h2>";
echo "<pre>";

try {
    // Use Artisan to call the seeder class
    Artisan::call('db:seed', [
        '--class' => 'Database\\Seeders\\UgandanSubjectsAndDepartmentsSeeder',
        '--force' => true // Use --force to run in production
    ]);

    // Capture and display the output from the Artisan command
    echo "✅ Seeder executed successfully!\n\n";
    echo "Output:\n";
    echo Artisan::output();

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
    echo "\n\nStack trace:\n" . $e->getTraceAsString();
}

echo "</pre>";
echo "<br><a href='test-seeder.php'>Back to results</a>";
