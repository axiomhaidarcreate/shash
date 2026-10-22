<div>
    {{-- ====== Trigger Button ====== --}}
    @php
        $lastShift = \App\Models\WorkShift::where('status', 'closed')
            ->whereNotNull('closed_at')
            ->latest('closed_at')
            ->first();
    @endphp

    @if($lastShift)
    <div style="margin-bottom: 0.8rem;">
        <button wire:click="showReport"
                style="background: linear-gradient(135deg, #1565C0, #0D47A1);
                       color: white; border: none; padding: 0.6rem 1.4rem;
                       border-radius: 8px; font-weight: bold; font-size: 0.95rem;
                       cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
                       box-shadow: 0 4px 15px rgba(21,101,192,0.4);
                       transition: all 0.2s;"
                onmouseover="this.style.transform='translateY(-1px)'"
                onmouseout="this.style.transform='translateY(0)'">
            <i class="fa-solid fa-chart-pie"></i>
            تقرير إغلاق الفترة المحاسبية
            <span style="background: rgba(255,255,255,0.2); padding: 1px 8px; border-radius: 20px; font-size: 0.8rem;">
                {{ $lastShift->closed_at->format('Y/m/d h:i A') }}
            </span>
        </button>
    </div>
    @endif

    {{-- ====== Modal ====== --}}
    @if($show && $shift)
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.75);
                backdrop-filter: blur(6px); z-index: 9999;
                display: flex; justify-content: center; align-items: flex-start;
                padding: 1rem; overflow-y: auto;">

        <div style="background: #0f1117; border: 1px solid rgba(255,255,255,0.1);
                    border-radius: 16px; width: 100%; max-width: 900px;
                    box-shadow: 0 25px 60px rgba(0,0,0,0.6);
                    animation: slideDown 0.3s ease; overflow: hidden; margin: auto;">

            {{-- ===== HEADER ===== --}}
            <div style="background: linear-gradient(135deg, #1a237e, #283593);
                        padding: 1.5rem 2rem;
                        display: flex; justify-content: space-between; align-items: center;
                        flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="margin: 0; color: #fff; font-size: 1.4rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-clipboard-list"></i>
                        تقرير إغلاق الفترة المحاسبية
                    </h2>
                    <div style="display: flex; gap: 2rem; margin-top: 0.6rem; flex-wrap: wrap;">
                        <div style="color: #90CAF9; font-size: 0.85rem;">
                            <i class="fa-solid fa-door-open" style="color: #4CAF50;"></i>
                            <strong style="color: #fff;">فتح:</strong>
                            {{ $shift->opened_at->format('Y/m/d h:i A') }}
                        </div>
                        <div style="color: #90CAF9; font-size: 0.85rem;">
                            <i class="fa-solid fa-door-closed" style="color: #F44336;"></i>
                            <strong style="color: #fff;">إغلاق:</strong>
                            {{ $shift->closed_at->format('Y/m/d h:i A') }}
                        </div>
                        <div style="color: #90CAF9; font-size: 0.85rem;">
                            <i class="fa-solid fa-hourglass-half" style="color: #FFC107;"></i>
                            <strong style="color: #fff;">المدة:</strong>
                            {{ $shift->opened_at->diffForHumans($shift->closed_at, true) }}
                        </div>
                    </div>
                </div>
                <button wire:click="close"
                        style="background: rgba(244,67,54,0.2); border: 1px solid #F44336;
                               color: #F44336; width: 38px; height: 38px; border-radius: 50%;
                               font-size: 1.1rem; cursor: pointer; display: flex;
                               align-items: center; justify-content: center;
                               transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(244,67,54,0.5)'"
                        onmouseout="this.style.background='rgba(244,67,54,0.2)'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div style="padding: 1.5rem 2rem; display: flex; flex-direction: column; gap: 1.5rem;">

                {{-- ===== SECTION 1: RECEPTION / TICKETS ===== --}}
                @if(in_array($section, ['all', 'reception']))
                <div style="background: rgba(33,150,243,0.06); border: 1px solid rgba(33,150,243,0.3);
                            border-radius: 12px; overflow: hidden;">
                    <div style="background: linear-gradient(135deg, rgba(33,150,243,0.2), rgba(21,101,192,0.2));
                                padding: 0.8rem 1.2rem; display: flex; align-items: center; gap: 10px;
                                border-bottom: 1px solid rgba(33,150,243,0.2);">
                        <i class="fa-solid fa-ticket" style="color: #42A5F5; font-size: 1.1rem;"></i>
                        <h3 style="margin: 0; color: #fff; font-size: 1.05rem;">قسم الاستقبال — التذاكر</h3>
                    </div>
                    <div style="padding: 1rem 1.2rem;">
                        {{-- Summary Cards --}}
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.8rem; margin-bottom: 1rem;">
                            <div style="background: rgba(76,175,80,0.1); border: 1px solid #4CAF50;
                                        border-radius: 10px; padding: 0.8rem; text-align: center;">
                                <div style="font-size: 0.75rem; color: #888; margin-bottom: 4px;">✅ مباع</div>
                                <div style="font-size: 2rem; font-weight: 900; color: #4CAF50;">{{ $totalSold }}</div>
                            </div>
                            <div style="background: rgba(244,67,54,0.1); border: 1px solid #F44336;
                                        border-radius: 10px; padding: 0.8rem; text-align: center;">
                                <div style="font-size: 0.75rem; color: #888; margin-bottom: 4px;">❌ ملغي</div>
                                <div style="font-size: 2rem; font-weight: 900; color: #F44336;">{{ $totalCancelled }}</div>
                            </div>
                            <div style="background: rgba(33,150,243,0.1); border: 1px solid #2196F3;
                                        border-radius: 10px; padding: 0.8rem; text-align: center;">
                                <div style="font-size: 0.75rem; color: #888; margin-bottom: 4px;">📊 الإجمالي</div>
                                <div style="font-size: 2rem; font-weight: 900; color: #2196F3;">{{ $totalTickets }}</div>
                            </div>
                        </div>

                        {{-- Products Sold Table --}}
                        @if(count($productsSold) > 0)
                        <div style="margin-bottom: 0.5rem; font-size: 0.85rem; color: #90CAF9; font-weight: bold;">
                            <i class="fa-solid fa-box-open"></i> المبيعات حسب الصنف:
                        </div>
                        <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                    <th style="text-align: right; padding: 0.5rem 0.8rem; color: #888; font-weight: 600;">الصنف</th>
                                    <th style="text-align: center; padding: 0.5rem 0.8rem; color: #888; font-weight: 600;">الكمية المباعة</th>
                                    <th style="text-align: center; padding: 0.5rem 0.8rem; color: #888; font-weight: 600;">النسبة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productsSold as $product => $qty)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td style="padding: 0.5rem 0.8rem; color: #fff; font-weight: 600;">
                                        <i class="fa-solid fa-smoking" style="color: #D4AF37; margin-left: 6px;"></i>
                                        {{ $product }}
                                    </td>
                                    <td style="padding: 0.5rem 0.8rem; text-align: center;">
                                        <span style="background: rgba(76,175,80,0.15); color: #4CAF50;
                                                     padding: 3px 12px; border-radius: 20px; font-weight: 700; font-size: 1rem;">
                                            {{ $qty }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.5rem 0.8rem; text-align: center;">
                                        @if($totalSold > 0)
                                        <div style="display: flex; align-items: center; gap: 6px; justify-content: center;">
                                            <div style="flex: 1; max-width: 80px; background: rgba(255,255,255,0.1);
                                                        border-radius: 4px; height: 6px; overflow: hidden;">
                                                <div style="height: 100%; background: #4CAF50; border-radius: 4px;
                                                            width: {{ round(($qty / $totalSold) * 100) }}%;"></div>
                                            </div>
                                            <span style="color: #888; font-size: 0.8rem;">{{ round(($qty / $totalSold) * 100) }}%</span>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        @else
                        <div style="text-align: center; color: #888; padding: 1rem; font-size: 0.9rem;">لا توجد مبيعات في هذه الفترة المحاسبية</div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- ===== SECTION 2: GREETINGS ===== --}}
                @if(in_array($section, ['all', 'greetings']))
                <div style="background: rgba(156,39,176,0.06); border: 1px solid rgba(156,39,176,0.3);
                            border-radius: 12px; overflow: hidden;">
                    <div style="background: linear-gradient(135deg, rgba(156,39,176,0.2), rgba(106,27,154,0.2));
                                padding: 0.8rem 1.2rem; display: flex; align-items: center; gap: 10px;
                                border-bottom: 1px solid rgba(156,39,176,0.2);">
                        <i class="fa-solid fa-handshake" style="color: #CE93D8; font-size: 1.1rem;"></i>
                        <h3 style="margin: 0; color: #fff; font-size: 1.05rem;">قسم التحايا</h3>
                    </div>
                    <div style="padding: 1rem 1.2rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.8rem; margin-bottom: 1rem;">
                            <div style="background: rgba(156,39,176,0.1); border: 1px solid #9C27B0;
                                        border-radius: 10px; padding: 0.8rem; text-align: center;">
                                <div style="font-size: 0.75rem; color: #888; margin-bottom: 4px;">🎁 إجمالي التحايا المُقدَّمة</div>
                                <div style="font-size: 2rem; font-weight: 900; color: #CE93D8;">{{ $totalGreetingsUsed }}</div>
                            </div>
                            <div style="background: rgba(255,193,7,0.1); border: 1px solid #FFC107;
                                        border-radius: 10px; padding: 0.8rem; text-align: center;">
                                <div style="font-size: 0.75rem; color: #888; margin-bottom: 4px;">💰 الإجمالي المالي</div>
                                <div style="font-size: 2rem; font-weight: 900; color: #FFC107;">{{ number_format($totalGreetingsAmount, 0) }}</div>
                                <div style="font-size: 0.7rem; color: #888;">ريال</div>
                            </div>
                        </div>

                        @if($greetingReports->count() > 0)
                        <div style="margin-bottom: 0.5rem; font-size: 0.85rem; color: #CE93D8; font-weight: bold;">
                            <i class="fa-solid fa-users"></i> تفاصيل حسب الموظف:
                        </div>
                        <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                    <th style="text-align: right; padding: 0.5rem 0.8rem; color: #888;">الموظف</th>
                                    <th style="text-align: center; padding: 0.5rem 0.8rem; color: #888;">الرصيد الافتتاحي</th>
                                    <th style="text-align: center; padding: 0.5rem 0.8rem; color: #888;">المستخدم</th>
                                    <th style="text-align: center; padding: 0.5rem 0.8rem; color: #888;">المتبقي</th>
                                    <th style="text-align: center; padding: 0.5rem 0.8rem; color: #888;">المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($greetingReports as $gr)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td style="padding: 0.5rem 0.8rem; color: #fff; font-weight: 600;">
                                        <i class="fa-solid fa-user-tie" style="color: #CE93D8; margin-left: 6px;"></i>
                                        {{ $gr->staff->name ?? 'غير معروف' }}
                                    </td>
                                    <td style="padding: 0.5rem 0.8rem; text-align: center; color: #FFC107; font-weight: 700;">{{ $gr->opening_balance }}</td>
                                    <td style="padding: 0.5rem 0.8rem; text-align: center;">
                                        <span style="color: #F44336; font-weight: 700;">{{ $gr->used_balance }}</span>
                                    </td>
                                    <td style="padding: 0.5rem 0.8rem; text-align: center;">
                                        <span style="color: #4CAF50; font-weight: 700;">{{ $gr->remaining_balance }}</span>
                                    </td>
                                    <td style="padding: 0.5rem 0.8rem; text-align: center; color: #FFC107; font-weight: 700;">
                                        {{ number_format($gr->total_amount, 0) }} ر
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        @else
                        <div style="text-align: center; color: #888; padding: 1rem; font-size: 0.9rem;">لا توجد تحايا في هذه الفترة المحاسبية</div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- ===== SECTION 3: INVENTORY ===== --}}
                @if(in_array($section, ['all', 'inventory']))
                <div style="background: rgba(255,152,0,0.06); border: 1px solid rgba(255,152,0,0.3);
                            border-radius: 12px; overflow: hidden;">
                    <div style="background: linear-gradient(135deg, rgba(255,152,0,0.2), rgba(230,81,0,0.2));
                                padding: 0.8rem 1.2rem; display: flex; align-items: center; gap: 10px;
                                border-bottom: 1px solid rgba(255,152,0,0.2);">
                        <i class="fa-solid fa-warehouse" style="color: #FFB74D; font-size: 1.1rem;"></i>
                        <h3 style="margin: 0; color: #fff; font-size: 1.05rem;">المخزون والجرد</h3>
                    </div>
                    <div style="padding: 1rem 1.2rem;">
                        @if($inventoryReport)
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.8rem; margin-bottom: 1rem;">
                            <div style="background: rgba(76,175,80,0.1); border: 1px solid #4CAF50;
                                        border-radius: 10px; padding: 0.8rem; text-align: center;">
                                <div style="font-size: 0.75rem; color: #888; margin-bottom: 4px;">📦 الوارد (غ)</div>
                                <div style="font-size: 1.8rem; font-weight: 900; color: #4CAF50;">{{ number_format($inventoryReport->total_stock_in) }}</div>
                            </div>
                            <div style="background: rgba(33,150,243,0.1); border: 1px solid #2196F3;
                                        border-radius: 10px; padding: 0.8rem; text-align: center;">
                                <div style="font-size: 0.75rem; color: #888; margin-bottom: 4px;">🔥 المستهلك (غ)</div>
                                <div style="font-size: 1.8rem; font-weight: 900; color: #2196F3;">{{ number_format($inventoryReport->total_consumed) }}</div>
                            </div>
                            <div style="background: rgba(244,67,54,0.1); border: 1px solid #F44336;
                                        border-radius: 10px; padding: 0.8rem; text-align: center;">
                                <div style="font-size: 0.75rem; color: #888; margin-bottom: 4px;">🗑️ التالف (غ)</div>
                                <div style="font-size: 1.8rem; font-weight: 900; color: #F44336;">{{ number_format($inventoryReport->total_waste) }}</div>
                            </div>
                        </div>
                        @endif

                        {{-- Waste per product --}}
                        @if(count($wasteByProduct) > 0)
                        <div style="margin-bottom: 0.5rem; font-size: 0.85rem; color: #FFB74D; font-weight: bold;">
                            <i class="fa-solid fa-triangle-exclamation"></i> التالف حسب الصنف (غرام):
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            @foreach($wasteByProduct as $product => $grams)
                            <div style="background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3);
                                        border-radius: 8px; padding: 0.4rem 0.8rem; display: flex; align-items: center; gap: 6px;">
                                <span style="color: #fff; font-size: 0.9rem;">{{ $product }}</span>
                                <span style="background: #F44336; color: #fff; padding: 1px 8px;
                                             border-radius: 20px; font-size: 0.85rem; font-weight: 700;">{{ number_format($grams) }}غ</span>
                            </div>
                            @endforeach
                        </div>
                        @elseif(!$inventoryReport)
                        <div style="text-align: center; color: #888; padding: 1rem; font-size: 0.9rem;">لا توجد بيانات جرد لهذه الفترة المحاسبية</div>
                        @else
                        <div style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                            <i class="fa-solid fa-check-circle" style="color: #4CAF50;"></i> لا يوجد تالف معتمد في هذه الفترة المحاسبية
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Hidden Thermal Print Layout --}}
                <div id="shift-thermal-print-content" style="display: none;">
                    <div style="text-align:center;border-bottom:2px dashed #000;padding-bottom:10px;margin-bottom:10px;">
                        <div style="font-size:18px;font-weight:900;">تقرير إغلاق الفترة المحاسبية</div>
                        <div style="font-size:12px;margin-top:5px;">فتح: {{ $shift->opened_at->format('Y/m/d h:i A') }}</div>
                        <div style="font-size:12px;">إغلاق: {{ $shift->closed_at->format('Y/m/d h:i A') }}</div>
                        <div style="font-size:12px;">المدة: {{ $shift->opened_at->diffForHumans($shift->closed_at, true) }}</div>
                    </div>

                    @if(in_array($section, ['all', 'reception']))
                    <div class="t">قسم الاستقبال — التذاكر</div>
                    <div class="r"><span>مباع</span><span>{{ $totalSold }}</span></div>
                    <div class="r"><span>ملغي</span><span>{{ $totalCancelled }}</span></div>
                    <div class="r"><span>الإجمالي</span><span>{{ $totalTickets }}</span></div>
                    @if(count($productsSold) > 0)
                    <table>
                        <tr><th>الصنف</th><th style="text-align:center;">الكمية</th></tr>
                        @foreach($productsSold as $product => $qty)
                        <tr><td>{{ $product }}</td><td style="text-align:center;">{{ $qty }}</td></tr>
                        @endforeach
                    </table>
                    @endif
                    @endif

                    @if(in_array($section, ['all', 'greetings']))
                    <div class="t">قسم التحايا</div>
                    <div class="r"><span>إجمالي التحايا</span><span>{{ $totalGreetingsUsed }}</span></div>
                    <div class="r"><span>الإجمالي المالي</span><span>{{ number_format($totalGreetingsAmount, 0) }} ريال</span></div>
                    @if(isset($greetingReports) && $greetingReports->count() > 0)
                    <table>
                        <tr><th>الموظف</th><th>افتتاحي</th><th>مستخدم</th><th>متبقي</th><th>المبلغ</th></tr>
                        @foreach($greetingReports as $gr)
                        <tr>
                            <td>{{ $gr->staff->name ?? 'غير معروف' }}</td>
                            <td style="text-align:center;">{{ $gr->opening_balance }}</td>
                            <td style="text-align:center;">{{ $gr->used_balance }}</td>
                            <td style="text-align:center;">{{ $gr->remaining_balance }}</td>
                            <td style="text-align:center;">{{ number_format($gr->total_amount, 0) }}</td>
                        </tr>
                        @endforeach
                    </table>
                    @endif
                    @endif

                    @if(in_array($section, ['all', 'inventory']))
                    <div class="t">المخزون والجرد</div>
                    @if($inventoryReport)
                    <div class="r"><span>الوارد (غ)</span><span>{{ number_format($inventoryReport->total_stock_in) }}</span></div>
                    <div class="r"><span>المستهلك (غ)</span><span>{{ number_format($inventoryReport->total_consumed) }}</span></div>
                    <div class="r"><span>التالف (غ)</span><span>{{ number_format($inventoryReport->total_waste) }}</span></div>
                    @endif
                    @if(count($wasteByProduct) > 0)
                    <table>
                        <tr><th>الصنف</th><th style="text-align:center;">التالف (غ)</th></tr>
                        @foreach($wasteByProduct as $product => $grams)
                        <tr><td>{{ $product }}</td><td style="text-align:center;">{{ number_format($grams) }}</td></tr>
                        @endforeach
                    </table>
                    @endif
                    @endif
                </div>

                {{-- ===== FOOTER ===== --}}
                <div style="text-align: center; padding-top: 0.5rem; border-top: 1px solid rgba(255,255,255,0.08); display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                    <button onclick="printShiftReport()"
                            style="background: linear-gradient(135deg, var(--primary-color, #D4AF37), #B8860B); border: none;
                                   color: #fff; padding: 0.6rem 2rem; border-radius: 8px;
                                   font-size: 0.95rem; cursor: pointer; transition: all 0.2s; font-weight: bold;"
                            onmouseover="this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                        <i class="fa-solid fa-print"></i> طباعة التقرير
                    </button>
                    <button wire:click="close"
                            style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2);
                                   color: #ccc; padding: 0.6rem 2rem; border-radius: 8px;
                                   font-size: 0.95rem; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <i class="fa-solid fa-xmark"></i> إغلاق التقرير
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endif

    <script>
        if (typeof window.printShiftReport !== 'function') {
            window.printShiftReport = function() {
                var content = document.getElementById('shift-thermal-print-content');
                if(!content) {
                    alert('عذراً، محتوى الطباعة غير جاهز بعد.');
                    return;
                }
                var pw = window.open('', '_blank', 'width=400,height=700');
                pw.document.write('<html dir="rtl"><head><title>تقرير إغلاق الفترة المحاسبية</title>');
                pw.document.write('<style>');
                pw.document.write('@page { margin: 2mm; size: 80mm auto; }');
                pw.document.write('body { font-family: Tahoma, Arial, sans-serif; padding: 5mm; margin: 0 auto; direction: rtl; font-size: 14px; font-weight: bold; color: #000; width: 75mm; box-sizing: border-box; }');
                pw.document.write('.t { text-align: center; font-size: 15px; font-weight: 900; border-top: 1px dashed #000; padding-top: 8px; margin-top: 10px; margin-bottom: 6px; }');
                pw.document.write('.r { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dotted #ccc; font-weight: bold; }');
                pw.document.write('table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 13px; }');
                pw.document.write('th, td { padding: 4px 6px; text-align: right; font-weight: bold; border-bottom: 1px dotted #ccc; }');
                pw.document.write('</style></head><body>');
                pw.document.write(content.innerHTML);
                pw.document.write('<div style="text-align:center;margin-top:15px;border-top:1px dashed #000;padding-top:8px;font-size:12px;">شكراً لجهودكم</div>');
                pw.document.write('</body></html>');
                pw.document.close();
                pw.focus();
                setTimeout(function() { pw.print(); pw.close(); }, 300);
            }
        }
    </script>
</div>
