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
    public $assignedKitchens = [];
    public $showForm = false;

    public $editingId = null;
    public $editName = '';
    public $editEmail = '';
    public $editRole = '';
    public $editPassword = '';
    public $editAssignedKitchens = [];

    public $availableRoles = ['admin', 'order_staff', 'kitchen_staff', 'barista_staff', 'restaurant_staff', 'delivery_staff', 'inventory_staff', 'reception_staff', 'greeting_staff', 'dynamic_kitchen_staff'];

    public $kitchenSlug = '';
    public $editKitchenSlug = '';

    public function save()
    {
        $isDynamic = $this->role === 'dynamic_kitchen_staff';

        $this->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'role'         => 'required',
            'kitchenSlug'  => $isDynamic ? 'required|exists:kitchens,slug' : 'nullable',
            'assignedKitchens' => 'nullable|array',
        ], [
            'kitchenSlug.required' => 'يجب اختيار المطبخ عند اختيار صلاحية موظف مطبخ ديناميكي.',
        ]);

        $user = User::create([
            'name'          => $this->name,
            'email'         => $this->email,
            'password'      => Hash::make($this->password),
            'assigned_kitchens' => $this->role === 'delivery_staff' ? $this->assignedKitchens : null,
            'kitchen_slug'  => $isDynamic ? $this->kitchenSlug : null,
        ]);

        $role = Role::firstOrCreate(['name' => $this->role, 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->reset(['name', 'email', 'password', 'showForm', 'assignedKitchens', 'kitchenSlug']);
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
        $this->editAssignedKitchens = $user->assigned_kitchens ?? [];
        $this->editKitchenSlug = $user->kitchen_slug ?? '';
    }

    public function updateEmployee()
    {
        $isDynamic = $this->editRole === 'dynamic_kitchen_staff';

        $this->validate([
            'editName'         => 'required|string|max:255',
            'editEmail'        => 'required|email|unique:users,email,' . $this->editingId,
            'editRole'         => 'required',
            'editAssignedKitchens' => 'nullable|array',
            'editKitchenSlug'  => $isDynamic ? 'required|exists:kitchens,slug' : 'nullable',
        ]);

        $user = User::find($this->editingId);
        $user->update([
            'name'          => $this->editName,
            'email'         => $this->editEmail,
            'assigned_kitchens' => $this->editRole === 'delivery_staff' ? $this->editAssignedKitchens : null,
            'kitchen_slug'  => $isDynamic ? $this->editKitchenSlug : null,
        ]);

        if ($this->editPassword) {
            $user->update(['password' => Hash::make($this->editPassword)]);
        }

        $role = Role::firstOrCreate(['name' => $this->editRole, 'guard_name' => 'web']);
        $user->syncRoles([$role]);

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
