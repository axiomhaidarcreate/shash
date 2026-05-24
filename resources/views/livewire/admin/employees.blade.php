<div>
    <div class="page-header">
        <h1 style="font-size: clamp(1.2rem, 4vw, 1.6rem);"><i class="fa-solid fa-users-gear" style="color: var(--primary-color);"></i> إدارة الموظفين</h1>
        <p>إضافة وتعديل وحذف حسابات الموظفين وصلاحياتهم.</p>
    </div>

    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div style="background: rgba(244,67,54,0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Add Employee Form -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3><i class="fa-solid fa-user-plus" style="color: var(--primary-color);"></i> إضافة موظف جديد</h3>
            <button wire:click="$toggle('showForm')" class="btn btn-sm btn-outline">
                <i class="fa-solid fa-{{ $showForm ? 'times' : 'plus' }}"></i>
                {{ $showForm ? 'إلغاء' : 'إضافة' }}
            </button>
        </div>

        @if($showForm)
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: flex-end; animation: slideDown 0.3s ease;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الاسم</label>
                <input type="text" wire:model="name" class="form-control" placeholder="اسم الموظف">
                @error('name') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" wire:model="email" class="form-control" dir="ltr" placeholder="email@example.com">
                @error('email') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">كلمة المرور</label>
                <input type="password" wire:model="password" class="form-control" dir="ltr" placeholder="******">
                @error('password') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الصلاحية</label>
                <select wire:model="role" class="form-control">
                    <option value="order_staff">موظف طلبات</option>
                    <option value="kitchen_staff">موظف مطبخ</option>
                    <option value="delivery_staff">موظف توصيل</option>
                    <option value="admin">مدير</option>
                </select>
            </div>
            <button wire:click="save" class="btn" style="height: 42px;">
                <i class="fa-solid fa-save"></i> حفظ
            </button>
        </div>
        @endif
    </div>

    <!-- Employees Table -->
    <div class="card">
        <h3 style="margin-bottom: 1rem;"><i class="fa-solid fa-list-ul" style="color: var(--primary-color);"></i> جميع الموظفين <span class="badge badge-active">{{ $employees->count() }}</span></h3>

        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; margin: 0 -0.5rem; padding: 0 0.5rem;">
            <table class="data-table" style="min-width: 520px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الصلاحية</th>
                        <th style="width: 200px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                    <tr wire:key="employee-{{ $emp->id }}">
                        <td style="color: var(--text-secondary);">{{ $emp->id }}</td>
                        <td>
                            @if($editingId === $emp->id)
                                <input type="text" wire:model="editName" class="form-control" style="padding: 0.35rem 0.6rem; font-size: 0.9rem;">
                            @else
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), #c9a020); display: flex; align-items: center; justify-content: center; color: #000; font-weight: 800; font-size: 0.8rem;">
                                        {{ mb_substr($emp->name, 0, 1) }}
                                    </div>
                                    <span style="font-weight: 600; white-space: nowrap;">{{ $emp->name }}</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($editingId === $emp->id)
                                <input type="email" wire:model="editEmail" class="form-control" dir="ltr" style="padding: 0.35rem 0.6rem; font-size: 0.9rem;">
                            @else
                                <span style="color: var(--text-secondary); direction: ltr; display: inline-block;">{{ $emp->email }}</span>
                            @endif
                        </td>
                        <td>
                            @if($editingId === $emp->id)
                                <select wire:model="editRole" class="form-control" style="padding: 0.35rem 0.6rem; font-size: 0.9rem;">
                                    <option value="order_staff">موظف طلبات</option>
                                    <option value="kitchen_staff">موظف مطبخ</option>
                                    <option value="delivery_staff">موظف توصيل</option>
                                    <option value="admin">مدير</option>
                                </select>
                            @else
                                @php
                                    $roleName = $emp->roles->first()?->name ?? 'بدون';
                                    $roleLabel = match($roleName) {
                                        'admin' => 'مدير',
                                        'order_staff' => 'موظف طلبات',
                                        'kitchen_staff' => 'موظف مطبخ',
                                        'delivery_staff' => 'موظف توصيل',
                                        default => $roleName,
                                    };
                                    $roleColor = match($roleName) {
                                        'admin' => 'var(--primary-color)',
                                        'order_staff' => 'var(--info-color)',
                                        'kitchen_staff' => 'var(--warning-color)',
                                        'delivery_staff' => 'var(--success-color)',
                                        default => 'var(--text-secondary)',
                                    };
                                @endphp
                                <span style="color: {{ $roleColor }}; font-weight: 600; white-space: nowrap;">
                                    <i class="fa-solid fa-shield-halved" style="font-size: 0.8rem;"></i> {{ $roleLabel }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($editingId === $emp->id)
                                <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 6px;">
                                    <input type="password" wire:model="editPassword" class="form-control" dir="ltr" placeholder="كلمة مرور جديدة (اختياري)" style="padding: 0.35rem 0.6rem; font-size: 0.8rem; width: 100%;">
                                </div>
                                <div style="display: flex; gap: 6px; white-space: nowrap;">
                                    <button wire:click="updateEmployee" class="btn btn-sm btn-success" style="padding: 0.4rem 0.8rem;" title="حفظ">
                                        <i class="fa-solid fa-check"></i> حفظ
                                    </button>
                                    <button wire:click="$set('editingId', null)" class="btn btn-sm" style="background: #444; color: #fff; border: none; padding: 0.4rem 0.8rem;" title="إلغاء">
                                        <i class="fa-solid fa-xmark"></i> إلغاء
                                    </button>
                                </div>
                            @else
                                <div style="display: flex; gap: 6px; white-space: nowrap;">
                                    <button wire:click="startEdit({{ $emp->id }})" class="btn btn-sm" title="تعديل" style="background: #2196F3; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                        <i class="fa-solid fa-pen"></i> تعديل
                                    </button>
                                    @if($emp->id !== auth()->id())
                                        <button wire:click="deleteEmployee({{ $emp->id }})" class="btn btn-sm" title="حذف" style="background: #F44336; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من حذف هذا الموظف؟')">
                                            <i class="fa-solid fa-trash"></i> حذف
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    </style>
</div>
