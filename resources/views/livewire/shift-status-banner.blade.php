<div>
    @if($activeShift)
        {{-- Shift OPEN banner --}}
        <div style="background: linear-gradient(135deg, rgba(76,175,80,0.15), rgba(76,175,80,0.05));
                    border: 1px solid #4CAF50;
                    border-radius: 8px;
                    padding: 0.8rem 1.2rem;
                    margin-bottom: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 10px; color: #4CAF50; font-weight: bold;">
                <i class="fa-solid fa-circle fa-beat" style="font-size: 9px;"></i>
                <span style="font-size: 1rem;">✅ الفترة المحاسبية مفتوحة</span>
            </div>

            <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                <div style="text-align: center;">
                    <div style="font-size: 0.7rem; color: #888; margin-bottom: 2px;">📅 تاريخ الفتح</div>
                    <div style="font-size: 0.95rem; font-weight: bold; color: #4CAF50;">
                        {{ $activeShift->opened_at->format('Y/m/d') }}
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 0.7rem; color: #888; margin-bottom: 2px;">🕐 وقت الفتح</div>
                    <div style="font-size: 0.95rem; font-weight: bold; color: #4CAF50;">
                        {{ $activeShift->opened_at->format('h:i A') }}
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 0.7rem; color: #888; margin-bottom: 2px;">⏰ الإغلاق المتوقع</div>
                    <div style="font-size: 0.95rem; font-weight: bold; color: #FF9800;">
                        {{ $formattedEnd }}
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Shift CLOSED banner --}}
        <div style="background: linear-gradient(135deg, rgba(244,67,54,0.12), rgba(244,67,54,0.04));
                    border: 2px solid #F44336;
                    border-radius: 8px;
                    padding: 0.8rem 1.5rem;
                    margin-bottom: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 0.8rem;">

            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.3rem;">🔒</span>
                <div>
                    <div style="font-size: 1rem; font-weight: bold; color: #F44336;">الفترة المحاسبية مغلقة حالياً</div>
                    <div style="font-size: 0.8rem; color: #888; margin-top: 2px;">
                        لا يمكن تسجيل أي عمليات حتى يقوم المدير بفتح الفترة المحاسبية
                    </div>
                </div>
            </div>

            @if($lastClosedShift)
            <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                <div style="text-align: center;">
                    <div style="font-size: 0.7rem; color: #888; margin-bottom: 2px;">📅 تاريخ الفتح</div>
                    <div style="font-size: 0.95rem; font-weight: bold; color: #FF9800;">
                        {{ $lastClosedShift->opened_at->format('Y/m/d') }}
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 0.7rem; color: #888; margin-bottom: 2px;">🕐 وقت الفتح</div>
                    <div style="font-size: 0.95rem; font-weight: bold; color: #FF9800;">
                        {{ $lastClosedShift->opened_at->format('h:i A') }}
                    </div>
                </div>
                <div style="width: 1px; height: 40px; background: rgba(255,255,255,0.15);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 0.7rem; color: #888; margin-bottom: 2px;">📅 تاريخ الإغلاق</div>
                    <div style="font-size: 0.95rem; font-weight: bold; color: #F44336;">
                        {{ $lastClosedShift->closed_at->format('Y/m/d') }}
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 0.7rem; color: #888; margin-bottom: 2px;">🕐 وقت الإغلاق</div>
                    <div style="font-size: 0.95rem; font-weight: bold; color: #F44336;">
                        {{ $lastClosedShift->closed_at->format('h:i A') }}
                    </div>
                </div>
            </div>
            @else
            <div style="text-align: center;">
                <div style="font-size: 0.85rem; font-weight: bold; color: #FFC107;">
                    ⏱️ وقت العمل: {{ $formattedStart }} — {{ $formattedEnd }}
                </div>
            </div>
            @endif
        </div>
    @endif
</div>
