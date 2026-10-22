$hrRoutesStr = @"
    // --- HR System Routes ---
    Route::prefix('hr')->name('hr.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\HR\HRDashboardController::class, 'index'])->name('dashboard');
        
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/dismissed', [\App\Http\Controllers\HR\EmployeeController::class, 'dismissed'])->name('dismissed');
            Route::get('/vacation', [\App\Http\Controllers\HR\EmployeeController::class, 'vacation'])->name('vacation');
            Route::get('/', [\App\Http\Controllers\HR\EmployeeController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\HR\EmployeeController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\HR\EmployeeController::class, 'store'])->name('store');
            Route::get('/{employee}/edit', [\App\Http\Controllers\HR\EmployeeController::class, 'edit'])->name('edit');
            Route::get('/{employee}/print-contract', [\App\Http\Controllers\HR\EmployeeController::class, 'printContract'])->name('printContract');
            Route::put('/{employee}', [\App\Http\Controllers\HR\EmployeeController::class, 'update'])->name('update');
            Route::delete('/{employee}', [\App\Http\Controllers\HR\EmployeeController::class, 'destroy'])->name('destroy');
            Route::patch('/{employee}/toggle-status', [\App\Http\Controllers\HR\EmployeeController::class, 'toggleStatus'])->name('toggleStatus');
            Route::patch('/{employee}/approve', [\App\Http\Controllers\HR\EmployeeController::class, 'approve'])->name('approve');
        });

        Route::resource('contract_templates', \App\Http\Controllers\HR\ContractTemplateController::class);

        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\HR\AttendanceController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\HR\AttendanceController::class, 'print'])->name('print');
            Route::post('/{attendance}/deduct', [\App\Http\Controllers\HR\AttendanceController::class, 'convertToDeduction'])->name('deduct');
        });

        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [\App\Http\Controllers\HR\PayrollController::class, 'index'])->name('index');
            Route::post('/process', [\App\Http\Controllers\HR\PayrollController::class, 'process'])->name('process');
            Route::post('/process-custom', [\App\Http\Controllers\HR\PayrollController::class, 'processCustom'])->name('process_custom');
            Route::post('/close-month/{employee}', [\App\Http\Controllers\HR\PayrollController::class, 'closeMonth'])->name('close_month');
            Route::get('/{payroll}/slip', [\App\Http\Controllers\HR\PayrollController::class, 'printSlip'])->name('slip');
            Route::patch('/{payroll}/pay', [\App\Http\Controllers\HR\PayrollController::class, 'markAsPaid'])->name('markAsPaid');
            Route::patch('/{payroll}/confirm-payment', [\App\Http\Controllers\HR\PayrollController::class, 'confirmPayment'])->name('confirmPayment');
        });

        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\HR\LeaveRequestController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\HR\LeaveRequestController::class, 'store'])->name('store');
            Route::patch('/{leaveRequest}/status', [\App\Http\Controllers\HR\LeaveRequestController::class, 'updateStatus'])->name('updateStatus');
            Route::get('/{leaveRequest}/voucher', [\App\Http\Controllers\HR\LeaveRequestController::class, 'printVoucher'])->name('voucher');
        });

        Route::prefix('entitlements')->name('entitlements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\HR\EntitlementController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\HR\EntitlementController::class, 'store'])->name('store');
            Route::put('/{entitlement}', [\App\Http\Controllers\HR\EntitlementController::class, 'update'])->name('update');
            Route::delete('/{entitlement}', [\App\Http\Controllers\HR\EntitlementController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\HR\HRReportController::class, 'index'])->name('index');
            Route::get('/payroll-summary', [\App\Http\Controllers\HR\HRReportController::class, 'payrollSummary'])->name('payroll_summary');
            Route::get('/employees-list', [\App\Http\Controllers\HR\HRReportController::class, 'employeesList'])->name('employees_list');
            Route::get('/departments', [\App\Http\Controllers\HR\HRReportController::class, 'departments'])->name('departments');
            Route::get('/payroll-history', [\App\Http\Controllers\HR\HRReportController::class, 'payrollHistory'])->name('payroll_history');
            Route::get('/employee/{employee}/full', [\App\Http\Controllers\HR\HRReportController::class, 'employeeFull'])->name('employee_full');
            Route::get('/employee/{employee}/salary', [\App\Http\Controllers\HR\HRReportController::class, 'employeeSalary'])->name('employee_salary');
            Route::get('/employee/{employee}/withdrawals', [\App\Http\Controllers\HR\HRReportController::class, 'employeeWithdrawals'])->name('employee_withdrawals');
            Route::get('/employee/{employee}/absences', [\App\Http\Controllers\HR\HRReportController::class, 'employeeAbsences'])->name('employee_absences');
            Route::get('/employee/{employee}/late', [\App\Http\Controllers\HR\HRReportController::class, 'employeeLate'])->name('employee_late');
            Route::get('/employee/{employee}/attendance', [\App\Http\Controllers\HR\HRReportController::class, 'employeeAttendance'])->name('employee_attendance');
            Route::get('/employee/{employee}/documents', [\App\Http\Controllers\HR\HRReportController::class, 'employeeDocuments'])->name('employee_documents');
        });

        Route::prefix('custodies')->name('custodies.')->group(function () {
            Route::get('/', [\App\Http\Controllers\HR\CustodyController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\HR\CustodyController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\HR\CustodyController::class, 'store'])->name('store');
            Route::get('/assign', [\App\Http\Controllers\HR\CustodyController::class, 'assignForm'])->name('assign');
            Route::post('/assign', [\App\Http\Controllers\HR\CustodyController::class, 'assignStore'])->name('assign.store');
            Route::get('/return', [\App\Http\Controllers\HR\CustodyController::class, 'returnForm'])->name('return');
            Route::post('/return', [\App\Http\Controllers\HR\CustodyController::class, 'returnStore'])->name('return.store');
            Route::get('/{custody}/edit', [\App\Http\Controllers\HR\CustodyController::class, 'edit'])->name('edit');
            Route::put('/{custody}', [\App\Http\Controllers\HR\CustodyController::class, 'update'])->name('update');
            Route::delete('/{custody}', [\App\Http\Controllers\HR\CustodyController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::resource('categories', \App\Http\Controllers\HR\TaskCategoryController::class)->except(['create', 'show', 'edit']);
            
            Route::get('/', [\App\Http\Controllers\HR\TaskController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\HR\TaskController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\HR\TaskController::class, 'store'])->name('store');
            Route::get('/{task}', [\App\Http\Controllers\HR\TaskController::class, 'show'])->name('show');
            Route::get('/{task}/edit', [\App\Http\Controllers\HR\TaskController::class, 'edit'])->name('edit');
            Route::put('/{task}', [\App\Http\Controllers\HR\TaskController::class, 'update'])->name('update');
            Route::delete('/{task}', [\App\Http\Controllers\HR\TaskController::class, 'destroy'])->name('destroy');
            
            Route::post('/{task}/comments', [\App\Http\Controllers\HR\TaskController::class, 'addComment'])->name('comments.store');
            Route::post('/{task}/cancel', [\App\Http\Controllers\HR\TaskController::class, 'cancel'])->name('cancel');
            Route::post('/{task}/reassign', [\App\Http\Controllers\HR\TaskController::class, 'reassign'])->name('reassign');
        });
    });
    // --- End HR System Routes ---
"@

$webPhpPath = "d:\mars\shash\routes\web.php"
$content = Get-Content $webPhpPath -Raw

# Insert after Route::get('/admin', AdminDashboard::class)->name('admin')->middleware('role:admin');
$content = $content -replace "(Route::get\('/admin', AdminDashboard::class\)->name\('admin'\)->middleware\('role:admin'\);)", "`$1`n$hrRoutesStr"

Set-Content -Path $webPhpPath -Value $content
Write-Host "Routes updated."
