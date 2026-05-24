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

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = Auth::user();
            
            if ($user->hasRole('admin')) {
                return redirect()->route('admin');
            } elseif ($user->hasRole('order_staff')) {
                return redirect()->route('order');
            } elseif ($user->hasRole('kitchen_staff')) {
                return redirect()->route('kitchen');
            } elseif ($user->hasRole('delivery_staff')) {
                return redirect()->route('delivery');
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
