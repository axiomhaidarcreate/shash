<div>
    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600; animation: slideDown 0.3s ease;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Grid for category management and products --}}
    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        
        <!-- Add / Edit Product Form -->
        <div class="card" style="{{ $showForm ? 'border: 2px solid var(--primary-color); box-shadow: 0 0 15px rgba(212, 175, 55, 0.2);' : '' }}">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0;">
                    @if($productId)
                        <i class="fa-solid fa-pen" style="color: var(--info-color);"></i> تعديل المنتج
                    @else
                        <i class="fa-solid fa-plus-circle" style="color: var(--primary-color);"></i> إضافة منتج جديد
                    @endif
                </h3>
                <button wire:click="$toggle('showForm')" class="btn btn-sm btn-outline">
                    <i class="fa-solid fa-{{ $showForm ? 'times' : 'plus' }}"></i>
                    {{ $showForm ? 'إلغاء' : 'إضافة منتج' }}
                </button>
            </div>

            @if($showForm)
            <div style="display: flex; flex-direction: column; gap: 1.5rem; animation: slideDown 0.3s ease; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">
                
                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label class="form-label">اسم المنتج بالعربي <span style="color:var(--danger-color);">*</span></label>
                        <input type="text" wire:model="name" class="form-control" placeholder="مثال: عصير رمان">
                        @error('name') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">الاسم الإنجليزي (اختياري)</label>
                        <input type="text" wire:model="name_en" class="form-control" placeholder="مثال: Pomegranate Juice">
                    </div>
                </div>

                <div class="grid grid-cols-3">
                    <div class="form-group">
                        <label class="form-label">السعر (ريال)</label>
                        <input type="number" step="0.01" wire:model="price" class="form-control" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">التصنيف <span style="color:var(--danger-color);">*</span></label>
                        <select wire:model="category" class="form-control">
                            @foreach($dbCategories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">توجيه المطبخ (تلقائي للطلبات)</label>
                        <select wire:model="target_kitchen" class="form-control">
                            @foreach(\App\Models\Kitchen::where('is_active', true)->orderBy('name')->get() as $kitchen)
                                <option value="{{ $kitchen->slug }}">{{ $kitchen->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3">
                    <div class="form-group">
                        <label class="form-label">وحدة القياس</label>
                        <input type="text" wire:model="unit" class="form-control" placeholder="كوب، حبة، الخ">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رقم الصنف (SKU)</label>
                        <input type="text" wire:model="sku" class="form-control" placeholder="رقم فريد (اختياري)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">نسبة الضريبة %</label>
                        <input type="number" step="0.01" wire:model="tax_percentage" class="form-control">
                    </div>
                </div>

                <div class="grid grid-cols-2">
                    <div class="form-group" style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.2);">
                        <label class="form-label"><i class="fa-solid fa-image"></i> صورة المنتج (أيقونة)</label>
                        <input type="file" wire:model="image" class="form-control" accept="image/*">
                        <div style="margin-top: 10px; display: flex; gap: 10px;">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid var(--primary-color);">
                            @elseif ($existing_image)
                                <img src="{{ route('file.serve', ['path' => $existing_image]) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid #555;">
                            @endif
                        </div>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 1.1rem;">
                            <input type="checkbox" wire:model.live="deduct_from_inventory" style="width: 20px; height: 20px; accent-color: var(--primary-color);">
                            <span>يخصم من المخزون</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 1.1rem; color: var(--warning-color);">
                            <input type="checkbox" wire:model.live="is_composite" style="width: 20px; height: 20px; accent-color: var(--warning-color);">
                            <span>منتج مركب (يتكون من منتجات أخرى)</span>
                        </label>
                    </div>
                </div>

                @if($is_composite)
                <div style="background: rgba(255,152,0,0.05); border: 1px solid rgba(255,152,0,0.2); padding: 1.5rem; border-radius: 8px; animation: slideDown 0.3s ease;">
                    <h4 style="color: var(--warning-color); margin-bottom: 1rem;"><i class="fa-solid fa-cubes"></i> مكونات المنتج المركب</h4>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1rem;">عند بيع هذا المنتج، سيتم خصم المكونات التالية من المخزون تلقائياً.</p>
                    
                    @foreach($composite_ingredients as $index => $ingredient)
                    <div style="display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <label class="form-label">المكون</label>
                            <select wire:model="composite_ingredients.{{ $index }}.ingredient_id" class="form-control">
                                <option value="">-- اختر المنتج/المكون --</option>
                                @foreach($allProducts as $p)
                                    @if($p->id !== $productId)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div style="width: 150px;">
                            <label class="form-label">الكمية</label>
                            <input type="number" step="0.01" wire:model="composite_ingredients.{{ $index }}.quantity" class="form-control">
                        </div>
                        <button wire:click="removeIngredientRow({{ $index }})" class="btn btn-danger" style="padding: 0 1rem; height: 44px;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                    @endforeach
                    
                    <button wire:click="addIngredientRow" class="btn btn-sm" style="background: var(--warning-color); color: #000;">
                        <i class="fa-solid fa-plus"></i> إضافة مكون
                    </button>
                </div>
                @endif

                {{-- Modifiers Section --}}
                <div style="background: rgba(156,39,176,0.05); border: 1px solid rgba(156,39,176,0.2); padding: 1.5rem; border-radius: 8px; margin-top: 1rem; animation: slideDown 0.3s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="color: #E040FB; margin: 0;"><i class="fa-solid fa-layer-group"></i> خيارات وإضافات المنتج</h4>
                        <button type="button" wire:click="addModifierGroup" class="btn btn-sm" style="background: #E040FB; color: #fff;">
                            <i class="fa-solid fa-plus"></i> إضافة مجموعة خيارات
                        </button>
                    </div>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1rem;">
                        (مثال: مجموعة "النكهات" تحتوي على "فراولة", "توت". مجموعة "الخيارات" تحتوي على "بارد", "حار")
                    </p>

                    @foreach($modifier_groups as $gIndex => $group)
                    <div style="background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.1); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; position: relative;">
                        <button type="button" wire:click="removeModifierGroup({{ $gIndex }})" class="btn btn-sm btn-danger" style="position: absolute; top: 10px; left: 10px; padding: 2px 6px;">
                            <i class="fa-solid fa-times"></i>
                        </button>
                        
                        <div class="grid grid-cols-3" style="gap: 1rem; align-items: end; margin-bottom: 1rem; padding-left: 30px;">
                            <div class="form-group" style="margin: 0;">
                                <label class="form-label">اسم المجموعة (مثال: النكهة)</label>
                                <input type="text" wire:model="modifier_groups.{{ $gIndex }}.name" class="form-control">
                            </div>
                            <div style="display: flex; gap: 1rem; padding-bottom: 8px;">
                                <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; font-size: 0.9rem;">
                                    <input type="checkbox" wire:model="modifier_groups.{{ $gIndex }}.is_required" style="accent-color: var(--primary-color);"> إجباري؟
                                </label>
                                <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; font-size: 0.9rem;">
                                    <input type="checkbox" wire:model="modifier_groups.{{ $gIndex }}.is_multiple" style="accent-color: var(--primary-color);"> اختيار متعدد؟
                                </label>
                            </div>
                        </div>

                        <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 6px;">
                            <h5 style="margin-top: 0; margin-bottom: 0.5rem; font-size: 0.9rem; color: var(--text-secondary);">عناصر المجموعة (الخيارات المتاحة)</h5>
                            @foreach($group['modifiers'] as $mIndex => $mod)
                            <div style="display: flex; gap: 1rem; margin-bottom: 0.5rem;">
                                <input type="text" wire:model="modifier_groups.{{ $gIndex }}.modifiers.{{ $mIndex }}.name" class="form-control form-control-sm" placeholder="اسم الخيار (مثال: توت)">
                                <input type="number" step="0.01" wire:model="modifier_groups.{{ $gIndex }}.modifiers.{{ $mIndex }}.price" class="form-control form-control-sm" placeholder="السعر الإضافي (0.00)" style="width: 150px;">
                                <button type="button" wire:click="removeModifier({{ $gIndex }}, {{ $mIndex }})" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            @endforeach
                            <button type="button" wire:click="addModifier({{ $gIndex }})" class="btn btn-sm btn-outline" style="margin-top: 0.5rem; font-size: 0.8rem;">
                                <i class="fa-solid fa-plus"></i> إضافة خيار
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button wire:click="save" class="btn btn-success" style="flex: 1; padding: 1rem; font-size: 1.1rem;">
                        <i class="fa-solid fa-save"></i> حفظ المنتج
                    </button>
                    <button wire:click="openAddForm" class="btn btn-outline" style="padding: 1rem;">
                        تفريغ الحقول
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Categories Management Card -->
        <div class="card">
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
                            <th style="text-align: center;">الظهور بالمنتجات</th>
                            <th style="text-align: center;">الظهور بالطلب</th>
                            <th style="text-align: center; width: 100px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dbCategories as $cat)
                        <tr wire:key="cat-row-{{ $cat->id }}" style="cursor: pointer; {{ $selectedCategory === $cat->name ? 'background: rgba(212, 175, 55, 0.1); border-right: 4px solid var(--primary-color);' : '' }}" wire:click="selectCategory('{{ $cat->name }}')">
                            <td>
                                @if($editingCategoryId === $cat->id)
                                    <input type="text" wire:model="editCategoryName" class="form-control" style="padding: 0.3rem 0.6rem; font-size: 0.9rem;" wire:click.stop>
                                @else
                                    <span style="font-weight: bold; color: {{ $selectedCategory === $cat->name ? 'var(--primary-color)' : 'inherit' }};">{{ $cat->name }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;" wire:click.stop>
                                <button wire:click="toggleCategoryVisibilityProducts({{ $cat->id }})" class="badge {{ $cat->is_visible_in_products ? 'badge-active' : 'badge-inactive' }}" style="cursor: pointer; border: none; background: {{ $cat->is_visible_in_products ? 'rgba(76,175,80,0.15)' : 'rgba(244,67,54,0.15)' }}; color: {{ $cat->is_visible_in_products ? 'var(--success-color)' : 'var(--danger-color)' }};">
                                    {{ $cat->is_visible_in_products ? 'ظاهر' : 'مخفي' }}
                                </button>
                            </td>
                            <td style="text-align: center;" wire:click.stop>
                                <button wire:click="toggleCategoryVisibility({{ $cat->id }})" class="badge {{ $cat->is_visible ? 'badge-active' : 'badge-inactive' }}" style="cursor: pointer; border: none; background: {{ $cat->is_visible ? 'rgba(76,175,80,0.15)' : 'rgba(244,67,54,0.15)' }}; color: {{ $cat->is_visible ? 'var(--success-color)' : 'var(--danger-color)' }};">
                                    {{ $cat->is_visible ? 'ظاهر' : 'مخفي' }}
                                </button>
                            </td>
                            <td wire:click.stop>
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

        <!-- Selected Category Products List -->
        @if($selectedCategory)
        @php
            $categoryProducts = $products->where('category', $selectedCategory);
        @endphp
        <div class="card" style="margin-bottom: 0; animation: slideDown 0.3s ease;">
            <h3 style="margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-list-check" style="color: var(--primary-color);"></i>
                    منتجات تصنيف: {{ $selectedCategory }}
                </span>
                <button wire:click="$set('category', '{{ addslashes($selectedCategory) }}'); $set('showForm', true)" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> إضافة منتج هنا
                </button>
            </h3>

            @if($categoryProducts->isEmpty())
                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-box-open fa-2x" style="opacity: 0.3; margin-bottom: 1rem;"></i>
                    <p>لا يوجد منتجات في هذا التصنيف بعد.</p>
                </div>
            @else
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; margin: 0 -0.5rem; padding: 0 0.5rem;">
                <table class="data-table" style="min-width: 600px; width: 100%;">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>السعر</th>
                            <th style="text-align: center;">المتبقي</th>
                            <th style="text-align: center;">العدد المباع</th>
                            <th>المطبخ</th>
                            <th style="width: 120px; text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categoryProducts as $product)
                        <tr wire:key="product-{{ $product->id }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    @if($product->image)
                                        <img src="{{ route('file.serve', ['path' => $product->image]) }}" style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 6px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-box" style="color: #555;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 600;">{{ $product->name }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                            @if($product->is_composite)
                                                <span style="color: var(--warning-color);"><i class="fa-solid fa-cubes"></i> مركب</span>
                                            @endif
                                            @if(!$product->deduct_from_inventory)
                                                <span style="color: var(--danger-color);"><i class="fa-solid fa-box-open"></i> لا يخصم</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->price }} ريال</td>
                            <td style="text-align: center; font-weight: bold; color: var(--info-color);">
                                {{ $stockStats[$product->id]['remaining'] ?? 0 }}
                                <span style="font-size: 0.75rem; font-weight: normal;">
                                    {{ in_array($product->category, ['الشيشة', 'معسل', 'الشيش']) ? 'جرام' : $product->unit }}
                                </span>
                            </td>
                            <td style="text-align: center; font-weight: bold;">
                                {{ $stockStats[$product->id]['soldCount'] ?? 0 }}
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(255,255,255,0.1);">{{ $product->target_kitchen }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; white-space: nowrap; justify-content: center;">
                                    <button wire:click="editProduct({{ $product->id }})" class="btn btn-sm" title="تعديل" style="background: #2196F3; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button wire:click="deleteProduct({{ $product->id }})" class="btn btn-sm btn-danger" title="حذف" style="background: #F44336; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endif
    </div>

    <style>
        @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    </style>
</div>
