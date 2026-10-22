<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);">
            <i class="fa-solid fa-gift" style="color: var(--primary-color);"></i> 
            إدارة التحايا (موظف الاستقبال)
        </h2>
        <a href="{{ route('reception') }}" class="btn btn-sm btn-outline">
            <i class="fa-solid fa-arrow-right"></i> العودة للاستقبال
        </a>
    </div>

    @if (session()->has('message'))
        <div style="background: linear-gradient(135deg, rgba(76,175,80,0.15), rgba(76,175,80,0.05)); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; text-align: center; font-weight: bold;">
            <i class="fa-solid fa-circle-check"></i> {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div style="background: linear-gradient(135deg, rgba(244,67,54,0.15), rgba(244,67,54,0.05)); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 0.8rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; text-align: center; font-weight: bold;">
            <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
        </div>
    @endif

    @error('balance')
        <div style="background: linear-gradient(135deg, rgba(244,67,54,0.15), rgba(244,67,54,0.05)); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 0.8rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; text-align: center; font-weight: bold;">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
        </div>
    @enderror

    {{-- Balance Cards: Opening, Used, Remaining --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: linear-gradient(135deg, rgba(255,193,7,0.15), rgba(255,193,7,0.05)); border: 1px solid #FFC107; border-radius: var(--radius-md); padding: 1.2rem; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;"><i class="fa-solid fa-vault" style="color: #FFC107;"></i> الرصيد الافتتاحي</div>
            <div style="font-size: 2rem; font-weight: 900; color: #FFC107;">{{ $openingBalance }}</div>
        </div>
        <div style="background: linear-gradient(135deg, rgba(244,67,54,0.15), rgba(244,67,54,0.05)); border: 1px solid #F44336; border-radius: var(--radius-md); padding: 1.2rem; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;"><i class="fa-solid fa-arrow-trend-down" style="color: #F44336;"></i> الرصيد المستخدم</div>
            <div style="font-size: 2rem; font-weight: 900; color: #F44336;">{{ $usedBalance }}</div>
        </div>
        <div style="background: linear-gradient(135deg, rgba(76,175,80,0.15), rgba(76,175,80,0.05)); border: 1px solid #4CAF50; border-radius: var(--radius-md); padding: 1.2rem; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;"><i class="fa-solid fa-wallet" style="color: #4CAF50;"></i> الرصيد المتبقي</div>
            <div style="font-size: 2rem; font-weight: 900; color: #4CAF50;">{{ $remainingBalance }}</div>
        </div>
    </div>

    <div class="layout-grid">

        <!-- Add Quota Section -->
        <div class="card">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-coins" style="color: var(--primary-color);"></i> شحن رصيد لموظف التحايا</h3>
            
            <div class="form-group">
                <label class="form-label">اختر الموظف</label>
                <select wire:model="quotaStaffId" class="form-control">
                    <option value="">-- اختر الموظف --</option>
                    @foreach($greetingStaff as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
                @error('quotaStaffId') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">عدد التحايا المراد إضافتها (رصيد)</label>
                <input type="number" wire:model="quotaQuantity" class="form-control" min="1" max="1000">
                @error('quotaQuantity') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <button wire:click="openPinModalForQuota" class="btn btn-primary" style="width: 100%; padding: 0.9rem; font-size: 1.1rem; margin-top: 1rem;">
                <i class="fa-solid fa-plus"></i> شحن الرصيد
            </button>
        </div>

        <!-- Staff Balances Section -->
        <div class="card" style="grid-column: span 2;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.1rem;"><i class="fa-solid fa-users" style="color: var(--primary-color);"></i> أرصدة الموظفين وإغلاق اليومية</h3>
            </div>
            
            @if($greetingStaff->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
                    @foreach($greetingStaff as $staff)
                        @php $bal = $staffBalances[$staff->id] ?? ['opening' => 0, 'used' => 0, 'remaining' => 0]; @endphp
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.2rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h4 style="margin: 0; color: var(--primary-color); font-size: 1.1rem;">
                                    <i class="fa-solid fa-user-tie"></i> {{ $staff->name }}
                                </h4>

                            </div>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                                <div style="background: rgba(255,193,7,0.1); border: 1px solid rgba(255,193,7,0.3); padding: 0.8rem; border-radius: var(--radius-sm); text-align: center;">
                                    <div style="font-size: 0.7rem; color: #FFC107; margin-bottom: 4px;">الرصيد الافتتاحي</div>
                                    <div style="font-size: 1.3rem; font-weight: bold; color: #fff;">{{ $bal['opening'] }}</div>
                                </div>
                                <div style="background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); padding: 0.8rem; border-radius: var(--radius-sm); text-align: center;">
                                    <div style="font-size: 0.7rem; color: #F44336; margin-bottom: 4px;">الرصيد المستخدم</div>
                                    <div style="font-size: 1.3rem; font-weight: bold; color: #fff;">{{ $bal['used'] }}</div>
                                </div>
                                <div style="background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); padding: 0.8rem; border-radius: var(--radius-sm); text-align: center;">
                                    <div style="font-size: 0.7rem; color: #4CAF50; margin-bottom: 4px;">الرصيد المتبقي</div>
                                    <div style="font-size: 1.3rem; font-weight: bold; color: #fff;">{{ $bal['remaining'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا يوجد موظفين تحايا مسجلين.</div>
            @endif
        </div>

        <!-- Open Accounts Grid -->
        <div class="card" style="grid-column: span 2;">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-list" style="color: var(--primary-color);"></i> الحسابات النشطة (جميع الموظفين)</h3>
            
            @if($openAccounts->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
                    @foreach($openAccounts as $account)
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.2rem; display: flex; flex-direction: column; gap: 10px; position: relative;">
                            
                            <div style="position: absolute; top: 10px; left: 10px; background: rgba(33,150,243,0.15); color: #2196F3; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: bold;">
                                موظف: {{ $account->staff->name ?? 'غير معروف' }}
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 10px;">
                                <div>
                                    <h4 style="margin: 0; color: var(--primary-color); font-size: 1.2rem;">{{ $account->guest_name }}</h4>
                                    <div style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 4px;">
                                        @if($account->table)
                                            <i class="fa-solid fa-border-all"></i> طاولة {{ $account->table->number }}
                                        @else
                                            <i class="fa-solid fa-user-clock"></i> بدون طاولة
                                        @endif
                                    </div>
                                    <div style="font-size: 0.8rem; color: #888; margin-top: 4px;">
                                        <i class="fa-regular fa-clock"></i> بدأ منذ {{ $account->created_at->diffForHumans() }}
                                    </div>
                                    @if($account->price)
                                        <div style="font-size: 0.8rem; color: #FF9800; margin-top: 4px;">
                                            <i class="fa-solid fa-tag"></i> السعر: {{ number_format($account->price, 2) }} ريال
                                        </div>
                                    @endif
                                </div>
                                <div style="text-align: center;">
                                    <div style="background: rgba(212, 175, 55, 0.2); border: 1px solid var(--primary-color); padding: 0.4rem 0.8rem; border-radius: var(--radius-sm);">
                                        <div style="font-size: 0.7rem; color: var(--primary-color);">إجمالي التحايا</div>
                                        <div style="font-size: 1.4rem; font-weight: bold; color: #fff;">{{ $account->total_greetings }}</div>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--success-color); margin-top: 4px; font-weight: bold;">
                                        {{ number_format($account->total_amount, 2) }} ريال
                                    </div>
                                </div>
                            </div>
                            
                            <hr style="border-color: rgba(255,255,255,0.05); margin: 5px 0;">
                            
                            {{-- Close Account Button (Reception Only) --}}
                            <button wire:click="closeAccount({{ $account->id }})" wire:confirm="هل انت متاكد من الغاء الحساب؟" class="btn" style="width: 100%; background: #F44336; color: #fff; padding: 0.6rem; font-weight: bold; margin-top: 5px;">
                                <i class="fa-solid fa-check-double"></i> إغلاق الحساب
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; color: var(--text-secondary); padding: 3rem 0;">
                    <i class="fa-solid fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color);"></i>
                    <p>لا توجد حسابات ضيوف مفتوحة للتحايا حالياً.</p>
                </div>
            @endif
        </div>
    </div>

        <!-- Daily Reports Grid -->
        <div class="card" style="margin-top: 1.5rem;">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-file-invoice" style="color: var(--primary-color);"></i> تقارير الإغلاق اليومي (اليوم)</h3>
            
            @if($dailyReports->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="data-table" style="min-width: 700px; width: 100%;">
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>أغلقه</th>
                                <th>الرصيد الافتتاحي</th>
                                <th>المباع</th>
                                <th>المتبقي</th>
                                <th>إجمالي المبلغ</th>
                                <th>وقت الفتح</th>
                                <th>وقت الإغلاق</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $shiftStartTime = \App\Models\Setting::where('key', 'shift_start_time')->value('value') ?? '16:00';
                                $shiftEndTime = \App\Models\Setting::where('key', 'shift_end_time')->value('value') ?? '02:00';
                                $formattedStartTime = \Carbon\Carbon::parse($shiftStartTime)->format('h:i A');
                                $formattedEndTime = \Carbon\Carbon::parse($shiftEndTime)->format('h:i A');
                            @endphp
                            @foreach($dailyReports as $dr)
                            @php
                                $openDate = $dr->opened_at ? $dr->opened_at->format('Y-m-d') : $dr->created_at->format('Y-m-d');
                                $closeDate = $dr->closed_at ? $dr->closed_at->format('Y-m-d') : $dr->created_at->format('Y-m-d');
                            @endphp
                            <tr>
                                <td style="font-weight: bold; color: var(--primary-color);">{{ $dr->staff->name ?? '-' }}</td>
                                <td>{{ $dr->manager->name ?? '-' }}</td>
                                <td>{{ $dr->opening_balance }}</td>
                                <td style="color: #F44336; font-weight: bold;">{{ $dr->used_balance }}</td>
                                <td style="color: #4CAF50; font-weight: bold;">{{ $dr->remaining_balance }}</td>
                                <td style="font-weight: bold;">{{ number_format($dr->total_amount, 2) }} ريال</td>
                                <td style="font-size: 0.85rem; color: #888; font-weight: bold;">
                                    {{ $openDate }} <span style="color: var(--primary-color);">{{ $formattedStartTime }}</span>
                                </td>
                                <td style="font-size: 0.85rem; color: #888; font-weight: bold;">
                                    {{ $closeDate }} <span style="color: var(--danger-color);">{{ $formattedEndTime }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; color: var(--text-secondary); padding: 1.5rem 0;">لا توجد تقارير إغلاق يومي لهذا اليوم.</div>
            @endif
        </div>

        <!-- Closed Accounts Grid -->
        <div class="card" style="margin-top: 1.5rem;">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-check-double" style="color: var(--primary-color);"></i> الحسابات المغلقة (اليوم)</h3>
            
            @if($closedAccounts->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="data-table" style="min-width: 800px; width: 100%;">
                        <thead>
                            <tr>
                                <th>رقم الحساب</th>
                                <th>اسم الضيف</th>
                                <th>الطاولة</th>
                                <th>الموظف</th>
                                <th>إجمالي التحايا</th>
                                <th>الإجمالي (ريال)</th>
                                <th>تاريخ الفتح</th>
                                <th>وقت الإغلاق</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($closedAccounts as $report)
                                <tr>
                                    <td>#{{ $report->id }}</td>
                                    <td style="font-weight: bold; color: var(--primary-color);">{{ $report->guest_name }}</td>
                                    <td>{{ $report->table ? $report->table->number : '-' }}</td>
                                    <td>{{ $report->staff ? $report->staff->name : '-' }}</td>
                                    <td style="font-weight: bold;">{{ $report->total_greetings }}</td>
                                    <td style="color: var(--success-color); font-weight: bold;">{{ number_format($report->total_amount, 2) }}</td>
                                    <td style="font-size: 0.85rem; color: #888;">{{ $report->created_at->format('Y-m-d h:i A') }}</td>
                                    <td style="font-size: 0.85rem; color: #888;">{{ $report->closed_at ? \Carbon\Carbon::parse($report->closed_at)->locale('ar')->translatedFormat('l, Y-m-d h:i A') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; color: var(--text-secondary); padding: 1.5rem 0;">لا توجد حسابات ضيوف مغلقة لهذا اليوم.</div>
            @endif
        </div>

    {{-- Close Account Report Modal (Print) --}}
    @if($showCloseReport && $closedAccountReport)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; justify-content: center; align-items: center; padding: 1rem;">
        <div style="background: var(--surface-color); border-radius: var(--radius-lg); width: 95%; max-width: 450px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); border: 1px solid var(--border-color); overflow: hidden;">
            
            {{-- Header --}}
            <div style="background: linear-gradient(135deg, var(--primary-color), #B8860B); padding: 1.2rem; text-align: center;">
                <h3 style="margin: 0; color: #fff; font-size: 1.3rem;"><i class="fa-solid fa-file-invoice"></i> تقرير إغلاق الحساب</h3>
            </div>

            {{-- Report Content --}}
            <div id="printable-close-report" style="padding: 1.5rem;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="font-size: 1.3rem; font-weight: bold; color: var(--primary-color); margin-bottom: 0.5rem;">{{ $closedAccountReport['guest_name'] }}</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary);">حساب التحايا - إغلاق</div>
                </div>

                <div style="display: grid; gap: 0.8rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">اسم الضيف:</span>
                        <span style="font-weight: bold;">{{ $closedAccountReport['guest_name'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الموظف:</span>
                        <span style="font-weight: bold;">{{ $closedAccountReport['staff_name'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الطاولة:</span>
                        <span style="font-weight: bold;">{{ $closedAccountReport['table'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">نوع التحية:</span>
                        <span style="font-weight: bold;">{{ $closedAccountReport['product'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">عدد التحايا:</span>
                        <span style="font-weight: bold; color: #2196F3; font-size: 1.1rem;">{{ $closedAccountReport['total_greetings'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">سعر الوحدة:</span>
                        <span style="font-weight: bold;">{{ number_format($closedAccountReport['price'], 2) }} ريال</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.8rem; background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); border-radius: var(--radius-sm);">
                        <span style="font-weight: bold; font-size: 1.1rem;">الإجمالي:</span>
                        <span style="font-weight: bold; color: var(--success-color); font-size: 1.2rem;">{{ number_format($closedAccountReport['total_amount'], 2) }} ريال</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">تاريخ الفتح:</span>
                        <span style="font-size: 0.9rem;">{{ $closedAccountReport['created_at'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">تاريخ الإغلاق:</span>
                        <span style="font-size: 0.9rem;">{{ $closedAccountReport['date'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                        <span style="color: var(--text-secondary);">وقت الإغلاق:</span>
                        <span style="font-size: 0.9rem;">{{ $closedAccountReport['time'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="padding: 1rem 1.5rem 1.5rem; display: flex; gap: 0.8rem;">
                <button onclick="printCloseReport()" class="btn" style="flex: 1; background: var(--primary-color); color: #fff; padding: 0.8rem; font-weight: bold; font-size: 1rem;">
                    <i class="fa-solid fa-print"></i> طباعة
                </button>
                <button wire:click="closeReportModal" class="btn" style="flex: 1; background: transparent; border: 1px solid var(--text-secondary); color: var(--text-secondary); padding: 0.8rem; font-size: 1rem;">
                    إغلاق
                </button>
            </div>
        </div>
    </div>

    @endif
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('trigger-print', () => {
                setTimeout(() => {
                    var reportContent = document.getElementById('printable-close-report');
                    if(reportContent) {
                        printCloseReport(reportContent.innerHTML);
                    }
                }, 500);
            });
        });

        function printCloseReport(reportContent = null) {
            if(!reportContent) {
                var el = document.getElementById('printable-close-report');
                if(!el) return;
                reportContent = el.innerHTML;
            }
            var printWindow = window.open('', '_blank', 'width=400,height=600');
            printWindow.document.write('<html dir="rtl"><head><title>تقرير إغلاق الحساب</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('@page { margin: 2mm; size: 80mm auto; }');
            printWindow.document.write('body { font-family: Tahoma, Arial, sans-serif; padding: 5mm; margin: 0 auto; direction: rtl; font-size: 14px; font-weight: bold; color: #000; width: 75mm; box-sizing: border-box; }');
            printWindow.document.write('div { margin-bottom: 5px; }');
            printWindow.document.write('span { color: #000 !important; }');
            printWindow.document.write('.header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }');
            printWindow.document.write('.row { display: flex; justify-content: space-between; border-bottom: 1px dotted #ccc; padding: 5px 0; }');
            printWindow.document.write('.total-row { display: flex; justify-content: space-between; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; font-weight: bold; font-size: 14px; margin-top: 10px; }');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write('<div class="header">');
            printWindow.document.write('<h2 style="margin:0 0 5px 0; font-size: 16px;">فاتورة تحايا</h2>');
            printWindow.document.write('</div>');
            
            // Clean up the modal layout for thermal printing
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = reportContent;
            
            // We just need the key-value pairs
            var rows = tempDiv.querySelectorAll('div[style*="display: flex"]');
            var thermalHtml = '';
            
            rows.forEach(function(row) {
                var spans = row.querySelectorAll('span');
                if(spans.length >= 2) {
                    var label = spans[0].innerText;
                    var val = spans[1].innerText;
                    if(label.includes('الإجمالي')) {
                        thermalHtml += '<div class="total-row"><span>' + label + '</span><span>' + val + '</span></div>';
                    } else {
                        thermalHtml += '<div class="row"><span>' + label + '</span><span>' + val + '</span></div>';
                    }
                }
            });

            printWindow.document.write(thermalHtml);
            
            printWindow.document.write('<div style="text-align:center; margin-top:15px; font-size:10px;">شكراً لزيارتكم</div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            
            // Auto close after print
            setTimeout(function() {
                printWindow.print();
                printWindow.close();
            }, 250);
        }
    </script>
    {{-- PIN Modal (Alpine.js) --}}
    <div x-data="{ pin: @entangle('enteredPin') }" 
         x-show="$wire.showPinModal" 
         style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; justify-content: center; align-items: center;"
         x-init="$watch('$wire.showPinModal', value => { if(value) pin = ''; })"
         :style="{ display: $wire.showPinModal ? 'flex' : 'none' }">
        
        <div style="background: var(--surface-color); padding: 2rem; border-radius: var(--radius-lg); width: 90%; max-width: 350px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid var(--border-color);">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem; font-size: 1.2rem;">
                <i class="fa-solid fa-lock"></i> أدخل رمز الدخول
            </h3>
            
            {{-- PIN Display --}}
            <div style="background: rgba(0,0,0,0.1); border: 2px solid var(--border-color); border-radius: 8px; height: 50px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; letter-spacing: 10px; font-weight: bold; color: var(--text-primary);">
                <span x-text="pin.replace(/./g, '*') || '----'" style="opacity: pin.length > 0 ? 1 : 0.3;"></span>
            </div>

            {{-- Numpad Grid --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 1.5rem;">
                @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $num)
                    <button type="button" @click="if(pin.length < 4) pin += '{{ $num }}'" style="padding: 15px; font-size: 1.5rem; font-weight: bold; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); cursor: pointer; transition: background 0.2s;" onmousedown="this.style.background='rgba(212,175,55,0.2)'" onmouseup="this.style.background='rgba(255,255,255,0.05)'" onmouseleave="this.style.background='rgba(255,255,255,0.05)'" ontouchstart="this.style.background='rgba(212,175,55,0.2)'" ontouchend="this.style.background='rgba(255,255,255,0.05)'">
                        {{ $num }}
                    </button>
                @endforeach
                <button type="button" @click="pin = pin.slice(0, -1)" style="padding: 15px; font-size: 1.5rem; font-weight: bold; background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); border-radius: 8px; color: var(--danger-color); cursor: pointer;" ontouchstart="this.style.background='rgba(244,67,54,0.3)'" ontouchend="this.style.background='rgba(244,67,54,0.1)'">
                    <i class="fa-solid fa-delete-left"></i>
                </button>
                <button type="button" @click="if(pin.length < 4) pin += '0'" style="padding: 15px; font-size: 1.5rem; font-weight: bold; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); cursor: pointer;" ontouchstart="this.style.background='rgba(212,175,55,0.2)'" ontouchend="this.style.background='rgba(255,255,255,0.05)'">
                    0
                </button>
                <button type="button" @click="$wire.verifyPinAndAddQuota()" style="padding: 15px; font-size: 1.2rem; font-weight: bold; background: #4CAF50; border: none; border-radius: 8px; color: #fff; cursor: pointer;" ontouchstart="this.style.opacity='0.7'" ontouchend="this.style.opacity='1'">
                    <i class="fa-solid fa-check"></i>
                </button>
            </div>

            <button type="button" @click="$wire.closePinModal()" style="width: 100%; padding: 12px; background: transparent; border: 1px solid var(--text-secondary); border-radius: 8px; color: var(--text-secondary); font-size: 1rem; cursor: pointer;">
                إلغاء
            </button>
        </div>
    </div>

</div>
