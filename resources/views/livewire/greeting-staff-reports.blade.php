<div>
    @php
        $shiftStartTime = \App\Models\Setting::where('key', 'shift_start_time')->value('value') ?? '16:00';
        $shiftEndTime = \App\Models\Setting::where('key', 'shift_end_time')->value('value') ?? '02:00';
        $formattedStartTime = \Carbon\Carbon::parse($shiftStartTime)->format('h:i A');
        $formattedEndTime = \Carbon\Carbon::parse($shiftEndTime)->format('h:i A');
    @endphp
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);">
            <i class="fa-solid fa-chart-bar" style="color: var(--primary-color);"></i> 
            تقارير الإغلاق اليومية (التحايا)
        </h2>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('greetings') }}" class="btn btn-sm btn-outline"><i class="fa-solid fa-arrow-right"></i> العودة لشاشة التحايا</a>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card" style="padding: 0;">
        <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">من تاريخ</label>
                <input type="date" wire:model.live="filterStartDate" class="form-control">
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" wire:model.live="filterEndDate" class="form-control">
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.05); border-bottom: 1px solid var(--border-color);">
                        <th style="padding: 1rem; font-weight: 600;">حالة اليومية</th>
                        <th style="padding: 1rem; font-weight: 600;">رصيد افتتاحي</th>
                        <th style="padding: 1rem; font-weight: 600;">رصيد مستخدم</th>
                        <th style="padding: 1rem; font-weight: 600;">متبقي (ختامي)</th>
                        <th style="padding: 1rem; font-weight: 600;">إجمالي الإيرادات</th>
                        <th style="padding: 1rem; font-weight: 600;">وقت الفتح</th>
                        <th style="padding: 1rem; font-weight: 600;">وقت الإغلاق</th>
                        <th style="padding: 1rem; font-weight: 600;">طباعة</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Active Day Row --}}
                    @if($activeReport)
                        <tr style="background: rgba(76, 175, 80, 0.1); border-bottom: 2px solid #4CAF50;">
                            <td style="padding: 1rem;">
                                <span style="background: rgba(76, 175, 80, 0.2); color: #4CAF50; padding: 4px 10px; border-radius: 4px; font-weight: bold;">
                                    <i class="fa-solid fa-door-open"></i> قيد العمل (اليوم)
                                </span>
                            </td>
                            <td style="padding: 1rem; color: #FFC107; font-weight: bold;">{{ $activeReport['opening_balance'] }}</td>
                            <td style="padding: 1rem; color: #F44336; font-weight: bold;">{{ $activeReport['used_balance'] }}</td>
                            <td style="padding: 1rem; color: #4CAF50; font-weight: bold;">{{ $activeReport['remaining_balance'] }}</td>
                            <td style="padding: 1rem; color: var(--primary-color); font-weight: bold; font-size: 1.1rem;">{{ number_format($activeReport['total_amount'], 2) }} ريال</td>
                            <td style="padding: 1rem; font-weight: bold;">{{ $activeReport['opened_at']->format('Y-m-d') }} <span style="color: var(--primary-color);">{{ $formattedStartTime }}</span></td>
                            <td style="padding: 1rem; color: var(--danger-color); font-weight: bold;">متوقع: {{ $formattedEndTime }}</td>
                            <td style="padding: 1rem;">-</td>
                        </tr>
                    @endif

                    {{-- History Rows --}}
                    @forelse($reports as $report)
                        @php
                            $openDate = $report->opened_at ? $report->opened_at->format('Y-m-d') : $report->created_at->format('Y-m-d');
                            $closeDate = $report->closed_at ? $report->closed_at->format('Y-m-d') : $report->created_at->format('Y-m-d');
                            $openTime = $report->opened_at ? $report->opened_at->format('h:i A') : $formattedStartTime;
                            $closeTime = $report->closed_at ? $report->closed_at->format('h:i A') : $formattedEndTime;
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 1rem;">
                                <span style="background: rgba(244,67,54,0.15); color: #F44336; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">مغلق</span>
                            </td>
                            <td style="padding: 1rem;">{{ $report->opening_balance }}</td>
                            <td style="padding: 1rem;">{{ $report->used_balance }}</td>
                            <td style="padding: 1rem;">{{ $report->remaining_balance }}</td>
                            <td style="padding: 1rem; color: var(--success-color); font-weight: bold;">{{ number_format($report->total_amount, 2) }} ريال</td>
                            <td style="font-size: 0.85rem; color: #888; padding: 1rem; font-weight: bold;">{{ $openDate }} <span style="color: var(--primary-color);">{{ $openTime }}</span></td>
                            <td style="font-size: 0.85rem; color: #888; padding: 1rem; font-weight: bold;">{{ $closeDate }} <span style="color: var(--danger-color);">{{ $closeTime }}</span></td>
                            <td style="padding: 1rem;">
                                <button wire:click="printDailyReport({{ $report->id }})" class="btn btn-sm btn-ghost" title="طباعة تقرير اليومية">
                                    <i class="fa-solid fa-print" style="color: var(--primary-color);"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        @if(!$activeReport)
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">لا توجد تقارير إغلاق في هذه الفترة.</td>
                        </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daily Report Print Modal --}}
    @if($showDailyPrintModal && $dailyPrintReport)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; justify-content: center; align-items: center; padding: 1rem;">
        <div style="background: var(--surface-color); border-radius: var(--radius-lg); width: 95%; max-width: 450px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); border: 1px solid var(--border-color); overflow: hidden;">
            
            <div style="background: linear-gradient(135deg, var(--primary-color), #B8860B); padding: 1.2rem; text-align: center;">
                <h3 style="margin: 0; color: #fff; font-size: 1.3rem;"><i class="fa-solid fa-file-invoice"></i> تقرير إغلاق يومية</h3>
            </div>

            <div id="printable-daily-report" style="padding: 1.5rem;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="font-size: 1.3rem; font-weight: bold; color: var(--primary-color); margin-bottom: 0.5rem;">تقرير يومية التحايا</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">الموظف: {{ $dailyPrintReport['staff_name'] }}</div>
                </div>

                <div style="display: grid; gap: 0.8rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الموظف:</span>
                        <span style="font-weight: bold;">{{ $dailyPrintReport['staff_name'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">المدير (المغلق):</span>
                        <span style="font-weight: bold;">{{ $dailyPrintReport['manager_name'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الرصيد الافتتاحي:</span>
                        <span style="font-weight: bold;">{{ $dailyPrintReport['opening_balance'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الرصيد المستخدم:</span>
                        <span style="font-weight: bold; color: #F44336;">{{ $dailyPrintReport['used_balance'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-secondary);">الرصيد المتبقي:</span>
                        <span style="font-weight: bold; color: #4CAF50;">{{ $dailyPrintReport['remaining_balance'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.8rem; background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); border-radius: var(--radius-sm);">
                        <span style="font-weight: bold; font-size: 1.1rem;">الإيرادات:</span>
                        <span style="font-weight: bold; color: var(--success-color); font-size: 1.2rem;">{{ number_format($dailyPrintReport['total_amount'], 2) }} ريال</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                        <span style="color: var(--text-secondary);">وقت الفتح:</span>
                        <span style="font-size: 0.9rem;">{{ $dailyPrintReport['opened_at'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                        <span style="color: var(--text-secondary);">وقت الإغلاق:</span>
                        <span style="font-size: 0.9rem;">{{ $dailyPrintReport['closed_at'] }}</span>
                    </div>
                </div>
            </div>

            <div style="padding: 1rem 1.5rem 1.5rem; display: flex; gap: 0.8rem;">
                <button onclick="printDailyReportFn()" class="btn" style="flex: 1; background: var(--primary-color); color: #fff; padding: 0.8rem; font-weight: bold;">
                    <i class="fa-solid fa-print"></i> طباعة
                </button>
                <button wire:click="closeDailyPrintModal" class="btn" style="flex: 1; background: transparent; border: 1px solid var(--text-secondary); color: var(--text-secondary); padding: 0.8rem;">
                    إغلاق
                </button>
            </div>
        </div>
    </div>

    <script>
        function printDailyReportFn() {
            var reportContent = document.getElementById('printable-daily-report').innerHTML;
            var pw = window.open('', '_blank', 'width=400,height=600');
            pw.document.write('<html dir="rtl"><head><title>تقرير يومية</title>');
            pw.document.write('<style>');
            pw.document.write('@page { margin: 2mm; size: 80mm auto; }');
            pw.document.write('body { font-family: Tahoma, Arial, sans-serif; padding: 5mm; margin: 0 auto; direction: rtl; font-size: 15px; font-weight: bold; color: #000; width: 75mm; box-sizing: border-box; }');
            pw.document.write('div, span { font-weight: bold !important; color: #000 !important; }');
            pw.document.write('</style></head><body>');
            
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = reportContent;
            
            var rows = tempDiv.querySelectorAll('div[style*="display: flex"]');
            var thermalHtml = '<div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; font-size: 18px;">تقرير يومية التحايا</div>';
            
            rows.forEach(function(row) {
                var spans = row.querySelectorAll('span');
                if(spans.length >= 2) {
                    var label = spans[0].innerText;
                    var val = spans[1].innerText;
                    var isTotal = label.includes('الإيرادات');
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
