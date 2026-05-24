<div wire:poll.5s>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);"><i class="fa-solid fa-chart-line" style="color: var(--primary-color);"></i> إحصائيات النظام</h2>
        <span style="color: var(--success-color); font-size: 0.8rem;"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> تحديث مباشر</span>
    </div>

    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-4" style="margin-bottom: 1.5rem; gap: 1rem;">
        <div class="card" style="text-align: center; border-top: 3px solid var(--primary-color); padding: clamp(0.7rem, 2vw, 1rem);">
            <i class="fa-solid fa-receipt" style="color: var(--primary-color); margin-bottom: 0.5rem; font-size: clamp(1rem, 3vw, 1.5rem);"></i>
            <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">طلبات اليوم</h3>
            <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: var(--primary-color);">{{ $ordersToday }}</p>
        </div>
        <div class="card" style="text-align: center; border-top: 3px solid var(--warning-color); padding: clamp(0.7rem, 2vw, 1rem);">
            <i class="fa-solid fa-fire-burner" style="color: var(--warning-color); margin-bottom: 0.5rem; font-size: clamp(1rem, 3vw, 1.5rem);"></i>
            <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">نشطة</h3>
            <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: var(--warning-color);">{{ $activeOrders }}</p>
        </div>
        <div class="card" style="text-align: center; border-top: 3px solid var(--success-color); padding: clamp(0.7rem, 2vw, 1rem);">
            <i class="fa-solid fa-check-circle" style="color: var(--success-color); margin-bottom: 0.5rem; font-size: clamp(1rem, 3vw, 1.5rem);"></i>
            <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">مكتملة</h3>
            <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: var(--success-color);">{{ $completedOrders }}</p>
        </div>
        <div class="card" style="text-align: center; border-top: 3px solid var(--info-color); padding: clamp(0.7rem, 2vw, 1rem);">
            <i class="fa-solid fa-star" style="color: var(--info-color); margin-bottom: 0.5rem; font-size: clamp(1rem, 3vw, 1.5rem);"></i>
            <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">الأكثر طلباً</h3>
            <p style="font-size: clamp(0.8rem, 2.5vw, 1rem); font-weight: 800; margin-top: 0.5rem; color: var(--info-color); word-break: break-word;">{{ $mostRequestedShisha }}</p>
        </div>
    </div>

    <!-- Inventory Summary -->
    <div style="margin-bottom: 1.5rem;">
        <h2 style="font-size: clamp(0.95rem, 3vw, 1.15rem); margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-boxes-stacked" style="color: var(--primary-color);"></i> ملخص المخزون
            <span style="font-size: 0.7rem; color: var(--text-secondary); font-weight: 400;">(كل رأس = 20 جرام)</span>
        </h2>
        <div class="grid grid-cols-4" style="gap: 1rem;">
            {{-- Stock In --}}
            <div class="card" style="text-align: center; border-top: 3px solid var(--success-color); padding: clamp(0.7rem, 2vw, 1rem); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -15px; left: -15px; width: 60px; height: 60px; background: rgba(76,175,80,0.06); border-radius: 50%;"></div>
                <i class="fa-solid fa-arrow-down" style="color: var(--success-color); margin-bottom: 0.4rem; font-size: clamp(1rem, 3vw, 1.4rem);"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.65rem, 1.8vw, 0.8rem); margin-bottom: 0.3rem;">إجمالي الداخل</h3>
                <p style="font-size: clamp(1.1rem, 4vw, 1.6rem); font-weight: 800; color: var(--success-color); line-height: 1.2;">
                    {{ number_format($totalStockIn) }} <span style="font-size: 0.75rem;">ج</span>
                </p>
                <div style="margin-top: 0.4rem; background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.25); border-radius: 20px; padding: 3px 10px; display: inline-block;">
                    <span style="font-size: clamp(0.7rem, 2vw, 0.85rem); font-weight: 700; color: var(--success-color);">
                        <i class="fa-solid fa-cubes" style="font-size: 0.7rem;"></i>
                        {{ number_format(floor($totalStockIn / 20)) }} رأس
                    </span>
                </div>
            </div>

            {{-- Consumed --}}
            <div class="card" style="text-align: center; border-top: 3px solid var(--warning-color); padding: clamp(0.7rem, 2vw, 1rem); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -15px; left: -15px; width: 60px; height: 60px; background: rgba(255,152,0,0.06); border-radius: 50%;"></div>
                <i class="fa-solid fa-fire" style="color: var(--warning-color); margin-bottom: 0.4rem; font-size: clamp(1rem, 3vw, 1.4rem);"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.65rem, 1.8vw, 0.8rem); margin-bottom: 0.3rem;">إجمالي المُستهلك</h3>
                <p style="font-size: clamp(1.1rem, 4vw, 1.6rem); font-weight: 800; color: var(--warning-color); line-height: 1.2;">
                    {{ number_format($totalConsumed) }} <span style="font-size: 0.75rem;">ج</span>
                </p>
                <div style="margin-top: 0.4rem; background: rgba(255,152,0,0.1); border: 1px solid rgba(255,152,0,0.25); border-radius: 20px; padding: 3px 10px; display: inline-block;">
                    <span style="font-size: clamp(0.7rem, 2vw, 0.85rem); font-weight: 700; color: var(--warning-color);">
                        <i class="fa-solid fa-cubes" style="font-size: 0.7rem;"></i>
                        {{ number_format(floor($totalConsumed / 20)) }} رأس
                    </span>
                </div>
            </div>

            {{-- Waste --}}
            <div class="card" style="text-align: center; border-top: 3px solid var(--danger-color); padding: clamp(0.7rem, 2vw, 1rem); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -15px; left: -15px; width: 60px; height: 60px; background: rgba(244,67,54,0.06); border-radius: 50%;"></div>
                <i class="fa-solid fa-trash" style="color: var(--danger-color); margin-bottom: 0.4rem; font-size: clamp(1rem, 3vw, 1.4rem);"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.65rem, 1.8vw, 0.8rem); margin-bottom: 0.3rem;">إجمالي التالف</h3>
                <p style="font-size: clamp(1.1rem, 4vw, 1.6rem); font-weight: 800; color: var(--danger-color); line-height: 1.2;">
                    {{ number_format($totalWaste) }} <span style="font-size: 0.75rem;">ج</span>
                </p>
                <div style="margin-top: 0.4rem; background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.25); border-radius: 20px; padding: 3px 10px; display: inline-block;">
                    <span style="font-size: clamp(0.7rem, 2vw, 0.85rem); font-weight: 700; color: var(--danger-color);">
                        <i class="fa-solid fa-cubes" style="font-size: 0.7rem;"></i>
                        {{ number_format(floor($totalWaste / 20)) }} رأس
                    </span>
                </div>
            </div>

            {{-- Remaining --}}
            <div class="card" style="text-align: center; border-top: 3px solid {{ $totalRemaining < 0 ? 'var(--danger-color)' : 'var(--primary-color)' }}; padding: clamp(0.7rem, 2vw, 1rem); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -15px; left: -15px; width: 60px; height: 60px; background: rgba(156,39,176,0.06); border-radius: 50%;"></div>
                <i class="fa-solid fa-scale-balanced" style="color: {{ $totalRemaining < 0 ? 'var(--danger-color)' : 'var(--primary-color)' }}; margin-bottom: 0.4rem; font-size: clamp(1rem, 3vw, 1.4rem);"></i>
                <h3 style="color: var(--text-secondary); font-size: clamp(0.65rem, 1.8vw, 0.8rem); margin-bottom: 0.3rem;">المتبقي في المخزون</h3>
                <p style="font-size: clamp(1.1rem, 4vw, 1.6rem); font-weight: 800; color: {{ $totalRemaining < 0 ? 'var(--danger-color)' : 'var(--primary-color)' }}; line-height: 1.2;">
                    {{ number_format($totalRemaining) }} <span style="font-size: 0.75rem;">ج</span>
                    @if($totalRemaining < 0)
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 0.8rem;"></i>
                    @endif
                </p>
                <div style="margin-top: 0.4rem; background: {{ $totalRemaining < 0 ? 'rgba(244,67,54,0.1)' : 'rgba(156,39,176,0.1)' }}; border: 1px solid {{ $totalRemaining < 0 ? 'rgba(244,67,54,0.25)' : 'rgba(156,39,176,0.25)' }}; border-radius: 20px; padding: 3px 10px; display: inline-block;">
                    <span style="font-size: clamp(0.7rem, 2vw, 0.85rem); font-weight: 700; color: {{ $totalRemaining < 0 ? 'var(--danger-color)' : 'var(--primary-color)' }};">
                        <i class="fa-solid fa-cubes" style="font-size: 0.7rem;"></i>
                        {{ number_format(floor($totalRemaining / 20)) }} رأس
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Table-Wise Detailed Inventory Report (Prevention of Theft) -->
    <div class="card" style="margin-bottom: 1.5rem; border-top: 3px solid var(--primary-color);">
        <h3 style="margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px; font-size: 1.15rem; color: var(--primary-color);">
            <i class="fa-solid fa-list-check" style="color: var(--primary-color);"></i>
            تفصيل مبيعات وجرد الطاولات لليوم (لمكافحة السرقات وفروقات الجرد)
        </h3>

        @if(empty($tableReport))
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <i class="fa-solid fa-receipt fa-2x" style="opacity: 0.3; margin-bottom: 0.8rem;"></i>
                <p>لا توجد مبيعات أو طلبات مسجلة لليوم بعد.</p>
            </div>
        @else
            <!-- Tables Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($tableReport as $key => $report)
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-right: 4px solid var(--primary-color); padding: 1rem; border-radius: var(--radius-sm); transition: transform 0.2s ease;">
                        <h4 style="margin-top: 0; margin-bottom: 0.8rem; font-size: 1.05rem; color: var(--primary-color); display: flex; align-items: center; justify-content: space-between;">
                            <span><i class="fa-solid fa-chair" style="color: var(--primary-color);"></i> طاولة {{ $report['table_number'] }}</span>
                            @if($report['section'])
                                <span style="font-size: 0.75rem; color: var(--text-secondary); background: rgba(255,255,255,0.05); padding: 2px 6px; border-radius: 4px;">{{ $report['section'] }}</span>
                            @endif
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @foreach($report['orders'] as $ord)
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.88rem; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 6px;">
                                    <span style="font-weight: 500;">
                                        <strong style="color: var(--primary-color);">{{ $ord['quantity'] }}x</strong> {{ $ord['shisha_type'] }}
                                    </span>
                                    <span style="color: var(--text-secondary); font-size: 0.75rem; display: flex; flex-direction: column; align-items: flex-end;">
                                        <span><i class="fa-solid fa-user-ninja" style="font-size: 0.7rem;"></i> {{ $ord['waiter'] }}</span>
                                        <span style="font-size: 0.65rem; opacity: 0.8;">{{ $ord['time'] }}</span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Product Quantities Daily Totals Summary Box -->
            <div style="background: rgba(212,175,55,0.04); border: 1px solid rgba(212,175,55,0.25); border-radius: var(--radius-sm); padding: 1.2rem;">
                <h4 style="margin-top: 0; margin-bottom: 0.8rem; color: var(--primary-color); display: flex; align-items: center; gap: 8px; font-size: 1rem;">
                    <i class="fa-solid fa-boxes-packing"></i> إجمالي كميات الأصناف المبيوعة اليوم (للجرد ومطابقة المستودع):
                </h4>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @foreach($todayProductTotals as $productName => $totalQty)
                        <span style="background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2); color: #fff; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; display: inline-flex; align-items: center; gap: 6px;">
                            <span>{{ $productName }}:</span>
                            <span style="color: var(--primary-color); font-size: 1rem; font-weight: 800;">{{ $totalQty }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Tables and Recent Orders -->
    <div class="layout-grid">

        <!-- Latest Orders -->
        <div class="card">
            <h3 style="margin-bottom: 1rem; font-size: clamp(0.95rem, 3vw, 1.1rem);"><i class="fa-solid fa-list" style="color: var(--primary-color);"></i> أحدث الطلبات</h3>
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="data-table" style="min-width: 550px; width: 100%; text-align: right; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 0.8rem;">#</th>
                            <th style="padding: 0.8rem;">الطاولة</th>
                            <th style="padding: 0.8rem;">نوع المعسل</th>
                            <th style="padding: 0.8rem;">الكمية</th>
                            <th style="padding: 0.8rem;">ملاحظات</th>
                            <th style="padding: 0.8rem;">بواسطة</th>
                            <th style="padding: 0.8rem;">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 0.8rem; color: var(--text-secondary);">{{ $order->id }}</td>
                            <td style="padding: 0.8rem; font-weight: bold; color: var(--primary-color);">
                                {{ $order->table->number }}
                                @if($order->table && $order->table->section)
                                    <span style="font-size: 0.8em; color: var(--text-secondary); font-weight: normal;">({{ $order->table->section }})</span>
                                @endif
                            </td>
                            <td style="padding: 0.8rem;">{{ $order->shisha_type }}</td>
                            <td style="padding: 0.8rem;">{{ $order->quantity }}</td>
                            <td style="padding: 0.8rem; font-size: 0.8rem; color: var(--text-secondary); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $order->notes ?? '—' }}</td>
                            <td style="padding: 0.8rem; font-size: 0.85rem;">{{ $order->user->name }}</td>
                                 <td style="padding: 0.8rem;">
                                <div style="display: flex; gap: 8px; align-items: center; justify-content: flex-end; flex-wrap: wrap;">
                                    @php
                                        $sLabel = match($order->status) {
                                            'pending' => 'انتظار',
                                            'prep' => 'قيد التحضير',
                                            'ready' => 'جاهز',
                                            'delivered' => 'تم التسليم',
                                            'cancelled' => 'ملغي',
                                            default => $order->status
                                        };
                                    @endphp
                                    @if($order->cancel_requested)
                                        <span class="badge" style="background: rgba(255,152,0,0.2); color: var(--warning-color); border: 1px solid var(--warning-color);">
                                            <i class="fa-solid fa-hourglass-half"></i> طلب إلغاء
                                        </span>
                                    @else
                                        <span class="badge badge-{{ $order->status }}">{{ $sLabel }}</span>
                                    @endif
                                    <a href="{{ route('invoice.print', $order->id) }}" target="_blank" class="btn btn-sm btn-ghost" title="طباعة الفاتورة">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-secondary);">لا توجد طلبات حتى الآن</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tables Status -->
        <div class="card">
            <h3 style="margin-bottom: 1rem; font-size: clamp(0.95rem, 3vw, 1.1rem);"><i class="fa-solid fa-border-all" style="color: var(--primary-color);"></i> حالة الطاولات</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 0.6rem;">
                @foreach($tables as $table)
                    @php
                        $color = match($table->status) {
                            'empty' => '#444',
                            'customer_seated' => 'var(--warning-color)',
                            'order_prep' => '#2196F3',
                            'order_ready' => 'var(--success-color)',
                            'delivered' => '#9e9e9e',
                        };
                        $statusLabel = match($table->status) {
                            'empty' => 'فارغة',
                            'customer_seated' => 'مشغولة',
                            'order_prep' => 'تحضير',
                            'order_ready' => 'جاهز',
                            'delivered' => 'تسليم',
                        };
                    @endphp
                    <div style="border: 2px solid {{ $color }}; padding: 0.6rem 0.3rem; border-radius: var(--radius-sm); text-align: center;">
                        <span style="font-size: clamp(1rem, 3vw, 1.2rem); font-weight: bold;">{{ $table->number }}</span>
                        @if($table->section)
                            <div style="font-size: clamp(0.7rem, 2vw, 0.8rem); color: var(--text-secondary); margin-top: -3px; font-weight: normal;">{{ $table->section }}</div>
                        @endif
                        <div style="font-size: clamp(0.6rem, 2vw, 0.7rem); color: {{ $color }}; margin-top: 4px; font-weight: 700;">
                            {{ $statusLabel }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Cancel Requests Section -->
    @if($cancelRequests->count() > 0)
    <div class="card" style="margin-top: 1.5rem; border: 2px solid var(--warning-color); background: rgba(255,152,0,0.03);">
        <h3 style="margin-bottom: 1rem; color: var(--warning-color);">
            <i class="fa-solid fa-triangle-exclamation"></i>
            طلبات الإلغاء المعلقة <span style="background: var(--warning-color); color: #000; border-radius: 50%; padding: 2px 8px; font-size: 0.9rem;">{{ $cancelRequests->count() }}</span>
        </h3>
        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="data-table" style="min-width: 500px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الطاولة</th>
                        <th>الطلب</th>
                        <th>بواسطة</th>
                        <th style="width: 220px;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cancelRequests as $req)
                    <tr wire:key="cancel-req-{{ $req->id }}" style="background: rgba(255,152,0,0.05);">
                        <td style="font-weight: bold; color: var(--warning-color);">#{{ $req->id }}</td>
                        <td>طاولة {{ $req->table->number ?? '-' }} @if($req->table && $req->table->section)<span style="font-size: 0.8rem; color: #777;">({{ $req->table->section }})</span>@endif</td>
                        <td>{{ $req->quantity }}x {{ $req->shisha_type }}</td>
                        <td>{{ $req->user->name ?? '-' }}</td>
                        <td>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <button wire:click="approveCancellation({{ $req->id }})" class="btn btn-sm" style="background: #4CAF50; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('موافقة على إلغاء الطلب، سيتم تحرير الطاولة. هل أنت متأكد؟')">
                                    <i class="fa-solid fa-check"></i> موافقة
                                </button>
                                <button wire:click="rejectCancellation({{ $req->id }})" class="btn btn-sm" style="background: #F44336; color: #fff; border: none; padding: 0.4rem 0.8rem;">
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

</div>
