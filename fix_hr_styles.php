<?php
$dir = __DIR__ . '/resources/views/hr';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    // Containers
    'class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all"' => 'class="card"',
    'class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8"' => 'class="card"',
    'class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4"' => 'class="card" style="display: flex; align-items: center; gap: 1rem;"',
    'class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden"' => 'class="card" style="padding: 0; overflow: hidden;"',
    
    // Inputs & Forms
    'class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20"' => 'class="form-control"',
    'class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-primary focus:ring-primary/20"' => 'class="form-control"',
    'class="w-full border border-slate-200 rounded-xl px-4 py-2 focus:border-primary focus:ring-primary/20 cursor-not-allowed bg-slate-50"' => 'class="form-control" style="background: rgba(0,0,0,0.1);"',
    'class="block text-sm font-bold text-slate-700 mb-2"' => 'class="form-label"',
    
    // Buttons
    'class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-bold shadow-sm transition-colors text-lg inline-flex items-center gap-2"' => 'class="btn"',
    'class="bg-white hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl font-semibold border border-slate-200 shadow-sm transition-colors text-sm"' => 'class="btn btn-outline"',
    'class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 transition-colors shadow-sm"' => 'class="btn"',
    
    // Headers
    'class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-6"' => 'style="font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;"',
    
    // Text colors
    'text-slate-800' => 'text-primary',
    'text-slate-700' => 'text-primary',
    'text-slate-500' => 'text-secondary',
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        
        // Advanced Regex replacements for page headers
        $newContent = preg_replace(
            '/<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">/s',
            '<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">',
            $newContent
        );
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated: $path\n";
        }
    }
}
echo "Done!\n";
