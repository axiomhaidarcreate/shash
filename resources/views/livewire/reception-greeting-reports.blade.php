<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);">
            <i class="fa-solid fa-chart-bar" style="color: var(--primary-color);"></i> 
            تقارير التحايا التفصيلية (الاستقبال)
        </h2>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('invoice.greetingsReportPrintThermal', ['date' => $filterDate, 'staff_id' => $filterStaffId]) }}" target="_blank" class="btn btn-sm btn-outline">
                <i class="fa-solid fa-receipt"></i> طباعة حراري
            </a>
            <a href="{{ route('invoice.greetingsReportPrint', ['date' => $filterDate, 'staff_id' => $filterStaffId]) }}" target="_blank" class="btn btn-sm btn-outline">
                <i class="fa-solid fa-print"></i> طباعة A4
            </a>
            <a href="{{ route('reception.greetings') }}" class="btn btn-sm btn-outline"><i class="fa-solid fa-arrow-right"></i> العودة لشاشة التحايا</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label class="form-label">تصفية حسب الموظف</label>
            <select wire:model.live="filterStaffId" class="form-control">
                <option value="">-- كل الموظفين --</option>
                @foreach($staffUsers as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label class="form-label">تصفية حسب التاريخ</label>
            <input type="date" wire:model.live="filterDate" class="form-control">
        </div>
    </div>

    <!-- Stats Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: rgba(33, 150, 243, 0.1); border: 1px solid rgba(33, 150, 243, 0.3); padding: 1.5rem; border-radius: var(--radius-md); text-align: center;">
            <div style="color: #2196F3; font-size: 1rem; margin-bottom: 0.5rem;"><i class="fa-solid fa-gift"></i> إجمالي التحايا</div>
            <div style="font-size: 2rem; font-weight: bold; color: #fff;">{{ $totalGreetings }}</div>
        </div>
        <div style="background: rgba(76, 175, 80, 0.1); border: 1px solid rgba(76, 175, 80, 0.3); padding: 1.5rem; border-radius: var(--radius-md); text-align: center;">
            <div style="color: #4CAF50; font-size: 1rem; margin-bottom: 0.5rem;"><i class="fa-solid fa-sack-dollar"></i> إجمالي الإيرادات</div>
            <div style="font-size: 2rem; font-weight: bold; color: #fff;">{{ number_format($totalRevenue, 2) }} <span style="font-size: 1rem;">ريال</span></div>
        </div>
    </div>

    {{-- Daily Closure Reports --}}
    @if($dailyReports->count() > 0)
    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-file-invoice" style="color: var(--primary-color);"></i> تقارير الإغلاق اليومي</h3>
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
    </div>
    @endif

    <!-- Data Table -->
    <div class="card">
        <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-table" style="color: var(--primary-color);"></i> تفاصيل حسابات التحايا</h3>
        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="data-table" style="min-width: 800px; width: 100%;">
                <thead>
                    <tr>
                        <th>رقم الحساب</th>
                        <th>اسم الضيف</th>
                        <th>الطاولة</th>
                        <th>المنتج</th>
                        <th>الموظف</th>
                        <th>إجمالي التحايا</th>
                        <th>الإجمالي (ريال)</th>
                        <th>الحالة</th>
                        <th>تاريخ الفتح</th>
                        <th>تاريخ الإغلاق</th>
                        <th>طباعة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>#{{ $report->id }}</td>
                            <td style="font-weight: bold; color: var(--primary-color);">{{ $report->guest_name }}</td>
                            <td>{{ $report->table ? $report->table->number : '-' }}</td>
                            <td>{{ $report->product ? $report->product->name : '-' }}</td>
                            <td>{{ $report->staff ? $report->staff->name : '-' }}</td>
                            <td style="font-weight: bold;">{{ $report->total_greetings }}</td>
                            <td style="color: var(--success-color); font-weight: bold;">{{ number_format($report->total_amount, 2) }}</td>
                            <td>
                                @if($report->is_closed)
                                    <span style="background: rgba(76,175,80,0.15); color: #4CAF50; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">مغلق</span>
                                @else
                                    <span style="background: rgba(255,152,0,0.15); color: #FF9800; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">نشط</span>
                                @endif
                            </td>
                            <td style="font-size: 0.85rem; color: #888;">{{ $report->created_at->format('Y-m-d H:i') }}</td>
                            <td style="font-size: 0.85rem; color: #888;">{{ $report->closed_at ? \Carbon\Carbon::parse($report->closed_at)->locale('ar')->translatedFormat('l, Y-m-d h:i A') : '-' }}</td>
                            <td>
                                @if($report->is_closed)
                                    <button wire:click="printGuestReport({{ $report->id }})" class="btn btn-sm btn-ghost" title="طباعة تقرير هذا الضيف">
                                        <i class="fa-solid fa-print" style="color: var(--primary-color);"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 2rem; color: var(--text-secondary);">لا توجد بيانات متاحة لهذا الفلتر.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Individual Print Modal --}}
    @if($showPrintModal && $printReport)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; justify-content: center; align-items: center; padding: 1rem;">
        <div style="background: var(--surface-color); border-radius: var(--radius-lg); width: 95%; max-width: 450px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); border: 1px solid var(--border-color); overflow: hidden;">
            
            <div style="background: linear-gradient(135deg, var(--primary-color), #B8860B); padding: 1.2rem; text-align: center;">
                <h3 style="margin: 0; color: #fff; font-size: 1.3rem;"><i class="fa-solid fa-file-invoice"></i> تقرير ضيف</h3>
            </div>

            <div id="printable-guest-report" style="padding: 1.5rem;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="font-size: 1.3rem; font-weight: bold; color: var(--primary-color); margin-bottom: 0.5rem;">{{ $printReport['guest_name'] }}</div>
                </div>

                <div style="display: grid; gap: 0.8rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">اسم الضيف:</span>
                        <span style="font-weight: bold;">{{ $printReport['guest_name'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الموظف:</span>
                        <span style="font-weight: bold;">{{ $printReport['staff_name'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الطاولة:</span>
                        <span style="font-weight: bold;">{{ $printReport['table'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">عدد التحايا:</span>
                        <span style="font-weight: bold; color: #2196F3;">{{ $printReport['total_greetings'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">سعر الوحدة:</span>
                        <span style="font-weight: bold;">{{ number_format($printReport['price'], 2) }} ريال</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.8rem; background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); border-radius: var(--radius-sm);">
                        <span style="font-weight: bold; font-size: 1.1rem;">الإجمالي:</span>
                        <span style="font-weight: bold; color: var(--success-color); font-size: 1.2rem;">{{ number_format($printReport['total_amount'], 2) }} ريال</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                        <span style="color: var(--text-secondary);">الفتح:</span>
                        <span>{{ $printReport['created_at'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                        <span style="color: var(--text-secondary);">الإغلاق:</span>
                        <span>{{ $printReport['closed_at'] }}</span>
                    </div>
                </div>
            </div>

            <div style="padding: 1rem 1.5rem 1.5rem; display: flex; gap: 0.8rem;">
                <button onclick="printGuestReportFn()" class="btn" style="flex: 1; background: var(--primary-color); color: #fff; padding: 0.8rem; font-weight: bold;">
                    <i class="fa-solid fa-print"></i> طباعة
                </button>
                <button wire:click="closePrintModal" class="btn" style="flex: 1; background: transparent; border: 1px solid var(--text-secondary); color: var(--text-secondary); padding: 0.8rem;">
                    إغلاق
                </button>
            </div>
        </div>
    </div>

    <script>
        function printGuestReportFn() {
            var reportContent = document.getElementById('printable-guest-report').innerHTML;
            var pw = window.open('', '_blank', 'width=400,height=600');
            pw.document.write('<html dir="rtl"><head><title>تقرير ضيف</title>');
            pw.document.write('<style>');
            pw.document.write('@page { margin: 2mm; size: 80mm auto; }');
            pw.document.write('body { font-family: Tahoma, Arial, sans-serif; padding: 5mm; margin: 0 auto; direction: rtl; font-size: 15px; font-weight: bold; color: #000; width: 75mm; box-sizing: border-box; }');
            pw.document.write('div, span { font-weight: bold !important; color: #000 !important; }');
            pw.document.write('</style></head><body>');
            
            // Clean up the modal layout for thermal printing
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = reportContent;
            
            var rows = tempDiv.querySelectorAll('div[style*="display: flex"]');
            var thermalHtml = '<div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; font-size: 18px;">تقرير ضيف (التحايا)</div>';
            
            rows.forEach(function(row) {
                var spans = row.querySelectorAll('span');
                if(spans.length >= 2) {
                    var label = spans[0].innerText;
                    var val = spans[1].innerText;
                    var isTotal = label.includes('الإجمالي');
                    var border = isTotal ? 'border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; font-size: 16px; margin-top: 10px;' : 'border-bottom: 1px dotted #ccc; padding: 5px 0;';
                    thermalHtml += '<div style="display: flex; justify-content: space-between; ' + border + '"><span>' + label + '</span><span>' + val + '</span></div>';
                }
            });

            pw.document.write(thermalHtml);
            pw.document.write('<div style="text-align:center; margin-top:15px; font-size:12px;">شكراً لجهودكم</div>');
            pw.document.write('</body></html>');
            pw.document.close();
            pw.focus();
            setTimeout(function() {
                pw.print();
                pw.close();
            }, 250);
        }
    </script>
    @endif
</div>
