<?php

$basePath = __DIR__ . '/resources/views/hr';

$files = [
    $basePath . '/dashboard.blade.php',
    $basePath . '/employees/index.blade.php',
    $basePath . '/attendance/index.blade.php',
    $basePath . '/payroll/index.blade.php',
    $basePath . '/requests/index.blade.php',
    $basePath . '/entitlements/index.blade.php',
    $basePath . '/custodies/index.blade.php',
    $basePath . '/tasks/index.blade.php',
    $basePath . '/reports/index.blade.php',
];

$backButton = <<<HTML
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>
HTML;

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Skip if already contains the back button link to portal
        if (strpos($content, 'url(\'/hr/portal\')') !== false) {
            echo "Skipped (already exists): $file\n";
            continue;
        }

        // Replace `<div class="page-header"` with the back button + `<div class="page-header"`
        if (strpos($content, '<div class="page-header"') !== false) {
            $content = preg_replace('/<div class="page-header"[^>]*>/', $backButton . "\n$0", $content, 1);
            file_put_contents($file, $content);
            echo "Updated: $file\n";
        } else {
            // Fallback for dashboard if page-header is different
            if (strpos($content, '<div class="page-header">') !== false) {
                $content = str_replace('<div class="page-header">', $backButton . "\n    <div class=\"page-header\">", $content);
                file_put_contents($file, $content);
                echo "Updated (fallback): $file\n";
            } else {
                echo "Could not find page-header in: $file\n";
            }
        }
    } else {
        echo "File not found: $file\n";
    }
}

echo "Done.\n";
