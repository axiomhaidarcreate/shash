<div>
    {{-- ====== Trigger Button ====== --}}
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
            تقرير المبيعات التاريخي (تذاكر)
        </button>
    </div>

    {{-- ====== Modal ====== --}}
    @if($show)
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8);
                backdrop-filter: blur(8px); z-index: 9999;
                display: flex; justify-content: center; align-items: flex-start;
                padding: 1rem; overflow-y: auto;">

        <div style="background: #0f1117; border: 1px solid rgba(255,255,255,0.1);
                    border-radius: 16px; width: 100%; max-width: 1000px;
                    box-shadow: 0 25px 60px rgba(0,0,0,0.6);
                    animation: slideDown 0.3s ease; overflow: hidden; margin: auto; position: relative;">

            {{-- ===== HEADER ===== --}}
            <div style="background: linear-gradient(135deg, #311B92, #4A148C);
                        padding: 1.5rem 2rem;
                        display: flex; justify-content: space-between; align-items: center;
                        border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    @if($viewState === 'tickets')
                        <button wire:click="goBack" 
                                style="background: rgba(255,255,255,0.1); border: none; color: #fff;
                                       width: 40px; height: 40px; border-radius: 50%; cursor: pointer;
                                       display: flex; align-items: center; justify-content: center;
                                       transition: all 0.2s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    @endif
                    <div>
                        <h2 style="margin: 0; color: #fff; font-size: 1.4rem; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-chart-line" style="color: #E040FB;"></i>
                            @if($viewState === 'templates')
                                ملخص التذاكر - اليوم
                            @elseif($viewState === 'tickets')
                                تفاصيل التذاكر - القالب
                            @endif
                        </h2>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; align-items: center;">
                    @if($viewState === 'templates' && $selectedDate)
                        <a href="/invoice/ticket-report-print?start_date={{ $selectedDate }}&end_date={{ $selectedDate }}"
                           target="_blank"
                           style="background: rgba(76,175,80,0.2); color: #4CAF50; border: 1px solid #4CAF50;
                                  padding: 0.5rem 1rem; border-radius: 8px; font-weight: bold; font-size: 0.85rem;
                                  text-decoration: none; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-print"></i> طباعة A4
                        </a>
                        <a href="/invoice/ticket-report-print-thermal?start_date={{ $selectedDate }}&end_date={{ $selectedDate }}"
                           target="_blank"
                           style="background: rgba(33,150,243,0.2); color: #2196F3; border: 1px solid #2196F3;
                                  padding: 0.5rem 1rem; border-radius: 8px; font-weight: bold; font-size: 0.85rem;
                                  text-decoration: none; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-receipt"></i> طباعة حراري
                        </a>
                    @endif
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
            </div>

            <div style="padding: 2rem; min-height: 400px; position: relative;">


                {{-- ==============================
                     STEP 2: SELECT TEMPLATE 
                =============================== --}}
                @if($viewState === 'templates')
                    @if(count($templatesForDay) > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                            @foreach($templatesForDay as $tpl)
                                <div wire:click="selectTemplate({{ $tpl['id'] }})"
                                     style="background: linear-gradient(135deg, rgba(33,150,243,0.1), rgba(21,101,192,0.2));
                                            border: 2px solid #2196F3;
                                            border-radius: 16px; padding: 1.5rem;
                                            cursor: pointer; transition: all 0.3s;"
                                     onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(33,150,243,0.3)';"
                                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                        <h3 style="margin: 0; color: #90CAF9; font-size: 1.3rem;">
                                            <i class="fa-solid fa-layer-group"></i> {{ $tpl['name'] }}
                                        </h3>
                                        <span style="background: rgba(33,150,243,0.2); color: #90CAF9; padding: 2px 8px; border-radius: 8px; font-size: 0.8rem;">
                                            الطلبات: {{ $tpl['max_items'] }}
                                        </span>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 10px;">
                                        <div style="background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); border-radius: 8px; padding: 0.8rem; text-align: center;">
                                            <div style="font-size: 0.75rem; color: #888;">مباع (مستخدم)</div>
                                            <div style="font-size: 1.5rem; font-weight: bold; color: #4CAF50;">{{ $tpl['sold_tickets'] }}</div>
                                        </div>
                                        <div style="background: rgba(255,193,7,0.1); border: 1px solid rgba(255,193,7,0.3); border-radius: 8px; padding: 0.8rem; text-align: center;">
                                            <div style="font-size: 0.75rem; color: #888;">نشط (انتظار)</div>
                                            <div style="font-size: 1.5rem; font-weight: bold; color: #FFC107;">{{ $tpl['active_tickets'] }}</div>
                                        </div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.2); padding: 0.5rem 1rem; border-radius: 8px;">
                                        <span style="color: #bbb; font-size: 0.85rem;">إجمالي التذاكر المصدرة</span>
                                        <strong style="color: #fff; font-size: 1.1rem;">{{ $tpl['total_tickets'] }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; color: #888; padding: 3rem;">
                            <i class="fa-solid fa-box-open fa-3x" style="margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p>لا توجد مبيعات تذاكر في هذا اليوم.</p>
                        </div>
                    @endif
                @endif

                {{-- ==============================
                     STEP 3: TICKETS LIST 
                =============================== --}}
                @if($viewState === 'tickets')
                    <div style="display: flex; gap: 2rem;">
                        {{-- Tickets Table --}}
                        <div style="flex: {{ $selectedTicketId ? '1' : '1' }}; transition: all 0.3s; overflow-x: auto;">
                            <table style="width: 100%; border-collapse: separate; border-spacing: 0 8px;">
                                <thead>
                                    <tr>
                                        <th style="text-align: right; padding: 0.5rem 1rem; color: #888; font-size: 0.85rem;">رقم التذكرة</th>
                                        <th style="text-align: right; padding: 0.5rem 1rem; color: #888; font-size: 0.85rem;">وقت الإصدار</th>
                                        <th style="text-align: center; padding: 0.5rem 1rem; color: #888; font-size: 0.85rem;">بواسطة</th>
                                        <th style="text-align: center; padding: 0.5rem 1rem; color: #888; font-size: 0.85rem;">الحالة</th>
                                        <th style="text-align: center; padding: 0.5rem 1rem; color: #888; font-size: 0.85rem;">التفاصيل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ticketsForTemplate as $ticket)
                                        <tr style="background: {{ $selectedTicketId == $ticket->id ? 'rgba(156,39,176,0.15)' : 'rgba(255,255,255,0.02)' }}; 
                                                   border: 1px solid {{ $selectedTicketId == $ticket->id ? '#9C27B0' : 'rgba(255,255,255,0.05)' }};
                                                   transition: background 0.2s;">
                                            <td style="padding: 1rem; border-radius: 0 10px 10px 0;">
                                                <strong style="color: #E040FB;">#{{ $ticket->id }}</strong>
                                                <div style="font-size: 0.75rem; color: #888; font-family: monospace; letter-spacing: 1px;">{{ $ticket->barcode }}</div>
                                            </td>
                                            <td style="padding: 1rem; color: #bbb; font-size: 0.9rem;">
                                                {{ $ticket->created_at->format('h:i A') }}
                                            </td>
                                            <td style="padding: 1rem; text-align: center; color: #fff; font-size: 0.9rem;">
                                                {{ $ticket->user->name ?? '-' }}
                                            </td>
                                            <td style="padding: 1rem; text-align: center;">
                                                @if($ticket->status === 'active')
                                                    <span style="background: rgba(255,193,7,0.15); color: #FFC107; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">نشط</span>
                                                @elseif($ticket->status === 'used')
                                                    <span style="background: rgba(76,175,80,0.15); color: #4CAF50; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">مستخدم</span>
                                                @else
                                                    <span style="background: rgba(244,67,54,0.15); color: #F44336; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">ملغي</span>
                                                @endif
                                            </td>
                                            <td style="padding: 1rem; text-align: center; border-radius: 10px 0 0 10px;">
                                                <button wire:click="selectTicket({{ $ticket->id }})"
                                                        style="background: {{ $selectedTicketId == $ticket->id ? '#9C27B0' : 'rgba(156,39,176,0.2)' }}; 
                                                               color: {{ $selectedTicketId == $ticket->id ? '#fff' : '#E040FB' }};
                                                               border: none; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;">
                                                    عرض الطلب
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Ticket Details Panel --}}
                        @if($selectedTicketId && $ticketDetails)
                            <div style="flex: 1; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 1.5rem; min-width: 300px; animation: slideLeft 0.3s ease;">
                                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem; margin-bottom: 1rem;">
                                    <h4 style="margin: 0; color: #fff; font-size: 1.2rem;">تذكرة #{{ $ticketDetails->id }}</h4>
                                    <button wire:click="closeTicketDetails" style="background: none; border: none; color: #888; cursor: pointer; font-size: 1.1rem;"><i class="fa-solid fa-times"></i></button>
                                </div>

                                <div style="margin-bottom: 1.5rem;">
                                    <div style="color: #888; font-size: 0.8rem; margin-bottom: 4px;">الباركود</div>
                                    <div style="font-family: monospace; font-size: 1.1rem; color: #E040FB; letter-spacing: 2px; background: rgba(224,64,251,0.1); padding: 0.5rem; border-radius: 6px; text-align: center;">
                                        {{ $ticketDetails->barcode }}
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                                    <div>
                                        <div style="color: #888; font-size: 0.8rem;">اسم الضيف</div>
                                        <div style="color: #fff; font-weight: bold;">{{ $ticketDetails->guest_name ?? 'بدون اسم' }}</div>
                                    </div>
                                    <div>
                                        <div style="color: #888; font-size: 0.8rem;">عدد الأفراد</div>
                                        <div style="color: #fff; font-weight: bold;">{{ $ticketDetails->num_people ?? 1 }} أفراد</div>
                                    </div>
                                    <div>
                                        <div style="color: #888; font-size: 0.8rem;">وقت الإصدار</div>
                                        <div style="color: #fff; font-weight: bold;">{{ $ticketDetails->created_at->format('h:i A') }}</div>
                                    </div>
                                    <div>
                                        <div style="color: #888; font-size: 0.8rem;">وقت الاستخدام</div>
                                        <div style="color: #fff; font-weight: bold;">
                                            @if($ticketDetails->status === 'used' && $ticketDetails->order && $ticketDetails->order->created_at != $ticketDetails->created_at)
                                                {{ $ticketDetails->order->created_at->format('h:i A') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h5 style="color: #bbb; font-size: 0.95rem; margin-bottom: 0.8rem; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 0.5rem;">الطلبات المحددة</h5>
                                    
                                    @if($ticketDetails->selected_products && is_array($ticketDetails->selected_products))
                                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                                            @foreach($ticketDetails->selected_products as $prod)
                                                <li style="background: rgba(255,255,255,0.05); padding: 0.5rem 1rem; border-radius: 8px; display: flex; align-items: center; gap: 10px; color: #fff;">
                                                    <i class="fa-solid fa-check" style="color: #4CAF50;"></i> {{ $prod }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div style="color: #888; font-size: 0.85rem;">لم يتم تحديد طلبات خاصة.</div>
                                    @endif
                                </div>

                            </div>
                        @endif
                    </div>
                @endif
                
            </div>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
    </style>
    @endif
</div>
