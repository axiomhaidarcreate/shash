$hrControllersDir = "d:\mars\hr-dashboard\app\Http\Controllers\Web"
$shashControllersDir = "d:\mars\shash\app\Http\Controllers\HR"

if (-not (Test-Path $shashControllersDir)) {
    New-Item -ItemType Directory -Path $shashControllersDir | Out-Null
}

$controllersList = @(
    "AttendanceController.php", "ContractTemplateController.php",
    "CustodyController.php", "DashboardController.php", "EmployeeController.php",
    "EntitlementController.php", "LeaveRequestController.php", "PayrollController.php",
    "ReportController.php", "TaskCategoryController.php", "TaskController.php",
    "VoucherController.php", "WorkPeriodController.php"
)

foreach ($ctrl in $controllersList) {
    $src = Join-Path $hrControllersDir $ctrl
    
    $destName = $ctrl
    if ($ctrl -eq "DashboardController.php") { $destName = "HRDashboardController.php" }
    if ($ctrl -eq "ReportController.php") { $destName = "HRReportController.php" }
    
    $dest = Join-Path $shashControllersDir $destName
    
    if (Test-Path $src) {
        $content = Get-Content $src -Raw
        
        # Update namespace
        $content = $content -replace 'namespace App\\Http\\Controllers\\Web;', 'namespace App\Http\Controllers\HR;'
        
        # Rename class if needed
        if ($ctrl -eq "DashboardController.php") { $content = $content -replace 'class DashboardController extends Controller', 'class HRDashboardController extends Controller' }
        if ($ctrl -eq "ReportController.php") { $content = $content -replace 'class ReportController extends Controller', 'class HRReportController extends Controller' }
        
        # Remove Tenant usage if there's any
        $content = $content -replace 'tenant\(\)->id', '1'
        
        # Fix view paths in controllers (e.g. view('employees.index') to view('hr.employees.index'))
        # This is a bit risky with regex, let's see.
        $content = $content -replace 'view\(''(employees|attendance|payroll|requests|entitlements|custodies|tasks|reports|contract_templates)', 'view(''hr.$1'
        $content = $content -replace 'view\("dashboard"\)', 'view("hr.dashboard")'
        
        Set-Content -Path $dest -Value $content
        Write-Host "Copied $destName"
    }
}
Write-Host "Done copying controllers."
