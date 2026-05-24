<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\AdminDashboard;
use App\Livewire\OrderStaffBoard;
use App\Livewire\KitchenBoard;
use App\Livewire\DeliveryBoard;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('admin')) return redirect()->route('admin');
        if ($user->hasRole('order_staff')) return redirect()->route('order');
        if ($user->hasRole('kitchen_staff')) return redirect()->route('kitchen');
        if ($user->hasRole('delivery_staff')) return redirect()->route('delivery');
        if ($user->hasRole('inventory_staff')) return redirect()->route('admin.inventory');
    }
    return redirect()->route('login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/admin', AdminDashboard::class)->name('admin')->middleware('role:admin');
    Route::get('/admin/products', \App\Livewire\Admin\Products::class)->name('admin.products')->middleware('role:admin');
    Route::get('/admin/employees', \App\Livewire\Admin\Employees::class)->name('admin.employees')->middleware('role:admin');
    Route::get('/admin/settings', \App\Livewire\Admin\Settings::class)->name('admin.settings')->middleware('role:admin');
    Route::get('/admin/inventory', \App\Livewire\Admin\Inventory::class)->name('admin.inventory')->middleware('role:admin|inventory_staff');
    Route::get('/invoice/print/{order}', [\App\Http\Controllers\InvoiceController::class, 'print'])->name('invoice.print')->middleware('role:admin|order_staff|kitchen_staff|delivery_staff');
    Route::get('/invoice/print-batch', [\App\Http\Controllers\InvoiceController::class, 'printBatch'])->name('invoice.printBatch')->middleware('role:admin|order_staff|kitchen_staff|delivery_staff');
    Route::get('/invoice/kitchen-print/{order}', [\App\Http\Controllers\InvoiceController::class, 'kitchenPrint'])->name('invoice.kitchenPrint')->middleware('role:admin|order_staff|kitchen_staff|delivery_staff');
    Route::get('/invoice/kitchen-print-batch', [\App\Http\Controllers\InvoiceController::class, 'kitchenPrintBatch'])->name('invoice.kitchenPrintBatch')->middleware('role:admin|order_staff|kitchen_staff|delivery_staff');
    Route::get('/invoice/inventory-print', [\App\Http\Controllers\InvoiceController::class, 'inventoryPrint'])->name('invoice.inventoryPrint')->middleware('role:admin|inventory_staff');
    Route::get('/invoice/inventory-print-thermal', [\App\Http\Controllers\InvoiceController::class, 'inventoryPrintThermal'])->name('invoice.inventoryPrintThermal')->middleware('role:admin|inventory_staff');
    Route::get('/order', OrderStaffBoard::class)->name('order')->middleware('role:order_staff|admin');
    Route::get('/kitchen', KitchenBoard::class)->name('kitchen')->middleware('role:kitchen_staff|admin');
    Route::get('/delivery', DeliveryBoard::class)->name('delivery')->middleware('role:delivery_staff|admin');
});

// Print Agent API Routes (Using GET/POST without CSRF for local agent script)
Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::get('/api/print-jobs', [\App\Http\Controllers\PrintApiController::class, 'getPendingJobs']);
    Route::get('/api/print-jobs/{id}/complete', [\App\Http\Controllers\PrintApiController::class, 'markAsPrinted']);
    Route::post('/api/printers/status', [\App\Http\Controllers\PrintApiController::class, 'updateStatus']);
});
