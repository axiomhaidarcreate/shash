<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine the correct base path
// If deployed with project outside public_html, adjust path
// Option 1: Project is in ~/shash/ and public_html points to public/
$basePath = __DIR__ . '/..';

// Option 2: If project is in ~/domains/app.marsksa.com/ structure
// Uncomment the line below if needed:
// $basePath = __DIR__ . '/../shash';

// Check if the autoload file exists at the expected path
if (!file_exists($basePath . '/vendor/autoload.php')) {
    // Try alternative path for Hostinger shared hosting
    $basePath = dirname(__DIR__) . '/shash';
}

require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
