<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], true)) {
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
            
            return redirect('/');
        }

        $this->addError('email', 'البيانات المدخلة غير صحيحة.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.app', ['title' => 'تسجيل الدخول']);
    }
}
