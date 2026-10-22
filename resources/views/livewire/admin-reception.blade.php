<div wire:poll.10s>
    <livewire:shift-status-banner />
    <livewire:admin-ticket-report-viewer />

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem; position: relative;">
        <!-- Secret Button -->
        <button onclick="handleSecretClick()" style="position: absolute; top: 0; right: 0; width: 60px; height: 60px; background: transparent; border: 1px dashed rgba(128, 128, 128, 0.3); border-radius: 8px; outline: none; z-index: 999; cursor: default;" title="إظهار أزرار الطباعة (3 ضغطات)"></button>
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem); display: flex; align-items: center; gap: 10px;">
            <span><i class="fa-solid fa-ticket" style="color: #9C27B0;"></i> الاستقبال — إدارة وإصدار التذاكر</span>
            <button onclick="Livewire.dispatch('openDailyPurchaseModal')" class="btn" style="background: linear-gradient(135deg, #FF9800, #F57C00); color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); font-weight: bold; font-size: 0.85rem; box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3); transition: all 0.3s;">
                <i class="fa-solid fa-cart-plus"></i> طلبات
            </button>
        </h2>
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            
            <div class="secret-print-target" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-left: 10px;">

                {{-- Date + Time Filters Row --}}
                <div style="display: flex; align-items: center; gap: 8px; background: rgba(0,0,0,0.2); padding: 6px 10px; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.1); flex-wrap: wrap;">
                    <i class="fa-regular fa-calendar" style="color: var(--primary-color);"></i>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <label style="font-size: 0.78rem; color: var(--text-secondary); white-space: nowrap;">من تاريخ:</label>
                        <input type="date" wire:model.live="filterStartDate" class="form-control" style="width: auto; padding: 0.15rem 0.4rem; min-height: 28px; font-size: 0.8rem;">
                    </div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <label style="font-size: 0.78rem; color: var(--text-secondary); white-space: nowrap;">إلى تاريخ:</label>
                        <input type="date" wire:model.live="filterEndDate" class="form-control" style="width: auto; padding: 0.15rem 0.4rem; min-height: 28px; font-size: 0.8rem;">
                    </div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <label style="font-size: 0.78rem; color: var(--primary-color); white-space: nowrap;"><i class="fa-solid fa-clock"></i> من:</label>
                        <input type="time" wire:model.live="filterStartTime" class="form-control" style="width: 100px; padding: 0.15rem 0.4rem; min-height: 28px; font-size: 0.8rem;">
                    </div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <label style="font-size: 0.78rem; color: var(--primary-color); white-space: nowrap;">إلى:</label>
                        <input type="time" wire:model.live="filterEndTime" class="form-control" style="width: 100px; padding: 0.15rem 0.4rem; min-height: 28px; font-size: 0.8rem;">
                    </div>
                    @if($filterStartTime || $filterEndTime)
                        <button wire:click="$set('filterStartTime', '')" onclick="document.querySelector('[wire\\:model\\.live=filterEndTime]').dispatchEvent(new Event('change'))" style="background: none; border: none; color: var(--danger-color); cursor: pointer; font-size: 0.9rem; padding: 0 2px;" title="مسح الساعات">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                    @if($filterStartTime && $filterEndTime)
                        <span style="background: rgba(212,175,55,0.2); color: var(--primary-color); font-size: 0.75rem; padding: 2px 7px; border-radius: 12px; font-weight: bold;">
                            {{ $filterStartTime }} ← {{ $filterEndTime }}
                        </span>
                    @endif
                </div>

                {{-- Print Report Buttons (Ticket) --}}
                <div style="display: flex; align-items: center; background: rgba(156, 39, 176, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(156, 39, 176, 0.3);">
                    <span style="font-size: 0.85rem; color: #9C27B0; padding: 0 10px; font-weight: bold; flex: 1;">طباعة تقرير اليوم:</span>
                    <a href="/invoice/ticket-report-print-thermal?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: #9C27B0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 4px; margin-left: 4px;">
                        <i class="fa-solid fa-receipt"></i> حراري
                    </a>
                    <a href="/invoice/ticket-report-print?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank"
                       class="btn"
                       style="background: #7B1FA2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 4px;">
                        <i class="fa-solid fa-file-lines"></i> A4
                    </a>
                </div>

                {{-- Manager approvals print --}}
                <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                    <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">موافقات المدير:</span>
                    <a href="/invoice/manager-approved-print-thermal?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank" class="btn"
                       style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                        <i class="fa-solid fa-receipt"></i> حراري
                    </a>
                    <a href="/invoice/manager-approved-print?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank" class="btn"
                       style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                        <i class="fa-solid fa-file-lines"></i> A4
                    </a>
                </div>

                {{-- Waste print --}}
                <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                    <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">تالف معتمد:</span>
                    <a href="/invoice/waste-print-thermal?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank" class="btn"
                       style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                        <i class="fa-solid fa-receipt"></i> حراري
                    </a>
                    <a href="/invoice/waste-print?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank" class="btn"
                       style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                        <i class="fa-solid fa-file-lines"></i> A4
                    </a>
                </div>

                {{-- Inventory print --}}
                <div style="display: flex; align-items: center; background: rgba(25, 118, 210, 0.05); border-radius: var(--radius-sm); padding: 4px; border: 1px solid rgba(25, 118, 210, 0.3);">
                    <span style="font-size: 0.8rem; color: #1976D2; padding: 0 10px; font-weight: bold; flex: 1;">تقرير المبيعات/الجرد:</span>
                    <a href="/invoice/inventory-print-thermal?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank" class="btn"
                       style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px; margin-left: 4px;">
                        <i class="fa-solid fa-receipt"></i> حراري
                    </a>
                    <a href="/invoice/inventory-print?start_date={{ $filterStartDate }}&end_date={{ $filterEndDate }}{{ $filterStartTime ? '&start_time='.$filterStartTime : '' }}{{ $filterEndTime ? '&end_time='.$filterEndTime : '' }}"
                       target="_blank" class="btn"
                       style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                        <i class="fa-solid fa-file-lines"></i> A4
                    </a>
                </div>
            </div>

            <span style="color: var(--success-color); font-size: 0.8rem; margin-right: 10px;">
                <i class="fa-solid fa-circle" style="font-size: 6px;"></i> تحديث مباشر
            </span>
        </div>
    </div>

    @if(session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div style="background: rgba(244,67,54,0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="grid grid-cols-4" style="margin-bottom: 1.5rem; gap: 1rem;">
        <div class="card" style="text-align: center; border-top: 3px solid #9C27B0; padding: clamp(0.7rem, 2vw, 1rem);">
            <i class="fa-solid fa-ticket" style="color: #9C27B0; font-size: clamp(1rem, 3vw, 1.5rem); margin-bottom: 0.4rem;"></i>
            <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">إجمالي التذاكر</h3>
            <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: #9C27B0;">{{ $stats['total'] }}</p>
        </div>
        <div class="card" style="text-align: center; border-top: 3px solid var(--warning-color); padding: clamp(0.7rem, 2vw, 1rem);">
            <i class="fa-solid fa-hourglass-half" style="color: var(--warning-color); font-size: clamp(1rem, 3vw, 1.5rem); margin-bottom: 0.4rem;"></i>
            <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">في الانتظار</h3>
            <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: var(--warning-color);">{{ $stats['active'] }}</p>
        </div>
        <div class="card" style="text-align: center; border-top: 3px solid var(--success-color); padding: clamp(0.7rem, 2vw, 1rem);">
            <i class="fa-solid fa-circle-check" style="color: var(--success-color); font-size: clamp(1rem, 3vw, 1.5rem); margin-bottom: 0.4rem;"></i>
            <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">مُستخدمة</h3>
            <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: var(--success-color);">{{ $stats['used'] }}</p>
        </div>
        <div class="card" style="text-align: center; border-top: 3px solid var(--danger-color); padding: clamp(0.7rem, 2vw, 1rem);">
            <i class="fa-solid fa-ban" style="color: var(--danger-color); font-size: clamp(1rem, 3vw, 1.5rem); margin-bottom: 0.4rem;"></i>
            <h3 style="color: var(--text-secondary); font-size: clamp(0.7rem, 2vw, 0.85rem);">ملغاة</h3>
            <p style="font-size: clamp(1.3rem, 5vw, 2rem); font-weight: 800; color: var(--danger-color);">{{ $stats['cancelled'] }}</p>
        </div>
    </div>

    <div class="layout-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

        {{-- Create / Edit Template (Manager Only) --}}
        <div class="card" style="border-top: 3px solid #2196F3;">
            <h3 style="margin-bottom: 1.2rem; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; color: #2196F3;">
                <i class="fa-solid fa-{{ $editTemplateId ? 'pen-to-square' : 'folder-plus' }}"></i> 
                {{ $editTemplateId ? 'تعديل قالب التذكرة' : 'إنشاء قالب تذكرة جديد' }}
            </h3>

            @if(session()->has('success_template'))
                <div style="color: var(--success-color); font-size: 0.9rem; margin-bottom: 1rem; background: rgba(76,175,80,0.08); padding: 0.6rem; border-radius: 6px;">
                    <i class="fa-solid fa-check"></i> {{ session('success_template') }}
                </div>
            @endif

            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label class="form-label">
                    <i class="fa-solid fa-tag" style="color: #2196F3;"></i> اسم التذكرة (مثال: 50)
                </label>
                <input type="text" wire:model="newTemplateName" class="form-control" placeholder="أدخل اسم التذكرة">
                @error('newTemplateName') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.8rem;">
                <label class="form-label">
                    <i class="fa-solid fa-list-ol" style="color: #2196F3;"></i> أقصى عدد للطلبات في التذكرة
                </label>
                <input type="number" wire:model="newTemplateMaxItems" class="form-control" min="1" max="10" placeholder="مثال: 1 أو 2">
                @error('newTemplateMaxItems') <span style="color: var(--danger-color); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.8rem;">
                <label class="form-label">
                    <i class="fa-solid fa-boxes-stacked" style="color: #2196F3;"></i> المنتجات المخصصة لهذا القالب
                </label>
                
                {{-- Existing Products Checkboxes --}}
                @if($products && $products->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; max-height: 200px; overflow-y: auto; padding: 10px; background: rgba(0,0,0,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); margin-bottom: 10px;">
                        @foreach($products as $prod)
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 5px;">
                                <input type="checkbox" wire:model="newTemplateProducts" value="{{ $prod->name }}" style="width: 18px; height: 18px;">
                                <span style="font-size: 0.9rem;">{{ $prod->name }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif

                {{-- Added Custom Products Tags --}}
                @if(count($newTemplateProducts) > 0)
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                        @foreach($newTemplateProducts as $p)
                            @php
                                // If it's not in the main products list, show it clearly as custom
                                $isCustom = !$products->contains('name', $p);
                            @endphp
                            <div style="background: {{ $isCustom ? '#9C27B0' : '#2196F3' }}; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
                                {{ $p }}
                                <button type="button" wire:click="removeProduct('{{ addslashes($p) }}')" style="background: none; border: none; color: #fff; cursor: pointer; font-size: 1rem; line-height: 1;">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Add Custom Product Field --}}
                <div style="display: flex; gap: 8px;">
                    <input type="text" wire:model="customProductInput" wire:keydown.enter.prevent="addCustomProduct" class="form-control" placeholder="أو أضف منتج جديد غير موجود بالقائمة..." style="flex: 1;">
                    <button type="button" wire:click="addCustomProduct" class="btn" style="background: #9C27B0; color: #fff; white-space: nowrap; border: none; padding: 0 15px;">
                        <i class="fa-solid fa-plus"></i> إضافة
                    </button>
                </div>

                @error('newTemplateProducts') <span style="color: var(--danger-color); font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 10px;">
                @if($editTemplateId)
                    <button wire:click="updateTemplate" class="btn" style="flex: 1; background: linear-gradient(135deg, #4CAF50, #388E3C); color: white; padding: 0.8rem; font-size: 1rem; font-weight: bold;">
                        <i class="fa-solid fa-save"></i> حفظ التعديلات
                    </button>
                    <button wire:click="cancelEditTemplate" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid rgba(255,255,255,0.2); padding: 0.8rem 1.2rem;">
                        <i class="fa-solid fa-times"></i> إلغاء
                    </button>
                @else
                    <button wire:click="createTemplate" class="btn" style="width: 100%; background: linear-gradient(135deg, #2196F3, #1976D2); color: white; padding: 0.8rem; font-size: 1rem; font-weight: bold;">
                        <i class="fa-solid fa-plus"></i> إنشاء القالب
                    </button>
                @endif
            </div>

            {{-- Existing Templates --}}
            @if($templates->count() > 0)
                <div style="margin-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                    <h4 style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.8rem;">القوالب الحالية:</h4>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($templates as $tpl)
                            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(33,150,243,0.05); border: 1px solid rgba(33,150,243,0.2); padding: 8px 12px; border-radius: 8px;">
                                <div>
                                    <div style="color: #2196F3; font-weight: bold; font-size: 0.95rem;">{{ $tpl->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                        {{ $tpl->max_items }} {{ $tpl->max_items == 1 ? 'طلب' : 'طلبات' }} 
                                        @if($tpl->products)
                                            | المنتجات: {{ implode('، ', $tpl->products) }}
                                        @endif
                                    </div>
                                </div>
                                <div style="display: flex; gap: 6px;">
                                    <button wire:click="editTemplate({{ $tpl->id }})" class="btn btn-sm" style="background: rgba(33,150,243,0.1); color: #2196F3; border: 1px solid rgba(33,150,243,0.3); padding: 4px 8px;" title="تعديل">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button wire:click="deleteTemplate({{ $tpl->id }})" class="btn btn-sm" style="background: rgba(244,67,54,0.1); color: var(--danger-color); border: 1px solid rgba(244,67,54,0.3); padding: 4px 8px;" onclick="return confirm('هل أنت متأكد من حذف هذا القالب؟')" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Issue Ticket --}}
        <div class="card" style="border-top: 3px solid #9C27B0;">
            <h3 style="margin-bottom: 1.2rem; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; color: #9C27B0;">
                <i class="fa-solid fa-plus-circle"></i> إصدار تذكرة
            </h3>

            {{-- Template Buttons --}}
            @if($templates->count() > 0)
                <label class="form-label" style="margin-bottom: 0.8rem;">
                    <i class="fa-solid fa-hand-pointer" style="color: #9C27B0;"></i> اختر نوع التذكرة:
                </label>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 1.5rem;">
                    @foreach($templates as $template)
                        <button wire:click="selectTemplate({{ $template->id }})" class="btn"
                                style="flex: 1; min-width: 100px; padding: 1.2rem 1rem; font-size: 1.8rem; font-weight: 900;
                                       background: {{ $selectedTemplateId == $template->id ? 'linear-gradient(135deg, #9C27B0, #7B1FA2)' : 'rgba(156,39,176,0.08)' }};
                                       color: {{ $selectedTemplateId == $template->id ? '#fff' : '#9C27B0' }};
                                       border: 2px solid {{ $selectedTemplateId == $template->id ? '#9C27B0' : 'rgba(156,39,176,0.3)' }};
                                       border-radius: 12px; transition: all 0.2s;">
                            {{ $template->name }}
                            <div style="font-size: 0.75rem; font-weight: 600; margin-top: 4px; opacity: 0.85;">
                                {{ $template->max_items == 1 ? 'طلب واحد فقط' : $template->max_items . ' طلبات' }}
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 2rem; color: #777; background: rgba(156,39,176,0.04); border-radius: 8px; border: 1px dashed rgba(156,39,176,0.2); margin-bottom: 1rem;">
                    <i class="fa-solid fa-info-circle" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block; color: #9C27B0;"></i>
                    يرجى إنشاء قالب تذكرة أولاً من القسم الأيسر
                </div>
            @endif

            {{-- Product Selection (visible after picking a template) --}}
            @if($selectedTemplateId)
                @php
                    $activeTemplate = $templates->firstWhere('id', $selectedTemplateId);
                @endphp
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="margin-bottom: 0.6rem;">
                        <i class="fa-solid fa-box-open" style="color: #9C27B0;"></i>
                        اختر المنتجات
                        <span style="font-weight: 400; color: var(--text-secondary);">
                            ({{ count($selectedProducts) }} / {{ $activeTemplate->max_items }})
                        </span>
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 8px; max-height: 250px; overflow-y: auto; padding: 4px;">
                        @if($activeTemplate->products && count($activeTemplate->products) > 0)
                            @foreach($activeTemplate->products as $productName)
                                @php
                                    $isSelected = in_array($productName, $selectedProducts);
                                @endphp
                                <div wire:click="toggleProduct('{{ addslashes($productName) }}')"
                                     style="padding: 10px 8px; text-align: center; border-radius: 10px; cursor: pointer; transition: all 0.2s; font-size: 0.9rem;
                                            background: {{ $isSelected ? 'rgba(156,39,176,0.12)' : 'rgba(255,255,255,0.05)' }};
                                            border: 2px solid {{ $isSelected ? '#9C27B0' : 'rgba(255,255,255,0.1)' }};
                                            color: {{ $isSelected ? '#9C27B0' : 'var(--text-primary)' }};
                                            font-weight: {{ $isSelected ? 'bold' : 'normal' }};">
                                    @if($isSelected)
                                        <i class="fa-solid fa-circle-check" style="color: #9C27B0; margin-left: 4px;"></i>
                                    @endif
                                    {{ $productName }}
                                </div>
                            @endforeach
                        @else
                            <div style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary); padding: 10px;">
                                لا توجد منتجات مخصصة لهذا القالب
                            </div>
                        @endif
                    </div>
                </div>

                <button wire:click="issueTicket" class="btn" wire:loading.attr="disabled"
                        style="width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: bold; background: linear-gradient(135deg, #9C27B0, #7B1FA2);">
                    <span wire:loading.remove wire:target="issueTicket">
                        <i class="fa-solid fa-ticket"></i> إنشاء وطباعة التذكرة
                    </span>
                    <span wire:loading wire:target="issueTicket">
                        <i class="fa-solid fa-spinner fa-spin"></i> جاري الإصدار...
                    </span>
                </button>
            @elseif($templates->count() > 0)
                <div style="text-align: center; padding: 2rem; color: #777; background: rgba(156,39,176,0.04); border-radius: 8px; border: 1px dashed rgba(156,39,176,0.2);">
                    <i class="fa-solid fa-arrow-up" style="font-size: 1.2rem; margin-bottom: 0.5rem; display: block; color: #9C27B0;"></i>
                    اختر نوع التذكرة من الأزرار أعلاه للمتابعة
                </div>
            @endif

            {{-- Last Issued Ticket Preview --}}
            @if($lastTicket)
                <div style="margin-top: 1.5rem; background: rgba(156,39,176,0.08); border: 1px solid rgba(156,39,176,0.3); border-radius: var(--radius-sm); padding: 1.2rem; text-align: center;">
                    <div style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 0.4rem;">آخر تذكرة صدرت</div>
                    <div style="font-size: 2rem; font-weight: 900; color: #9C27B0; margin-bottom: 0.4rem;">
                        #{{ str_pad($lastTicket->id, 4, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="font-family: monospace; font-size: 1rem; background: rgba(0,0,0,0.3); padding: 0.5rem 1rem; border-radius: var(--radius-sm); margin-bottom: 0.7rem; letter-spacing: 2px;">
                        {{ $lastTicket->barcode }}
                    </div>
                    <button wire:click="$dispatch('print-ticket', { ticketId: {{ $lastTicket->id }}, productsCount: {{ is_array($lastTicket->selected_products) && count($lastTicket->selected_products) > 0 ? count($lastTicket->selected_products) : 1 }} })" 
                       class="btn btn-sm"
                       style="background: rgba(156,39,176,0.2); color: #9C27B0; border: 1px solid #9C27B0; cursor: pointer;">
                        <i class="fa-solid fa-print"></i> إعادة طباعة
                    </button>
                </div>
            @endif
        </div>

    </div>

    {{-- Today's Tickets Table --}}
    <div class="card" style="border-top: 3px solid var(--info-color); margin-top: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-size: 1.1rem; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
            <span><i class="fa-solid fa-list" style="color: var(--info-color);"></i> تذاكر اليوم</span>
            <span style="background: #9C27B0; color: #fff; padding: 2px 10px; border-radius: 20px; font-size: 0.88rem; font-weight: bold;">
                {{ $todayTickets->count() }}
            </span>
        </h3>

        @if($todayTickets->isEmpty())
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-secondary);">
                <i class="fa-solid fa-ticket fa-3x" style="opacity: 0.2; margin-bottom: 1rem; display: block; color: #9C27B0;"></i>
                <p>لا توجد تذاكر لليوم بعد.</p>
            </div>
        @else
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="data-table" style="min-width: 550px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الباركود</th>
                            <th>الوقت</th>
                            <th>القالب</th>
                            <th>المنتجات</th>
                            <th>بواسطة</th>
                            <th>الحالة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayTickets as $ticket)
                        <tr wire:key="admin-rcpt-ticket-{{ $ticket->id }}">
                            <td style="font-weight: bold; color: #9C27B0;">#{{ $ticket->id }}</td>
                            <td style="font-family: monospace; font-size: 0.82rem; letter-spacing: 1px;">{{ $ticket->barcode }}</td>
                            <td style="font-size: 0.82rem; color: var(--text-secondary); white-space: nowrap;">
                                <i class="fa-regular fa-clock" style="margin-left: 3px;"></i>{{ $ticket->created_at->format('h:i A') }}
                            </td>
                            <td>{{ $ticket->ticketTemplate->name ?? '—' }}</td>
                            <td style="font-size: 0.82rem;">
                                @if($ticket->selected_products && is_array($ticket->selected_products))
                                    {{ implode('، ', $ticket->selected_products) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="font-size: 0.85rem;">{{ $ticket->user->name ?? '—' }}</td>
                            <td>
                                @if($ticket->status === 'active')
                                    <span class="badge" style="background: rgba(76,175,80,0.15); color: var(--success-color); border: 1px solid var(--success-color);">نشطة</span>
                                @elseif($ticket->status === 'used')
                                    <span class="badge" style="background: rgba(33,150,243,0.15); color: var(--info-color); border: 1px solid var(--info-color);">مستخدمة</span>
                                @else
                                    <span class="badge" style="background: rgba(244,67,54,0.15); color: var(--danger-color); border: 1px solid var(--danger-color);">ملغاة</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <button wire:click="$dispatch('print-ticket', { ticketId: {{ $ticket->id }}, productsCount: {{ is_array($ticket->selected_products) && count($ticket->selected_products) > 0 ? count($ticket->selected_products) : 1 }} })" class="btn btn-sm btn-ghost" title="طباعة">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                    @if($ticket->status === 'active')

                                        <button wire:click="markTicketAsUsed({{ $ticket->id }})"
                                                class="btn btn-sm"
                                                style="background: rgba(33,150,243,0.15); color: var(--info-color); border: 1px solid rgba(33,150,243,0.3); padding: 0.3rem 0.7rem;"
                                                onclick="return confirm('هل أنت متأكد من استخدام هذه التذكرة يدوياً؟')">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Cancelled Tickets Section --}}
    @if(isset($cancelledTickets) && $cancelledTickets->count() > 0)
    <div x-data="{ showCancelled: false }" class="card" style="margin-top: 1.5rem; border-top: 3px solid var(--danger-color); background: rgba(244,67,54,0.02);">
        <button @click="showCancelled = !showCancelled" style="width: 100%; display: flex; justify-content: space-between; align-items: center; background: none; border: none; cursor: pointer; padding: 0; color: var(--text-primary);">
            <h3 style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; color: var(--danger-color);">
                <i class="fa-solid fa-ban"></i> التذاكر الملغاة
                <span style="background: var(--danger-color); color: #fff; padding: 2px 10px; border-radius: 20px; font-size: 0.88rem; font-weight: bold;">{{ $cancelledTickets->count() }}</span>
            </h3>
            <i class="fa-solid" :class="showCancelled ? 'fa-chevron-up' : 'fa-chevron-down'" style="color: var(--danger-color); font-size: 1rem; transition: transform 0.2s;"></i>
        </button>

        <div x-show="showCancelled" x-collapse style="margin-top: 1rem;">
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="data-table" style="min-width: 600px;">
                    <thead>
                        <tr style="background: rgba(244,67,54,0.08);">
                            <th>#</th>
                            <th>الباركود</th>
                            <th>القالب</th>
                            <th>المنتجات</th>
                            <th>أصدرها</th>
                            <th>وقت الإصدار</th>
                            <th>وقت الإلغاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cancelledTickets as $cTicket)
                        <tr wire:key="cancelled-ticket-{{ $cTicket->id }}" style="background: rgba(244,67,54,0.03);">
                            <td style="font-weight: bold; color: var(--danger-color);">#{{ $cTicket->id }}</td>
                            <td style="font-family: monospace; font-size: 0.82rem; letter-spacing: 1px; text-decoration: line-through; opacity: 0.7;">{{ $cTicket->barcode }}</td>
                            <td>{{ $cTicket->ticketTemplate->name ?? '—' }}</td>
                            <td style="font-size: 0.82rem;">
                                @if($cTicket->selected_products && is_array($cTicket->selected_products))
                                    {{ implode('، ', $cTicket->selected_products) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="font-size: 0.85rem;">{{ $cTicket->user->name ?? '—' }}</td>
                            <td style="font-size: 0.82rem; color: var(--text-secondary);">{{ $cTicket->created_at->format('h:i A') }}</td>
                            <td style="font-size: 0.82rem; color: var(--danger-color); font-weight: 600;">{{ $cTicket->updated_at->format('h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    @endif

    {{-- Daily Closure Reports --}}
    @if(isset($dailyReports))
    <div class="card" style="margin-top: 1.5rem; border-top: 3px solid #FF9800;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
            <h3 style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice" style="color: #FF9800;"></i> تقارير إغلاق الفترات المحاسبية (الاستقبال)
            </h3>
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div class="form-group" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <label class="form-label" style="margin: 0; white-space: nowrap;">من تاريخ</label>
                    <input type="date" wire:model.live="reportStartDate" class="form-control" style="padding: 0.3rem; height: auto;">
                </div>
                <div class="form-group" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <label class="form-label" style="margin: 0; white-space: nowrap;">إلى تاريخ</label>
                    <input type="date" wire:model.live="reportEndDate" class="form-control" style="padding: 0.3rem; height: auto;">
                </div>
                
                <div style="display: flex; align-items: center; gap: 5px;">
                    <a href="/invoice/ticket-report-print-thermal?start_date={{ $reportStartDate }}&end_date={{ $reportEndDate }}"
                       target="_blank" class="btn"
                       style="background: #1565C0; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                        <i class="fa-solid fa-receipt"></i> طباعة الكل (حراري)
                    </a>
                    <a href="/invoice/ticket-report-print?start_date={{ $reportStartDate }}&end_date={{ $reportEndDate }}"
                       target="_blank" class="btn"
                       style="background: #1976D2; color: #fff; border: none; font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                        <i class="fa-solid fa-file-lines"></i> طباعة الكل (A4)
                    </a>
                </div>
            </div>
        </div>
        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            @if($dailyReports && $dailyReports->count() > 0)
            <table class="data-table" style="min-width: 700px; width: 100%;">
                <thead>
                    <tr>
                        <th>رقم التقرير</th>
                        <th>الموظف</th>
                        <th>وقت الفتح</th>
                        <th>وقت الإغلاق</th>
                        <th>إجمالي التذاكر</th>
                        <th>المباعة</th>
                        <th>الملغاة</th>
                        <th>طباعة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyReports as $report)
                    <tr wire:click="$dispatch('openShiftReport', { reportId: {{ $report->id }} })" style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='rgba(33, 150, 243, 0.05)'" onmouseout="this.style.background='transparent'">
                        <td style="font-weight: bold; color: #1E88E5;">#{{ $report->id }}</td>
                        <td style="font-size: 0.9rem;">{{ $report->user->name ?? 'غير محدد' }}</td>
                        <td style="font-size: 0.85rem; color: var(--text-secondary);">
                            {{ $report->opened_at ? $report->opened_at->format('Y-m-d h:i A') : $report->created_at->format('Y-m-d h:i A') }}
                        </td>
                        <td style="font-size: 0.85rem; color: var(--danger-color); font-weight: bold;">
                            {{ $report->closed_at ? $report->closed_at->format('Y-m-d h:i A') : $report->created_at->format('Y-m-d h:i A') }}
                        </td>
                        <td style="font-size: 1.1rem; font-weight: bold;">{{ $report->total_tickets }}</td>
                        <td style="color: var(--success-color); font-weight: bold; font-size: 1.1rem;">{{ $report->sold_tickets }}</td>
                        <td style="color: var(--danger-color); font-weight: bold; font-size: 1.1rem;">{{ $report->cancelled_tickets }}</td>
                        <td>
                            <a href="/invoice/ticket-report-print-thermal?start_date=shift_{{ $report->id }}" onclick="event.stopPropagation()" target="_blank" class="btn btn-sm" style="background: var(--info-color); color: #fff; border: none; padding: 0.3rem 0.6rem; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-receipt"></i> طباعة
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div style="text-align: center; padding: 3rem 1rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-folder-open fa-3x" style="opacity: 0.2; margin-bottom: 1rem; display: block;"></i>
                    <h4 style="margin: 0; font-weight: normal;">لا توجد تقارير إغلاق مطابقة</h4>
                </div>
            @endif
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('print-ticket', (event) => {
                const ticketId = event[0]?.ticketId || event.ticketId;
                const productsCount = event[0]?.productsCount || event.productsCount || 1;
                
                if (ticketId) {
                    let currentIndex = 0;

                    function printNext() {
                        if (currentIndex >= productsCount) return;

                        const printUrl = `/ticket/${ticketId}/print?product_index=${currentIndex}`;
                        let iframeId = 'admin-rcpt-print-frame-' + currentIndex;
                        let iframe = document.getElementById(iframeId);
                        
                        if (!iframe) {
                            iframe = document.createElement('iframe');
                            iframe.id = iframeId;
                            iframe.style.display = 'none';
                            document.body.appendChild(iframe);
                        }

                        // Don't use auto=1 so the iframe doesn't call print itself. We control it from parent.
                        iframe.src = printUrl + '&t=' + new Date().getTime();
                        
                        iframe.onload = function() {
                            iframe.contentWindow.focus();
                            
                            let jobDone = false;
                            
                            // Listen for afterprint event
                            iframe.contentWindow.addEventListener('afterprint', function() {
                                if (!jobDone) {
                                    jobDone = true;
                                    currentIndex++;
                                    setTimeout(printNext, 1000); // 1s delay before next ticket to let printer cut
                                }
                            });

                            // Call print
                            iframe.contentWindow.print();

                            // Fallback timeout in case afterprint is not supported or silent printing is on
                            setTimeout(() => {
                                if (!jobDone) {
                                    jobDone = true;
                                    currentIndex++;
                                    printNext();
                                }
                            }, 3500); // 3.5 seconds fallback per ticket
                        };
                    }

                    printNext(); // Start the queue
                }
            });
        });
    </script>
    <style>
        .hide-prints .secret-print-target {
            display: none !important;
        }
    </style>
    <script>
        let secretClickCount = 0;
        let secretClickResetTimer;
        let hidePrintTimer;

        // Hide print buttons by default on load
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('hide-prints');
        });

        function handleSecretClick() {
            secretClickCount++;
            
            // Reset click count after 1 second if not clicked again rapidly
            clearTimeout(secretClickResetTimer);
            secretClickResetTimer = setTimeout(() => {
                secretClickCount = 0;
            }, 1000);
            
            // If clicked 3 times rapidly
            if (secretClickCount >= 3) {
                // Show print buttons
                document.body.classList.remove('hide-prints');
                secretClickCount = 0; // Reset counter
                
                // Clear any existing hide timer
                clearTimeout(hidePrintTimer);
                
                // Automatically hide after 30 seconds
                hidePrintTimer = setTimeout(() => {
                    document.body.classList.add('hide-prints');
                }, 30000);
            }
        }
    </script>

    @livewire('create-daily-purchase-request', ['kitchenSource' => 'استقبال'])
</div>
