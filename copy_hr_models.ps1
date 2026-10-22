$hrModelsDir = "d:\mars\hr-dashboard\app\Models"
$shashModelsDir = "d:\mars\shash\app\Models"

$models = @(
    "Announcement.php", "Attendance.php", "ContractTemplate.php",
    "Custody.php", "CustodyAssignment.php", "Department.php",
    "Employee.php", "Entitlement.php", "LeaveRequest.php",
    "Notification.php", "Payroll.php", "Task.php",
    "TaskAttachment.php", "TaskCategory.php", "TaskComment.php",
    "TaskCustomField.php", "TaskCustomFieldValue.php", "Voucher.php",
    "WorkPeriod.php"
)

foreach ($model in $models) {
    $src = Join-Path $hrModelsDir $model
    $dest = Join-Path $shashModelsDir $model
    
    if (Test-Path $src) {
        $content = Get-Content $src -Raw
        # Remove Tenancy traits
        $content = $content -replace 'use Stancl\\Tenancy\\Database\\Concerns\\BelongsToTenant;\r?\n', ''
        $content = $content -replace 'use BelongsToTenant;\r?\n', ''
        
        Set-Content -Path $dest -Value $content
        Write-Host "Copied $model"
    }
}
Write-Host "Done copying models."
