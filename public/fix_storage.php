<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>Fixing Storage Link for Hostinger...</h2>";

$publicStoragePath = __DIR__.'/public/storage';

if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        unlink($publicStoragePath);
        echo "<p>Deleted existing broken symlink.</p>";
    } else {
        echo "<p style='color:red;'>Warning: public/storage exists but is NOT a symlink. It might be a real directory. Please delete the 'public/storage' directory manually via File Manager.</p>";
    }
}

try {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo "<p style='color:green; font-weight:bold;'>Storage link created successfully! Product images should now appear.</p>";
} catch (\Exception $e) {
    echo "<p style='color:red;'>Failed to create via Artisan: " . $e->getMessage() . "</p>";
    
    // Fallback manual symlink creation
    echo "<p>Attempting manual symlink creation fallback...</p>";
    $target = __DIR__.'/storage/app/public';
    $link = __DIR__.'/public/storage';
    if (symlink($target, $link)) {
        echo "<p style='color:green; font-weight:bold;'>Manual storage link created successfully!</p>";
    } else {
        echo "<p style='color:red;'>Manual symlink also failed. Please check folder permissions.</p>";
    }
}
