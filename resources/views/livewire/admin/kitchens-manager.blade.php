<div>
    <livewire:shift-status-banner />
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; margin-top: 1rem; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="margin: 0; font-size: clamp(1.2rem, 4vw, 1.6rem);"><i class="fa-solid fa-kitchen-set" style="color: var(--primary-color);"></i> إدارة المطابخ</h2>
            <p style="margin: 4px 0 0; color: var(--text-secondary); font-size: 0.9rem;">إنشاء مطابخ جديدة وإدارة موظفيها وصلاحياتهم</p>
        </div>
        <button wire:click="openKitchenForm()" class="btn btn-success" style="padding: 0.7rem 1.5rem; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> إضافة مطبخ جديد
        </button>
    </div>

    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.12); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Kitchen Form Modal --}}
    @if($showKitchenForm)
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; justify-content: center; align-items: center; padding: 20px; backdrop-filter: blur(5px);">
        <div style="background: var(--surface-color); width: 100%; max-width: 500px; border-radius: var(--radius-lg); padding: 2rem; box-shadow: 0 10px 40px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); animation: slideIn 0.3s ease;">
            <h3 style="margin: 0 0 1.5rem; color: var(--primary-color);">
                <i class="fa-solid fa-{{ $editingKitchenId ? 'pen' : 'plus' }}"></i>
                {{ $editingKitchenId ? 'تعديل المطبخ' : 'إضافة مطبخ جديد' }}
            </h3>

            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label class="form-label"><i class="fa-solid fa-font"></i> اسم المطبخ <span style="color: var(--danger-color);">*</span></label>
                <input type="text" wire:model.live="kitchenName" class="form-control" placeholder="مثال: مطبخ الحلويات">
                @error('kitchenName') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label"><i class="fa-solid fa-link"></i> المعرف الفريد (بالإنجليزي) <span style="color: var(--danger-color);">*</span></label>
                <input type="text" wire:model="kitchenSlug" class="form-control" placeholder="مثال: dessert" dir="ltr"
                    {{ $editingKitchenId && in_array($kitchenSlug, ['shisha','barista','restaurant']) ? 'readonly' : '' }}>
                <small style="color: var(--text-secondary);">يُستخدم في الروابط. أحرف إنجليزية صغيرة وأرقام وشرطات سفلية فقط.</small>
                @error('kitchenSlug') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button wire:click="resetKitchenForm" class="btn btn-outline" style="padding: 0.6rem 1.5rem;">إلغاء</button>
                <button wire:click="saveKitchen" class="btn btn-success" style="padding: 0.6rem 1.5rem;"><i class="fa-solid fa-save"></i> حفظ</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Add User Form Modal --}}
    @if($showUserForm)
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; justify-content: center; align-items: center; padding: 20px; backdrop-filter: blur(5px);">
        <div style="background: var(--surface-color); width: 100%; max-width: 500px; border-radius: var(--radius-lg); padding: 2rem; box-shadow: 0 10px 40px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); animation: slideIn 0.3s ease;">
            <h3 style="margin: 0 0 0.3rem; color: var(--primary-color);"><i class="fa-solid fa-user-plus"></i> إضافة موظف لـ {{ $selectedKitchenName }}</h3>
            <p style="color: var(--text-secondary); font-size: 0.88rem; margin: 0 0 1.5rem;">سيتم إنشاء حساب دخول للموظف وتخصيصه لهذا المطبخ تلقائياً.</p>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label"><i class="fa-solid fa-user"></i> اسم الموظف <span style="color: var(--danger-color);">*</span></label>
                <input type="text" wire:model="userName" class="form-control" placeholder="الاسم الكامل">
                @error('userName') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label"><i class="fa-solid fa-envelope"></i> البريد الإلكتروني <span style="color: var(--danger-color);">*</span></label>
                <input type="email" wire:model="userEmail" class="form-control" placeholder="example@email.com" dir="ltr">
                @error('userEmail') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label"><i class="fa-solid fa-lock"></i> كلمة المرور <span style="color: var(--danger-color);">*</span></label>
                <input type="password" wire:model="userPassword" class="form-control" placeholder="6 أحرف على الأقل" dir="ltr">
                @error('userPassword') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button wire:click="$set('showUserForm', false)" class="btn btn-outline" style="padding: 0.6rem 1.5rem;">إلغاء</button>
                <button wire:click="saveUser" class="btn btn-success" style="padding: 0.6rem 1.5rem;"><i class="fa-solid fa-user-plus"></i> إنشاء الحساب</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Kitchens Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
        @foreach($kitchens as $kitchen)
        <div class="card" style="border-top: 4px solid var(--primary-color); position: relative;">
            {{-- Kitchen Header --}}
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.2rem; color: var(--text-primary);">
                        <i class="fa-solid fa-fire" style="color: var(--warning-color);"></i> {{ $kitchen->name }}
                    </h3>
                    <span style="font-family: monospace; font-size: 0.8rem; color: var(--text-secondary); background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 4px; display: inline-block; margin-top: 4px;">/kitchen/{{ $kitchen->slug }}</span>
                </div>
                <div style="display: flex; gap: 6px;">
                    <button wire:click="openKitchenForm({{ $kitchen->id }})" class="btn btn-outline" style="padding: 0.3rem 0.7rem; font-size: 0.8rem;" title="تعديل"><i class="fa-solid fa-pen"></i></button>
                    @if(!in_array($kitchen->slug, ['shisha','barista','restaurant']))
                        @if($showConfirmDelete === $kitchen->id)
                            <button wire:click="deleteKitchen({{ $kitchen->id }})" class="btn btn-danger" style="padding: 0.3rem 0.7rem; font-size: 0.8rem;">تأكيد الحذف</button>
                            <button wire:click="cancelDelete" class="btn btn-outline" style="padding: 0.3rem 0.7rem; font-size: 0.8rem;">إلغاء</button>
                        @else
                            <button wire:click="confirmDelete({{ $kitchen->id }})" class="btn btn-outline" style="padding: 0.3rem 0.7rem; font-size: 0.8rem; border-color: var(--danger-color); color: var(--danger-color);" title="حذف"><i class="fa-solid fa-trash"></i></button>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Staff List --}}
            <div style="margin-bottom: 1rem;">
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 600;"><i class="fa-solid fa-users"></i> موظفو المطبخ</div>
                @if(isset($kitchenUsers[$kitchen->slug]) && $kitchenUsers[$kitchen->slug]->count() > 0)
                    @foreach($kitchenUsers[$kitchen->slug] as $user)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 10px; background: rgba(255,255,255,0.03); border-radius: 6px; margin-bottom: 4px; border: 1px solid var(--border-color);">
                            <div>
                                <span style="font-size: 0.9rem; font-weight: 600;">{{ $user->name }}</span>
                                <br><span style="font-size: 0.78rem; color: var(--text-secondary);">{{ $user->email }}</span>
                            </div>
                            <button wire:click="removeUserFromKitchen({{ $user->id }})" onclick="return confirm('هل تريد إزالة هذا الموظف من المطبخ؟')" class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-color: var(--danger-color); color: var(--danger-color);" title="إزالة"><i class="fa-solid fa-user-xmark"></i></button>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 0.8rem; color: var(--text-secondary); font-size: 0.85rem; background: rgba(255,255,255,0.02); border-radius: 6px; border: 1px dashed var(--border-color);">
                        لا يوجد موظفون مخصصون لهذا المطبخ
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button wire:click="openUserForm('{{ $kitchen->slug }}', '{{ $kitchen->name }}')" class="btn" style="flex: 1; background: linear-gradient(135deg, #43A047, #2E7D32); color: #fff; border: none; padding: 0.5rem; font-size: 0.88rem;">
                    <i class="fa-solid fa-user-plus"></i> إضافة موظف
                </button>
                <a href="/kitchen/{{ $kitchen->slug }}" target="_blank" class="btn btn-outline" style="padding: 0.5rem 0.8rem; font-size: 0.88rem;" title="فتح شاشة المطبخ">
                    <i class="fa-solid fa-external-link-alt"></i> فتح الشاشة
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <style>
        @keyframes slideIn { from { opacity:0; transform:scale(0.95); } to { opacity:1; transform:scale(1); } }
    </style>
</div>
