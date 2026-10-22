<x-layouts.admin>
    
    <!-- Page Header & Actions -->
        <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/hr/portal') }}" class="btn btn-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary); background: rgba(0,0,0,0.2);">
            <i class="fa-solid fa-arrow-right"></i> عودة لبوابة الموارد البشرية
        </a>
    </div>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="text-2xl font-bold text-primary">الطلبات والاعتمادات</h2>
            <p class="text-secondary mt-1">إدارة ومراجعة كافة طلبات الموظفين المعلقة والسابقة.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-100 font-bold mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Stats Grid -->
    @php
        $typeMap = [
            'leave' => 'إجازة',
            'annual' => 'إجازة سنوية',
            'sick' => 'إجازة مرضية',
            'unpaid' => 'إجازة غير مدفوعة',
            'loan' => 'قرض',
            'withdrawal' => 'سحب',
            'سحب' => 'سحب',
            'resignation' => 'استقالة'
        ];
    @endphp
    <div class="grid grid-cols-3 gap-4 mb-8">
        <!-- Pendings Card (Active Style) -->
        <div class="card" style="background: linear-gradient(135deg, var(--primary-color), #1565C0); color: white; display: flex; flex-direction: column; justify-content: space-between; border: none; padding: 1.5rem; position: relative;">
            <div style="position: absolute; top: 0; right: 0; background: rgba(255,255,255,0.15); padding: 4px 12px; border-bottom-left-radius: var(--radius-md); font-size: 0.75rem;">هذا الأسبوع</div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div style="text-align: center;">
                <h3 style="font-size: 0.85rem; font-weight: 600; opacity: 0.9; margin-bottom: 0.2rem;">الطلبات المعلقة</h3>
                <div style="font-size: 1.8rem; font-weight: 800;">{{ $pendingCount }}</div>
            </div>
        </div>

        <!-- Approved -->
        <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; align-items: center; text-align: center;">
            <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(76, 175, 80, 0.15); color: var(--success-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 0.5rem;">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.2rem;">تمت الموافقة</h3>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--success-color);">{{ $approvedCount }}</div>
        </div>

        <!-- Denied -->
        <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; align-items: center; text-align: center;">
            <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: rgba(244, 67, 54, 0.15); color: var(--danger-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 0.5rem;">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <h3 style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.2rem;">طلبات مرفوضة</h3>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--danger-color);">{{ $rejectedCount }}</div>
        </div>
    </div>

    <!-- Main Two-Column Content -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem; @media (min-width: 1024px) { flex-direction: row; }">
        
        <!-- Right Column: Requests List -->
        <div style="flex: 1; min-width: 0;">
            
            <!-- Tabs -->
            <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1rem; overflow-x: auto; padding-bottom: 0.5rem;">
                <!-- Leaves Tab -->
                <a href="?type=leave" style="text-decoration: none; font-weight: 600; padding: 0.5rem 1rem; {{ $type === 'leave' || $type === 'all' ? 'color: var(--primary-color); border-bottom: 2px solid var(--primary-color);' : 'color: var(--text-secondary);' }}">
                    <i class="fa-solid fa-plane"></i> الإجازات
                    <span class="badge" style="background: {{ $type === 'leave' || $type === 'all' ? 'rgba(var(--primary-rgb), 0.1)' : 'var(--border-color)' }}; color: {{ $type === 'leave' || $type === 'all' ? 'var(--primary-color)' : 'var(--text-secondary)' }}; margin-right: 0.5rem;">{{ $leavesCount }}</span>
                </a>
                <!-- Loans Tab -->
                <a href="?type=loan" style="text-decoration: none; font-weight: 600; padding: 0.5rem 1rem; {{ $type === 'loan' ? 'color: var(--primary-color); border-bottom: 2px solid var(--primary-color);' : 'color: var(--text-secondary);' }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i> القروض
                    <span class="badge" style="background: {{ $type === 'loan' ? 'rgba(var(--primary-rgb), 0.1)' : 'var(--border-color)' }}; color: {{ $type === 'loan' ? 'var(--primary-color)' : 'var(--text-secondary)' }}; margin-right: 0.5rem;">{{ $loansCount }}</span>
                </a>
                <!-- Withdrawals Tab -->
                <a href="?type=withdrawal" style="text-decoration: none; font-weight: 600; padding: 0.5rem 1rem; {{ $type === 'withdrawal' ? 'color: var(--primary-color); border-bottom: 2px solid var(--primary-color);' : 'color: var(--text-secondary);' }}">
                    <i class="fa-solid fa-money-bill-transfer"></i> السحوبات
                    <span class="badge" style="background: {{ $type === 'withdrawal' ? 'rgba(var(--primary-rgb), 0.1)' : 'var(--border-color)' }}; color: {{ $type === 'withdrawal' ? 'var(--primary-color)' : 'var(--text-secondary)' }}; margin-right: 0.5rem;">{{ $withdrawalsCount ?? 0 }}</span>
                </a>
                <!-- Resignations Tab -->
                <a href="?type=resignation" style="text-decoration: none; font-weight: 600; padding: 0.5rem 1rem; {{ $type === 'resignation' ? 'color: var(--primary-color); border-bottom: 2px solid var(--primary-color);' : 'color: var(--text-secondary);' }}">
                    <i class="fa-solid fa-door-open"></i> الاستقالات
                    <span class="badge" style="background: {{ $type === 'resignation' ? 'rgba(var(--primary-rgb), 0.1)' : 'var(--border-color)' }}; color: {{ $type === 'resignation' ? 'var(--primary-color)' : 'var(--text-secondary)' }}; margin-right: 0.5rem;">{{ $resignationsCount }}</span>
                </a>
            </div>

            <!-- List Body -->
            <div class="card" style="padding: 0; overflow: hidden;">
                <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">الموظف</th>
                            <th scope="col">التاريخ</th>
                            <th scope="col">النوع</th>
                            <th scope="col">السبب</th>
                            <th scope="col" style="text-align: left;">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <!-- Request (Active if selected) -->
                        <tr style="cursor: pointer; {{ isset($selectedRequest) && $selectedRequest->id === $req->id ? 'background: rgba(var(--primary-rgb), 0.05); border-right: 4px solid var(--primary-color);' : 'transition: background 0.2s;' }}" onclick="window.location.href='?type={{ $type }}&selected={{$req->id}}'" onmouseover="this.style.backgroundColor='var(--surface-color-hover)'" onmouseout="this.style.backgroundColor='{{ isset($selectedRequest) && $selectedRequest->id === $req->id ? 'rgba(var(--primary-rgb), 0.05)' : 'transparent' }}'">
                            <td style="display: flex; align-items: center; gap: 12px; padding: 12px;">
                                <img style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" src="{{ $req->employee->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($req->employee->full_name_ar ?? $req->employee->full_name ?? '').'&background=d4af37&color=000' }}" alt="Avatar">
                                <div>
                                    <div style="font-weight: 800; font-size: 0.95rem;">{{ $req->employee->full_name_ar ?? 'غير متوفر' }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $req->employee->department->name_ar ?? 'غير محدد' }}</div>
                                </div>
                            </td>
                            <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}</td>
                            <td>
                                <span class="badge" style="background: var(--primary-color); color: white;">{{ $typeMap[strtolower($req->leave_type ?? '')] ?? ($req->leave_type ?? 'إجازة') }}</span>
                            </td>
                            <td style="color: var(--text-secondary); font-size: 0.85rem; max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $req->reason }}">
                                {{ $req->reason }}
                            </td>
                            <td style="text-align: left;">
                                @if($req->status === 'pending')
                                    @if(auth()->user()->role === 'admin')
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                            <form action="{{ route('hr.requests.updateStatus', $req) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.6rem; color: var(--danger-color); border-color: var(--danger-color);" title="رفض">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('hr.requests.updateStatus', $req) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-success" style="padding: 0.3rem 0.6rem;" title="موافقة">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="badge badge-pending">بانتظار المدير</span>
                                    @endif
                                @elseif($req->status === 'approved')
                                    <span class="badge badge-active">معتمد</span>
                                @else
                                    <span class="badge" style="background: rgba(244, 67, 54, 0.1); color: var(--danger-color);">مرفوض</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                <i class="fa-solid fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                                لا يوجد طلبات.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- Left Column: Request Details Pane -->
        <div class="card" style="width: 100%; max-width: 400px; @media (max-width: 1024px) { max-width: 100%; }">
            <h3 style="font-weight: 800; text-align: center; font-size: 1.1rem; margin-bottom: 1.5rem; color: var(--primary-color);">تفاصيل الطلب</h3>
            
            @if(isset($selectedRequest))
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 1.5rem;">
                <img src="{{ $selectedRequest->employee->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($selectedRequest->employee->full_name_ar ?? $selectedRequest->employee->full_name ?? '').'&background=d4af37&color=000' }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--border-color); margin-bottom: 0.75rem;" alt="Avatar">
                <h4 style="font-weight: 800; font-size: 1.1rem; margin-bottom: 0.25rem;">{{ $selectedRequest->employee->full_name_ar ?? 'غير متوفر' }}</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary);">طلب {{ $typeMap[strtolower($selectedRequest->leave_type ?? '')] ?? ($selectedRequest->leave_type ?? 'إجازة') }}</p>
                <div style="margin-top: 0.5rem;">
                    @if($selectedRequest->status === 'approved')
                        <span class="badge badge-active">معتمد</span>
                    @elseif($selectedRequest->status === 'rejected')
                        <span class="badge" style="background: rgba(244,67,54,0.1); color: var(--danger-color);">مرفوض</span>
                    @else
                        <span class="badge badge-pending">بانتظار</span>
                    @endif
                </div>
            </div>

            @php
                $reqType = strtolower($selectedRequest->leave_type ?? '');
                $isLoan = in_array($reqType, ['loan', 'advance', 'قرض']) || str_contains($reqType, 'قرض');
                $isWithdrawal = in_array($reqType, ['withdrawal', 'سحب']) || str_contains($reqType, 'سحب');
            @endphp

            @if($isLoan)
                <!-- Loan Financial Breakdown -->
                <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1rem;">
                    <div style="font-size: 0.8rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">تفاصيل القرض</div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">المبلغ الإجمالي</span>
                        <strong style="font-size: 0.9rem;" dir="ltr">{{ number_format($selectedRequest->amount, 2) }} ر.س</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">مدة السداد</span>
                        <strong style="font-size: 0.9rem;">{{ $selectedRequest->duration_months }} شهر</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">القسط الشهري</span>
                        <strong style="font-size: 0.9rem; color: var(--info-color);" dir="ltr">{{ number_format($selectedRequest->monthly_installment, 2) }} ر.س</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">المسدد حتى الآن</span>
                        <strong style="font-size: 0.9rem; color: var(--success-color);" dir="ltr">{{ number_format($selectedRequest->paid_amount, 2) }} ر.س</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color);">
                        <span style="font-size: 0.8rem; font-weight: 800;">المتبقي</span>
                        <strong style="font-size: 1rem; color: {{ $selectedRequest->remaining_amount > 0 ? 'var(--danger-color)' : 'var(--success-color)' }};" dir="ltr">{{ number_format($selectedRequest->remaining_amount, 2) }} ر.س</strong>
                    </div>
                </div>
                <!-- Notes -->
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.8rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 0.5rem;">الملاحظات</div>
                    <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem; font-size: 0.85rem; line-height: 1.5;">{{ $selectedRequest->reason }}</div>
                </div>

            @elseif($isWithdrawal)
                <!-- Withdrawal Details -->
                <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1rem;">
                    <div style="font-size: 0.8rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">تفاصيل السحب</div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">مبلغ السحب</span>
                        <strong style="font-size: 1.1rem; color: var(--warning-color); font-weight: 800;" dir="ltr">{{ number_format($selectedRequest->amount, 2) }} ر.س</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">نوع الخصم</span>
                        <strong style="font-size: 0.8rem; color: var(--danger-color);">مبلغ مقطوع من الراتب</strong>
                    </div>
                </div>
                <div style="background: rgba(255, 152, 0, 0.1); border: 1px solid rgba(255, 152, 0, 0.2); border-radius: var(--radius-md); padding: 0.75rem; margin-bottom: 1rem;">
                    <p style="font-size: 0.8rem; color: var(--warning-color); font-weight: 800; margin-bottom: 0.25rem;">ملاحظة:</p>
                    <p style="font-size: 0.8rem; color: var(--warning-color); margin: 0; line-height: 1.4;">عند اعتماد هذا الطلب، سيتم خصم مبلغ السحب تلقائياً من راتب الموظف في نفس الشهر.</p>
                </div>
                <!-- Voucher button if approved -->
                @if($selectedRequest->status === 'approved')
                <a href="{{ route('hr.requests.voucher', $selectedRequest) }}" target="_blank" class="btn" style="width: 100%; margin-bottom: 1rem; background: var(--warning-color); color: white; border-color: var(--warning-color);">
                    <i class="fa-solid fa-print"></i> طباعة سند السحب
                </a>
                @endif
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.8rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 0.5rem;">سبب السحب</div>
                    <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem; font-size: 0.85rem; line-height: 1.5;">{{ $selectedRequest->reason }}</div>
                </div>

            @else
                <!-- Default: Leave / Resignation Dates -->
                <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; display: flex; align-items: center; justify-content: space-between; text-align: center; margin-bottom: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 0.25rem;">تاريخ البدء</div>
                        <div style="font-weight: 800; font-size: 0.9rem;">{{ \Carbon\Carbon::parse($selectedRequest->start_date)->format('d M') }}</div>
                    </div>
                    <div style="color: var(--border-color);">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 0.25rem;">الانتهاء</div>
                        <div style="font-weight: 800; font-size: 0.9rem;">{{ \Carbon\Carbon::parse($selectedRequest->end_date)->format('d M') }}</div>
                    </div>
                </div>
                <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="font-size: 0.85rem; font-weight: 800; color: var(--text-secondary);">المدة</div>
                    <div style="font-weight: 800; font-size: 0.95rem;">
                        {{ \Carbon\Carbon::parse($selectedRequest->start_date)->diffInDays(\Carbon\Carbon::parse($selectedRequest->end_date)) + 1 }} يوم
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.8rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 0.5rem;">الملاحظات</div>
                    <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; font-size: 0.85rem; line-height: 1.6;">
                        "{{ $selectedRequest->reason }}"
                    </div>
                </div>
            @endif

            <!-- Footer Actions -->
            @if($selectedRequest->status === 'pending')
                @if(auth()->user()->role === 'admin')
                <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                    <form action="{{ route('hr.requests.updateStatus', $selectedRequest) }}" method="POST" style="flex: 1;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn" style="width: 100%; background: rgba(244,67,54,0.1); color: var(--danger-color); border-color: rgba(244,67,54,0.2);">رفض الطلب</button>
                    </form>
                    <form action="{{ route('hr.requests.updateStatus', $selectedRequest) }}" method="POST" style="flex: 1;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-success" style="width: 100%;">اعتماد</button>
                    </form>
                </div>
                @else
                <div style="text-align: center; background: rgba(255, 152, 0, 0.1); border: 1px solid rgba(255, 152, 0, 0.2); border-radius: var(--radius-md); padding: 0.75rem; margin-top: 1rem;">
                    <p style="font-size: 0.8rem; font-weight: 800; color: var(--warning-color); margin-bottom: 0.25rem;">أدوات الاعتماد مخصصة للمدير فقط</p>
                    <p style="font-size: 0.75rem; color: var(--warning-color); margin: 0;">يمكنك عرض الطلبات ومتابعتها</p>
                </div>
                @endif
            @endif
            
            @else
            <div style="text-align: center; padding: 3rem 0; color: var(--text-secondary); font-size: 0.9rem;">
                <i class="fa-solid fa-hand-pointer" style="font-size: 2rem; opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                لم يتم تحديد طلب للمراجعة.
            </div>
            @endif
        </div>

    </div>

    </div>


</x-layouts.admin>


