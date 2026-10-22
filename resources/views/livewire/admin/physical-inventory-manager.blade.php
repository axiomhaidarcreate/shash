<div>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>الجرد الفعلي</h1>
            <p>إدارة جرد ومطابقة المخزون</p>
        </div>
        @if($viewMode !== 'list')
            <button class="btn btn-outline" wire:click="$set('viewMode', 'list')">
                <i class="fa-solid fa-list"></i> العودة للقائمة
            </button>
        @endif
    </div>

    @if($viewMode === 'create' || $viewMode === 'view')
    <!-- Add/Edit/View Form -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem; color: var(--primary-color);">
            @if($viewMode === 'view')
                <i class="fa-solid fa-eye"></i> عرض تفاصيل الجرد
            @else
                <i class="fa-solid fa-{{ $isEditing ? 'pen' : 'file-circle-plus' }}"></i> {{ $isEditing ? 'تعديل الجرد' : 'إضافة جرد جديد' }}
            @endif
        </h3>
        
        <form wire:submit.prevent="save" style="gap: 1rem; align-items: start;">
            <div class="grid grid-cols-2" style="gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">تاريخ الجرد</label>
                    <input type="date" class="form-control" wire:model="inventory_date" @if($viewMode === 'view') disabled @endif>
                    @error('inventory_date') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">ملاحظات عامة</label>
                    <input type="text" class="form-control" wire:model="notes" placeholder="أية ملاحظات عن هذا الجرد..." @if($viewMode === 'view') disabled @endif>
                </div>
            </div>

            @if(count($inventoryItems) > 0 && ($viewMode === 'view' || $viewMode === 'create' && $isEditing))
            <!-- Statistics Summary Card -->
            @php
                $totalShortage = 0;
                $totalSurplus = 0;
                $totalMatched = 0;
                $totalSystem = 0;
                $totalPhysical = 0;
                foreach($inventoryItems as $item) {
                    $prev = is_numeric($item['previous_quantity']) ? (float)$item['previous_quantity'] : 0;
                    $sysDaily = is_numeric($item['system_daily_consumed']) ? (float)$item['system_daily_consumed'] : 0;
                    $actDaily = is_numeric($item['actual_daily_consumed']) ? (float)$item['actual_daily_consumed'] : 0;
                    
                    $curr = ($prev + $sysDaily) - $actDaily;
                    $diff = $sysDaily - $actDaily;

                    $totalSystem += $prev;
                    $totalPhysical += $curr;
                    if ($diff < 0) $totalShortage += abs($diff);
                    elseif ($diff > 0) $totalSurplus += $diff;
                    else $totalMatched++;
                }
            @endphp
            <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 8px; margin-top: 1rem; margin-bottom: 1rem;">
                <h4 style="margin-bottom: 1rem;"><i class="fa-solid fa-chart-bar" style="color: var(--primary-color);"></i> إحصائيات الجرد المبدئية</h4>
                <div class="grid grid-cols-5" style="gap: 1rem;">
                    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 5px;">إجمالي المخزون الكلي</div>
                        <div style="font-size: 1.3rem; font-weight: bold;">{{ $totalSystem }}</div>
                    </div>
                    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 5px;">إجمالي المتبقي الفعلي</div>
                        <div style="font-size: 1.3rem; font-weight: bold;">{{ $totalPhysical }}</div>
                    </div>
                    <div style="background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="color: var(--success-color); font-size: 0.8rem; margin-bottom: 5px;">أصناف مطابقة</div>
                        <div style="font-size: 1.3rem; font-weight: bold; color: var(--success-color);">{{ $totalMatched }}</div>
                    </div>
                    <div style="background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="color: var(--danger-color); font-size: 0.8rem; margin-bottom: 5px;">الفارق (عجز)</div>
                        <div style="font-size: 1.3rem; font-weight: bold; color: var(--danger-color);">{{ $totalShortage }}</div>
                    </div>
                    <div style="background: rgba(33,150,243,0.1); border: 1px solid rgba(33,150,243,0.3); padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="color: var(--info-color); font-size: 0.8rem; margin-bottom: 5px;">الفارق (زيادة)</div>
                        <div style="font-size: 1.3rem; font-weight: bold; color: var(--info-color);">{{ $totalSurplus }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Inventory Items Section -->
            <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem;">
                <h4 style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                    <span><i class="fa-solid fa-boxes-stacked" style="color: var(--info-color);"></i> أصناف الجرد</span>
                </h4>
                
                @if($viewMode !== 'view')
                <!-- Product Search & Filter -->
                <div class="grid grid-cols-2" style="gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="searchProduct" placeholder="البحث باسم المنتج أو رقم الصنف SKU...">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <select class="form-control" wire:model.live="selectedProductCategory">
                            <option value="">-- تصفية حسب التصنيف --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Available Products Grid -->
                @if($searchProduct || $selectedProductCategory)
                    <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 15px; margin-bottom: 1rem; border-bottom: 1px dashed rgba(255,255,255,0.1);">
                        @forelse($availableProducts as $prod)
                            <div class="card" wire:click="addProductToInventory({{ $prod->id }}, '{{ addslashes($prod->name) }}', '{{ $prod->unit ?? '' }}')" style="min-width: 150px; padding: 10px; cursor: pointer; text-align: center; background: rgba(255,255,255,0.05); margin-bottom: 0; transition: 0.2s;">
                                <div style="font-weight: bold; font-size: 0.9rem;">{{ $prod->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $prod->category }}</div>
                                <div style="font-size: 0.8rem; margin-top: 5px; color: var(--success-color);"><i class="fa-solid fa-plus"></i> إضافة</div>
                            </div>
                        @empty
                            <div style="color: var(--text-secondary); width: 100%; text-align: center; padding: 10px;">لا يوجد منتجات مطابقة للبحث.</div>
                        @endforelse
                    </div>
                @endif
                @endif

                <!-- Selected Items Table -->
                <div style="overflow-x: auto;">
                <table class="data-table" style="width: 100%; margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th style="width: 90px;">الوحدة</th>
                            <th style="width: 110px;">المخزون الكلي</th>
                            <th style="width: 120px;">استهلاك النظام (اليوم)</th>
                            <th style="width: 120px;">الاستهلاك الفعلي (اليوم)</th>
                            <th style="width: 120px;">المتبقي في المخزون</th>
                            @if($viewMode === 'view')
                            <th style="width: 90px;">الفارق</th>
                            @endif
                            @if($viewMode !== 'view')
                            <th style="width: 70px; text-align: center;">إجراء</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventoryItems as $index => $item)
                        <tr>
                            <td>{{ $item['product_name'] }}</td>
                            <td>
                                <input type="text" class="form-control" wire:model="inventoryItems.{{ $index }}.unit" style="padding: 5px; height: 35px; background: rgba(0,0,0,0.1);" disabled>
                            </td>
                            
                            <td>
                                <input type="number" step="0.01" class="form-control" wire:model="inventoryItems.{{ $index }}.previous_quantity" style="padding: 5px; height: 35px; background: rgba(0,0,0,0.1);" disabled>
                            </td>

                            <td>
                                <input type="number" step="0.01" class="form-control" wire:model="inventoryItems.{{ $index }}.system_daily_consumed" style="padding: 5px; height: 35px; background: rgba(0,0,0,0.1); color: var(--warning-color);" disabled>
                            </td>

                            <td>
                                <input type="number" step="0.01" class="form-control" wire:model.live.debounce.500ms="inventoryItems.{{ $index }}.actual_daily_consumed" style="padding: 5px; height: 35px; border-color: var(--warning-color);" @if($viewMode === 'view') disabled @endif>
                            </td>
                            
                            @php
                                $prev = is_numeric($item['previous_quantity']) ? (float)$item['previous_quantity'] : 0;
                                $sysDaily = is_numeric($item['system_daily_consumed']) ? (float)$item['system_daily_consumed'] : 0;
                                $actDaily = is_numeric($item['actual_daily_consumed']) ? (float)$item['actual_daily_consumed'] : 0;
                                $curr = ($prev + $sysDaily) - $actDaily;
                                $diff = $sysDaily - $actDaily; // same as curr - prev
                            @endphp
                            
                            <td style="vertical-align: middle; font-weight: bold; font-size: 1.1em; color: var(--info-color);">
                                {{ $curr }}
                            </td>
                            
                            @if($viewMode === 'view')
                            <td style="font-weight: bold; vertical-align: middle; color: {{ $diff > 0 ? 'var(--success-color)' : ($diff < 0 ? 'var(--danger-color)' : 'var(--text-color)') }}">
                                {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                            </td>
                            @endif

                            @if($viewMode !== 'view')
                            <td style="text-align: center;">
                                <button type="button" wire:click="removeInventoryItem({{ $index }})" class="btn btn-sm btn-danger" style="padding: 5px 10px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                                يرجى البحث وإضافة منتجات للجرد
                                @error('inventoryItems') <div style="color: var(--danger-color); margin-top: 5px;">{{ $message }}</div> @enderror
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            
            @if($viewMode !== 'view')
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 1rem;">
                @if($isEditing && auth()->user()->hasRole('admin'))
                    <button type="button" class="btn btn-outline" wire:click="resetFields">إلغاء التعديل</button>
                @endif
                <button type="submit" class="btn btn-success" style="padding: 10px 30px; font-size: 1.1em;">
                    <i class="fa-solid fa-save"></i> {{ $isEditing ? 'حفظ التعديلات' : 'حفظ الجرد المبدئي' }}
                </button>
            </div>
            @endif
        </form>
    </div>
    @endif

    @if($viewMode === 'list')
    <!-- Filters and Table (Admin Only) -->
    <div class="card">
        <div style="display: flex; gap: 15px; margin-bottom: 1.5rem; flex-wrap: wrap; justify-content: space-between;">
            <div style="display: flex; gap: 15px;">
                <div style="min-width: 150px;">
                    <select class="form-control" wire:model.live="filterPeriod">
                        <option value="all">كل الأوقات</option>
                        <option value="daily">يومي</option>
                        <option value="weekly">أسبوعي</option>
                        <option value="monthly">شهري</option>
                    </select>
                </div>
                @if($filterPeriod !== 'all')
                <div style="min-width: 150px;">
                    <input type="date" class="form-control" wire:model.live="filterDate">
                </div>
                @endif
            </div>
            <button class="btn btn-primary" wire:click="$set('viewMode', 'create')">
                <i class="fa-solid fa-plus"></i> إضافة جرد جديد
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>رقم الجرد</th>
                        <th>الموظف</th>
                        <th>التاريخ</th>
                        <th>الأصناف</th>
                        <th>المخزون الكلي (نظام)</th>
                        <th>المتبقي في المخزون</th>
                        <th>الفارق (عجز)</th>
                        <th>الفارق (زيادة)</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inv)
                    @php
                        $totalShortage = 0;
                        $totalSurplus = 0;
                        $totalSystem = 0;
                        $totalPhysical = 0;
                        foreach($inv->items as $item) {
                            $curr = is_numeric($item->current_quantity) ? (float)$item->current_quantity : 0;
                            $prev = is_numeric($item->previous_quantity) ? (float)$item->previous_quantity : 0;
                            $diff = $curr - $prev;
                            $totalSystem += $prev;
                            $totalPhysical += $curr;
                            if ($diff < 0) $totalShortage += abs($diff);
                            elseif ($diff > 0) $totalSurplus += $diff;
                        }
                    @endphp
                    <tr>
                        <td><span class="badge badge-inactive">INV-{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                        <td><strong>{{ $inv->user->name ?? 'غير معروف' }}</strong></td>
                        <td>{{ $inv->date->format('Y-m-d') }}</td>
                        <td><span class="badge badge-inactive">{{ $inv->items->count() }} صنف</span></td>
                        <td>{{ $totalSystem }}</td>
                        <td>{{ $totalPhysical }}</td>
                        <td>
                            @if($totalShortage > 0)
                                <span style="color: var(--danger-color); font-weight: bold; padding: 2px 8px; background: rgba(244,67,54,0.1); border-radius: 4px;">-{{ $totalShortage }}</span>
                            @else
                                <span style="color: var(--text-secondary);">-</span>
                            @endif
                        </td>
                        <td>
                            @if($totalSurplus > 0)
                                <span style="color: var(--info-color); font-weight: bold; padding: 2px 8px; background: rgba(33,150,243,0.1); border-radius: 4px;">+{{ $totalSurplus }}</span>
                            @else
                                <span style="color: var(--text-secondary);">-</span>
                            @endif
                        </td>
                        <td>
                            @if($inv->status === 'approved')
                                <span class="badge" style="background: rgba(76,175,80,0.1); color: #4CAF50; border: 1px solid rgba(76,175,80,0.3);">معتمد</span>
                            @else
                                <span class="badge" style="background: rgba(255,152,0,0.1); color: #FF9800; border: 1px solid rgba(255,152,0,0.3);">مسودة / معلق</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <button wire:click="view({{ $inv->id }})" class="btn btn-sm btn-info" title="عرض التفاصيل">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <a href="{{ route('invoice.physicalInventoryPrint', $inv->id) }}" target="_blank" class="btn btn-sm" style="background: #4CAF50; color: white; text-decoration: none;" title="طباعة الجرد">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @if($inv->status !== 'approved')
                                    <button wire:click="edit({{ $inv->id }})" class="btn btn-sm btn-outline" title="تعديل">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button wire:click="approve({{ $inv->id }})" wire:confirm="هل أنت متأكد من اعتماد هذا الجرد؟ سيتم تحديث المخزون الفعلي بناءً على الفروقات." class="btn btn-sm btn-success" title="اعتماد وتحديث المخزون">
                                        <i class="fa-solid fa-check-double"></i>
                                    </button>
                                    <button wire:click="delete({{ $inv->id }})" wire:confirm="هل أنت متأكد من حذف هذا الجرد؟" class="btn btn-sm btn-danger" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            لا توجد عمليات جرد مطابقة لخيارات الفلتر.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
