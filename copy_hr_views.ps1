$hrViewsDir = "d:\mars\hr-dashboard\resources\views"
$shashHrViewsDir = "d:\mars\shash\resources\views\hr"

if (-not (Test-Path $shashHrViewsDir)) {
    New-Item -ItemType Directory -Path $shashHrViewsDir | Out-Null
}

$viewFolders = @(
    "attendance", "contract_templates", "custodies", "employees",
    "entitlements", "payroll", "reports", "requests", "tasks"
)

foreach ($folder in $viewFolders) {
    $srcFolder = Join-Path $hrViewsDir $folder
    $destFolder = Join-Path $shashHrViewsDir $folder
    if (Test-Path $srcFolder) {
        Copy-Item -Path $srcFolder -Destination $shashHrViewsDir -Recurse -Force
        Write-Host "Copied $folder views"
    }
}

# Copy dashboard
Copy-Item (Join-Path $hrViewsDir "dashboard.blade.php") (Join-Path $shashHrViewsDir "dashboard.blade.php")

# Now update all copied blade files
$allBlades = Get-ChildItem -Path $shashHrViewsDir -Recurse -Filter "*.blade.php"

foreach ($blade in $allBlades) {
    $content = Get-Content $blade.FullName -Raw
    
    # Replace layout component
    $content = $content -replace '<x-layouts\.app>', '<x-layouts.admin>'
    $content = $content -replace '</x-layouts\.app>', '</x-layouts.admin>'
    
    # Also in case it uses @extends
    $content = $content -replace "@extends\('layouts\.app'\)", "@extends('components.layouts.admin')"
    
    Set-Content -Path $blade.FullName -Value $content
}

Write-Host "Done copying and updating views."
