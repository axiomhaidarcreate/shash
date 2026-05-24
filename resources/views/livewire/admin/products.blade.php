<div>
    <div class="page-header">
        <h1 style="font-size: clamp(1.2rem, 4vw, 1.6rem);"><i class="fa-solid fa-box-open" style="color: var(--primary-color);"></i> إدارة المنتجات والتصنيفات</h1>
        <p>إضافة وتعديل نكهات المعسل والمنتجات، وإدارة تصنيفات القائمة وإظهارها أو إخفائها.</p>
    </div>

    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600; animation: slideDown 0.3s ease;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Grid for category management and products --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
        
        <!-- Add Product Form & Category management -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Add Product Form -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="margin: 0;"><i class="fa-solid fa-plus-circle" style="color: var(--primary-color);"></i> إضافة منتج جديد</h3>
                    <button wire:click="$toggle('showForm')" class="btn btn-sm btn-outline">
                        <i class="fa-solid fa-{{ $showForm ? 'times' : 'plus' }}"></i>
                        {{ $showForm ? 'إلغاء' : 'إضافة منتج' }}
                    </button>
                </div>

                @if($showForm)
                <div style="display: flex; flex-direction: column; gap: 1rem; animation: slideDown 0.3s ease;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">اسم المنتج</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="مثال: تفاحتين فاخر">
                        @error('name') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">التصنيف</label>
                        <select wire:model="category" class="form-control">
                            @foreach($dbCategories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button wire:click="save" class="btn" style="width: 100%;">
                        <i class="fa-solid fa-save"></i> حفظ المنتج
                    </button>
                </div>
                @endif
            </div>

            <!-- Categories Management Card -->
            <div class="card" style="border-top: 3px solid var(--primary-color);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="margin: 0;"><i class="fa-solid fa-tags" style="color: var(--primary-color);"></i> إدارة التصنيفات</h3>
                    <button wire:click="$toggle('showCategoryForm')" class="btn btn-sm btn-outline">
                        <i class="fa-solid fa-{{ $showCategoryForm ? 'times' : 'plus' }}"></i>
                        {{ $showCategoryForm ? 'إلغاء' : 'إضافة تصنيف' }}
                    </button>
                </div>

                <!-- Add Category Form -->
                @if($showCategoryForm)
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; animation: slideDown 0.3s ease;">
                    <input type="text" wire:model="newCategoryName" class="form-control" placeholder="تصنيف جديد..." style="flex: 1;">
                    <button wire:click="addCategory" class="btn" style="padding: 0 1rem; white-space: nowrap;">حفظ</button>
                </div>
                @error('newCategoryName') <div style="color: var(--danger-color); font-size: 0.8rem; margin-bottom: 1rem;">{{ $message }}</div> @enderror
                @endif

                <!-- Categories list -->
                <div style="overflow-x: auto;">
                    <table class="data-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>اسم التصنيف</th>
                                <th style="text-align: center;">الظهور بالطلب</th>
                                <th style="text-align: center; width: 100px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dbCategories as $cat)
                            <tr wire:key="cat-row-{{ $cat->id }}">
                                <td>
                                    @if($editingCategoryId === $cat->id)
                                        <input type="text" wire:model="editCategoryName" class="form-control" style="padding: 0.3rem 0.6rem; font-size: 0.9rem;">
                                    @else
                                        <span style="font-weight: bold; color: var(--primary-color);">{{ $cat->name }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <button wire:click="toggleCategoryVisibility({{ $cat->id }})" class="badge {{ $cat->is_visible ? 'badge-active' : 'badge-inactive' }}" style="cursor: pointer; border: none; background: {{ $cat->is_visible ? 'rgba(76,175,80,0.15)' : 'rgba(244,67,54,0.15)' }}; color: {{ $cat->is_visible ? 'var(--success-color)' : 'var(--danger-color)' }};">
                                        {{ $cat->is_visible ? 'ظاهر' : 'مخفي' }}
                                    </button>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 4px; justify-content: center;">
                                        @if($editingCategoryId === $cat->id)
                                            <button wire:click="updateCategory" class="btn btn-sm btn-success" style="padding: 3px 8px;" title="حفظ"><i class="fa-solid fa-check"></i></button>
                                            <button wire:click="$set('editingCategoryId', null)" class="btn btn-sm" style="padding: 3px 8px; background: #444;" title="إلغاء"><i class="fa-solid fa-xmark"></i></button>
                                        @else
                                            <button wire:click="startEditCategory({{ $cat->id }})" class="btn btn-sm" style="padding: 3px 8px; background: #2196F3; color: white;" title="تعديل"><i class="fa-solid fa-pen"></i></button>
                                            <button wire:click="deleteCategory({{ $cat->id }})" class="btn btn-sm btn-danger" style="padding: 3px 8px;" title="حذف" onclick="return confirm('حذف هذا التصنيف سيقوم بنقل جميع المنتجات المندرجة تحته إلى تصنيف آخر. هل أنت متأكد؟')"><i class="fa-solid fa-trash"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Products List grouped by category -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @php
                $grouped = $products->groupBy('category');
            @endphp

            @foreach($grouped as $catName => $items)
                <div class="card" style="margin-bottom: 0;">
                    <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                        @if($catName === 'معسل')
                            <i class="fa-solid fa-wind" style="color: var(--primary-color);"></i>
                        @elseif($catName === 'مشروبات')
                            <i class="fa-solid fa-mug-hot" style="color: var(--info-color);"></i>
                        @elseif($catName === 'مستلزمات')
                            <i class="fa-solid fa-screwdriver-wrench" style="color: var(--warning-color);"></i>
                        @else
                            <i class="fa-solid fa-cube" style="color: #9e9e9e;"></i>
                        @endif
                        {{ $catName }}
                        <span class="badge badge-active" style="font-size: 0.7rem;">{{ $items->count() }}</span>
                    </h3>

                    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; margin: 0 -0.5rem; padding: 0 0.5rem;">
                        <table class="data-table" style="min-width: 400px; width: 100%;">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>الحالة</th>
                                    <th style="width: 150px; text-align: center;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $product)
                                <tr wire:key="product-{{ $product->id }}">
                                    <td>
                                        @if($editingId === $product->id)
                                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                                <input type="text" wire:model="editName" class="form-control" style="padding: 0.4rem 0.6rem; font-size: 0.9rem; flex: 1; min-width: 120px;">
                                                <select wire:model="editCategory" class="form-control" style="padding: 0.4rem 0.6rem; font-size: 0.9rem; width: auto;">
                                                    @foreach($dbCategories as $cat)
                                                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @else
                                            <span style="font-weight: 600;">{{ $product->name }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="toggleActive({{ $product->id }})" class="badge {{ $product->is_active ? 'badge-active' : 'badge-inactive' }}" style="cursor: pointer; border: none; background: {{ $product->is_active ? 'rgba(76,175,80,0.15)' : 'rgba(158,158,158,0.15)' }};">
                                            {{ $product->is_active ? 'مفعّل' : 'معطّل' }}
                                        </button>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($editingId === $product->id)
                                            <div style="display: flex; gap: 6px; white-space: nowrap; justify-content: center;">
                                                <button wire:click="updateProduct" class="btn btn-sm btn-success" style="padding: 0.4rem 0.8rem;" title="حفظ">
                                                    <i class="fa-solid fa-check"></i> حفظ
                                                </button>
                                                <button wire:click="$set('editingId', null)" class="btn btn-sm" style="background: #444; color: #fff; border: none; padding: 0.4rem 0.8rem;" title="إلغاء">
                                                    <i class="fa-solid fa-xmark"></i> إلغاء
                                                </button>
                                            </div>
                                        @else
                                            <div style="display: flex; gap: 6px; white-space: nowrap; justify-content: center;">
                                                <button wire:click="startEdit({{ $product->id }})" class="btn btn-sm" title="تعديل" style="background: #2196F3; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                                    <i class="fa-solid fa-pen"></i> تعديل
                                                </button>
                                                <button wire:click="deleteProduct({{ $product->id }})" class="btn btn-sm btn-danger" title="حذف" style="background: #F44336; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                    <i class="fa-solid fa-trash"></i> حذف
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($products->isEmpty())
        <div class="card" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
            <i class="fa-solid fa-box-open fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i>
            <p>لم يتم إضافة أي منتجات بعد. اضغط "إضافة منتج" لبدء إضافة النكهات.</p>
        </div>
    @endif

    <style>
        @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    </style>
</div>
