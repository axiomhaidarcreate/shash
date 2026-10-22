$hrDir = "d:\mars\hr-dashboard\database\migrations"
$shashDir = "d:\mars\shash\database\migrations"
$datePrefix = "2026_09_02_"
$i = 0

function Copy-HrMigration {
    param ($srcFile, $newName)
    $pad = $i.ToString("000000")
    $dest = Join-Path $shashDir "${datePrefix}${pad}_${newName}.php"
    Copy-Item (Join-Path $hrDir $srcFile) $dest
    $script:i++
}

# Base tables (needs modification)
$baseContent = Get-Content (Join-Path $hrDir "2026_01_01_000000_create_base_tables.php") -Raw
# Remove settings table creation
$baseContent = $baseContent -replace '(?s)// 5\. Settings.*?Schema::create\(''settings'', function \(Blueprint \$table\) \{.*?\}\);', ''
$baseContent = $baseContent -replace 'Schema::dropIfExists\(''settings''\);', ''

$pad = $i.ToString("000000")
$dest = Join-Path $shashDir "${datePrefix}${pad}_create_hr_base_tables.php"
Set-Content -Path $dest -Value $baseContent
$i++

Copy-HrMigration "2026_04_20_133937_add_detailed_fields_to_employees_table.php" "add_detailed_fields_to_employees_table"
Copy-HrMigration "2026_04_20_134207_create_contract_templates_table.php" "create_contract_templates_table"
Copy-HrMigration "2026_04_20_141317_add_contract_template_id_to_employees_table.php" "add_contract_template_id_to_employees_table"
Copy-HrMigration "2026_04_23_193535_create_entitlements_table.php" "create_entitlements_table"
Copy-HrMigration "2026_04_24_133838_add_delay_minutes_to_attendance_table.php" "add_delay_minutes_to_attendance_table"
Copy-HrMigration "2026_04_24_145043_add_expiry_dates_to_employees_table.php" "add_expiry_dates_to_employees_table"
Copy-HrMigration "2026_04_25_212432_create_custodies_table.php" "create_custodies_table"
Copy-HrMigration "2026_04_25_212450_create_custody_assignments_table.php" "create_custody_assignments_table"
Copy-HrMigration "2026_04_28_210423_add_health_cert_number_to_employees_table.php" "add_health_cert_number_to_employees_table"
Copy-HrMigration "2026_04_29_123920_add_nationality_to_employees_table.php" "add_nationality_to_employees_table"
Copy-HrMigration "2026_04_29_141100_add_attachment_and_receipt_to_entitlements_table.php" "add_attachment_and_receipt_to_entitlements_table"
Copy-HrMigration "2026_04_29_200925_add_daily_work_hours_to_employees_table.php" "add_daily_work_hours_to_employees_table"
Copy-HrMigration "2026_04_29_222611_add_health_cert_expiry_to_employees_table.php" "add_health_cert_expiry_to_employees_table"
Copy-HrMigration "2026_06_05_145359_add_withdrawal_deduction_to_payrolls_table.php" "add_withdrawal_deduction_to_payrolls_table"
Copy-HrMigration "2026_06_05_220000_add_signature_fields_to_payrolls_table.php" "add_signature_fields_to_payrolls_table"
Copy-HrMigration "2026_06_16_202008_add_photo_and_biometrics_to_attendances_table.php" "add_photo_and_biometrics_to_attendances_table"
Copy-HrMigration "2026_06_16_203942_create_vouchers_table.php" "create_vouchers_table"
Copy-HrMigration "2026_07_02_202500_create_tasks_tables.php" "create_tasks_tables"
Copy-HrMigration "2026_07_04_165112_add_execution_details_to_tasks_table.php" "add_execution_details_to_tasks_table"
Copy-HrMigration "2026_07_04_173927_create_task_custom_fields_tables.php" "create_task_custom_fields_tables"
Copy-HrMigration "2026_07_06_225955_add_client_name_to_tasks_table.php" "add_client_name_to_tasks_table"
Copy-HrMigration "2026_07_06_233723_add_manual_employee_name_to_tasks_table.php" "add_manual_employee_name_to_tasks_table"
Copy-HrMigration "2026_07_07_175902_alter_type_in_task_custom_fields_table.php" "alter_type_in_task_custom_fields_table"
Copy-HrMigration "2026_07_09_130000_add_cancellation_fields_to_tasks_table.php" "add_cancellation_fields_to_tasks_table"

Write-Host "Migrations copied successfully!"
