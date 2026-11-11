<?php

namespace App\Livewire\Admin\Purchases;

use Livewire\Component;
use App\Models\DailyPurchaseRequest;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Expense;

class Dashboard extends Component
{
    public function render()
    {
        $pendingPurchasesCount = DailyPurchaseRequest::where('status', 'معلق')->count();
        $invoicesCount = PurchaseInvoice::count();
        $suppliersCount = Supplier::count();
        $productsCount = Product::count();
        $expensesCount = Expense::count();

        return view('livewire.admin.purchases.dashboard', [
            'pendingPurchasesCount' => $pendingPurchasesCount,
            'invoicesCount' => $invoicesCount,
            'suppliersCount' => $suppliersCount,
            'productsCount' => $productsCount,
            'expensesCount' => $expensesCount,
        ])->layout('components.layouts.admin', ['title' => 'بوابة المشتريات والمخزون']);
    }
}
