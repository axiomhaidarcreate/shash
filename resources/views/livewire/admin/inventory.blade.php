<div>
    <div class="page-header">
        <h1><i class="fa-solid fa-boxes-stacked" style="color: var(--primary-color);"></i> إدارة المخزون</h1>
        <p>تتبع كميات المعسل المُضافة والمُستهلكة، وتقارير الجرد الذكية.</p>
    </div>

    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div style="background: rgba(244,67,54,0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Filter Dropdown and Print Button --}}
    <div class="no-print" style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <label style="font-weight: bold; color: var(--text-secondary);"><i class="fa-solid fa-filter"></i> فترة التقرير:</label>
            <select wire:model.live="filterPeriod" class="form-control" style="width: auto; display: inline-block;">
                <option value="all">كل الأوقات (شامل)</option>
                <option value="daily">جرد يومي (اليوم)</option>
                <option value="weekly">جرد أسبوعي (هذا الأسبوع)</option>
                <option value="monthly">جرد شهري (هذا الشهر)</option>
                <option value="yearly">جرد سنوي (هذا العام)</option>
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="printReport('a4')" class="btn btn-primary">
                <i class="fa-solid fa-file-lines"></i> طباعة تقرير A4
            </button>
            <button onclick="printReport('thermal')" class="btn" style="background: #2196F3; color: white;">
                <i class="fa-solid fa-receipt"></i> طباعة حراري (80mm)
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="card" style="margin-bottom: 1.5rem; background: var(--card-bg);">
        <h3 style="margin-bottom: 1rem; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;"><i class="fa-solid fa-wind"></i> إحصائيات المعسل (بالجرامات)</h3>
        <div class="grid grid-cols-4" style="gap: clamp(0.5rem, 2vw, 1rem);">
            <div class="card" style="text-align: center; border-top: 3px solid var(--success-color); padding: clamp(0.6rem, 2vw, 1rem);">
                <i class="fa-solid fa-arrow-down" style="color: var(--success-color); font-size: clamp(1rem, 3vw, 1.4rem); margin-bottom: 0.5rem;"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">إجمالي المُضاف</h3>
                <p style="font-size: clamp(1.2rem, 4vw, 1.8rem); font-weight: 800; color: var(--success-color);">{{ number_format($totalStockInShisha) }} <span style="font-size:0.9rem;">ج</span></p>
            </div>
            <div class="card" style="text-align: center; border-top: 3px solid var(--warning-color); padding: clamp(0.6rem, 2vw, 1rem);">
                <i class="fa-solid fa-fire" style="color: var(--warning-color); font-size: clamp(1rem, 3vw, 1.4rem); margin-bottom: 0.5rem;"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">المُستهلك (مباع)</h3>
                <p style="font-size: clamp(1.2rem, 4vw, 1.8rem); font-weight: 800; color: var(--warning-color);">{{ number_format($totalConsumedShisha) }} <span style="font-size:0.9rem;">ج</span></p>
            </div>
            <div class="card" style="text-align: center; border-top: 3px solid var(--danger-color); padding: clamp(0.6rem, 2vw, 1rem);">
                <i class="fa-solid fa-trash" style="color: var(--danger-color); font-size: clamp(1rem, 3vw, 1.4rem); margin-bottom: 0.5rem;"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">إجمالي التالف</h3>
                <p style="font-size: clamp(1.2rem, 4vw, 1.8rem); font-weight: 800; color: var(--danger-color);">{{ number_format($totalWasteShisha) }} <span style="font-size:0.9rem;">ج</span></p>
            </div>
            <div class="card" style="text-align: center; border-top: 3px solid var(--primary-color); padding: clamp(0.6rem, 2vw, 1rem);">
                <i class="fa-solid fa-scale-balanced" style="color: var(--primary-color); font-size: clamp(1rem, 3vw, 1.4rem); margin-bottom: 0.5rem;"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">المتبقي الفعلي</h3>
                <p style="font-size: clamp(1.2rem, 4vw, 1.8rem); font-weight: 800; color: {{ $totalRemainingShisha < 0 ? 'var(--danger-color)' : 'var(--primary-color)' }};">
                    {{ number_format($totalRemainingShisha) }} <span style="font-size:0.9rem;">ج</span>
                </p>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 1.5rem; background: var(--card-bg);">
        <h3 style="margin-bottom: 1rem; color: #ff9800; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;"><i class="fa-solid fa-screwdriver-wrench"></i> إحصائيات المستلزمات (بالعدد/الحبة)</h3>
        <div class="grid grid-cols-4" style="gap: clamp(0.5rem, 2vw, 1rem);">
            <div class="card" style="text-align: center; border-top: 3px solid var(--success-color); padding: clamp(0.6rem, 2vw, 1rem);">
                <i class="fa-solid fa-arrow-down" style="color: var(--success-color); font-size: clamp(1rem, 3vw, 1.4rem); margin-bottom: 0.5rem;"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">إجمالي المُضاف</h3>
                <p style="font-size: clamp(1.2rem, 4vw, 1.8rem); font-weight: 800; color: var(--success-color);">{{ number_format($totalStockInAcc) }} <span style="font-size:0.9rem;">حبة</span></p>
            </div>
            <div class="card" style="text-align: center; border-top: 3px solid var(--warning-color); padding: clamp(0.6rem, 2vw, 1rem);">
                <i class="fa-solid fa-fire" style="color: var(--warning-color); font-size: clamp(1rem, 3vw, 1.4rem); margin-bottom: 0.5rem;"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">المُستهلك (مباع)</h3>
                <p style="font-size: clamp(1.2rem, 4vw, 1.8rem); font-weight: 800; color: var(--warning-color);">{{ number_format($totalConsumedAcc) }} <span style="font-size:0.9rem;">حبة</span></p>
            </div>
            <div class="card" style="text-align: center; border-top: 3px solid var(--danger-color); padding: clamp(0.6rem, 2vw, 1rem);">
                <i class="fa-solid fa-trash" style="color: var(--danger-color); font-size: clamp(1rem, 3vw, 1.4rem); margin-bottom: 0.5rem;"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">إجمالي التالف</h3>
                <p style="font-size: clamp(1.2rem, 4vw, 1.8rem); font-weight: 800; color: var(--danger-color);">{{ number_format($totalWasteAcc) }} <span style="font-size:0.9rem;">حبة</span></p>
            </div>
            <div class="card" style="text-align: center; border-top: 3px solid var(--primary-color); padding: clamp(0.6rem, 2vw, 1rem);">
                <i class="fa-solid fa-scale-balanced" style="color: var(--primary-color); font-size: clamp(1rem, 3vw, 1.4rem); margin-bottom: 0.5rem;"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">المتبقي الفعلي</h3>
                <p style="font-size: clamp(1.2rem, 4vw, 1.8rem); font-weight: 800; color: {{ $totalRemainingAcc < 0 ? 'var(--danger-color)' : 'var(--primary-color)' }};">
                    {{ number_format($totalRemainingAcc) }} <span style="font-size:0.9rem;">حبة</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Pending Wastes for Admin --}}
    @if(isset($pendingWastes) && $pendingWastes->count() > 0)
    <div class="card no-print" style="margin-bottom: 1.5rem; border: 2px solid var(--warning-color); background: rgba(255,152,0,0.05);">
        <h3 style="margin-bottom: 1rem; color: var(--warning-color);">
            <i class="fa-solid fa-triangle-exclamation"></i> طلبات إتلاف بانتظار الموافقة ({{ $pendingWastes->count() }})
        </h3>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الصنف</th>
                        <th>الكمية</th>
                        <th>الموظف</th>
                        <th>التاريخ</th>
                        <th>ملاحظات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingWastes as $waste)
                    <tr>
                        <td style="font-weight: bold;">{{ $waste->product->name }}</td>
                        <td>{{ number_format($waste->quantity_grams) }} {{ $waste->product->category === 'معسل' ? 'ج' : 'حبة' }}</td>
                        <td>{{ $waste->creator->name ?? 'غير معروف' }}</td>
                        <td>{{ $waste->created_at->format('Y-m-d h:i A') }}</td>
                        <td>{{ $waste->notes ?: '-' }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button wire:click="approveWaste({{ $waste->id }})" class="btn btn-sm btn-success" style="padding: 0.4rem 0.8rem;">
                                    <i class="fa-solid fa-check"></i> موافقة
                                </button>
                                <button wire:click="rejectWaste({{ $waste->id }})" class="btn btn-sm btn-danger" style="padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من رفض الطلب؟')">
                                    <i class="fa-solid fa-xmark"></i> رفض
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Add Stock / Waste Form --}}
    <div class="card no-print" style="margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 1.2rem;"><i class="fa-solid fa-plus-circle" style="color: var(--success-color);"></i> إدارة حركة المخزون</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">نوع الحركة</label>
                <select wire:model="entryType" class="form-control">
                    @role('admin')
                    <option value="addition">إضافة للمخزون (توريد)</option>
                    @endrole
                    <option value="waste">تسجيل تالف (خسارة)</option>
                </select>
                @error('entryType') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الصنف</label>
                <select wire:model="selectedProductId" class="form-control">
                    <option value="">-- اختر الصنف --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('selectedProductId') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الكمية (بالجرام للمعسل، بالحبه للمستلزمات)</label>
                <input type="number" wire:model="quantityGrams" class="form-control" min="1" placeholder="مثال: 1000">
                @error('quantityGrams') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">ملاحظة (سبب التالف/التوريد)</label>
                <input type="text" wire:model="stockNotes" class="form-control" placeholder="مثال: روس معسل خربانة">
            </div>
            <button wire:click="addStock" class="btn btn-primary" style="height: 42px; padding: 0 1.5rem;">
                <i class="fa-solid fa-save"></i> حفظ الحركة
            </button>
        </div>
    </div>

    {{-- Inventory Table per Product --}}
    <div class="card">
        <h3 style="margin-bottom: 1.2rem;"><i class="fa-solid fa-table-list" style="color: var(--primary-color);"></i> تقرير المخزون المُفصّل</h3>

        @if($inventoryData->isEmpty())
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <i class="fa-solid fa-box-open fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i>
                <p>لا توجد منتجات معسل حتى الآن. أضف منتجات من صفحة المنتجات أولاً.</p>
            </div>
        @else
        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="data-table" style="min-width: 800px;">
                <thead>
                    <tr>
                        <th>الصنف</th>
                        <th>الاستهلاك/الطلب</th>
                        <th>الداخل</th>
                        <th>المُستهلك</th>
                        <th>التالف</th>
                        <th>الطلبات</th>
                        <th>المتبقي الكلي</th>
                        <th class="no-print" style="width: 150px;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventoryData as $row)
                    @php 
                        $remaining = $row['remaining']; 
                        $isShisha = $row['product']->category === 'معسل';
                        $unit = $isShisha ? 'ج' : 'حبة';
                        $reqUnit = $isShisha ? 'رأس' : 'طلب';
                    @endphp
                    <tr wire:key="inv-{{ $row['product']->id }}" style="{{ $remaining < 0 ? 'background: rgba(244,67,54,0.05);' : '' }}">
                        <td style="font-weight: 700; color: var(--primary-color);">{{ $row['product']->name }} <br><span style="font-size:0.75rem; color:#888">{{ $row['product']->category }}</span></td>
                        <td>
                            @if($editingGramsId === $row['product']->id)
                                <input type="number" wire:model="editGramsValue" class="form-control"
                                    style="width: 80px; padding: 0.3rem; background: #1a1a1a; border: 1px solid var(--primary-color); color: #fff;">
                            @else
                                <span style="font-weight: 600; color: var(--warning-color);">{{ $row['product']->grams_per_order }}{{ $unit }}</span>
                            @endif
                        </td>
                        <td style="color: var(--success-color); font-weight: 700;">
                            {{ number_format($row['filtered_in']) }}{{ $unit }} <span style="font-size: 0.8rem; color: var(--text-secondary);">({{ floor($row['filtered_in'] / max($row['product']->grams_per_order, 1)) }} {{ $reqUnit }})</span>
                        </td>
                        <td style="color: var(--warning-color); font-weight: 700;">
                            {{ number_format($row['filtered_out']) }}{{ $unit }} <span style="font-size: 0.8rem; color: var(--text-secondary);">({{ floor($row['filtered_out'] / max($row['product']->grams_per_order, 1)) }} {{ $reqUnit }})</span>
                        </td>
                        <td style="color: var(--danger-color); font-weight: 700;">
                            {{ number_format($row['filtered_waste']) }}{{ $unit }} <span style="font-size: 0.8rem; color: var(--text-secondary);">({{ floor($row['filtered_waste'] / max($row['product']->grams_per_order, 1)) }} {{ $reqUnit }})</span>
                        </td>
                        <td style="color: var(--text-secondary);">{{ $row['total_orders'] }} طلب</td>
                        <td>
                            <span style="font-weight: 800; font-size: 1rem; color: {{ $remaining < 0 ? 'var(--danger-color)' : ($remaining < 200 && $isShisha ? 'var(--warning-color)' : 'var(--success-color)') }};">
                                {{ number_format($remaining) }}{{ $unit }} <span style="font-size: 0.85rem; font-weight: bold; color: var(--text-secondary);">({{ floor($remaining / max($row['product']->grams_per_order, 1)) }} {{ $reqUnit }})</span>
                                @if($remaining < 0)
                                    <i class="fa-solid fa-triangle-exclamation" title="تحذير: خسارة في المخزون!"></i>
                                @elseif($remaining < 200)
                                    <i class="fa-solid fa-exclamation-circle" title="تحذير: المخزون منخفض"></i>
                                @endif
                            </span>
                        </td>
                        <td class="no-print">
                            @role('admin')
                            @if($editingGramsId === $row['product']->id)
                                <div style="display: flex; gap: 6px;">
                                    <button wire:click="saveGrams" class="btn btn-sm btn-success" style="padding: 0.4rem 0.8rem;">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                    <button wire:click="cancelEditGrams" class="btn btn-sm" style="background: #444; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            @else
                                <button wire:click="startEditGrams({{ $row['product']->id }})" class="btn btn-sm" style="background: #2196F3; color: #fff; border: none; padding: 0.4rem 0.8rem;" title="تعديل الجرامات">
                                    <i class="fa-solid fa-pen"></i> تعديل الجرام
                                </button>
                            @endif
                            @else
                                <span style="color: var(--text-secondary); font-size: 0.8rem;">للمدير فقط</span>
                            @endrole
                        </td>
                    </tr>

                    {{-- History sub-rows --}}
                    @if($row['history']->count() > 0)
                    <tr wire:key="inv-hist-{{ $row['product']->id }}" style="background: rgba(255,255,255,0.02);">
                        <td colspan="8" style="padding: 0.5rem 1rem;">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                <span style="color: var(--text-secondary); font-size: 0.8rem; white-space: nowrap;">
                                    <i class="fa-solid fa-history"></i> آخر حركات ({{ $filterPeriod === 'all' ? 'الكل' : 'خلال الفترة' }}):
                                </span>
                                @foreach($row['history'] as $entry)
                                    <span style="background: {{ $entry->type === 'waste' ? 'rgba(244,67,54,0.1)' : 'rgba(76,175,80,0.1)' }}; border: 1px solid {{ $entry->type === 'waste' ? 'rgba(244,67,54,0.3)' : 'rgba(76,175,80,0.3)' }}; color: {{ $entry->type === 'waste' ? 'var(--danger-color)' : 'var(--success-color)' }}; padding: 2px 8px; border-radius: 20px; font-size: 0.8rem; white-space: nowrap;">
                                        @if($entry->type === 'waste')
                                            -{{ number_format($entry->quantity_grams) }}{{ $unit }} ({{ floor($entry->quantity_grams / max($row['product']->grams_per_order, 1)) }} {{ $reqUnit }} - تالف)
                                        @else
                                            +{{ number_format($entry->quantity_grams) }}{{ $unit }} ({{ floor($entry->quantity_grams / max($row['product']->grams_per_order, 1)) }} {{ $reqUnit }})
                                        @endif
                                        <span style="color: var(--text-secondary);">{{ $entry->created_at->format('d/m') }}</span>
                                        @if($entry->notes)
                                            <span style="color: var(--text-secondary);">({{ $entry->notes }})</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Low Stock Alert --}}
    @php $lowStock = $inventoryData->filter(fn($r) => $r['remaining'] < 200 && $r['overall_in'] > 0); @endphp
    @if($lowStock->count() > 0)
    <div class="card" style="margin-top: 1.5rem; border: 2px solid var(--warning-color); background: rgba(255,152,0,0.03);">
        <h3 style="margin-bottom: 1rem; color: var(--warning-color);">
            <i class="fa-solid fa-triangle-exclamation"></i> تنبيه: مخزون منخفض
            <span style="background: var(--warning-color); color: #000; border-radius: 50%; padding: 2px 8px; font-size: 0.85rem;">{{ $lowStock->count() }}</span>
        </h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            @foreach($lowStock as $row)
            <div style="background: rgba(255,152,0,0.1); border: 1px solid rgba(255,152,0,0.3); padding: 0.6rem 1rem; border-radius: var(--radius-sm);">
                <span style="font-weight: 700; color: var(--warning-color);">{{ $row['product']->name }}</span>
                <span style="color: var(--text-secondary); font-size: 0.85rem;">
                    — متبقي: {{ $row['remaining'] < 0 ? '<span style="color:var(--danger-color)">'.number_format($row['remaining']).'ج ⚠️</span>' : number_format($row['remaining']).'ج' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- Daily Table-Wise Detailed Inventory Report (Theft Prevention)      --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div class="card" style="margin-top: 1.5rem; border-top: 3px solid var(--primary-color);">
        <h3 style="margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px; font-size: 1.15rem; color: var(--primary-color);">
            <i class="fa-solid fa-list-check" style="color: var(--primary-color);"></i>
            تفصيل جرد الطاولات لليوم
            <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 400; margin-right: auto;">(لمكافحة السرقات ومراقبة الفروقات)</span>
        </h3>

        @if(empty($tableReport))
            <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-secondary);">
                <i class="fa-solid fa-chair fa-2x" style="opacity: 0.25; margin-bottom: 0.8rem;"></i>
                <p style="margin: 0;">لا توجد طلبات مسجلة على الطاولات لليوم بعد.</p>
            </div>
        @else
            {{-- Tables Grid --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($tableReport as $key => $report)
                    @php
                        $orderCount = count($report['orders']);
                        $totalQty = array_sum(array_column($report['orders'], 'quantity'));
                    @endphp
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-right: 4px solid var(--primary-color); padding: 1rem; border-radius: var(--radius-sm); transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 20px rgba(212,175,55,0.08)';"
                         onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='none';">

                        {{-- Table Header --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.8rem; padding-bottom: 0.6rem; border-bottom: 1px dashed rgba(255,255,255,0.08);">
                            <h4 style="margin: 0; font-size: 1.05rem; color: var(--primary-color); display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-chair" style="font-size: 0.9rem;"></i>
                                طاولة {{ $report['table_number'] }}
                            </h4>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($report['section'])
                                    <span style="font-size: 0.7rem; color: var(--text-secondary); background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.06);">
                                        <i class="fa-solid fa-door-open" style="font-size: 0.6rem;"></i> {{ $report['section'] }}
                                    </span>
                                @endif
                                <span style="font-size: 0.7rem; color: var(--warning-color); background: rgba(255,152,0,0.1); padding: 2px 8px; border-radius: 12px; font-weight: 700;">
                                    {{ $orderCount }} طلبية — {{ $totalQty }} رأس
                                </span>
                            </div>
                        </div>

                        {{-- Orders List --}}
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            @foreach($report['orders'] as $idx => $ord)
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.88rem; padding: 6px 8px; border-radius: 6px; background: rgba(255,255,255,0.015); border-bottom: 1px dashed rgba(255,255,255,0.04);">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="background: var(--primary-color); color: #000; font-size: 0.7rem; font-weight: 800; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; border-radius: 50%; flex-shrink: 0;">{{ $idx + 1 }}</span>
                                        <span style="font-weight: 600;">
                                            <strong style="color: var(--primary-color);">{{ $ord['quantity'] }}x</strong> {{ $ord['shisha_type'] }}
                                        </span>
                                    </div>
                                    <div style="color: var(--text-secondary); font-size: 0.75rem; display: flex; flex-direction: column; align-items: flex-end; gap: 1px;">
                                        <span><i class="fa-solid fa-user-ninja" style="font-size: 0.65rem; color: var(--warning-color);"></i> {{ $ord['waiter'] }}</span>
                                        <span style="font-size: 0.65rem; opacity: 0.7;"><i class="fa-regular fa-clock" style="font-size: 0.6rem;"></i> {{ $ord['time'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Daily Totals Summary --}}
            <div style="background: rgba(212,175,55,0.04); border: 1px solid rgba(212,175,55,0.25); border-radius: var(--radius-sm); padding: 1.2rem;">
                <h4 style="margin-top: 0; margin-bottom: 0.8rem; color: var(--primary-color); display: flex; align-items: center; gap: 8px; font-size: 1rem;">
                    <i class="fa-solid fa-boxes-packing"></i> الإجمالي الكلي لأصناف اليوم (للجرد والمطابقة):
                </h4>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @foreach($todayProductTotals as $productName => $totalQty)
                        <span style="background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.88rem; font-weight: bold; display: inline-flex; align-items: center; gap: 8px;">
                            <span>{{ $productName }}:</span>
                            <span style="color: var(--primary-color); font-size: 1.05rem; font-weight: 800;">{{ $totalQty }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        function printReport(type) {
            if (type === 'thermal') {
                window.open('/invoice/inventory-print-thermal', 'inventoryThermal', 'width=400,height=700');
            } else {
                window.open('/invoice/inventory-print', 'inventoryA4', 'width=900,height=700');
            }
        }
    </script>
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .sidebar, .nav, .page-header, .no-print, header, footer, .sidebar-wrapper, nav, [class*="no-print"] {
                display: none !important;
            }
            .main-content, .card {
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                background: none !important;
                box-shadow: none !important;
                width: 100% !important;
            }
            .grid-cols-4 {
                display: flex !important;
                flex-direction: row !important;
                gap: 5px !important;
                width: 100% !important;
                margin-bottom: 10px !important;
            }
            .grid-cols-4 .card {
                border: 1px solid #ccc !important;
                border-top: 3px solid black !important;
                padding: 6px !important;
                margin-bottom: 3px !important;
                background: #fff !important;
                box-shadow: none !important;
                text-align: center !important;
            }
            .grid-cols-4 .card i {
                display: none !important;
            }
            .grid-cols-4 .card h3 {
                margin: 0 !important;
                font-size: 11px !important;
                color: #333 !important;
            }
            .grid-cols-4 .card p {
                margin: 0 !important;
                font-size: 14px !important;
                font-weight: bold !important;
                color: #000 !important;
            }
            .data-table {
                width: 100% !important;
                min-width: 100% !important;
                border-collapse: collapse !important;
                font-size: 10px !important;
            }
            .data-table th, .data-table td {
                padding: 4px 2px !important;
                border: 1px solid #ddd !important;
                text-align: center !important;
                color: black !important;
            }
            .data-table th {
                background: #f0f0f0 !important;
                font-weight: bold !important;
            }
            
            /* Thermal Print Overrides */
            body.thermal-print .main-content, body.thermal-print .card {
                max-width: 300px !important;
            }
            body.thermal-print .grid-cols-4 {
                flex-direction: column !important;
            }
            body.thermal-print .grid-cols-4 .card {
                text-align: right !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                flex-direction: row-reverse !important;
            }
            body.thermal-print .data-table th:nth-child(2),
            body.thermal-print .data-table td:nth-child(2),
            body.thermal-print .data-table th:nth-child(6),
            body.thermal-print .data-table td:nth-child(6) {
                display: none !important;
            }
            body.thermal-print .data-table tr[wire\:key^="inv-hist-"] td {
                padding: 2px !important;
                font-size: 8px !important;
            }
        }
    </style>
</div>
