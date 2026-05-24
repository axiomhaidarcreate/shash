<div>
    <div class="page-header">
        <h1 style="font-size: clamp(1.2rem, 4vw, 1.6rem);"><i class="fa-solid fa-gear" style="color: var(--primary-color);"></i> إعدادات النظام</h1>
        <p>إدارة الطاولات وإعدادات الصالة.</p>
    </div>

    @if (session()->has('success'))
        <div style="background: rgba(76,175,80,0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.8rem 1.2rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 1.2rem;"><i class="fa-solid fa-sliders" style="color: var(--primary-color);"></i> الإعدادات العامة</h3>
        
        <div class="layout-grid-reverse">
            <!-- Lounge Info -->
            <div style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <h4 style="margin-bottom: 1rem; color: var(--text-secondary);"><i class="fa-solid fa-circle-info"></i> معلومات اللاونج</h4>
                <div class="form-group">
                    <label class="form-label">اسم اللاونج</label>
                    <input type="text" wire:model="loungeName" class="form-control" placeholder="اسم اللاونج">
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" wire:model="lounge_phone" class="form-control" placeholder="مثال: 0500000000">
                </div>
                <div class="form-group">
                    <label class="form-label">العنوان</label>
                    <textarea wire:model="lounge_address" class="form-control" rows="2" placeholder="المدينة، الحي، الشارع"></textarea>
                </div>
            </div>

            <!-- System Settings -->
            <div style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <h4 style="margin-bottom: 1rem; color: var(--text-secondary);"><i class="fa-solid fa-cogs"></i> إعدادات النظام</h4>
                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label class="form-label">بداية الدوام</label>
                        <input type="time" wire:model="working_hours_start" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">نهاية الدوام</label>
                        <input type="time" wire:model="working_hours_end" class="form-control">
                    </div>
                </div>
                
            <!-- Printer Settings -->
            <div style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 style="color: var(--text-secondary); margin: 0;"><i class="fa-solid fa-print"></i> إعدادات طابعات الشبكة المحلية</h4>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">مفتاح وكيل الطباعة (API Token)</span>
                        <input type="text" wire:model="print_api_token" class="form-control" style="width: 250px; background-color: #111; color: var(--warning-color); font-family: monospace; font-size: 0.85rem;" readonly>
                    </div>
                </div>
                
                <!-- Printers IPs & Statuses -->
                <div class="grid grid-cols-3" style="gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px dashed var(--border-color);">
                    <!-- Cashier Printer -->
                    <div style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <label class="form-label" style="color: var(--primary-color);">طابعة الكاشير</label>
                            <span class="badge" style="background-color: {{ $printer_cashier_status == 'Online' ? 'var(--success-color)' : ($printer_cashier_status == 'Error' ? 'var(--danger-color)' : '#555') }}; color: #fff; font-size: 0.7rem;">{{ $printer_cashier_status }}</span>
                        </div>
                        <input type="text" wire:model="printer_cashier_ip" class="form-control" dir="ltr" placeholder="192.168.8.189">
                        <button wire:click="printTest('cashier')" type="button" class="btn btn-sm" style="margin-top: 10px; width: 100%; background-color: var(--info-color); color: #fff;">تجربة طباعة</button>
                    </div>

                    <!-- Kitchen Printer -->
                    <div style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <label class="form-label" style="color: var(--warning-color);">طابعة المطبخ</label>
                            <span class="badge" style="background-color: {{ $printer_kitchen_status == 'Online' ? 'var(--success-color)' : ($printer_kitchen_status == 'Error' ? 'var(--danger-color)' : '#555') }}; color: #fff; font-size: 0.7rem;">{{ $printer_kitchen_status }}</span>
                        </div>
                        <input type="text" wire:model="printer_kitchen_ip" class="form-control" dir="ltr" placeholder="مثال: 192.168.8.190">
                        <button wire:click="printTest('kitchen')" type="button" class="btn btn-sm" style="margin-top: 10px; width: 100%; background-color: var(--warning-color); color: #fff;">تجربة طباعة</button>
                    </div>

                    <!-- Shisha Printer -->
                    <div style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <label class="form-label" style="color: #9C27B0;">طابعة الشيشة</label>
                            <span class="badge" style="background-color: {{ $printer_shisha_status == 'Online' ? 'var(--success-color)' : ($printer_shisha_status == 'Error' ? 'var(--danger-color)' : '#555') }}; color: #fff; font-size: 0.7rem;">{{ $printer_shisha_status }}</span>
                        </div>
                        <input type="text" wire:model="printer_shisha_ip" class="form-control" dir="ltr" placeholder="مثال: 192.168.8.191">
                        <button wire:click="printTest('shisha')" type="button" class="btn btn-sm" style="margin-top: 10px; width: 100%; background-color: #9C27B0; color: #fff;">تجربة طباعة</button>
                    </div>
                </div>

                <!-- Global Printer Settings -->
                <h5 style="color: var(--text-secondary); margin-bottom: 1rem;">إعدادات الطباعة العامة (تطبق على جميع الطابعات)</h5>
                <div class="grid grid-cols-3" style="gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">نوع الاتصال</label>
                        <select wire:model="printer_connection_type" class="form-control" style="background-color: #111;">
                            <option value="Network">Network / Ethernet</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">المنفذ الافتراضي (Port)</label>
                        <input type="text" wire:model="printer_port" class="form-control" dir="ltr" placeholder="9100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">حجم الورق</label>
                        <select wire:model="printer_paper_size" class="form-control" style="background-color: #111;">
                            <option value="80mm">80mm</option>
                            <option value="72mm">72mm</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ترميز الحروف</label>
                        <select wire:model="printer_encoding" class="form-control" style="background-color: #111;">
                            <option value="UTF-8">UTF-8</option>
                            <option value="CP864">CP864 (للطباعة العربية)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">كثافة الطباعة</label>
                        <select wire:model="printer_density" class="form-control" style="background-color: #111;">
                            <option value="Light">خفيف</option>
                            <option value="Medium">متوسط (Medium)</option>
                            <option value="Dark">داكن</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2" style="gap: 1rem; margin-top: 1rem;">
                    <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" wire:model="printer_auto_cut" id="autoCutCheck" style="width: 20px; height: 20px; accent-color: var(--primary-color);">
                        <label for="autoCutCheck" style="cursor: pointer; font-weight: 600;">القص الآلي (Auto Cut)</label>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" wire:model="printer_cash_drawer" id="cashDrawerCheck" style="width: 20px; height: 20px; accent-color: var(--primary-color);">
                        <label for="cashDrawerCheck" style="cursor: pointer; font-weight: 600;">فتح درج الكاشير (للكاشير فقط)</label>
                    </div>
                </div>
            </div>
                
                <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 1rem;">
                    <input type="checkbox" wire:model="notifications_enabled" id="notifCheck" style="width: 20px; height: 20px; accent-color: var(--primary-color);">
                    <label for="notifCheck" style="cursor: pointer; font-weight: 600;">تفعيل الإشعارات الصوتية والتنبيهات</label>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 1rem; text-align: left;">
            <button wire:click="saveGeneralSettings" class="btn btn-success" style="padding: 0.8rem 2rem;">
                <i class="fa-solid fa-save"></i> حفظ الإعدادات العامة
            </button>
        </div>
    </div>

    <div class="grid grid-cols-3">
        <!-- Manage Sections -->
        <div class="card">
            <h3 style="margin-bottom: 1.2rem;"><i class="fa-solid fa-layer-group" style="color: var(--primary-color);"></i> إدارة الأقسام / الصالات</h3>
            
            <div class="form-group">
                <label class="form-label">اسم القسم / الصالة الجديد</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" wire:model="newSectionName" class="form-control" placeholder="مثال: صالة رقم 1">
                    <button wire:click="addSection" class="btn btn-sm btn-success" style="height: 44px; min-height: 44px; padding: 0 1rem;">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                @error('newSectionName') <span style="color: var(--danger-color); font-size: 0.8rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-top: 1rem; max-height: 200px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 0.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <label class="form-label" style="margin-bottom: 0.5rem; display: block;"><i class="fa-solid fa-list-ul"></i> الأقسام الحالية:</label>
                @forelse($sections as $sec)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0.6rem; border-bottom: 1px solid rgba(255,255,255,0.05); gap: 10px;" wire:key="sec-row-{{ $sec->id }}">
                        @if($editingSectionId === $sec->id)
                            <input type="text" wire:model="editSectionName" class="form-control" style="padding: 0.3rem 0.6rem; font-size: 0.85rem; height: 32px; min-height: 32px; background: #000; color: #fff; border: 1px solid var(--primary-color);">
                            <div style="display: flex; gap: 4px;">
                                <button wire:click="updateSection" class="btn btn-sm btn-success" style="padding: 0 0.5rem; min-height: 32px; height: 32px;" title="حفظ"><i class="fa-solid fa-check" style="font-size:0.8rem;"></i></button>
                                <button wire:click="cancelEditSection" class="btn btn-sm" style="padding: 0 0.5rem; min-height: 32px; height: 32px; background:#444; color:#fff;" title="إلغاء"><i class="fa-solid fa-xmark" style="font-size:0.8rem;"></i></button>
                            </div>
                        @else
                            <span style="font-weight: 700; color: var(--primary-color); font-size: 0.9rem;">{{ $sec->name }}</span>
                            <div style="display: flex; gap: 4px;">
                                <button wire:click="editSection({{ $sec->id }})" class="btn btn-sm btn-ghost" style="padding: 0 0.5rem; min-height: 32px; height: 32px;" title="تعديل"><i class="fa-solid fa-pen" style="color: var(--info-color); font-size:0.8rem;"></i></button>
                                <button wire:click="deleteSection({{ $sec->id }})" class="btn btn-sm btn-ghost" style="padding: 0 0.5rem; min-height: 32px; height: 32px;" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا القسم؟ سيتم إزالة القسم من جميع طاولاته.')"><i class="fa-solid fa-trash" style="color: var(--danger-color); font-size:0.8rem;"></i></button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-secondary); font-size: 0.8rem; padding: 0.5rem;">لا توجد أقسام مضافة بعد. يرجى إضافة قسم أولاً.</div>
                @endforelse
            </div>
        </div>

        <!-- Add Table -->
        <div class="card">
            <h3 style="margin-bottom: 1.2rem;"><i class="fa-solid fa-plus-circle" style="color: var(--primary-color);"></i> إضافة طاولة جديدة</h3>

            <div class="form-group">
                <label class="form-label">رقم / اسم الطاولة</label>
                <input type="text" wire:model="newTableNumber" class="form-control" placeholder="مثال: T11 أو VIP-1">
                @error('newTableNumber') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">عدد المقاعد</label>
                <input type="number" wire:model="newTableCapacity" class="form-control" min="1" max="20">
            </div>

            <div class="form-group">
                <label class="form-label">القسم / الصالة (إجباري)</label>
                <select wire:model="newTableSection" class="form-control">
                    <option value="">-- اختر القسم/الصالة --</option>
                    @foreach($sections as $sec)
                        <option value="{{ $sec->name }}">{{ $sec->name }}</option>
                    @endforeach
                </select>
                @error('newTableSection') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <button wire:click="addTable" class="btn" style="width: 100%;" @if($sections->isEmpty()) disabled title="يرجى إضافة قسم أولاً" @endif>
                <i class="fa-solid fa-plus"></i> إضافة الطاولة
            </button>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <h3 style="margin-bottom: 1.2rem;"><i class="fa-solid fa-bolt" style="color: var(--warning-color);"></i> إجراءات سريعة</h3>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <button wire:click="resetAllTables" class="btn btn-warning" style="width: 100%; padding: 1rem;" onclick="return confirm('سيتم إعادة تعيين جميع الطاولات إلى \"فارغة\". هل أنت متأكد؟')">
                    <i class="fa-solid fa-rotate-left"></i> إعادة تعيين جميع الطاولات
                </button>

                <div style="padding: 1rem; background: rgba(255,255,255,0.03); border-radius: var(--radius-sm);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: var(--text-secondary);">إجمالي الطاولات</span>
                        <span style="font-weight: 800; color: var(--primary-color);">{{ $tables->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: var(--text-secondary);">طاولات فارغة</span>
                        <span style="font-weight: 800; color: var(--success-color);">{{ $tables->where('status', 'empty')->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">طاولات مشغولة</span>
                        <span style="font-weight: 800; color: var(--warning-color);">{{ $tables->where('status', '!=', 'empty')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables List -->
    <div class="card" style="margin-top: 1.5rem; background: linear-gradient(145deg, #1f1f1f, #252525); border: 1px solid #333;">
        <h3 style="margin-bottom: 1rem;"><i class="fa-solid fa-border-all" style="color: var(--primary-color);"></i> جميع الطاولات</h3>

        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.05); margin: 0 -0.5rem; padding: 0 0.5rem;">
            <table class="data-table" style="min-width: 480px; background-color: #222;">
                <thead style="background-color: #2a2a2a;">
                    <tr>
                        <th>الرقم</th>
                        <th>المقاعد</th>
                        <th>القسم</th>
                        <th>الحالة</th>
                        <th style="width: 150px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tables as $table)
                    @php
                        $statusLabel = match($table->status) {
                            'empty' => 'فارغة',
                            'customer_seated' => 'يوجد عميل',
                            'order_prep' => 'قيد التحضير',
                            'order_ready' => 'جاهز',
                            'delivered' => 'تم التسليم',
                        };
                        $statusClass = match($table->status) {
                            'empty' => 'badge-inactive',
                            'customer_seated' => 'badge-pending',
                            'order_prep' => 'badge-prep',
                            'order_ready' => 'badge-ready',
                            'delivered' => 'badge-delivered',
                        };
                    @endphp
                    
                    @if($editingTableId === $table->id)
                    <tr style="background-color: rgba(212, 175, 55, 0.1);" wire:key="table-edit-{{ $table->id }}">
                        <td>
                            <input type="text" wire:model="editTableNumber" class="form-control" style="padding: 0.5rem; font-size: 0.9rem; background: #000; border: 1px solid var(--primary-color); color: #fff;">
                        </td>
                        <td>
                            <input type="number" wire:model="editTableCapacity" class="form-control" style="padding: 0.5rem; font-size: 0.9rem; max-width: 80px; background: #000; border: 1px solid var(--primary-color); color: #fff;">
                        </td>
                        <td>
                            <select wire:model="editTableSection" class="form-control" style="padding: 0.5rem; font-size: 0.9rem; background: #000; border: 1px solid var(--primary-color); color: #fff; height: 38px; min-height: 38px;">
                                <option value="">-- اختر القسم/الصالة --</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec->name }}">{{ $sec->name }}</option>
                                @endforeach
                            </select>
                            @error('editTableSection') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </td>
                        <td><span class="badge {{ $statusClass }}" style="white-space: nowrap;">{{ $statusLabel }}</span></td>
                        <td>
                            <div style="display: flex; gap: 6px; white-space: nowrap;">
                                <button wire:click="updateTable" class="btn btn-sm btn-success" style="padding: 0.4rem 0.8rem;" title="حفظ">
                                    <i class="fa-solid fa-check"></i> حفظ
                                </button>
                                <button wire:click="cancelEdit" class="btn btn-sm" style="background: #444; color: #fff; padding: 0.4rem 0.8rem;" title="إلغاء">
                                    <i class="fa-solid fa-xmark"></i> إلغاء
                                </button>
                            </div>
                        </td>
                    </tr>
                    @else
                    <tr wire:key="table-view-{{ $table->id }}">
                        <td style="font-weight: 700; color: var(--primary-color);">{{ $table->number }}</td>
                        <td>{{ $table->capacity }} <span style="color: var(--text-secondary);">مقاعد</span></td>
                        <td>{{ $table->section ?? '—' }}</td>
                        <td><span class="badge {{ $statusClass }}" style="white-space: nowrap;">{{ $statusLabel }}</span></td>
                        <td>
                            <div style="display: flex; gap: 6px; white-space: nowrap;">
                                <button wire:click="editTable({{ $table->id }})" class="btn btn-sm" title="تعديل" style="background: #2196F3; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                    <i class="fa-solid fa-pen"></i> تعديل
                                </button>
                                @if($table->status !== 'empty')
                                    <button wire:click="resetTable({{ $table->id }})" class="btn btn-sm" title="إعادة تعيين" style="background: #FF9800; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                        <i class="fa-solid fa-rotate-left"></i> تفريغ
                                    </button>
                                @endif
                                <button wire:click="deleteTable({{ $table->id }})" class="btn btn-sm" title="حذف" style="background: #F44336; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من حذف هذه الطاولة؟')">
                                    <i class="fa-solid fa-trash"></i> حذف
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
