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
                

                
                <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 1rem;">
                    <input type="checkbox" wire:model="notifications_enabled" id="notifCheck" style="width: 20px; height: 20px; accent-color: var(--primary-color);">
                    <label for="notifCheck" style="cursor: pointer; font-weight: 600;">تفعيل الإشعارات الصوتية والتنبيهات</label>
                </div>

                <div class="form-group" style="margin-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
                    <label class="form-label" style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-palette"></i> مظهر النظام (الثيم)
                    </label>
                    <select wire:model="app_theme" class="form-control" style="max-width: 300px;">
                        <option value="dark">الوضع الليلي (الافتراضي - أسود)</option>
                        <option value="light">الوضع النهاري (أبيض)</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
                    <label class="form-label" style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-key"></i> رمز موافقة المدير (لموظف الاستقبال)
                    </label>
                    <input type="text" wire:model="manager_pin" class="form-control" style="max-width: 300px;" placeholder="مثال: 7925">
                </div>
            </div>
        </div>
        
        <div style="margin-top: 1rem; text-align: left;">
            <button wire:click="saveGeneralSettings" class="btn btn-success" style="padding: 0.8rem 2rem;">
                <i class="fa-solid fa-save"></i> حفظ الإعدادات العامة
            </button>
        </div>
    </div>

    <!-- Messaging Settings Card -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 1.2rem;"><i class="fa-brands fa-whatsapp" style="color: #25D366;"></i> إعدادات المراسلة (WhatsApp / SMS)</h3>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.9rem;">إعدادات ربط النظام مع مزود خدمة الرسائل لإرسال التنبيهات واشتراكات العملاء. استخدم <code>@{{phone}}</code> و <code>@{{message}}</code> كمتغيرات في الرابط.</p>
        
        <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.5rem;">
            <input type="checkbox" wire:model="messaging_enabled" id="msgCheck" style="width: 20px; height: 20px; accent-color: var(--primary-color);">
            <label for="msgCheck" style="cursor: pointer; font-weight: 600;">تفعيل إرسال الرسائل للعملاء</label>
        </div>

        @if($messaging_enabled)
        <div class="grid grid-cols-2">
            <div class="form-group">
                <label class="form-label">نوع المراسلة</label>
                <select wire:model="messaging_type" class="form-control">
                    <option value="whatsapp">واتساب (WhatsApp)</option>
                    <option value="sms">رسائل نصية قصيرة (SMS)</option>
                </select>
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">رابط الـ API (مع المتغيرات)</label>
                <textarea wire:model="messaging_api_url" class="form-control" rows="2" placeholder="مثال: https://api.whatsapp.com/send?phone={{'@'}}phone&text={{'@'}}message" dir="ltr"></textarea>
                <small style="color: var(--text-secondary); display: block; margin-top: 5px;">مثال Jawaly: <code>https://api.jawaly.net/api/v1/sendsms?user=X&pass=Y&to={{'@'}}phone&message={{'@'}}message</code></small>
            </div>
        </div>

        <div style="margin-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.2rem;">
                <h4 style="margin: 0; color: var(--text-secondary);"><i class="fa-solid fa-file-lines"></i> قوالب رسائل الواتساب</h4>
                <span style="font-size: 0.8rem; color: #888; background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 20px;">اتركها فارغة لاستخدام النصوص الافتراضية</span>
            </div>

            {{-- Template 1: Welcome --}}
            <div style="background: rgba(37,211,102,0.04); border: 1px solid rgba(37,211,102,0.15); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
                    <label class="form-label" style="margin: 0; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-hand-wave" style="color: #25D366;"></i> رسالة الترحيب (عند التسجيل)
                    </label>
                    <button type="button" wire:click="useDefaultWelcomeTemplate" style="background: rgba(37,211,102,0.15); border: 1px solid rgba(37,211,102,0.4); color: #25D366; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; cursor: pointer; white-space: nowrap;">
                        <i class="fa-solid fa-rotate-left" style="font-size: 0.7rem;"></i> استخدم الافتراضي
                    </button>
                </div>
                <textarea wire:model="whatsapp_welcome_template" class="form-control" rows="4" dir="rtl"
                    placeholder="مرحباً بك *{name}* في برنامج الولاء! 🎉&#10;&#10;🔑 رقم اشتراكك: *{subscription_number}*&#10;📱 احتفظ بهذا الرقم لتتبع نقاطك.{offer_details}&#10;&#10;نسعد بخدمتك دائماً! 💛"
                    style="font-size: 0.9rem; line-height: 1.6;"></textarea>
                <small style="color: #888; display: block; margin-top: 5px;">المتغيرات: <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{name}</code> <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{subscription_number}</code> <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{offer_details}</code></small>
            </div>

            {{-- Template 2: Points Update --}}
            <div style="background: rgba(33,150,243,0.04); border: 1px solid rgba(33,150,243,0.15); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
                    <label class="form-label" style="margin: 0; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-star" style="color: #2196F3;"></i> رسالة إضافة النقاط
                    </label>
                    <button type="button" wire:click="useDefaultPointsTemplate" style="background: rgba(33,150,243,0.15); border: 1px solid rgba(33,150,243,0.4); color: #2196F3; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; cursor: pointer; white-space: nowrap;">
                        <i class="fa-solid fa-rotate-left" style="font-size: 0.7rem;"></i> استخدم الافتراضي
                    </button>
                </div>
                <textarea wire:model="whatsapp_points_updated_template" class="form-control" rows="4" dir="rtl"
                    placeholder="مرحباً *{name}* 👋&#10;&#10;✅ تم إضافة *{points_added}* نقطة إلى حسابك{reason}&#10;&#10;📊 رصيدك الحالي: *{remaining_points}* نقطة&#10;{next_reward}&#10;شكراً لزيارتك! 💛"
                    style="font-size: 0.9rem; line-height: 1.6;"></textarea>
                <small style="color: #888; display: block; margin-top: 5px;">المتغيرات: <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{name}</code> <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{points_added}</code> <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{reason}</code> <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{remaining_points}</code> <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{next_reward}</code></small>
            </div>

            {{-- Template 3: Reward Redemption --}}
            <div style="background: rgba(212,175,55,0.04); border: 1px solid rgba(212,175,55,0.15); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
                    <label class="form-label" style="margin: 0; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-gift" style="color: var(--primary-color);"></i> رسالة استبدال المكافأة
                    </label>
                    <button type="button" wire:click="useDefaultRedemptionTemplate" style="background: rgba(212,175,55,0.15); border: 1px solid rgba(212,175,55,0.4); color: var(--primary-color); padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; cursor: pointer; white-space: nowrap;">
                        <i class="fa-solid fa-rotate-left" style="font-size: 0.7rem;"></i> استخدم الافتراضي
                    </button>
                </div>
                <textarea wire:model="whatsapp_reward_reached_template" class="form-control" rows="4" dir="rtl"
                    placeholder="مرحباً *{name}* 🎉&#10;&#10;🎁 تم استبدال *{points_used}* نقطة بنجاح!&#10;📦 المكافأة: *{reward_name}*&#10;&#10;📊 رصيدك المتبقي: *{remaining_points}* نقطة&#10;&#10;استمر في جمع النقاط للحصول على مكافآت أكثر! 💛"
                    style="font-size: 0.9rem; line-height: 1.6;"></textarea>
                <small style="color: #888; display: block; margin-top: 5px;">المتغيرات: <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{name}</code> <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{points_used}</code> <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{reward_name}</code> <code style="background:rgba(255,255,255,0.08); padding: 1px 5px; border-radius: 3px;">{remaining_points}</code></small>
            </div>
        </div>
        @endif

        <div style="margin-top: 1rem; text-align: left;">
            <button wire:click="saveGeneralSettings" class="btn btn-success" style="padding: 0.8rem 2rem;">
                <i class="fa-solid fa-save"></i> حفظ إعدادات المراسلة
            </button>
        </div>
    </div>

    <div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(145deg, #1f1f1f, #252525); border: 1px solid var(--border-color);">
        <h3 style="margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
            <i class="fa-solid fa-toggle-on" style="color: var(--primary-color);"></i> التحكم اليدوي بالفترة المحاسبية
        </h3>

        <div style="text-align: center; margin: 1.5rem 0;">
            <div style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 0.5rem;">حالة الفترة المحاسبية الآن:</div>
            @if($activeShift)
                <div style="display: inline-block; background: rgba(76,175,80,0.15); border: 2px solid #4CAF50; color: #4CAF50; padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: bold; font-size: 1.2rem;">
                    <i class="fa-solid fa-door-open"></i> الفترة المحاسبية مفتوحة
                </div>
                <div style="font-size: 0.85rem; color: #888; margin-top: 0.5rem;">
                    تم الفتح بواسطة: {{ $activeShift->openedBy->name ?? 'النظام الآلي' }}<br>
                    الوقت: {{ $activeShift->opened_at->format('Y-m-d h:i A') }}
                </div>
            @else
                <div style="display: inline-block; background: rgba(244,67,54,0.15); border: 2px solid #F44336; color: #F44336; padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: bold; font-size: 1.2rem;">
                    <i class="fa-solid fa-door-closed"></i> الفترة المحاسبية مغلقة
                </div>
            @endif
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button wire:click="startShiftManual" 
                    class="btn" 
                    style="flex: 1; background: #4CAF50; color: white; padding: 1rem; font-size: 1.1rem;"
                    {{ $activeShift ? 'disabled' : '' }}>
                <i class="fa-solid fa-play"></i> بدء العمل الآن (فتح للجميع)
            </button>

            <button wire:click="closeShiftManual" 
                    class="btn" 
                    style="flex: 1; background: #F44336; color: white; padding: 1rem; font-size: 1.1rem;"
                    onclick="return confirm('هل أنت متأكد من إغلاق الفترة المحاسبية لجميع الموظفين؟ سيتم إغلاق يومياتهم ورفع التقارير.')"
                    {{ !$activeShift ? 'disabled' : '' }}>
                <i class="fa-solid fa-stop"></i> إغلاق العمل الآن (إغلاق للجميع)
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

        @php
            $tablesBySection = $tables->groupBy('section');
        @endphp

        @forelse($sections as $sec)
            @php
                $sectionTables = ($tablesBySection[$sec->name] ?? collect())->sortBy('sort_order');
            @endphp
            @if($sectionTables->isNotEmpty())
            <div style="margin-bottom: 1.5rem;">
                <div style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.25); border-radius: var(--radius-sm); padding: 0.6rem 1rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-layer-group" style="color: var(--primary-color);"></i>
                    <span style="font-weight: 700; font-size: 1rem; color: var(--primary-color);">{{ $sec->name }}</span>
                    <span style="color: var(--text-secondary); font-size: 0.85rem; margin-right: auto;">({{ $sectionTables->count() }} طاولة)</span>
                </div>

                <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.05);">
                    <table class="data-table" style="min-width: 520px; background-color: #222;">
                        <thead style="background-color: #2a2a2a;">
                            <tr>
                                <th style="width: 70px;">الترتيب</th>
                                <th>الرقم</th>
                                <th>المقاعد</th>
                                <th>الحالة</th>
                                <th style="width: 200px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sectionTables as $table)
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
                                    <div style="display: flex; flex-direction: column; gap: 2px; align-items: center;">
                                        <button wire:click="moveTableUp({{ $table->id }})" class="btn btn-sm btn-ghost" style="padding: 2px 8px; min-height: 24px; height: 24px; line-height: 1;" title="تحريك لأعلى" @if($loop->first) disabled @endif>
                                            <i class="fa-solid fa-chevron-up" style="font-size: 0.7rem; color: var(--primary-color);"></i>
                                        </button>
                                        <button wire:click="moveTableDown({{ $table->id }})" class="btn btn-sm btn-ghost" style="padding: 2px 8px; min-height: 24px; height: 24px; line-height: 1;" title="تحريك لأسفل" @if($loop->last) disabled @endif>
                                            <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; color: var(--primary-color);"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" wire:model="editTableNumber" class="form-control" style="padding: 0.5rem; font-size: 0.9rem; background: #000; border: 1px solid var(--primary-color); color: #fff;">
                                </td>
                                <td>
                                    <input type="number" wire:model="editTableCapacity" class="form-control" style="padding: 0.5rem; font-size: 0.9rem; max-width: 80px; background: #000; border: 1px solid var(--primary-color); color: #fff;">
                                </td>
                                <td><span class="badge {{ $statusClass }}" style="white-space: nowrap;">{{ $statusLabel }}</span></td>
                                <td>
                                    <div style="display: flex; gap: 6px; white-space: nowrap; flex-wrap: wrap;">
                                        <select wire:model="editTableSection" class="form-control" style="padding: 0.3rem 0.5rem; font-size: 0.85rem; background: #000; border: 1px solid var(--primary-color); color: #fff; height: 34px; min-height: 34px; max-width: 130px;">
                                            <option value="">-- القسم --</option>
                                            @foreach($sections as $s)
                                                <option value="{{ $s->name }}">{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                        <button wire:click="updateTable" class="btn btn-sm btn-success" style="padding: 0.4rem 0.8rem;" title="حفظ">
                                            <i class="fa-solid fa-check"></i> حفظ
                                        </button>
                                        <button wire:click="cancelEdit" class="btn btn-sm" style="background: #444; color: #fff; padding: 0.4rem 0.8rem;" title="إلغاء">
                                            <i class="fa-solid fa-xmark"></i> إلغاء
                                        </button>
                                    </div>
                                    @error('editTableSection') <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span> @enderror
                                </td>
                            </tr>
                            @else
                            <tr wire:key="table-view-{{ $table->id }}">
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 2px; align-items: center;">
                                        <button wire:click="moveTableUp({{ $table->id }})" class="btn btn-sm btn-ghost" style="padding: 2px 8px; min-height: 24px; height: 24px; line-height: 1;" title="تحريك لأعلى" @if($loop->first) disabled @endif>
                                            <i class="fa-solid fa-chevron-up" style="font-size: 0.7rem; color: var(--primary-color);"></i>
                                        </button>
                                        <button wire:click="moveTableDown({{ $table->id }})" class="btn btn-sm btn-ghost" style="padding: 2px 8px; min-height: 24px; height: 24px; line-height: 1;" title="تحريك لأسفل" @if($loop->last) disabled @endif>
                                            <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; color: var(--primary-color);"></i>
                                        </button>
                                    </div>
                                </td>
                                <td style="font-weight: 700; color: var(--primary-color);">{{ $table->number }}</td>
                                <td>{{ $table->capacity }} <span style="color: var(--text-secondary);">مقاعد</span></td>
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
                            <!-- Chairs Management Row -->
                            <tr style="background-color: rgba(0,0,0,0.2);">
                                <td></td>
                                <td colspan="4" style="padding: 0.5rem;">
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
                                        <span style="font-size: 0.85rem; color: var(--text-secondary);"><i class="fa-solid fa-chair"></i> الكراسي:</span>
                                        @foreach($table->chairs->sortBy('sort_order') as $chair)
                                            <div style="background: rgba(255,255,255,0.05); border: 1px solid {{ $chair->status === 'empty' ? 'var(--border-color)' : 'var(--warning-color)' }}; border-radius: var(--radius-sm); padding: 4px 8px; display: flex; align-items: center; gap: 6px;">
                                                <input type="text" wire:blur="renameChair({{ $chair->id }}, $event.target.value)" value="{{ $chair->number }}" class="form-control" style="width: 50px; height: 24px; padding: 2px 4px; font-size: 0.8rem; background: #000; color: #fff; text-align: center; border: none;">
                                                @if($chair->status !== 'empty')
                                                    <button wire:click="resetChair({{ $chair->id }})" class="btn btn-sm" title="تفريغ الكرسي" style="padding: 0 4px; color: var(--warning-color); background: none;"><i class="fa-solid fa-rotate-left"></i></button>
                                                @endif
                                                <button wire:click="removeChair({{ $chair->id }})" class="btn btn-sm" title="حذف الكرسي" style="padding: 0 4px; color: var(--danger-color); background: none;" onclick="return confirm('هل أنت متأكد؟')"><i class="fa-solid fa-times"></i></button>
                                            </div>
                                        @endforeach
                                        <button wire:click="addChair({{ $table->id }})" class="btn btn-sm btn-outline" style="padding: 2px 8px; font-size: 0.8rem;"><i class="fa-solid fa-plus"></i> إضافة كرسي</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @empty
            <div style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد أقسام. يرجى إضافة قسم أولاً.</div>
        @endforelse

        {{-- Tables without a section --}}
        @php
            $unsectionedTables = $tables->filter(fn($t) => empty($t->section))->sortBy('sort_order');
        @endphp
        @if($unsectionedTables->isNotEmpty())
        <div style="margin-bottom: 1.5rem;">
            <div style="background: rgba(244, 67, 54, 0.08); border: 1px solid rgba(244, 67, 54, 0.25); border-radius: var(--radius-sm); padding: 0.6rem 1rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-exclamation-triangle" style="color: var(--danger-color);"></i>
                <span style="font-weight: 700; font-size: 1rem; color: var(--danger-color);">بدون قسم</span>
                <span style="color: var(--text-secondary); font-size: 0.85rem; margin-right: auto;">({{ $unsectionedTables->count() }} طاولة)</span>
            </div>
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.05);">
                <table class="data-table" style="min-width: 520px; background-color: #222;">
                    <thead style="background-color: #2a2a2a;">
                        <tr>
                            <th style="width: 70px;">الترتيب</th>
                            <th>الرقم</th>
                            <th>المقاعد</th>
                            <th>الحالة</th>
                            <th style="width: 200px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unsectionedTables as $table)
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
                        <tr wire:key="table-unsec-{{ $table->id }}">
                            <td style="text-align: center; color: var(--text-secondary);">—</td>
                            <td style="font-weight: 700; color: var(--primary-color);">{{ $table->number }}</td>
                            <td>{{ $table->capacity }} <span style="color: var(--text-secondary);">مقاعد</span></td>
                            <td><span class="badge {{ $statusClass }}" style="white-space: nowrap;">{{ $statusLabel }}</span></td>
                            <td>
                                <div style="display: flex; gap: 6px; white-space: nowrap;">
                                    <button wire:click="editTable({{ $table->id }})" class="btn btn-sm" title="تعديل" style="background: #2196F3; color: #fff; border: none; padding: 0.4rem 0.8rem;">
                                        <i class="fa-solid fa-pen"></i> تعديل
                                    </button>
                                    <button wire:click="deleteTable({{ $table->id }})" class="btn btn-sm" title="حذف" style="background: #F44336; color: #fff; border: none; padding: 0.4rem 0.8rem;" onclick="return confirm('هل أنت متأكد من حذف هذه الطاولة؟')">
                                        <i class="fa-solid fa-trash"></i> حذف
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Chairs Management Row -->
                        <tr style="background-color: rgba(0,0,0,0.2);">
                            <td></td>
                            <td colspan="4" style="padding: 0.5rem;">
                                <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
                                    <span style="font-size: 0.85rem; color: var(--text-secondary);"><i class="fa-solid fa-chair"></i> الكراسي:</span>
                                    @foreach($table->chairs->sortBy('sort_order') as $chair)
                                        <div style="background: rgba(255,255,255,0.05); border: 1px solid {{ $chair->status === 'empty' ? 'var(--border-color)' : 'var(--warning-color)' }}; border-radius: var(--radius-sm); padding: 4px 8px; display: flex; align-items: center; gap: 6px;">
                                            <input type="text" wire:blur="renameChair({{ $chair->id }}, $event.target.value)" value="{{ $chair->number }}" class="form-control" style="width: 50px; height: 24px; padding: 2px 4px; font-size: 0.8rem; background: #000; color: #fff; text-align: center; border: none;">
                                            @if($chair->status !== 'empty')
                                                <button wire:click="resetChair({{ $chair->id }})" class="btn btn-sm" title="تفريغ الكرسي" style="padding: 0 4px; color: var(--warning-color); background: none;"><i class="fa-solid fa-rotate-left"></i></button>
                                            @endif
                                            <button wire:click="removeChair({{ $chair->id }})" class="btn btn-sm" title="حذف الكرسي" style="padding: 0 4px; color: var(--danger-color); background: none;" onclick="return confirm('هل أنت متأكد؟')"><i class="fa-solid fa-times"></i></button>
                                        </div>
                                    @endforeach
                                    <button wire:click="addChair({{ $table->id }})" class="btn btn-sm btn-outline" style="padding: 2px 8px; font-size: 0.8rem;"><i class="fa-solid fa-plus"></i> إضافة كرسي</button>
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
</div>
