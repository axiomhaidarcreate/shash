<div>
    <div style="background: var(--surface-color); padding: 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); margin-bottom: 2rem;">
        <h2 style="margin-top: 0; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-crown"></i> إدارة باقات الاشتراك (الديناميكية)
        </h2>

        @if(session('success'))
            <div style="background: rgba(76, 175, 80, 0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; font-weight: bold;">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(244, 67, 54, 0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; font-weight: bold;">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label class="form-label">اسم الباقة (مثال: ذهبية، شهرية، VIP)</label>
                <input type="text" wire:model="name" class="form-control" placeholder="أدخل اسم الباقة">
                @error('name') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="form-label">مدة الاشتراك (بالأيام)</label>
                <input type="number" wire:model="duration_days" class="form-control" placeholder="مثال: 30">
                @error('duration_days') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="form-label">السعر</label>
                <input type="number" wire:model="price" class="form-control" placeholder="السعر" step="0.01">
                @error('price') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="form-label">الحد اليومي للطلبات (عدد التذاكر يومياً)</label>
                <input type="number" wire:model="daily_order_limit" class="form-control" placeholder="مثال: 1">
                @error('daily_order_limit') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label class="form-label"><i class="fa-solid fa-box-open"></i> الأصناف المشمولة في الباقة</label>
            
            {{-- Search Bar --}}
            <div style="margin-bottom: 0.8rem; position: relative;">
                <i class="fa-solid fa-search" style="position: absolute; right: 10px; top: 10px; color: var(--text-secondary);"></i>
                <input type="text" wire:model.live.debounce.300ms="productSearch" class="form-control" placeholder="ابحث عن صنف لإضافته..." style="padding-right: 2rem;">
            </div>

            {{-- Selected Products (always visible) --}}
            @if(count($included_products) > 0)
                <div style="margin-bottom: 1rem; padding: 0.5rem; background: rgba(76, 175, 80, 0.05); border: 1px solid rgba(76, 175, 80, 0.2); border-radius: var(--radius-sm);">
                    <div style="font-size: 0.85rem; color: var(--success-color); font-weight: bold; margin-bottom: 0.5rem;">الأصناف المختارة ({{ count($included_products) }}):</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($included_products as $selProd)
                            <span style="display: inline-flex; align-items: center; gap: 5px; padding: 0.3rem 0.8rem; background: var(--primary-color); color: #fff; border-radius: 20px; font-size: 0.85rem;">
                                {{ $selProd }}
                                <button wire:click="toggleProduct('{{ addslashes($selProd) }}')" type="button" style="background: none; border: none; color: #fff; cursor: pointer; padding: 0; margin-right: 5px;"><i class="fa-solid fa-xmark"></i></button>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Search Results --}}
            @if(strlen(trim($productSearch)) > 0)
                <div style="display: flex; flex-wrap: wrap; gap: 10px; background: rgba(0,0,0,0.02); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); max-height: 200px; overflow-y: auto;">
                    @forelse($products as $product)
                        @if(!in_array($product->name, $included_products))
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; padding: 0.4rem 0.8rem; background: var(--surface-color); color: var(--text-color); border: 1px solid var(--border-color); border-radius: 20px; transition: 0.2s;">
                                <input type="checkbox" wire:click="toggleProduct('{{ addslashes($product->name) }}')" style="display: none;">
                                <i class="fa-solid fa-plus" style="color: var(--success-color);"></i> {{ $product->name }}
                            </label>
                        @endif
                    @empty
                        <div style="width: 100%; text-align: center; color: var(--text-secondary); padding: 1rem;">لا توجد أصناف مطابقة للبحث.</div>
                    @endforelse
                </div>
            @else
                <div style="font-size: 0.85rem; color: var(--text-secondary);"><i class="fa-solid fa-info-circle"></i> ابحث عن اسم الصنف أعلاه لإضافته إلى الباقة.</div>
            @endif
            
            @error('included_products') <div style="color: var(--danger-color); font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" wire:model="is_active">
                <span style="font-weight: bold;">الباقة نشطة (تظهر لموظف الاستقبال)</span>
            </label>
        </div>

        <div>
            @if($editId)
                <button wire:click="updatePackage" class="btn" style="background: var(--success-color);">
                    <i class="fa-solid fa-save"></i> حفظ التعديلات
                </button>
                <button wire:click="resetForm" class="btn btn-outline">إلغاء</button>
            @else
                <button wire:click="createPackage" class="btn">
                    <i class="fa-solid fa-plus"></i> إضافة الباقة
                </button>
            @endif
        </div>
    </div>

    <div style="background: var(--surface-color); padding: 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-md);">
        <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--primary-color);">الباقات الحالية</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: rgba(0,0,0,0.03);">
                        <th style="padding: 1rem; text-align: right; border-bottom: 2px solid var(--border-color);">اسم الباقة</th>
                        <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border-color);">السعر</th>
                        <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border-color);">المدة</th>
                        <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border-color);">الحد اليومي</th>
                        <th style="padding: 1rem; text-align: right; border-bottom: 2px solid var(--border-color);">الأصناف المشمولة</th>
                        <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border-color);">الحالة</th>
                        <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border-color);">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $pkg)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; font-weight: bold;">{{ $pkg->name }}</td>
                            <td style="padding: 1rem; text-align: center; color: var(--success-color); font-weight: bold;">{{ number_format($pkg->price, 0) }}</td>
                            <td style="padding: 1rem; text-align: center;">{{ $pkg->duration_days }} يوم</td>
                            <td style="padding: 1rem; text-align: center;">{{ $pkg->daily_order_limit }} طلب</td>
                            <td style="padding: 1rem;">
                                @if(is_array($pkg->included_products))
                                    @foreach($pkg->included_products as $p)
                                        <span style="display: inline-block; background: rgba(212,175,55,0.1); color: var(--primary-color); padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; margin: 2px;">{{ $p }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                @if($pkg->is_active)
                                    <span style="color: var(--success-color);"><i class="fa-solid fa-check-circle"></i> نشط</span>
                                @else
                                    <span style="color: var(--danger-color);"><i class="fa-solid fa-times-circle"></i> معطل</span>
                                @endif
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <button wire:click="editPackage({{ $pkg->id }})" class="btn btn-sm btn-outline" style="margin-left: 5px;">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <button wire:click="deletePackage({{ $pkg->id }})" class="btn btn-sm" style="background: var(--danger-color); color: #fff; border: none;" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">لا توجد باقات حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
