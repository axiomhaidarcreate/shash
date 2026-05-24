<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class Employees extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'order_staff';
    public $showForm = false;

    public $editingId = null;
    public $editName = '';
    public $editEmail = '';
    public $editRole = '';
    public $editPassword = '';

    public $availableRoles = ['admin', 'order_staff', 'kitchen_staff', 'delivery_staff'];

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,order_staff,kitchen_staff,delivery_staff',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->assignRole($this->role);

        $this->reset(['name', 'email', 'password', 'showForm']);
        $this->role = 'order_staff';
        session()->flash('success', 'تم إضافة الموظف بنجاح!');
    }

    public function startEdit($id)
    {
        $user = User::find($id);
        $this->editingId = $id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editRole = $user->roles->first()?->name ?? 'order_staff';
        $this->editPassword = '';
    }

    public function updateEmployee()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $this->editingId,
            'editRole' => 'required|in:admin,order_staff,kitchen_staff,delivery_staff',
        ]);

        $user = User::find($this->editingId);
        $user->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
        ]);

        if ($this->editPassword) {
            $user->update(['password' => Hash::make($this->editPassword)]);
        }

        $user->syncRoles([$this->editRole]);

        $this->editingId = null;
        session()->flash('success', 'تم تحديث بيانات الموظف بنجاح!');
    }

    public function deleteEmployee($id)
    {
        if ($id == auth()->id()) {
            session()->flash('error', 'لا يمكنك حذف حسابك الخاص!');
            return;
        }
        User::destroy($id);
        session()->flash('success', 'تم حذف الموظف.');
    }

    public function render()
    {
        $employees = User::with('roles')->orderBy('name')->get();

        return view('livewire.admin.employees', [
            'employees' => $employees,
        ])->layout('components.layouts.admin');
    }
}
