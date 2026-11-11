<div wire:poll.5s>
    <div style="margin-bottom: 1.5rem; margin-top: 0.5rem;">
        <a href="{{ route('admin.purchases') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 0.5rem 1rem; border-radius: var(--radius-sm);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة المشتريات والمخزون
        </a>
    </div>

    <livewire:shift-status-banner />
    <livewire:shift-close-report section="inventory" />
    <div class="page-header" style="position: relative;">
        <!-- Secret Button -->
        <button onclick="handleSecretClick()" style="position: absolute; top: 0; left: 0; width: 60px; height: 60px; background: transparent; border: 1px dashed rgba(128, 128, 128, 0.3); border-radius: 8px; outline: none; z-index: 999; cursor: default;"></button>
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

    {{-- Filter + Date + Print Buttons --}}
    <div class="no-print" style="margin-bottom: 1.5rem;">

        {{-- Row 1: Period filter + Date picker --}}
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 0.8rem;">
            @role('admin')
            <div style="display: flex; align-items: center;">
                <button wire:click="resetAllInventory" class="btn" style="background: var(--primary-color, #1976D2); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-weight: bold; cursor: pointer;" onclick="return confirm('تنبيه خطير!\nهل أنت متأكد من تصفير جميع عدادات المخزون (المضاف، المباع، والتالف)؟\nهذا الإجراء سيحذف كل الحركات والطلبات ولن تتمكن من التراجع عنه.')">
                    <i class="fa-solid fa-power-off"></i> تصفير العدادات
                </button>
            </div>
            @endrole
            
            <div style="display: flex; align-items: center; gap: 8px;">

                <button wire:click="toggleReportModal" class="btn btn-outline" style="border: 2px solid var(--info-color); color: var(--info-color); padding: 0.5rem 1rem; border-radius: 6px; font-weight: bold; cursor: pointer; background: transparent;">
                    <i class="fa-solid fa-file-contract"></i> تقارير الإغلاق
                </button>
            </div>

            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <label style="font-weight: bold; color: var(--text-secondary); white-space: nowrap;"><i class="fa-solid fa-filter"></i> فترة التقرير:</label>
                <select wire:model.live="filterPeriod" class="form-control" style="width: auto; display: inline-block;">
                    <option value="daily">جرد يومي (اليوم)</option>
                    <option value="weekly">جرد أسبوعي (هذا الأسبوع)</option>
                    <option value="monthly">جرد شهري (هذا الشهر)</option>
                    <option value="yearly">جرد سنوي (هذا العام)</option>
                    <option value="all">كل الأوقات (شامل)</option>
                </select>
            </div>

            {{-- Date picker --}}
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <label style="font-weight: bold; color: var(--text-secondary); white-space: nowrap;"><i class="fa-regular fa-calendar"></i> التاريخ:</label>
                <input type="date" id="inventory-date-picker" wire:model.live="filterDate" class="form-control" style="width: auto; display: inline-block;">
            </div>

            {{-- Time range picker (for custom hour range) --}}
            <div style="display: flex; align-items: center; gap: 0.6rem; background: rgba(212,175,55,0.07); border: 1px solid rgba(212,175,55,0.25); border-radius: 8px; padding: 4px 10px;">
                <label style="font-weight: bold; color: var(--primary-color); white-space: nowrap; font-size: 0.85rem;">
                    <i class="fa-solid fa-clock"></i> من ساعة:
                </label>
                <input type="time" wire:model.live="filterStartTime" class="form-control" style="width: 110px; display: inline-block; font-size: 0.9rem;">
                <label style="font-weight: bold; color: var(--primary-color); white-space: nowrap; font-size: 0.85rem;">إلى:</label>
                <input type="time" wire:model.live="filterEndTime" class="form-control" style="width: 110px; display: inline-block; font-size: 0.9rem;">
                @if($filterStartTime && $filterEndTime)
                    <button wire:click="$set('filterStartTime', '')" style="background: none; border: none; color: var(--danger-color); cursor: pointer; font-size: 1rem; padding: 0 4px;" title="مسح تحديد الساعات">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                @endif
            </div>

            {{-- Shift badge --}}
            @if($isShiftFilter)
                <span style="background: rgba(212,175,55,0.15); border: 1px solid var(--primary-color); color: var(--primary-color); padding: 0.3rem 0.9rem; border-radius: 20px; font-weight: 700; font-size: 0.9rem;">
                    <i class="fa-solid fa-clock"></i> {{ $shiftLabel }} &mdash; {{ \Carbon\Carbon::parse($filterDate)->format('Y/m/d') }}
                </span>
            @elseif($hasCustomTime)
                <span style="background: rgba(212,175,55,0.15); border: 1px solid var(--primary-color); color: var(--primary-color); padding: 0.3rem 0.9rem; border-radius: 20px; font-weight: 700; font-size: 0.9rem;">
                    <i class="fa-solid fa-clock"></i> {{ $shiftLabel }} &mdash; {{ \Carbon\Carbon::parse($filterDate)->format('Y/m/d') }}
                </span>
            @endif
        </div>

        {{-- Row 2: Print buttons --}}
        <div class="secret-print-target" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; align-items: center;">
            {{-- Manager approvals print --}}
            <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">موافقات المدير:</span>
                <a id="btn-print-manager-thermal"
                   href="/invoice/manager-approved-print-thermal?date={{ $filterDate }}"
                   target="_blank"
                   class="btn"
                   style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                    <i class="fa-solid fa-receipt"></i> حراري
                </a>
                <a id="btn-print-manager-a4"
                   href="/invoice/manager-approved-print?date={{ $filterDate }}"
                   target="_blank"
                   class="btn"
                   style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                    <i class="fa-solid fa-file-lines"></i> A4
                </a>
            </div>

            {{-- Waste print --}}
            <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">تالف معتمد:</span>
                <a id="btn-print-waste-thermal"
                   href="/invoice/waste-print-thermal?date={{ $filterDate }}"
                   target="_blank"
                   class="btn"
                   style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                    <i class="fa-solid fa-receipt"></i> حراري
                </a>
                <a id="btn-print-waste-a4"
                   href="/invoice/waste-print?date={{ $filterDate }}"
                   target="_blank"
                   class="btn"
                   style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                    <i class="fa-solid fa-file-lines"></i> A4
                </a>
            </div>

            {{-- Daily print --}}
            <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">📋 تقرير اليوم:</span>
                <a id="btn-print-daily-thermal"
                   href="/invoice/inventory-print-thermal?date={{ $filterDate }}&shift=daily"
                   target="_blank"
                   class="btn"
                   style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                    <i class="fa-solid fa-receipt"></i> حراري
                </a>
                <a id="btn-print-daily-a4"
                   href="/invoice/inventory-print?date={{ $filterDate }}&shift=daily"
                   target="_blank"
                   class="btn"
                   style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                    <i class="fa-solid fa-file-lines"></i> A4
                </a>
            </div>

            {{-- Custom time range print (shown only when time is selected) --}}
            @if($hasCustomTime)
            <div style="display: flex; align-items: center; background: rgba(212,175,55,0.08); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(212,175,55,0.4);">
                <span style="font-size: 0.8rem; color: var(--primary-color); padding: 0 8px; font-weight: bold; flex: 1;">
                    <i class="fa-solid fa-clock"></i> {{ $filterStartTime }} → {{ $filterEndTime }}:
                </span>
                <a href="/invoice/inventory-print-thermal?start_date={{ $filterDate }}&end_date={{ $filterDate }}&start_time={{ $filterStartTime }}&end_time={{ $filterEndTime }}"
                   target="_blank" class="btn"
                   style="background: #7B1FA2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                    <i class="fa-solid fa-receipt"></i> حراري
                </a>
                <a href="/invoice/inventory-print?start_date={{ $filterDate }}&end_date={{ $filterDate }}&start_time={{ $filterStartTime }}&end_time={{ $filterEndTime }}"
                   target="_blank" class="btn"
                   style="background: #9C27B0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                    <i class="fa-solid fa-file-lines"></i> A4
                </a>
            </div>
            @endif
        </div>
    </div>


    {{-- Summary Cards - Shisha --}}
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

    {{-- Pending Wastes for Admin (Shisha) --}}
    @if(isset($pendingWastesShisha) && $pendingWastesShisha->count() > 0)
    <div class="card no-print" style="margin-bottom: 1.5rem; border: 2px solid var(--warning-color); background: rgba(255,152,0,0.05);">
        <h3 style="margin-bottom: 1rem; color: var(--warning-color);">
            <i class="fa-solid fa-triangle-exclamation"></i> طلبات إتلاف معسل بانتظار الموافقة ({{ $pendingWastesShisha->count() }})
        </h3>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الصنف</th>
                        <th>الكمية (بالجرام)</th>
                        <th>الموظف</th>
                        <th>التاريخ</th>
                        <th>ملاحظات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingWastesShisha as $waste)
                    <tr>
                        <td style="font-weight: bold;">{{ $waste->product->name }}</td>
                        <td style="color: var(--danger-color); font-weight: bold;">{{ number_format($waste->quantity_grams) }} ج</td>
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

    {{-- Pending Wastes for Admin (Accessories) --}}
    @if(isset($pendingWastesAcc) && $pendingWastesAcc->count() > 0)
    <div class="card no-print" style="margin-bottom: 1.5rem; border: 2px solid #ff9800; background: rgba(255,152,0,0.05);">
        <h3 style="margin-bottom: 1rem; color: #ff9800;">
            <i class="fa-solid fa-triangle-exclamation"></i> طلبات إتلاف مستلزمات بانتظار الموافقة ({{ $pendingWastesAcc->count() }})
        </h3>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الصنف</th>
                        <th>الكمية (بالحبة)</th>
                        <th>الموظف</th>
                        <th>التاريخ</th>
                        <th>ملاحظات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingWastesAcc as $waste)
                    <tr>
                        <td style="font-weight: bold;">{{ $waste->product->name }}</td>
                        <td style="color: var(--danger-color); font-weight: bold;">{{ number_format($waste->quantity_grams) }} حبة</td>
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
                <label class="form-label">وحدة الكمية</label>
                <select wire:model.live="entryUnit" class="form-control" style="border: 1px solid var(--primary-color); background: rgba(212,175,55,0.05);">
                    <option value="" style="background: #1a1a1a; color: #fff;">-- عرض كل الأصناف --</option>
                    <option value="gram" style="background: #1a1a1a; color: #fff;">بالجرام (للشيشة ورؤوس الشيشة)</option>
                    <option value="head" style="background: #1a1a1a; color: #fff;">بالرأس (تُحسب كجرامات للمعسل)</option>
                    <option value="piece" style="background: #1a1a1a; color: #fff;">بالحبة (لباقي الأصناف)</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">الصنف</label>
                <select wire:model.live="selectedProductId" class="form-control">
                    <option value="">-- اختر الصنف --</option>
                    @foreach($filteredProducts as $product)
                        <option value="{{ $product->id }}" wire:key="prod-opt-{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('selectedProductId') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                @php
                    $selectedProd = $products->firstWhere('id', $selectedProductId);
                    $unitLabel = $selectedProd ? ($this->isGramCategory($selectedProd->category) ? ($entryUnit === 'head' ? '(بالرأس)' : '(بالجرام)') : '(بالحبة)') : '(بالجرام للشيشة، بالحبه للمستلزمات)';
                @endphp
                <label class="form-label">الكمية <span style="color: var(--warning-color);">{{ $unitLabel }}</span></label>
                <input type="number" wire:model="quantityGrams" class="form-control" min="1" placeholder="مثال: 100">
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

    {{-- Detailed Inventory Report Redesign (Tabbed & Card Based) --}}
    <div class="card">
        <h3 style="margin-bottom: 1.2rem;"><i class="fa-solid fa-boxes-stacked" style="color: var(--primary-color);"></i> تقرير المخزون المُفصّل</h3>

        @if($inventoryData->isEmpty())
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <i class="fa-solid fa-box-open fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i>
                <p>لا توجد منتجات حتى الآن. أضف منتجات من صفحة المنتجات أولاً.</p>
            </div>
        @else
            {{-- Category Tabs --}}
            <div style="display: flex; gap: 10px; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; flex-wrap: wrap;">
                @foreach($categories as $cat)
                    @php
                        // Rename "معسل" or "الشيش" to "الشيشة" in the UI
                        $displayName = in_array($cat->name, ['معسل', 'الشيش', 'الشيشة']) ? 'الشيشة' : $cat->name;
                        
                        // Dynamic icons based on category name
                        $catIcon = match($cat->name) {
                            'الشيشة', 'معسل', 'الشيش' => 'fa-solid fa-wind',
                            'رؤوس الشيشة', 'رأس شيشة' => 'fa-solid fa-fire-burner',
                            'طبقات إضافية' => 'fa-solid fa-layer-group',
                            'مشروبات' => 'fa-solid fa-mug-hot',
                            'مستلزمات' => 'fa-solid fa-screwdriver-wrench',
                            default => 'fa-solid fa-cube',
                        };
                    @endphp
                    <button wire:click="switchCategory('{{ $cat->name }}')" class="btn {{ $activeCategory === $cat->name ? 'btn-primary' : 'btn-outline' }}" style="flex: 1; min-width: 120px; font-size: 1.05rem; padding: 10px; font-weight: bold; transition: all 0.2s;">
                        <i class="{{ $catIcon }}"></i> {{ $displayName }}
                    </button>
                @endforeach
            </div>

            @php
                // Filter inventory data by active category
                $currentCategoryData = $inventoryData->filter(function($row) use ($activeCategory) {
                    return $row['product']->category === $activeCategory;
                });
                $isShishaCat = $this->isGramCategory($activeCategory);
                $catUnit = $isShishaCat ? 'ج' : 'حبة';
                $reqUnit = $isShishaCat ? 'رأس' : 'طلب';

                // Automatically select first product if none selected or selected product not in current category
                if (!$activeProductId && $currentCategoryData->isNotEmpty()) {
                    $activeProductId = $currentCategoryData->first()['product']->id;
                }
            @endphp

            @if($currentCategoryData->isEmpty())
                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    <p>لا توجد منتجات في هذا القسم.</p>
                </div>
            @else
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                    
                    {{-- Products Grid (Sidebar style) --}}
                    <div style="flex: 1; min-width: 250px; max-width: 350px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @foreach($currentCategoryData as $row)
                                @php
                                    $isSelected = $activeProductId === $row['product']->id;
                                    $rem = $row['remaining'];
                                @endphp
                                <button wire:click="selectProduct({{ $row['product']->id }})" 
                                        style="text-align: right; padding: 12px 15px; border-radius: 8px; border: 1px solid {{ $isSelected ? 'var(--primary-color)' : 'var(--border-color)' }}; background: {{ $isSelected ? 'rgba(212, 175, 55, 0.1)' : 'var(--bg-secondary)' }}; color: {{ $isSelected ? 'var(--primary-color)' : '#fff' }}; font-weight: bold; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s;">
                                    <span>{{ $row['product']->name }}</span>
                                    <span style="font-size: 0.85rem; color: {{ $rem < 0 ? 'var(--danger-color)' : ($rem < 200 && $isShishaCat ? 'var(--warning-color)' : 'var(--success-color)') }};">
                                        {{ number_format($rem) }}{{ $catUnit }}
                                        @if($rem < 0) <i class="fa-solid fa-triangle-exclamation"></i> @endif
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Selected Product Detail Card --}}
                    @if($activeProductId)
                        @php
                            $selectedRow = $currentCategoryData->firstWhere('product.id', $activeProductId);
                        @endphp
                        @if($selectedRow)
                            @php
                                $p = $selectedRow['product'];
                                $rem = $selectedRow['remaining'];
                                $reqDivider = max($p->grams_per_order, 1);
                            @endphp
                            <div style="flex: 2; min-width: 300px; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 15px;">
                                    <div>
                                        <h2 style="margin: 0 0 5px; color: var(--primary-color); font-size: 1.5rem;">{{ $p->name }}</h2>
                                        <span style="color: var(--text-secondary); font-size: 0.9rem;">الفئة: {{ $displayName }}</span>
                                    </div>
                                    
                                    @role('admin')
                                        @if($editingGramsId === $p->id)
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <span style="font-size: 0.85rem; color: var(--text-secondary);">استهلاك الطلب:</span>
                                                <input type="number" wire:model="editGramsValue" class="form-control" style="width: 70px; padding: 0.3rem; height: 32px; background: #000; border: 1px solid var(--primary-color);">
                                                <button wire:click="saveGrams" class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i></button>
                                                <button wire:click="cancelEditGrams" class="btn btn-sm" style="background: #555; color: white;"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                        @else
                                            <button wire:click="startEditGrams({{ $p->id }})" class="btn btn-sm btn-outline" style="font-size: 0.85rem;" title="تعديل الاستهلاك لكل طلب">
                                                <i class="fa-solid fa-pen"></i> معدل الاستهلاك: {{ $p->grams_per_order }}{{ $catUnit }}
                                            </button>
                                        @endif
                                    @endrole
                                </div>

                                {{-- Main Stats --}}
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 15px; margin-bottom: 25px;">
                                    <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px; text-align: center; border-bottom: 3px solid {{ $rem < 0 ? 'var(--danger-color)' : 'var(--success-color)' }};">
                                        <div style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 5px;">المتبقي الكلي</div>
                                        <div style="font-size: 1.4rem; font-weight: 900; color: {{ $rem < 0 ? 'var(--danger-color)' : 'var(--success-color)' }};">
                                            {{ number_format($rem) }}{{ $catUnit }}
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 3px;">~ {{ floor($rem / $reqDivider) }} {{ $reqUnit }}</div>
                                    </div>
                                    <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px; text-align: center; border-bottom: 3px solid var(--info-color);">
                                        <div style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 5px;">إجمالي التخزين</div>
                                        <div style="font-size: 1.2rem; font-weight: bold; color: var(--info-color);">
                                            {{ number_format($selectedRow['filtered_in']) }}{{ $catUnit }}
                                        </div>
                                    </div>
                                    <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px; text-align: center; border-bottom: 3px solid var(--warning-color);">
                                        <div style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 5px;">المُستهلك بالمبيعات</div>
                                        <div style="font-size: 1.2rem; font-weight: bold; color: var(--warning-color);">
                                            {{ number_format($selectedRow['filtered_out']) }}{{ $catUnit }}
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 3px;">{{ $selectedRow['total_orders'] }} طلب</div>
                                    </div>
                                    <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px; text-align: center; border-bottom: 3px solid var(--danger-color);">
                                        <div style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 5px;">التالف (المعتمد)</div>
                                        <div style="font-size: 1.2rem; font-weight: bold; color: var(--danger-color);">
                                            {{ number_format($selectedRow['filtered_waste']) }}{{ $catUnit }}
                                        </div>
                                    </div>
                                </div>

                                {{-- History Log --}}
                                <div>
                                    <h4 style="margin-bottom: 10px; font-size: 1rem; color: #ddd; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 5px;">
                                        <i class="fa-solid fa-clock-rotate-left"></i> آخر الحركات
                                        <span style="font-size: 0.75rem; font-weight: normal; color: var(--text-secondary);">({{ $filterPeriod === 'all' ? 'الكل' : 'مفلترة بالفترة' }})</span>
                                    </h4>
                                    
                                    @if($selectedRow['history']->count() > 0)
                                        <div style="display: flex; flex-direction: column; gap: 8px; max-height: 200px; overflow-y: auto; padding-right: 5px;">
                                            @foreach($selectedRow['history'] as $entry)
                                                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); padding: 8px 12px; border-radius: 6px; border-right: 3px solid {{ $entry->type === 'waste' ? 'var(--danger-color)' : 'var(--success-color)' }};">
                                                     <div style="display: flex; align-items: center; gap: 10px;">
                                                        @if($entry->type === 'waste')
                                                            <i class="fa-solid fa-trash-can" style="color: var(--danger-color);"></i>
                                                            <span style="font-weight: bold; color: var(--danger-color);">-{{ number_format($entry->quantity_grams) }}{{ $catUnit }}</span>
                                                            <span style="font-size: 0.8rem; color: var(--text-secondary);">إتلاف</span>
                                                        @else
                                                            <i class="fa-solid fa-arrow-right-to-bracket" style="color: var(--success-color);"></i>
                                                            <span style="font-weight: bold; color: var(--success-color);">+{{ number_format($entry->quantity_grams) }}{{ $catUnit }}</span>
                                                            <span style="font-size: 0.8rem; color: var(--text-secondary);">توريد/تخزين</span>
                                                        @endif
                                                        
                                                        @if($entry->notes)
                                                            <span style="font-size: 0.8rem; color: #aaa; background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px;">{{ $entry->notes }}</span>
                                                        @endif
                                                    </div>
                                                    <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                                        {{ $entry->created_at->format('Y/m/d h:i A') }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div style="color: var(--text-secondary); font-size: 0.9rem; padding: 10px 0;">لا توجد حركات سابقة لهذا الصنف.</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
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
            @php
                $unit = $this->isGramCategory($row['product']->category) ? 'ج' : 'حبة';
            @endphp
            <div style="background: rgba(255,152,0,0.1); border: 1px solid rgba(255,152,0,0.3); padding: 0.6rem 1rem; border-radius: var(--radius-sm);">
                <span style="font-weight: 700; color: var(--warning-color);">{{ $row['product']->name }}</span>
                <span style="color: var(--text-secondary); font-size: 0.85rem;">
                    — متبقي: {!! $row['remaining'] < 0 ? '<span style="color:var(--danger-color)">'.number_format($row['remaining']).$unit.' ⚠️</span>' : number_format($row['remaining']).$unit !!}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══ Table-Wise Detailed Inventory Report ═══ --}}
    <div class="card" style="margin-top: 1.5rem; border-top: 3px solid var(--primary-color);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 8px; font-size: 1.15rem; color: var(--primary-color);">
                <i class="fa-solid fa-list-check" style="color: var(--primary-color);"></i>
                تفصيل جرد الطاولات
                <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 400;">(لمكافحة السرقات ومراقبة الفروقات)</span>
            </h3>
            
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label style="font-weight: bold; color: var(--text-secondary); white-space: nowrap; font-size: 0.9rem;">التاريخ:</label>
                    <input type="date" wire:model.live="tableReportDate" class="form-control" style="width: auto; padding: 0.3rem 0.6rem; height: 34px; background: #000; border: 1px solid var(--border-color); color: #fff;">
                </div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label style="font-weight: bold; color: var(--text-secondary); white-space: nowrap; font-size: 0.9rem;">بحث (رقم/قسم):</label>
                    <input type="text" wire:model.live="tableReportSearch" placeholder="ابحث برقم الطاولة أو القسم..." class="form-control" style="width: 200px; padding: 0.3rem 0.6rem; height: 34px; background: #000; border: 1px solid var(--border-color); color: #fff;">
                </div>
            </div>
        </div>

        @if(empty($tableReport))
            <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-secondary);">
                <i class="fa-solid fa-chair fa-2x" style="opacity: 0.25; margin-bottom: 0.8rem;"></i>
                <p style="margin: 0;">لا توجد طلبات مسجلة على الطاولات لهذه الفترة بعد.</p>
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
                    <i class="fa-solid fa-boxes-packing"></i> الإجمالي الكلي للأصناف (للجرد والمطابقة):
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

    {{-- Inventory Daily Reports Modal --}}
    @if($showDailyReportModal)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1050; display: flex; justify-content: center; align-items: center; padding: 1rem; backdrop-filter: blur(5px);">
        <div style="background: var(--surface-color, #1e1e1e); width: 100%; max-width: 900px; max-height: 90vh; border-radius: 12px; box-shadow: 0 15px 50px rgba(0,0,0,0.5); display: flex; flex-direction: column; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); animation: modalFadeIn 0.3s ease;">
            <div style="padding: 1.5rem; background: linear-gradient(135deg, rgba(30,136,229,0.1), rgba(13,71,161,0.1)); border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <h3 style="margin: 0; color: #1E88E5; display: flex; align-items: center; gap: 10px; font-size: 1.3rem;">
                    <i class="fa-solid fa-file-contract"></i> تقارير إغلاق الجرد
                </h3>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 5px; background: rgba(0, 0, 0, 0.1); border-radius: 6px; padding: 4px; border: 1px solid rgba(0, 0, 0, 0.2);">
                        <span style="font-size: 0.85rem; color: #aaa; padding-right: 5px;">بحث بتاريخ:</span>
                        <input type="date" wire:model.live="filterReportDate" class="form-control" style="width: 130px; padding: 0.3rem 0.5rem; font-size: 0.85rem; background: #000; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px;">
                        @if($filterReportDate)
                            <button wire:click="$set('filterReportDate', '')" style="background: none; border: none; color: #F44336; cursor: pointer; padding: 4px;" title="مسح التاريخ">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        @endif
                    </div>
                    <button wire:click="toggleReportModal" style="background: none; border: none; color: #aaa; font-size: 1.5rem; cursor: pointer; padding: 0; line-height: 1; transition: color 0.2s; margin-right: 15px;" onmouseover="this.style.color='#F44336'" onmouseout="this.style.color='#aaa'">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            
            <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                @if(isset($dailyReports) && $dailyReports->count() > 0)
                    <div style="overflow-x: auto;">
                        <table class="data-table" style="min-width: 700px; width: 100%;">
                            <thead>
                                <tr>
                                    <th># الإغلاق</th>
                                    <th>حالة الجرد</th>
                                    <th>الفترة الزمنية (الفتح - الإغلاق)</th>
                                    <th>إجمالي المضاف اليوم</th>
                                    <th>المباع اليوم</th>
                                    <th>التالف اليوم</th>
                                    <th>بواسطة</th>
                                    <th style="width: 150px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $shiftStartTime = \App\Models\Setting::where('key', 'shift_start_time')->value('value') ?? '16:00';
                                    $shiftEndTime = \App\Models\Setting::where('key', 'shift_end_time')->value('value') ?? '02:00';
                                    $formattedStartTime = \Carbon\Carbon::parse($shiftStartTime)->format('h:i A');
                                    $formattedEndTime = \Carbon\Carbon::parse($shiftEndTime)->format('h:i A');
                                @endphp
                                {{-- Active Day Row --}}
                                @php
                                    $activeStockIn = $totalStockInShisha + $totalStockInAcc;
                                    $activeConsumed = $totalConsumedShisha + $totalConsumedAcc;
                                    $activeWaste = $totalWasteShisha + $totalWasteAcc;
                                @endphp
                                @if($activeStockIn > 0 || $activeConsumed > 0 || $activeWaste > 0)
                                    <tr style="background: rgba(76, 175, 80, 0.1); border-bottom: 2px solid #4CAF50;">
                                        <td style="padding: 1rem;">-</td>
                                        <td style="padding: 1rem;">
                                            <span style="background: rgba(76, 175, 80, 0.2); color: #4CAF50; padding: 4px 10px; border-radius: 4px; font-weight: bold;">
                                                <i class="fa-solid fa-door-open"></i> قيد العمل (اليوم)
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; color: var(--text-secondary); font-weight: bold;">
                                            {{ now()->format('Y-m-d') }} <span style="color: var(--primary-color);">{{ $formattedStartTime }}</span>
                                        </td>
                                        <td style="padding: 1rem; color: var(--success-color); font-weight: bold;">{{ number_format($activeStockIn) }}</td>
                                        <td style="padding: 1rem; color: var(--warning-color); font-weight: bold;">{{ number_format($activeConsumed) }}</td>
                                        <td style="padding: 1rem; color: var(--danger-color); font-weight: bold;">{{ number_format($activeWaste) }}</td>
                                        <td style="padding: 1rem;">{{ auth()->user()->name }}</td>
                                        <td style="padding: 1rem; color: var(--danger-color); font-weight: bold;">متوقع: {{ $formattedEndTime }}</td>
                                    </tr>
                                @endif

                                @foreach($dailyReports as $report)
                                @php
                                    $openDate = $report->opened_at ? $report->opened_at->format('Y-m-d') : $report->created_at->format('Y-m-d');
                                    $closeDate = $report->closed_at ? $report->closed_at->format('Y-m-d') : $report->created_at->format('Y-m-d');
                                    $openTime = $report->opened_at ? $report->opened_at->format('h:i A') : $formattedStartTime;
                                    $closeTime = $report->closed_at ? $report->closed_at->format('h:i A') : $formattedEndTime;
                                @endphp
                                <tr wire:key="inv-report-{{ $report->id }}">
                                    <td style="font-weight: bold; color: #1E88E5;">{{ $report->id }}</td>
                                    <td>
                                        <span style="background: rgba(244, 67, 54, 0.1); color: #F44336; padding: 4px 10px; border-radius: 4px; font-weight: bold;">مغلق</span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem; color: #888; font-weight: bold;">
                                            مـن: {{ $openDate }} <span style="color: var(--primary-color);">{{ $openTime }}</span>
                                        </div>
                                        <div style="font-size: 0.85rem; color: #888; font-weight: bold;">
                                            إلى: {{ $closeDate }} <span style="color: var(--danger-color);">{{ $closeTime }}</span>
                                        </div>
                                    </td>
                                    <td style="font-weight: bold; font-size: 1.1rem; color: #4CAF50;">{{ number_format($report->total_stock_in) }}</td>
                                    <td style="color: #FF9800; font-weight: bold; font-size: 1.1rem;">{{ number_format($report->total_consumed) }}</td>
                                    <td style="color: #F44336; font-weight: bold; font-size: 1.1rem;">{{ number_format($report->total_waste) }}</td>
                                    <td>{{ $report->user->name ?? 'غير محدد' }}</td>
                                    <td>
                                        <a href="/invoice/inventory-print-thermal?date={{ $report->created_at->format('Y-m-d') }}" target="_blank" class="btn btn-sm" style="background: #1E88E5; color: #fff; border: none; padding: 0.4rem 0.8rem; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                            <i class="fa-solid fa-receipt"></i> طباعة حراري
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem 1rem; color: #aaa;">
                        <i class="fa-solid fa-folder-open fa-3x" style="opacity: 0.2; margin-bottom: 1rem; display: block;"></i>
                        <h4 style="margin: 0; font-weight: normal;">لا توجد تقارير إغلاق مطابقة</h4>
                    </div>
                @endif
            </div>
            
            <div style="padding: 1rem 1.5rem; background: rgba(0,0,0,0.2); border-top: 1px solid rgba(255,255,255,0.1); text-align: left;">
                <button wire:click="toggleReportModal" class="btn" style="background: rgba(255,255,255,0.1); color: #fff; border: none; padding: 0.6rem 1.5rem; border-radius: 6px; font-weight: bold;">
                    إغلاق النافذة
                </button>
            </div>
        </div>
    </div>
    <style>
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endif

    {{-- No JS print functions needed - links are rendered server-side with correct dates --}}
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
            .grid-cols-4 .card i { display: none !important; }
            .grid-cols-4 .card h3 { margin: 0 !important; font-size: 11px !important; color: #333 !important; }
            .grid-cols-4 .card p { margin: 0 !important; font-size: 14px !important; font-weight: bold !important; color: #000 !important; }
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
            .data-table th { background: #f0f0f0 !important; font-weight: bold !important; }
        }
    </style>
    <style>
        .hide-prints .secret-print-target {
            display: none !important;
        }
    </style>
    <script>
        let secretClickCount = 0;
        let secretClickResetTimer;
        let hidePrintTimer;

        // Hide print buttons by default on load
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('hide-prints');
        });

        function handleSecretClick() {
            secretClickCount++;
            
            // Reset click count after 1 second if not clicked again rapidly
            clearTimeout(secretClickResetTimer);
            secretClickResetTimer = setTimeout(() => {
                secretClickCount = 0;
            }, 1000);
            
            // If clicked 3 times rapidly
            if (secretClickCount >= 3) {
                // Show print buttons
                document.body.classList.remove('hide-prints');
                secretClickCount = 0; // Reset counter
                
                // Clear any existing hide timer
                clearTimeout(hidePrintTimer);
                
                // Automatically hide after 30 seconds
                hidePrintTimer = setTimeout(() => {
                    document.body.classList.add('hide-prints');
                }, 30000);
            }
        }
    </script>
</div>
