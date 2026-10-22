<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\AdminDashboard;
use App\Livewire\OrderStaffBoard;
use App\Livewire\KitchenBoard;
use App\Livewire\BaristaBoard;
use App\Livewire\RestaurantBoard;
use App\Livewire\DeliveryBoard;
use App\Livewire\ReceptionBoard;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('admin')) return redirect()->route('admin');
        if ($user->hasRole('order_staff')) return redirect()->route('order');
        if ($user->hasRole('kitchen_staff')) return redirect()->route('kitchen');
        if ($user->hasRole('barista_staff')) return redirect()->route('barista');
        if ($user->hasRole('restaurant_staff')) return redirect()->route('restaurant');
        if ($user->hasRole('delivery_staff')) return redirect()->route('delivery');
        if ($user->hasRole('inventory_staff')) return redirect()->route('admin.physical_inventory');
        if ($user->hasRole('reception_staff')) return redirect()->route('reception');
        if ($user->hasRole('greeting_staff')) return redirect()->route('greetings');
        if ($user->hasRole('daily_purchases_staff')) return redirect()->route('admin.daily_purchases_requests');
        if ($user->hasRole('dynamic_kitchen_staff') && $user->kitchen_slug) {
            return redirect()->route('dynamic.kitchen', ['slug' => $user->kitchen_slug]);
        }
    }
    return redirect()->route('login');
});

Route::get('/file-serve/{path}', function ($path) {
    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        return \Illuminate\Support\Facades\Storage::disk('public')->response($path);
    }
    abort(404);
})->where('path', '.*')->name('file.serve');

Route::get('/create-storage-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'تم إنشاء اختصار مجلد الصور بنجاح! يمكنك الآن رؤية الصور.';
    } catch (\Exception $e) {
        return 'حدث خطأ: ' . $e->getMessage();
    }
});

Route::get('/site', \App\Livewire\PublicSite::class)->name('site');

Route::get('/event-booking/{id}/print', function ($id) {
    $booking = \App\Models\EventBooking::with('event')->findOrFail($id);
    $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'لاونج الشيشة';
    return view('invoice.print-event-booking', compact('booking', 'siteName'));
})->name('event-booking.print');
Route::get('/event-order', \App\Livewire\EventSelfOrder::class)->name('event.self-order');

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/admin', AdminDashboard::class)->name('admin')->middleware('role:admin');
    // --- HR System Routes ---
    Route::prefix('hr')->name('hr.')->middleware('role:admin')->group(function () {
        Route::view('/portal', 'hr.index')->name('portal');
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
    Route::get('/admin/purchases', \App\Livewire\Admin\Purchases\Dashboard::class)->name('admin.purchases')->middleware('role:admin');
    Route::get('/admin/purchases/suppliers', \App\Livewire\Admin\Purchases\Suppliers::class)->name('admin.purchases.suppliers')->middleware('role:admin');
    Route::get('/admin/purchases/invoices', \App\Livewire\Admin\Purchases\Invoices::class)->name('admin.purchases.invoices')->middleware('role:admin');
    Route::get('/admin/products', \App\Livewire\Admin\Products::class)->name('admin.products')->middleware('role:admin');
    Route::get('/admin/employees', \App\Livewire\Admin\Employees::class)->name('admin.employees')->middleware('role:admin');
    Route::get('/admin/settings', \App\Livewire\Admin\Settings::class)->name('admin.settings')->middleware('role:admin');
    Route::get('/admin/site', \App\Livewire\Admin\SiteManager::class)->name('admin.site')->middleware('role:admin');
    Route::get('/admin/inventory', \App\Livewire\Admin\Inventory::class)->name('admin.inventory')->middleware('role:admin|inventory_staff');
    Route::get('/admin/physical-inventory', \App\Livewire\Admin\PhysicalInventoryManager::class)->name('admin.physical_inventory')->middleware('role:admin|inventory_staff');
    Route::get('/admin/reports', \App\Livewire\Admin\Reports::class)->name('admin.reports')->middleware('role:admin');
    Route::get('/admin/reception', \App\Livewire\AdminReception::class)->name('admin.reception')->middleware('role:admin');
    Route::get('/admin/shifts', \App\Livewire\AdminShiftSettings::class)->name('admin.shifts')->middleware('role:admin');
    Route::get('/admin/expenses', \App\Livewire\Admin\Expenses::class)->name('admin.expenses')->middleware('role:admin');
    Route::get('/admin/reminders', \App\Livewire\Admin\Reminders::class)->name('admin.reminders')->middleware('role:admin');
    Route::get('/admin/daily-purchases-requests', \App\Livewire\Admin\DailyPurchasesBoard::class)->name('admin.daily_purchases_requests')->middleware('role:admin|daily_purchases_staff');
    Route::get('/admin/kitchens', \App\Livewire\Admin\KitchensManager::class)->name('admin.kitchens')->middleware('role:admin');
    Route::get('/kitchen/{slug}', \App\Livewire\DynamicKitchenBoard::class)->name('dynamic.kitchen')->middleware('role:admin|dynamic_kitchen_staff');
    Route::get('/admin/customers', \App\Livewire\Admin\CustomersManager::class)->name('admin.customers')->middleware('role:admin|reception_staff');
    Route::get('/admin/artists', \App\Livewire\Admin\ArtistsManager::class)->name('admin.artists')->middleware('role:admin');
    Route::get('/admin/bookings', \App\Livewire\ReceptionCustomerRequests::class)->name('admin.bookings')->middleware('role:admin');
    Route::get('/admin/loyalty/offers', \App\Livewire\Admin\Loyalty\Offers::class)->name('admin.loyalty.offers')->middleware('role:admin');
    Route::get('/admin/loyalty/customers', \App\Livewire\Admin\Loyalty\Customers::class)->name('admin.loyalty.customers')->middleware('role:admin|reception_staff');
    Route::get('/admin/subscription-packages', \App\Livewire\AdminSubscriptionPackages::class)->name('admin.subscription_packages')->middleware('role:admin');
    Route::get('/admin/subscribed-customers', \App\Livewire\Admin\SubscribedCustomers::class)->name('admin.subscribed_customers')->middleware('role:admin');
    Route::get('/invoice/print/{order}', [\App\Http\Controllers\InvoiceController::class, 'print'])->name('invoice.print')->middleware('role:admin|order_staff|kitchen_staff|barista_staff|restaurant_staff|delivery_staff|dynamic_kitchen_staff');
    Route::get('/invoice/print-batch', [\App\Http\Controllers\InvoiceController::class, 'printBatch'])->name('invoice.printBatch')->middleware('role:admin|order_staff|kitchen_staff|barista_staff|restaurant_staff|delivery_staff|dynamic_kitchen_staff');
    Route::get('/invoice/kitchen-print/{order}', [\App\Http\Controllers\InvoiceController::class, 'kitchenPrint'])->name('invoice.kitchenPrint')->middleware('role:admin|order_staff|kitchen_staff|barista_staff|restaurant_staff|delivery_staff|dynamic_kitchen_staff');
    Route::get('/invoice/kitchen-print-batch', [\App\Http\Controllers\InvoiceController::class, 'kitchenPrintBatch'])->name('invoice.kitchenPrintBatch')->middleware('role:admin|order_staff|kitchen_staff|barista_staff|restaurant_staff|delivery_staff|dynamic_kitchen_staff');
    Route::get('/invoice/inventory-print', [\App\Http\Controllers\InvoiceController::class, 'inventoryPrint'])->name('invoice.inventoryPrint')->middleware('role:admin|inventory_staff|kitchen_staff|reception_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/inventory-print-thermal', [\App\Http\Controllers\InvoiceController::class, 'inventoryPrintThermal'])->name('invoice.inventoryPrintThermal')->middleware('role:admin|inventory_staff|kitchen_staff|reception_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/manager-approved-print-thermal', [\App\Http\Controllers\InvoiceController::class, 'managerApprovedPrintThermal'])->name('invoice.managerApprovedPrintThermal')->middleware('role:admin|kitchen_staff|inventory_staff|reception_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/manager-approved-print', [\App\Http\Controllers\InvoiceController::class, 'managerApprovedPrint'])->name('invoice.managerApprovedPrint')->middleware('role:admin|kitchen_staff|inventory_staff|reception_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/waste-print-thermal', [\App\Http\Controllers\InvoiceController::class, 'wastePrintThermal'])->name('invoice.wastePrintThermal')->middleware('role:admin|inventory_staff|reception_staff|kitchen_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/waste-print', [\App\Http\Controllers\InvoiceController::class, 'wastePrint'])->name('invoice.wastePrint')->middleware('role:admin|inventory_staff|reception_staff|kitchen_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/category-print-thermal', [\App\Http\Controllers\InvoiceController::class, 'categoryPrintThermal'])->name('invoice.categoryPrintThermal')->middleware('role:admin|inventory_staff|kitchen_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/category-print', [\App\Http\Controllers\InvoiceController::class, 'categoryPrint'])->name('invoice.categoryPrint')->middleware('role:admin|inventory_staff|kitchen_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/ticket-report-print-thermal', [\App\Http\Controllers\InvoiceController::class, 'ticketReportPrintThermal'])->name('invoice.ticketReportPrintThermal')->middleware('role:admin|reception_staff|kitchen_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/ticket-report-print', [\App\Http\Controllers\InvoiceController::class, 'ticketReportPrint'])->name('invoice.ticketReportPrint')->middleware('role:admin|reception_staff|kitchen_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/kitchen-report-print', [\App\Http\Controllers\InvoiceController::class, 'kitchenReportPrint'])->name('invoice.kitchenReportPrint')->middleware('role:admin|kitchen_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/kitchen-report-print-thermal', [\App\Http\Controllers\InvoiceController::class, 'kitchenReportPrintThermal'])->name('invoice.kitchenReportPrintThermal')->middleware('role:admin|kitchen_staff|barista_staff|restaurant_staff|dynamic_kitchen_staff');
    Route::get('/invoice/supplier/{id}/print', [\App\Http\Controllers\InvoiceController::class, 'supplierPrint'])->name('invoice.supplierPrint')->middleware('role:admin');
    Route::get('/invoice/supplier/{id}/print-thermal', [\App\Http\Controllers\InvoiceController::class, 'supplierPrintThermal'])->name('invoice.supplierPrintThermal')->middleware('role:admin');
    Route::get('/invoice/purchase/{id}/print', [\App\Http\Controllers\InvoiceController::class, 'purchasePrint'])->name('invoice.purchasePrint')->middleware('role:admin|daily_purchases_staff');
    Route::get('/invoice/expenses-report', [\App\Http\Controllers\InvoiceController::class, 'expensesReport'])->name('admin.expenses.print')->middleware('role:admin');
    Route::get('/invoice/daily-purchases-report', [\App\Http\Controllers\InvoiceController::class, 'dailyPurchasesReport'])->name('admin.daily_purchases.print')->middleware('role:admin|daily_purchases_staff');
    Route::get('/invoice/physical-inventory/{id}/print', [\App\Http\Controllers\InvoiceController::class, 'physicalInventoryPrint'])->name('invoice.physicalInventoryPrint')->middleware('role:admin|inventory_staff');
    Route::get('/ticket/{ticket}/print', [\App\Http\Controllers\TicketController::class, 'print'])->name('ticket.print')->middleware('role:admin|reception_staff');
    Route::get('/order', OrderStaffBoard::class)->name('order')->middleware('role:order_staff|admin');
    Route::get('/kitchen', KitchenBoard::class)->name('kitchen')->middleware('role:kitchen_staff|admin');
    Route::get('/barista', BaristaBoard::class)->name('barista')->middleware('role:barista_staff|admin');
    Route::get('/restaurant', RestaurantBoard::class)->name('restaurant')->middleware('role:restaurant_staff|admin');
    Route::get('/delivery', DeliveryBoard::class)->name('delivery')->middleware('role:delivery_staff|admin');
    Route::get('/reception', ReceptionBoard::class)->name('reception')->middleware('role:reception_staff|admin');
    
    // Greeting System Routes
    Route::get('/greetings', \App\Livewire\GreetingStaffBoard::class)->name('greetings')->middleware('role:greeting_staff|admin');
    Route::get('/greetings/reports', \App\Livewire\GreetingStaffReports::class)->name('greetings.reports')->middleware('role:greeting_staff|admin');
    Route::get('/admin/greetings', \App\Livewire\AdminGreetings::class)->name('admin.greetings')->middleware('role:admin');
    Route::get('/admin/greetings/reports', \App\Livewire\AdminGreetingReports::class)->name('admin.greetings.reports')->middleware('role:admin');
    Route::get('/reception/greetings', \App\Livewire\ReceptionGreetingBoard::class)->name('reception.greetings')->middleware('role:reception_staff|admin');
    Route::get('/reception/greetings/reports', \App\Livewire\ReceptionGreetingReports::class)->name('reception.greetings.reports')->middleware('role:reception_staff|admin');
    Route::get('/reception/loyalty', \App\Livewire\ReceptionLoyaltyBoard::class)->name('reception.loyalty')->middleware('role:reception_staff|admin');
    Route::get('/reception/customer-requests', \App\Livewire\ReceptionCustomerRequests::class)->name('reception.customer-requests')->middleware('role:reception_staff|admin');
    
    Route::get('/invoice/greetings-report-print', [\App\Http\Controllers\InvoiceController::class, 'greetingsReportPrint'])->name('invoice.greetingsReportPrint')->middleware('role:admin|greeting_staff|reception_staff');
    Route::get('/invoice/greetings-report-print-thermal', [\App\Http\Controllers\InvoiceController::class, 'greetingsReportPrintThermal'])->name('invoice.greetingsReportPrintThermal')->middleware('role:admin|greeting_staff|reception_staff');
});

// Print Agent API Routes (Using GET/POST without CSRF for local agent script)
Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::get('/api/print-jobs', [\App\Http\Controllers\PrintApiController::class, 'getPendingJobs']);
    Route::get('/api/print-jobs/{id}/complete', [\App\Http\Controllers\PrintApiController::class, 'markAsPrinted']);
    Route::post('/api/printers/status', [\App\Http\Controllers\PrintApiController::class, 'updateStatus']);
});

