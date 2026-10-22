<?php

namespace App\Livewire\Admin\Purchases;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.purchases.dashboard')->layout('components.layouts.admin', ['title' => 'إدارة المشتريات']);
    }
}
