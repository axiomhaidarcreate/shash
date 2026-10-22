<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Kitchen;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class KitchensManager extends Component
{
    // Kitchen form
    public $kitchenName = '';
    public $kitchenSlug = '';
    public $showKitchenForm = false;
    public $editingKitchenId = null;

    // User form
    public $showUserForm = false;
    public $selectedKitchenSlug = '';
    public $selectedKitchenName = '';
    public $userName = '';
    public $userEmail = '';
    public $userPassword = '';

    public $showConfirmDelete = null;

    protected $rules = [
        'kitchenName' => 'required|string|min:2|max:100',
        'kitchenSlug' => 'required|string|min:2|max:50|alpha_dash',
    ];

    // ─── Kitchen CRUD ────────────────────────────────────────────────────────

    public function openKitchenForm($id = null)
    {
        $this->resetKitchenForm();
        if ($id) {
            $k = Kitchen::findOrFail($id);
            $this->editingKitchenId = $k->id;
            $this->kitchenName = $k->name;
            $this->kitchenSlug = $k->slug;
        }
        $this->showKitchenForm = true;
    }

    public function updatedKitchenName($value)
    {
        if (!$this->editingKitchenId) {
            $this->kitchenSlug = Str::slug($value, '_');
        }
    }

    public function saveKitchen()
    {
        $this->validate([
            'kitchenName' => 'required|string|min:2|max:100',
            'kitchenSlug' => 'required|string|min:2|max:50|regex:/^[a-z0-9_]+$/'
                . ($this->editingKitchenId ? '|unique:kitchens,slug,' . $this->editingKitchenId : '|unique:kitchens,slug'),
        ], [
            'kitchenSlug.unique' => 'هذا المعرف مستخدم مسبقاً، اختر معرفاً آخر.',
            'kitchenSlug.regex'  => 'المعرف يجب أن يحتوي على أحرف إنجليزية صغيرة وأرقام وشرطات سفلية فقط.',
        ]);

        if ($this->editingKitchenId) {
            Kitchen::where('id', $this->editingKitchenId)->update([
                'name' => $this->kitchenName,
                'slug' => $this->kitchenSlug,
            ]);
            session()->flash('success', 'تم تحديث المطبخ بنجاح.');
        } else {
            Kitchen::create([
                'name'      => $this->kitchenName,
                'slug'      => $this->kitchenSlug,
                'is_active' => true,
            ]);
            session()->flash('success', 'تم إضافة المطبخ الجديد بنجاح!');
        }
        $this->resetKitchenForm();
    }

    public function confirmDelete($id) { $this->showConfirmDelete = $id; }
    public function cancelDelete()    { $this->showConfirmDelete = null; }

    public function deleteKitchen($id)
    {
        Kitchen::destroy($id);
        $this->showConfirmDelete = null;
        session()->flash('success', 'تم حذف المطبخ.');
    }

    public function resetKitchenForm()
    {
        $this->kitchenName = '';
        $this->kitchenSlug = '';
        $this->editingKitchenId = null;
        $this->showKitchenForm = false;
        $this->resetErrorBag();
    }

    // ─── User/Staff CRUD ─────────────────────────────────────────────────────

    public function openUserForm(string $slug, string $name)
    {
        $this->selectedKitchenSlug = $slug;
        $this->selectedKitchenName = $name;
        $this->userName = '';
        $this->userEmail = '';
        $this->userPassword = '';
        $this->showUserForm = true;
        $this->resetErrorBag();
    }

    public function saveUser()
    {
        $this->validate([
            'userName'     => 'required|string|min:2',
            'userEmail'    => 'required|email|unique:users,email',
            'userPassword' => 'required|string|min:6',
        ], [
            'userEmail.unique' => 'هذا البريد الإلكتروني مستخدم مسبقاً.',
        ]);

        $user = User::create([
            'name'          => $this->userName,
            'email'         => $this->userEmail,
            'password'      => Hash::make($this->userPassword),
            'kitchen_slug'  => $this->selectedKitchenSlug,
        ]);

        // Assign role (create if not exists)
        $role = Role::firstOrCreate(['name' => 'dynamic_kitchen_staff', 'guard_name' => 'web']);
        $user->assignRole($role);

        session()->flash('success', "تم إنشاء حساب موظف المطبخ '{$this->selectedKitchenName}' بنجاح.");
        $this->showUserForm = false;
    }

    public function removeUserFromKitchen($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->kitchen_slug = null;
            $user->save();
            $user->removeRole('dynamic_kitchen_staff');
        }
        session()->flash('success', 'تم إزالة الموظف من المطبخ.');
    }

    public function render()
    {
        $kitchens = Kitchen::withCount('users')->orderBy('created_at')->get();
        $kitchenUsers = [];
        foreach ($kitchens as $k) {
            $kitchenUsers[$k->slug] = User::where('kitchen_slug', $k->slug)->get();
        }
        return view('livewire.admin.kitchens-manager', compact('kitchens', 'kitchenUsers'))
            ->layout('components.layouts.app');
    }
}
