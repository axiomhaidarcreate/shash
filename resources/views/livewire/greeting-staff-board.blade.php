<div wire:poll.10s>
    <livewire:shift-status-banner />
    <livewire:shift-close-report section="greetings" />
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);">
            <i class="fa-solid fa-handshake" style="color: var(--primary-color);"></i> 
            إدارة حسابات التحايا للضيوف
        </h2>
        <a href="{{ route('greetings.reports') }}" class="btn btn-sm btn-outline">
            <i class="fa-solid fa-chart-bar"></i> التقارير والطباعة
        </a>
    </div>

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

    <div class="layout-grid">
        <!-- Create Account -->
        <div class="card">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-user-plus" style="color: var(--primary-color);"></i> فتح حساب للضيف</h3>
            
            <div class="form-group">
                <label class="form-label">اسم الضيف (مطلوب)</label>
                <input type="text" wire:model="guestName" class="form-control" placeholder="أدخل اسم الضيف">
                @error('guestName') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">بحث واختيار الطاولة (اختياري)</label>
                <input type="text" wire:model.live="tableSearch" class="form-control" placeholder="ابحث برقم الطاولة أو القسم..." style="margin-bottom: 0.5rem;">
                <select wire:model="tableId" class="form-control">
                    <option value="">بدون طاولة</option>
                    @foreach($tables as $table)
                        <option value="{{ $table->id }}">طاولة {{ $table->number }} {{ $table->section ? '('.$table->section.')' : '' }}</option>
                    @endforeach
                </select>
                @error('tableId') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <div class="form-group">
                    <label class="form-label">العدد</label>
                    <input type="number" wire:model="initialQuantity" class="form-control" min="1" max="100" placeholder="1">
                    @error('initialQuantity') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">السعر (اختياري)</label>
                    <input type="number" wire:model="greetingPrice" class="form-control" min="0" step="0.5" placeholder="10.00">
                    @error('greetingPrice') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <button wire:click="openAccount" class="btn btn-success" style="width: 100%; padding: 0.9rem; font-size: 1.1rem; margin-top: 1rem;">
                <i class="fa-solid fa-play"></i> فتح حساب وبدء التحايا
            </button>
        </div>

        <!-- Open Accounts Grid -->
        <div class="card" style="grid-column: span 2;">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-list" style="color: var(--primary-color);"></i> الحسابات النشطة الآن</h3>
            
            @if($openAccounts->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                    @foreach($openAccounts as $account)
                        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.2rem; display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
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
                            
                            {{-- Add Greeting with optional Quantity and Price --}}
                            <div style="display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 60px;">
                                    <label style="font-size: 0.7rem; color: var(--text-secondary); display: block; margin-bottom: 2px;">العدد</label>
                                    <input type="number" wire:model="addQuantity.{{ $account->id }}" class="form-control" style="padding: 0.4rem; font-size: 0.85rem;" min="1" placeholder="1">
                                </div>

                                <button wire:click="addGreeting({{ $account->id }})" class="btn" style="background: var(--success-color); color: #fff; padding: 0.5rem 1rem; font-weight: bold; font-size: 0.85rem;">
                                    <i class="fa-solid fa-plus"></i> إضافة
                                </button>
                            </div>
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

    {{-- Daily Reports History --}}
    @if(isset($dailyReports))
    <div class="card" style="margin-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
            <h3 style="margin: 0; font-size: 1.1rem;"><i class="fa-solid fa-file-invoice" style="color: var(--primary-color);"></i> تقارير الإغلاق اليومي للورديات</h3>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div class="form-group" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <label class="form-label" style="margin: 0; white-space: nowrap;">من تاريخ</label>
                    <input type="date" wire:model.live="filterStartDate" class="form-control" style="padding: 0.3rem; height: auto;">
                </div>
                <div class="form-group" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <label class="form-label" style="margin: 0; white-space: nowrap;">إلى تاريخ</label>
                    <input type="date" wire:model.live="filterEndDate" class="form-control" style="padding: 0.3rem; height: auto;">
                </div>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="data-table" style="min-width: 700px; width: 100%;">
                <thead>
                    <tr>
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
                    @forelse($dailyReports as $report)
                        @php
                            $openDate = $report->opened_at ? $report->opened_at->format('Y-m-d h:i A') : $report->created_at->format('Y-m-d h:i A');
                            $closeDate = $report->closed_at ? $report->closed_at->format('Y-m-d h:i A') : $report->created_at->format('Y-m-d h:i A');
                        @endphp
                        <tr>
                            <td style="padding: 1rem; font-weight: bold;">{{ $report->opening_balance }}</td>
                            <td style="padding: 1rem; color: #F44336; font-weight: bold;">{{ $report->used_balance }}</td>
                            <td style="padding: 1rem; color: #4CAF50; font-weight: bold;">{{ $report->remaining_balance }}</td>
                            <td style="padding: 1rem; color: var(--success-color); font-weight: bold;">{{ number_format($report->total_amount, 2) }} ريال</td>
                            <td style="font-size: 0.85rem; color: #888; padding: 1rem; font-weight: bold;">{{ $openDate }}</td>
                            <td style="font-size: 0.85rem; color: #888; padding: 1rem; font-weight: bold;">{{ $closeDate }}</td>
                            <td style="padding: 1rem;">
                                <button wire:click="printDailyReport({{ $report->id }})" class="btn btn-sm btn-ghost" title="طباعة تقرير اليومية">
                                    <i class="fa-solid fa-print" style="color: var(--primary-color);"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">لا توجد تقارير في هذه الفترة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

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
