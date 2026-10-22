<div class="card" style="border: 1px solid var(--primary-color);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <h3 style="font-size: clamp(0.95rem, 3vw, 1.1rem); margin: 0;"><i class="fa-solid fa-border-all" style="color: var(--primary-color);"></i> اختر الطاولة</h3>
        <input type="text" wire:model.live="tableSearch" class="form-control" placeholder="بحث..." style="max-width: 120px; padding: 0.4rem 0.8rem; font-size: 0.9rem;">
    </div>

    <!-- Lounge Sections Tabs -->
    @if($sections->count() > 0)
    <div style="display: flex; gap: 6px; margin-bottom: 1.2rem; overflow-x: auto; padding-bottom: 6px; -webkit-overflow-scrolling: touch; border-bottom: 1px solid var(--border-color); touch-action: pan-x;">
        @foreach($sections as $sec)
            <button type="button" wire:click="selectSection('{{ $sec->name }}')" 
                    class="btn btn-sm {{ $selectedSection === $sec->name ? '' : 'btn-outline' }}" 
                    style="padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); font-weight: bold; white-space: nowrap; font-size: clamp(0.75rem, 2vw, 0.85rem); transition: all 0.2s ease;">
                <i class="fa-solid fa-map-location-dot"></i> {{ $sec->name }}
            </button>
        @endforeach
    </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 0.5rem;">
        @foreach($tables as $table)
            @php
                $isReserved = $table->eventBookings->isNotEmpty();
                $reservedFor = $isReserved ? $table->eventBookings->first()->customer_name : null;

                $borderColor = $selectedTableId == $table->id ? 'var(--primary-color)' : 'var(--border-color)';
                
                if ($isReserved) {
                    $bgColor = $selectedTableId == $table->id ? 'rgba(244, 67, 54, 0.2)' : 'rgba(244, 67, 54, 0.1)';
                    $borderColor = $selectedTableId == $table->id ? 'var(--primary-color)' : 'var(--danger-color)';
                } else {
                    $bgColor = $selectedTableId == $table->id ? 'rgba(212, 175, 55, 0.12)' : 'transparent';
                }

                $statusDot = match($table->status) {
                    'empty' => '#555',
                    'customer_seated' => 'var(--warning-color)',
                    'order_prep' => '#2196F3',
                    'order_ready' => 'var(--success-color)',
                    'delivered' => '#9e9e9e',
                    default => '#555'
                };
                if ($isReserved && $table->status == 'empty') {
                    $statusDot = 'var(--danger-color)';
                }
                
                $isOccupied = $table->status !== 'empty';
                $isTableFull = $table->chairs->count() > 0 ? $table->chairs->where('status', 'empty')->count() === 0 : $isOccupied;
                
                // For tables, we allow clicking to see chairs, but if excludeOccupied is true and it's full/occupied, we might disable it
                $isDisabled = $excludeOccupied && ($isOccupied || $isReserved) && $selectedTableId != $table->id;
                $opacity = $isDisabled ? '0.5' : '1';
                $cursor = $isDisabled ? 'not-allowed' : 'pointer';
            @endphp
            <div wire:click="selectTable({{ $table->id }})" 
                  style="border: 2px solid {{ $borderColor }}; 
                         padding: 0.5rem 0.2rem; border-radius: var(--radius-sm); text-align: center; cursor: {{ $cursor }}; opacity: {{ $opacity }};
                         background-color: {{ $bgColor }};
                         transition: all 0.15s ease; position: relative; min-height: 55px;
                         display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;
                         touch-action: manipulation; -webkit-tap-highlight-color: transparent;">
                <span style="position: absolute; top: 4px; right: 4px; width: 7px; height: 7px; border-radius: 50%; background: {{ $statusDot }};"></span>
                @if($isReserved)
                    <span style="position: absolute; top: 2px; left: 4px; font-size: 0.7rem; color: var(--danger-color);" title="محجوزة"><i class="fa-solid fa-lock"></i></span>
                @endif
                <span style="font-size: clamp(1rem, 3vw, 1.3rem); font-weight: 800; line-height: 1; color: {{ ($isTableFull || $isReserved) ? 'var(--danger-color)' : 'inherit' }};">{{ $table->number }}</span>
                @if($isReserved)
                    <span style="font-size: 0.6rem; color: var(--danger-color); font-weight: bold; margin-top: -2px; width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $reservedFor }}</span>
                @elseif($table->section)
                    <span style="font-size: clamp(0.7rem, 2vw, 0.8rem); color: var(--text-secondary); margin-top: -2px;">{{ $table->section }}</span>
                @endif
                <span style="font-size: clamp(0.65rem, 2vw, 0.75rem); color: var(--text-secondary);"><i class="fa-solid fa-users"></i> {{ $table->capacity }}</span>
            </div>
        @endforeach
    </div>

    @if($selectedTableId)
        @php
            $selTable = \App\Models\Table::with('chairs')->find($selectedTableId);
        @endphp
        @if($selTable && $selTable->chairs->count() > 0)
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-t: 1px dashed var(--border-color);">
                <h4 style="font-size: 0.95rem; margin-bottom: 0.8rem; color: var(--primary-color);">
                    <i class="fa-solid fa-chair"></i> اختر الكرسي لطاولة ({{ $selTable->number }}):
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(55px, 1fr)); gap: 0.5rem;">
                    @foreach($selTable->chairs->sortBy('sort_order') as $chair)
                        @php
                            // Check if this specific chair is reserved in EventBookings
                            $isChairReserved = $selTable->eventBookings->where('chair_id', $chair->id)->isNotEmpty();
                            $isChairOccupied = $chair->status !== 'empty' || $isChairReserved;
                            
                            $chairBorder = $isChairOccupied ? 'var(--danger-color)' : ($selectedChairId == $chair->id ? 'var(--primary-color)' : 'var(--border-color)');
                            $chairBg = $isChairOccupied ? 'rgba(244, 67, 54, 0.1)' : ($selectedChairId == $chair->id ? 'rgba(212, 175, 55, 0.15)' : 'transparent');
                            $chairOpacity = $isChairOccupied ? '0.5' : '1';
                            $chairCursor = $isChairOccupied ? 'not-allowed' : 'pointer';
                            $chairPointerEvents = $isChairOccupied ? 'none' : 'auto';
                            $chairStatusDot = $isChairOccupied ? 'var(--danger-color)' : 'var(--success-color)';
                        @endphp
                        <div @if(!$isChairOccupied) wire:click="selectChair({{ $chair->id }})" @endif 
                             style="border: 2px solid {{ $chairBorder }}; padding: 0.4rem 0.2rem; border-radius: var(--radius-sm); 
                                    text-align: center; cursor: {{ $chairCursor }}; opacity: {{ $chairOpacity }}; pointer-events: {{ $chairPointerEvents }}; background-color: {{ $chairBg }};
                                    transition: all 0.15s ease; position: relative; min-height: 48px;
                                    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;">
                            <span style="position: absolute; top: 3px; right: 3px; width: 6px; height: 6px; border-radius: 50%; background: {{ $chairStatusDot }};"></span>
                            @if($isChairOccupied)
                                <span style="position: absolute; top: 1px; left: 3px; font-size: 0.65rem; color: var(--danger-color);" title="محجوز"><i class="fa-solid fa-lock"></i></span>
                            @endif
                            <span style="font-size: 0.95rem; font-weight: 800; line-height: 1; color: {{ $isChairOccupied ? 'var(--danger-color)' : 'inherit' }};">{{ $chair->number }}</span>
                            <span style="font-size: 0.65rem; color: {{ $isChairOccupied ? 'var(--danger-color)' : 'var(--text-secondary)' }};">{{ $isChairOccupied ? 'محجوز' : 'كرسي' }}</span>
                        </div>
                    @endforeach
                </div>
                @error('selectedChair') <span style="color: var(--danger-color); display: block; margin-top: 6px; font-size: 0.85rem; font-weight: bold;">{{ $message }}</span> @enderror
            </div>
        @endif
    @endif

    @if($selectedSection === null)
        <div style="text-align: center; color: var(--warning-color); margin-top: 1.5rem; font-size: 1rem; font-weight: bold; border: 1px dashed var(--warning-color); padding: 1rem; border-radius: var(--radius-sm); background: rgba(255, 152, 0, 0.05);">
            <i class="fa-solid fa-hand-point-up fa-bounce" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block;"></i>
            يرجى تحديد الصالة أولاً من الأزرار العلوية لعرض الطاولات.
        </div>
    @elseif($tables->isEmpty())
        <div style="text-align: center; color: var(--text-secondary); margin-top: 1rem; font-size: 0.9rem;">لا توجد طاولات في هذا القسم حالياً.</div>
    @endif
    @error('selectedTable') <span style="color: var(--danger-color); display: block; margin-top: 8px; font-size: 0.9rem; text-align: center; font-weight: bold;">{{ $message }}</span> @enderror

    <div style="display: flex; gap: 10px; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
        <button type="button" wire:click="cancelSelection" class="btn btn-outline" style="flex: 1;">إلغاء</button>
        <button type="button" wire:click="confirmSelection" class="btn btn-success" style="flex: 2; font-weight: bold;"><i class="fa-solid fa-check"></i> تأكيد الطاولة</button>
    </div>
</div>
