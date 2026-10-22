$hrViewsDir = "d:\mars\shash\resources\views\hr"
$allBlades = Get-ChildItem -Path $hrViewsDir -Recurse -Filter "*.blade.php"

foreach ($blade in $allBlades) {
    $content = Get-Content $blade.FullName -Raw
    
    # Replace route('group.action') with route('hr.group.action')
    # Groups: employees, attendance, payroll, requests, entitlements, custodies, tasks, contract_templates, reports
    $content = $content -replace "route\('employees\.", "route('hr.employees."
    $content = $content -replace "route\('attendance\.", "route('hr.attendance."
    $content = $content -replace "route\('payroll\.", "route('hr.payroll."
    $content = $content -replace "route\('requests\.", "route('hr.requests."
    $content = $content -replace "route\('entitlements\.", "route('hr.entitlements."
    $content = $content -replace "route\('custodies\.", "route('hr.custodies."
    $content = $content -replace "route\('tasks\.", "route('hr.tasks."
    $content = $content -replace "route\('contract_templates\.", "route('hr.contract_templates."
    $content = $content -replace "route\('reports\.", "route('hr.reports."
    
    # Replace dashboard
    $content = $content -replace "route\('dashboard'\)", "route('hr.dashboard')"
    
    Set-Content -Path $blade.FullName -Value $content
}
Write-Host "Done fixing routes in blade files."
