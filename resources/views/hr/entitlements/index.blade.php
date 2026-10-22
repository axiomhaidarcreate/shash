<x-layouts.admin>
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1><i class="fa-solid fa-file-invoice" style="color: #9C27B0;"></i> الاستحقاقات والخصومات</h1>
            <p>إدارة حوافز وخصومات الموظفين</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="document.getElementById('incentiveModal').style.display='flex'" class="btn" style="background: linear-gradient(135deg, #4CAF50, #2E7D32); color:white;">
                <i class="fa-solid fa-plus me-1"></i> إضافة حافز
            </button>
            <button onclick="document.getElementById('deductionModal').style.display='flex'" class="btn" style="background: linear-gradient(135deg, #F44336, #C62828); color:white;">
                <i class="fa-solid fa-minus me-1"></i> إضافة خصم
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); color: #4CAF50; border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @foreach(['error', 'error_password'] as $err)
        @if(session($err))
            <div class="alert alert-danger" style="background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); color: #F44336; border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session($err) }}
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="alert alert-danger" style="background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); color: #F44336; border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem;">
            <ul style="margin:0; padding-right:1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search / Filter -->
    <div class="card mb-4" style="border: 1px solid rgba(156, 39, 176, 0.2);">
        <h6 class="fw-bold mb-3" style="color: #9C27B0;"><i class="fa-solid fa-filter me-2"></i> فلاتر البحث</h6>
        <form action="{{ route('hr.entitlements.index') }}" method="GET" class="row align-items-end" style="gap: 0;">
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold" style="font-size:0.8rem;">الموظف</label>
                <select name="employee_id" class="form-control">
                    <option value="">الكل</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name_ar ?? $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold" style="font-size:0.8rem;">من تاريخ</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold" style="font-size:0.8rem;">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
            </div>
            <div class="col-md-3 mb-3 d-flex gap-2">
                <button type="submit" class="btn flex-grow-1" style="background: linear-gradient(135deg, #9C27B0, #6A1B9A); color:white;">
                    <i class="fa-solid fa-search me-1"></i> بحث
                </button>
                <button type="submit" name="print" value="1" formtarget="_blank" class="btn" style="background: #333; color:white;" title="طباعة">
                    <i class="fa-solid fa-print"></i>
                </button>
                <a href="{{ route('hr.entitlements.index') }}" class="btn btn-outline" style="border-color: rgba(255,255,255,0.2); color: var(--text-secondary);">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Salary Counters Summary -->
    @if(isset($salaryCounters))
    <div style="margin-bottom: 2rem;">
        <h5 style="font-weight:800; margin-bottom:1rem; color: #9C27B0;"><i class="fa-solid fa-chart-pie me-2"></i> ملخص الخصومات المعلقة (لم تُخصم من الرواتب بعد)</h5>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <!-- Withdrawals -->
            <a href="{{ route('hr.entitlements.index', array_merge(request()->query(), ['type_filter' => 'withdrawals'])) }}" class="card text-center" style="text-decoration:none; border: 1px solid rgba(244,67,54,0.2); padding: 1.5rem 1rem; transition: all 0.3s ease;">
                <i class="fa-solid fa-money-bill-transfer" style="font-size:2rem; color: #F44336; margin-bottom: 0.8rem;"></i>
                <div style="font-size: 1.8rem; font-weight: 900; color: #F44336;" dir="ltr">{{ $salaryCounters['withdrawals'] }}</div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top:5px;">إجمالي السحوبات (ر.س)</div>
            </a>
            <!-- Loans -->
            <a href="{{ route('hr.entitlements.index', array_merge(request()->query(), ['type_filter' => 'loans'])) }}" class="card text-center" style="text-decoration:none; border: 1px solid rgba(255,152,0,0.2); padding: 1.5rem 1rem; transition: all 0.3s ease;">
                <i class="fa-solid fa-hand-holding-dollar" style="font-size:2rem; color: #FF9800; margin-bottom: 0.8rem;"></i>
                <div style="font-size: 1.8rem; font-weight: 900; color: #FF9800;" dir="ltr">{{ $salaryCounters['loans'] }}</div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top:5px;">متبقي القروض والسلف (ر.س)</div>
            </a>
            <!-- Absences -->
            <a href="{{ route('hr.entitlements.index', array_merge(request()->query(), ['type_filter' => 'absences'])) }}" class="card text-center" style="text-decoration:none; border: 1px solid rgba(233,30,99,0.2); padding: 1.5rem 1rem; transition: all 0.3s ease;">
                <i class="fa-solid fa-user-minus" style="font-size:2rem; color: #E91E63; margin-bottom: 0.8rem;"></i>
                <div style="font-size: 1.8rem; font-weight: 900; color: #E91E63;" dir="ltr">{{ $salaryCounters['absences_deduction'] }}</div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top:5px;">خصومات الغياب (ر.س)</div>
            </a>
            <!-- Lateness -->
            <a href="{{ route('hr.entitlements.index', array_merge(request()->query(), ['type_filter' => 'lateness'])) }}" class="card text-center" style="text-decoration:none; border: 1px solid rgba(156,39,176,0.2); padding: 1.5rem 1rem; transition: all 0.3s ease;">
                <i class="fa-solid fa-clock-rotate-left" style="font-size:2rem; color: #9C27B0; margin-bottom: 0.8rem;"></i>
                <div style="font-size: 1.8rem; font-weight: 900; color: #9C27B0;" dir="ltr">{{ $salaryCounters['lateness_deduction'] }}</div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top:5px;">خصومات التأخير (ر.س)</div>
            </a>
        </div>
        <style>
            .card.text-center:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        </style>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card">
        <h5 style="font-weight:800; margin-bottom:1rem; color: #9C27B0;"><i class="fa-solid fa-list me-2"></i> سجل الاستحقاقات والخصومات</h5>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>نوع الاستحقاق</th>
                        <th>النوع / المسمى</th>
                        <th style="width: 25%;">السبب</th>
                        <th>التاريخ والوقت</th>
                        <th>المبلغ</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entitlements as $ent)
                    <tr>
                        <td style="font-weight:700;">
                            {{ $ent->employee?->full_name_ar ?? $ent->employee?->full_name }}
                        </td>
                        <td>
                            @if(isset($isUnified) && $isUnified)
                                <span class="badge" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">{{ $ent->type_badge }}</span>
                            @else
                                @if($ent->type == 'incentive')
                                    <span class="badge" style="background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.3);">حافز</span>
                                @else
                                    <span class="badge" style="background: rgba(244,67,54,0.15); color: #F44336; border: 1px solid rgba(244,67,54,0.3);">خصم</span>
                                @endif
                            @endif
                        </td>
                        <td style="font-weight:bold; color:var(--text-primary);">{{ $ent->title }}</td>
                        <td style="color:var(--text-secondary);" title="{{ $ent->reason }}">{{ $ent->reason ?? '-' }}</td>
                        <td style="color:var(--text-secondary);">
                            <div dir="ltr" style="font-weight:bold;">{{ \Carbon\Carbon::parse($ent->date)->format('Y-m-d') }}</div>
                            <div style="font-size:0.8rem;" dir="ltr">{{ $ent->time ? \Carbon\Carbon::parse($ent->time)->format('H:i') : '' }}</div>
                        </td>
                        <td dir="ltr" style="font-weight:900; font-size:1.1rem; {{ (isset($isUnified) && $isUnified) ? ($ent->type_badge == 'حافز مباشر' ? 'color:#4CAF50;' : 'color:#F44336;') : ($ent->type == 'incentive' ? 'color:#4CAF50;' : 'color:#F44336;') }}">
                            {{ number_format($ent->amount, 2) }}
                        </td>
                        <td class="text-center">
                            @if(isset($isUnified) && $isUnified && !$ent->is_editable)
                                <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">مُضاف تلقائياً</span>
                            @else
                                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'manager', 'accountant']))
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <button type="button" onclick="editEntitlement({{ json_encode((isset($isUnified) && $isUnified) ? $ent->original_model : $ent) }})" class="btn btn-sm btn-warning" title="تعديل">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <form action="{{ route('hr.entitlements.destroy', (isset($isUnified) && $isUnified) ? $ent->id : $ent->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')" style="display:inline-block; margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span style="font-size:0.8rem; color:var(--text-secondary);">لا يوجد صلاحية</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color: var(--text-secondary);">لا توجد بيانات ليتم عرضها</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($entitlements->count() > 0)
                @php
                    if (isset($isUnified) && $isUnified) {
                        $totalIncentives = $entitlements->where('type_badge', 'حافز مباشر')->sum('amount');
                        $totalDeductions = $entitlements->where('type_badge', '!=', 'حافز مباشر')->sum('amount');
                    } else {
                        $totalIncentives = $entitlements->where('type', 'incentive')->sum('amount');
                        $totalDeductions = $entitlements->where('type', 'deduction')->sum('amount');
                    }
                    $net = $totalIncentives - $totalDeductions;
                @endphp
                <tfoot>
                    <tr style="background: rgba(255,255,255,0.05); border-top: 2px solid var(--border-color);">
                        <td colspan="5" style="font-weight:bold; color: #9C27B0;">المجموع الكلي</td>
                        <td colspan="2">
                            <div style="display:flex; flex-direction:column; gap:5px; font-size:0.9rem;">
                                <div style="display:flex; justify-content:space-between;">
                                    <span style="color:#4CAF50; font-weight:bold;">حوافز:</span>
                                    <span style="color:#4CAF50; font-weight:bold;" dir="ltr">{{ number_format($totalIncentives, 2) }}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between;">
                                    <span style="color:#F44336; font-weight:bold;">خصومات:</span>
                                    <span style="color:#F44336; font-weight:bold;" dir="ltr">{{ number_format($totalDeductions, 2) }}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; border-top: 1px solid rgba(255,255,255,0.1); padding-top:5px; margin-top:2px;">
                                    <span style="color:var(--text-primary); font-weight:900;">الصافي:</span>
                                    <span style="font-weight:900; font-size:1.1rem; {{ $net >= 0 ? 'color:#4CAF50;' : 'color:#F44336;' }}" dir="ltr">{{ number_format($net, 2) }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div class="mt-4" dir="ltr">
            {{ $entitlements->links() }}
        </div>
    </div>

    <!-- Modals CSS -->
    <style>
        .custom-modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.7); backdrop-filter: blur(5px);
            z-index: 9998; display: none; align-items: center; justify-content: center;
        }
        .custom-modal {
            background: var(--surface-color); border-radius: 20px;
            width: 95%; max-width: 500px; max-height: 90vh; overflow-y: auto;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
            animation: modalFadeIn 0.3s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-header {
            padding: 1.5rem; border-bottom: 1px solid var(--border-color);
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-body { padding: 1.5rem; }
        .close-btn { background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; }
    </style>

    <!-- Incentive Modal -->
    <div id="incentiveModal" class="custom-modal-overlay">
        <div class="custom-modal" style="border: 1px solid rgba(76,175,80,0.3);">
            <div class="modal-header">
                <h5 class="m-0 fw-bold" style="color: #4CAF50;"><i class="fa-solid fa-plus-circle me-2"></i> إضافة حافز</h5>
                <button type="button" class="close-btn" onclick="document.getElementById('incentiveModal').style.display='none'">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('hr.entitlements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="incentive">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">الموظف <span class="text-danger">*</span></label>
                        <select name="employee_id" required class="form-control">
                            <option value="">اختر الموظف...</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name_ar ?? $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">نوع الحافز <span class="text-danger">*</span></label>
                        <select name="title" required class="form-control">
                            <option value="">اختر النوع...</option>
                            @foreach($incentiveTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" required class="form-control" placeholder="0.00">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">التاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">الوقت</label>
                            <input type="time" name="time" value="{{ date('H:i') }}" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">رقم السند (اختياري)</label>
                        <input type="text" name="receipt_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <input type="checkbox" id="hasAttachIncentive" style="width: 18px; height: 18px; cursor: pointer;" onchange="document.getElementById('attachIncentiveBox').style.display = this.checked ? 'block' : 'none'">
                            <label for="hasAttachIncentive" class="form-label fw-bold mb-0" style="cursor: pointer; color: var(--primary-color);">إرفاق ملف / صورة (اختياري)</label>
                        </div>
                        <div id="attachIncentiveBox" style="display: none;">
                            <input type="file" name="attachment" class="form-control">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">السبب</label>
                        <textarea name="reason" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn" style="background: #333; color: white; border: none; min-width: 120px;" onclick="printDraftVoucher('incentive')"><i class="fa-solid fa-print me-1"></i> طباعة السند</button>
                        <button type="submit" class="btn flex-grow-1" style="background: linear-gradient(135deg, #4CAF50, #2E7D32); color:white;">حفظ وإضافة الحافز</button>
                        <button type="button" class="btn btn-outline" style="border-color:var(--border-color); color:var(--text-secondary);" onclick="document.getElementById('incentiveModal').style.display='none'">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Deduction Modal -->
    <div id="deductionModal" class="custom-modal-overlay">
        <div class="custom-modal" style="border: 1px solid var(--border-color);">
            <div class="modal-header">
                <h5 class="m-0 fw-bold text-primary"><i class="fa-solid fa-minus-circle me-2"></i> إضافة خصم</h5>
                <button type="button" class="close-btn" onclick="document.getElementById('deductionModal').style.display='none'">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('hr.entitlements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="deduction">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">الموظف <span class="text-danger">*</span></label>
                        <select name="employee_id" required class="form-control">
                            <option value="">اختر الموظف...</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name_ar ?? $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">نوع الخصم <span class="text-danger">*</span></label>
                        <select name="title" required class="form-control">
                            <option value="">اختر النوع...</option>
                            @foreach($deductionTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" required class="form-control" placeholder="0.00">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">التاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">الوقت</label>
                            <input type="time" name="time" value="{{ date('H:i') }}" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">رقم السند (اختياري)</label>
                        <input type="text" name="receipt_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <input type="checkbox" id="hasAttachDeduction" style="width: 18px; height: 18px; cursor: pointer;" onchange="document.getElementById('attachDeductionBox').style.display = this.checked ? 'block' : 'none'">
                            <label for="hasAttachDeduction" class="form-label fw-bold mb-0" style="cursor: pointer; color: var(--primary-color);">إرفاق ملف / صورة (اختياري)</label>
                        </div>
                        <div id="attachDeductionBox" style="display: none;">
                            <input type="file" name="attachment" class="form-control">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">السبب</label>
                        <textarea name="reason" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn" style="background: #333; color: white; border: none; min-width: 120px;" onclick="printDraftVoucher('deduction')"><i class="fa-solid fa-print me-1"></i> طباعة السند</button>
                        <button type="submit" class="btn flex-grow-1" style="background: var(--primary-color); color:white;">حفظ وإضافة الخصم</button>
                        <button type="button" class="btn btn-outline" style="border-color:var(--border-color); color:var(--text-secondary);" onclick="document.getElementById('deductionModal').style.display='none'">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="custom-modal-overlay">
        <div class="custom-modal" style="border: 1px solid rgba(156,39,176,0.3);">
            <div class="modal-header">
                <h5 class="m-0 fw-bold" style="color: #9C27B0;"><i class="fa-solid fa-pen-to-square me-2"></i> تعديل الاستحقاق</h5>
                <button type="button" class="close-btn" onclick="document.getElementById('editModal').style.display='none'">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">النوع / المسمى <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title" name="title" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" id="edit_amount" name="amount" required class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">التاريخ <span class="text-danger">*</span></label>
                            <input type="date" id="edit_date" name="date" required class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">الوقت</label>
                            <input type="time" id="edit_time" name="time" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">رقم السند (اختياري)</label>
                            <input type="text" id="edit_receipt_number" name="receipt_number" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">مرفق جديد</label>
                            <input type="file" name="attachment" class="form-control">
                            <div id="edit_attachment_link" class="mt-2" style="display:none;">
                                <a href="#" target="_blank" style="font-size:0.8rem; color:#9C27B0; text-decoration:none;"><i class="fa-solid fa-link"></i> عرض المرفق الحالي</a>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">السبب</label>
                        <textarea id="edit_reason" name="reason" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn flex-grow-1" style="background: linear-gradient(135deg, #9C27B0, #6A1B9A); color:white;">حفظ التعديلات</button>
                        <button type="button" class="btn btn-outline" style="border-color:var(--border-color); color:var(--text-secondary);" onclick="document.getElementById('editModal').style.display='none'">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editEntitlement(ent) {
            document.getElementById('edit_title').value = ent.title;
            document.getElementById('edit_amount').value = parseFloat(ent.amount).toFixed(2);
            document.getElementById('edit_date').value = ent.date.split('T')[0];
            
            if (ent.time) {
                try {
                    let timeParts = ent.time.split('T');
                    if (timeParts.length > 1) {
                        document.getElementById('edit_time').value = timeParts[1].substring(0, 5);
                    } else {
                        document.getElementById('edit_time').value = ent.time.substring(0, 5);
                    }
                } catch(e) {
                    document.getElementById('edit_time').value = '';
                }
            } else {
                document.getElementById('edit_time').value = '';
            }
            
            document.getElementById('edit_reason').value = ent.reason || '';
            document.getElementById('edit_receipt_number').value = ent.receipt_number || '';
            
            const attachmentLink = document.getElementById('edit_attachment_link');
            if (ent.attachment) {
                attachmentLink.style.display = 'block';
                attachmentLink.querySelector('a').href = ent.attachment;
            } else {
                attachmentLink.style.display = 'none';
            }
            
            document.getElementById('editForm').action = `{{ url('hr/entitlements') }}/${ent.id}`;
            document.getElementById('editModal').style.display = 'flex';
        }

        function printDraftVoucher(type) {
            const modalId = type === 'incentive' ? 'incentiveModal' : 'deductionModal';
            const modal = document.getElementById(modalId);
            
            const empSelect = modal.querySelector('select[name="employee_id"]');
            const empName = empSelect.options[empSelect.selectedIndex]?.text || '';
            
            const titleSelect = modal.querySelector('select[name="title"]');
            const titleText = titleSelect.options[titleSelect.selectedIndex]?.text || '';
            
            const amount = modal.querySelector('input[name="amount"]').value || '0';
            const date = modal.querySelector('input[name="date"]').value || '';
            const reason = modal.querySelector('textarea[name="reason"]').value || '';
            const receipt = modal.querySelector('input[name="receipt_number"]').value || '';
            
            if(!empSelect.value || !titleSelect.value || !amount) {
                alert('يرجى تعبئة الموظف والنوع والمبلغ قبل الطباعة.');
                return;
            }

            const voucherTitle = type === 'incentive' ? 'سند استحقاق / مكافأة' : 'سند خصم';
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html dir="rtl">
                <head>
                    <title>طباعة مسودة السند</title>
                    <style>
                        body { font-family: 'Tajawal', Arial, sans-serif; padding: 40px; color: #000; }
                        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
                        .row { margin-bottom: 15px; font-size: 18px; }
                        .label { font-weight: bold; width: 150px; display: inline-block; }
                        .val { border-bottom: 1px dashed #999; display: inline-block; min-width: 300px; }
                        .signature { margin-top: 60px; display: flex; justify-content: space-between; font-weight: bold; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>${voucherTitle}</h2>
                        ${receipt ? `<p>رقم السند: ${receipt}</p>` : ''}
                    </div>
                    <div class="row"><span class="label">اسم الموظف:</span> <span class="val">${empName}</span></div>
                    <div class="row"><span class="label">النوع / المسمى:</span> <span class="val">${titleText}</span></div>
                    <div class="row"><span class="label">المبلغ:</span> <span class="val">${amount}</span></div>
                    <div class="row"><span class="label">التاريخ:</span> <span class="val">${date}</span></div>
                    <div class="row"><span class="label">البيان / السبب:</span> <span class="val">${reason}</span></div>
                    
                    <div class="signature">
                        <div style="text-align: center;">توقيع الموظف<br><br>_________________</div>
                        <div style="text-align: center;">توقيع المسؤول<br><br>_________________</div>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => { printWindow.print(); printWindow.close(); }, 500);
        }
    </script>
</x-layouts.admin>
