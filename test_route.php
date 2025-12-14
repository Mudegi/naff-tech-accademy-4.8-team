<?php
// Quick test to verify route exists
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$routes = Route::getRoutes();
foreach ($routes as $route) {
    if (strpos($route->uri(), 'university-cut-offs/import') !== false) {
        echo "Route found: " . $route->uri() . " -> " . $route->getName() . "\n";
        echo "Methods: " . implode(', ', $route->methods()) . "\n";
    }
}

