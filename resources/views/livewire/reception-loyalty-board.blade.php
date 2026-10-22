<x-slot name="title">برنامج الولاء - الاستقبال</x-slot>

<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="font-size: clamp(1.1rem, 4vw, 1.4rem);">
            <i class="fa-solid fa-gift" style="color: var(--primary-color);"></i> الاستقبال - برنامج الولاء
        </h2>
        <div style="display: flex; gap: 10px;">
            <button wire:click="openRegisterModal" class="btn btn-success" style="font-weight: bold;">
                <i class="fa-solid fa-user-plus"></i> تسجيل عميل جديد
            </button>
            <a href="{{ route('reception') }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">
                <i class="fa-solid fa-arrow-right"></i> عودة للاستقبال
            </a>
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

    <div class="layout-grid">
        {{-- Search Section --}}
        <div class="card" style="border-top: 3px solid var(--primary-color); position: relative;">
            <h3 style="margin-bottom: 1.2rem; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-search" style="color: var(--primary-color);"></i> البحث عن عميل (بالرقم، الجوال، أو الاسم)
            </h3>
            
            <form wire:submit.prevent="searchCustomer" style="display: flex; gap: 10px; margin-bottom: 1rem; position: relative;">
                <div style="position: relative; flex: 1;">
                    <input type="text" wire:model.live.debounce.300ms="searchQuery" class="form-control" placeholder="أدخل أول أرقام من رقم الجوال، الاسم، أو رقم الاشتراك..." style="font-size: 1.1rem; padding: 0.8rem 1rem;" autocomplete="off">
                    @if($searchQuery)
                        <button type="button" wire:click="clearSelectedCustomer" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--danger-color); font-size: 1.2rem; cursor: pointer;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0 1.5rem; font-size: 1.1rem;">
                    <i class="fa-solid fa-search"></i> بحث
                </button>
            </form>

            {{-- Live Search Results Dropdown/List --}}
            @if(count($searchResults) > 0)
                <div style="background: var(--surface-color); border: 1px solid var(--primary-color); border-radius: var(--radius-sm); margin-bottom: 1.5rem; box-shadow: 0 8px 25px rgba(0,0,0,0.4); max-height: 300px; overflow-y: auto; z-index: 100;">
                    <div style="padding: 0.5rem 1rem; background: rgba(212,175,55,0.1); border-bottom: 1px solid var(--border-color); font-size: 0.85rem; color: var(--primary-color); font-weight: bold;">
                        <i class="fa-solid fa-users"></i> نتائح البحث المتطابقة (اضغط على العميل لإظهار تفاصيله):
                    </div>
                    @foreach($searchResults as $resCustomer)
                        @php $resRemaining = $resCustomer->total_points - $resCustomer->used_points; @endphp
                        <div wire:click="selectCustomer({{ $resCustomer->id }})" 
                             style="padding: 0.8rem 1.2rem; border-bottom: 1px solid var(--border-color); cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s ease; background: {{ $foundCustomer && $foundCustomer->id == $resCustomer->id ? 'rgba(76,175,80,0.1)' : 'transparent' }};"
                             onmouseover="this.style.background='rgba(212,175,55,0.08)'" 
                             onmouseout="this.style.background='{{ $foundCustomer && $foundCustomer->id == $resCustomer->id ? 'rgba(76,175,80,0.1)' : 'transparent' }}'">
                            <div>
                                <div style="font-weight: bold; font-size: 1.05rem; color: var(--primary-color);">
                                    <i class="fa-solid fa-user"></i> {{ $resCustomer->name }}
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-secondary); display: flex; gap: 15px; margin-top: 3px;">
                                    <span><i class="fa-solid fa-phone"></i> {{ $resCustomer->phone ?: 'بدون جوال' }}</span>
                                    <span><i class="fa-solid fa-id-card"></i> رقم الاشتراك: <strong>{{ $resCustomer->subscription_number }}</strong></span>
                                </div>
                            </div>
                            <div style="text-align: left;">
                                <span class="badge badge-active" style="font-size: 0.85rem;">
                                    {{ $resRemaining }} نقطة
                                </span>
                                @if($resCustomer->subscriptionPackage && $resCustomer->hasActiveSubscription())
                                    <span class="badge" style="background: rgba(0,188,212,0.2); color: #00BCD4; border: 1px solid #00BCD4; margin-right: 4px; font-size: 0.75rem;">
                                        <i class="fa-solid fa-crown"></i> مشترِك
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(!$foundCustomer && !empty($searchQuery) && session()->has('error'))
                <div style="text-align: center; padding: 2rem; background: rgba(244,67,54,0.05); border-radius: var(--radius-sm); border: 1px dashed var(--danger-color); margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-user-xmark" style="font-size: 2rem; color: var(--danger-color); margin-bottom: 1rem; display: block;"></i>
                    <p style="color: var(--danger-color); font-weight: bold;">العميل غير مسجل في برنامج الولاء.</p>
                    <button wire:click="openRegisterModal" class="btn btn-success mt-2">تسجيل العميل الآن</button>
                </div>
            @endif
        </div>

        {{-- Customer Result Section --}}
        @if($foundCustomer)
        <div class="card" style="border-top: 3px solid var(--success-color);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div>
                    <h3 style="margin-bottom: 0.3rem; font-size: 1.3rem; color: var(--primary-color);">
                        {{ $foundCustomer->name }}
                        @if($foundCustomer->is_phone_verified)
                            <i class="fa-solid fa-circle-check" style="color: #25D366; font-size: 0.9rem;" title="رقم الجوال موثق ✅"></i>
                        @else
                            <i class="fa-solid fa-circle-exclamation" style="color: var(--warning-color); font-size: 0.8rem;" title="رقم الجوال غير موثق"></i>
                        @endif
                    </h3>
                    <div style="color: var(--text-secondary); font-family: monospace; font-size: 1.1rem; letter-spacing: 1px;">
                        رقم الاشتراك: <span style="color: #fff; font-weight: bold;">{{ $foundCustomer->subscription_number }}</span>
                    </div>
                </div>
                <div>
                    <span class="badge badge-active" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                        {{ $foundCustomer->loyaltyOffer ? $foundCustomer->loyaltyOffer->name : 'بدون عرض' }}
                    </span>
                </div>
            </div>

            @php
                $remaining = $foundCustomer->total_points - $foundCustomer->used_points;
                $offer = $foundCustomer->loyaltyOffer;
                $tiers = $offer ? $offer->rewardTiers : collect();
            @endphp

            <div class="grid grid-cols-3 gap-3 mb-4 text-center">
                <div style="background:rgba(255,255,255,0.03); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
                    <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:5px;">إجمالي النقاط</div>
                    <div style="font-size:1.5rem; font-weight:bold; color:var(--info-color);">{{ $foundCustomer->total_points }}</div>
                </div>
                <div style="background:rgba(255,255,255,0.03); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
                    <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:5px;">النقاط المستخدمة</div>
                    <div style="font-size:1.5rem; font-weight:bold; color:var(--warning-color);">{{ $foundCustomer->used_points }}</div>
                </div>
                <div style="background:rgba(212,175,55,0.1); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--primary-color); box-shadow: 0 0 10px rgba(212,175,55,0.2);">
                    <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:5px;">النقاط المتوفرة</div>
                    <div style="font-size:1.8rem; font-weight:bold; color:var(--primary-color);">{{ $remaining }}</div>
                </div>
            </div>

            @if($offer)
                {{-- Multi-tier reward progress --}}
                @if($tiers->count() > 0)
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="font-size: 1rem; margin-bottom: 1rem; color: var(--primary-color); display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-trophy"></i> مستويات المكافآت
                        </h4>

                        @foreach($tiers as $tier)
                            @php
                                $tierProgress = $tier->required_points > 0 ? min(100, ($remaining / $tier->required_points) * 100) : 0;
                                $canRedeem = $remaining >= $tier->required_points;
                                $needed = $tier->required_points - $remaining;
                            @endphp
                            <div style="background: {{ $canRedeem ? 'rgba(76,175,80,0.08)' : 'rgba(255,255,255,0.02)' }}; border: 1px solid {{ $canRedeem ? 'var(--success-color)' : 'var(--border-color)' }}; border-radius: var(--radius-sm); padding: 0.8rem 1rem; margin-bottom: 0.8rem; transition: all 0.3s ease;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        @if($canRedeem)
                                            <i class="fa-solid fa-circle-check" style="color: var(--success-color);"></i>
                                        @else
                                            <i class="fa-solid fa-lock" style="color: var(--text-secondary); font-size: 0.85rem;"></i>
                                        @endif
                                        <span style="font-weight: bold; font-size: 0.95rem;">{{ $tier->reward_description }}</span>
                                    </div>
                                    <span class="badge {{ $canRedeem ? 'badge-success' : 'badge-prep' }}" style="font-size: 0.8rem;">{{ $tier->required_points }} نقطة</span>
                                </div>
                                
                                {{-- Progress bar --}}
                                <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden; margin-bottom: 6px;">
                                    <div style="height: 100%; width: {{ $tierProgress }}%; background: {{ $canRedeem ? 'var(--success-color)' : 'var(--primary-color)' }}; border-radius: 3px; transition: width 0.5s ease;"></div>
                                </div>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.8rem; color: var(--text-secondary);">
                                        @if($canRedeem)
                                            <span style="color: var(--success-color);">✅ متاح للاستبدال</span>
                                        @else
                                            باقي {{ $needed }} نقطة
                                        @endif
                                    </span>
                                    
                                    @if($canRedeem)
                                        <button wire:click="redeemReward({{ $tier->id }})" wire:confirm="هل أنت متأكد من استبدال {{ $tier->required_points }} نقطة وحصول العميل على: {{ $tier->reward_description }}؟" class="btn btn-sm" style="background: linear-gradient(135deg, #FF9800, #F57C00); color: #fff; padding: 4px 14px; font-size: 0.85rem; font-weight: bold;">
                                            <i class="fa-solid fa-gift fa-bounce"></i> استبدال
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($offer->required_points > 0 && $offer->reward_description)
                    {{-- Fallback: legacy single reward --}}
                    @php
                        $progress = min(100, ($remaining / $offer->required_points) * 100);
                    @endphp
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 5px;">
                            <span>التقدم نحو المكافأة</span>
                            <span style="color: var(--primary-color); font-weight: bold;">{{ $remaining }} / {{ $offer->required_points }} نقطة</span>
                        </div>
                        <div style="width: 100%; height: 10px; background: rgba(255,255,255,0.1); border-radius: 5px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $progress }}%; background: var(--primary-color); border-radius: 5px; transition: width 0.5s ease;"></div>
                        </div>
                        <div style="text-align: center; margin-top: 8px; font-size: 0.9rem; color: var(--success-color); font-weight: bold;">
                            المكافأة: {{ $offer->reward_description }}
                        </div>
                    </div>
                @endif

                <div style="display: flex; gap: 10px;">
                    <button wire:click="openAddPointsModal" class="btn btn-success" style="flex: 1; padding: 1rem; font-size: 1.1rem;">
                        <i class="fa-solid fa-plus-circle"></i> تسجيل زيارة / إضافة نقاط
                    </button>
                    
                    @if($tiers->count() === 0 && $offer->required_points > 0 && $offer->reward_description)
                        {{-- Legacy single redeem button --}}
                        @if($remaining >= $offer->required_points)
                            <button wire:click="redeemReward" wire:confirm="هل أنت متأكد من استبدال النقاط وحصول العميل على المكافأة؟" class="btn btn-warning" style="flex: 1; padding: 1rem; font-size: 1.1rem; background: linear-gradient(135deg, #FF9800, #F57C00); color: #fff;">
                                <i class="fa-solid fa-gift fa-bounce"></i> استبدال المكافأة الآن
                            </button>
                        @else
                            <button disabled class="btn" style="flex: 1; padding: 1rem; font-size: 1.1rem; background: rgba(255,255,255,0.1); color: var(--text-secondary); cursor: not-allowed;">
                                <i class="fa-solid fa-lock"></i> النقاط غير كافية للاستبدال
                            </button>
                        @endif
                    @endif
                </div>
            @else
                <div style="text-align: center; padding: 1rem; color: var(--warning-color); background: rgba(255,152,0,0.1); border-radius: var(--radius-sm);">
                    هذا العميل غير مرتبط بأي عرض ولاء حالياً. يرجى مراجعة الإدارة.
                </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Modal for Register Customer -->
    @if($showRegisterModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1050; display:flex; justify-content:center; align-items:center;">
            <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); width:90%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <div class="modal-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between">
                        <h5 class="modal-title">تسجيل عميل جديد في برنامج الولاء</h5>
                        <button type="button" class="btn-close btn-ghost" wire:click="closeRegisterModal" style="font-size:1.5rem; line-height:1;">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if(session()->has('register_error'))
                            <div class="text-danger mb-3 font-weight-bold">{{ session('register_error') }}</div>
                        @endif
                        <form wire:submit.prevent="registerCustomer">
                            <div class="form-group mb-3">
                                <label class="form-label">اسم العميل</label>
                                <input type="text" wire:model="newName" class="form-control" required placeholder="أدخل اسم العميل">
                                @error('newName') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">رقم الجوال (لإرسال رسالة الترحيب)</label>
                                <input type="text" wire:model="newPhone" class="form-control" required dir="ltr" placeholder="مثال: 05XXXXXXXX">
                                @error('newPhone') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">اختيار العرض</label>
                                <select wire:model="newOfferId" class="form-control" required>
                                    <option value="">-- اختر عرض الولاء --</option>
                                    @foreach($offers as $offer)
                                        @php
                                            $tiersDesc = '';
                                            if ($offer->rewardTiers && $offer->rewardTiers->count() > 0) {
                                                $tiersDesc = ' (' . $offer->rewardTiers->map(fn($t) => $t->required_points . ':' . $t->reward_description)->implode('، ') . ')';
                                            } elseif ($offer->required_points > 0 && $offer->reward_description) {
                                                $tiersDesc = ' (' . $offer->required_points . ':' . $offer->reward_description . ')';
                                            }
                                        @endphp
                                        <option value="{{ $offer->id }}">{{ $offer->name }}{{ $tiersDesc }}</option>
                                    @endforeach
                                </select>
                                @error('newOfferId') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-secondary">
                                <button type="button" wire:click="closeRegisterModal" class="btn btn-ghost">إلغاء</button>
                                <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="registerCustomer">تسجيل العميل</span>
                                    <span wire:loading wire:target="registerCustomer"><i class="fa-solid fa-spinner fa-spin"></i> جاري التسجيل...</span>
                                </button>
                            </div>
                        </form>
                    </div>
        </div>
    @endif

    <!-- Modal for Add Points -->
    @if($showAddPointsModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1050; display:flex; justify-content:center; align-items:center;">
            <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); width:90%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <div class="modal-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between">
                        <h5 class="modal-title text-success">إضافة نقاط للعميل: {{ $foundCustomer->name }}</h5>
                        <button type="button" class="btn-close btn-ghost" wire:click="closeAddPointsModal" style="font-size:1.5rem; line-height:1;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="addPoints">
                            <div class="form-group mb-3">
                                <label class="form-label">سبب إضافة النقاط</label>
                                <select wire:model.live="pointsSource" class="form-control">
                                    <option value="visit">تسجيل زيارة ({{ $foundCustomer->loyaltyOffer->points_per_visit ?? 0 }} نقطة)</option>
                                    <option value="order">طلب مبيعات (بحسب الفاتورة)</option>
                                    <option value="custom">إدخال يدوي</option>
                                </select>
                            </div>
                            
                            @if($pointsSource === 'order')
                                <div class="form-group mb-3">
                                    <label class="form-label">إجمالي مبلغ الفاتورة (بالريال)</label>
                                    <input type="number" wire:model.live="orderAmount" class="form-control" min="0" placeholder="أدخل مبلغ الفاتورة">
                                </div>
                            @endif

                            <div class="form-group mb-4">
                                <label class="form-label">عدد النقاط المضافة</label>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <input type="number" wire:model="pointsToAdd" class="form-control" min="1" {{ $pointsSource !== 'custom' ? 'readonly style=background:rgba(255,255,255,0.05);' : '' }}>
                                    <span style="color: var(--primary-color); font-weight: bold;">نقطة</span>
                                </div>
                                @error('pointsToAdd') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-secondary">
                                <button type="button" wire:click="closeAddPointsModal" class="btn btn-ghost">إلغاء</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check"></i> تأكيد إضافة النقاط
                                </button>
                            </div>
                        </form>
                    </div>
        </div>
    @endif

    <!-- OTP Verification Modal (optional) -->
    @if($showOtpModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1050; display:flex; justify-content:center; align-items:center;">
            <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); width:90%; max-width:420px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); text-align:center;">
                <div style="margin-bottom:1.5rem;">
                    <div style="width:70px; height:70px; margin:0 auto 1rem; background: linear-gradient(135deg, #25D366, #128C7E); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-brands fa-whatsapp" style="font-size:2rem; color:#fff;"></i>
                    </div>
                    <h4 style="color:var(--primary-color); margin-bottom:0.5rem;">التحقق من رقم الجوال</h4>
                    <p style="color:var(--text-secondary); font-size:0.9rem;">تم إرسال رمز تحقق مكون من 4 أرقام إلى الرقم:</p>
                    <div style="font-family:monospace; font-size:1.2rem; color:#fff; font-weight:bold; direction:ltr;">{{ $otpPhone }}</div>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <input type="text" wire:model="enteredOtp" maxlength="4" 
                           style="width:180px; margin:0 auto; display:block; text-align:center; font-size:2rem; font-weight:bold; letter-spacing:15px; font-family:monospace; padding:0.8rem; border:2px solid var(--primary-color); border-radius:var(--radius-sm); background:rgba(0,0,0,0.2); color:var(--primary-color);"
                           placeholder="- - - -">
                </div>

                <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                    <button wire:click="verifyOtp" class="btn btn-success" style="padding:0.7rem 1.5rem; font-size:1rem;">
                        <i class="fa-solid fa-check-circle"></i> تحقق
                    </button>
                    <button wire:click="resendOtp" class="btn" style="padding:0.7rem 1.5rem; font-size:0.9rem; background:rgba(255,255,255,0.1); color:var(--text-primary); border:1px solid var(--border-color);">
                        <i class="fa-solid fa-rotate-right"></i> إعادة الإرسال
                    </button>
                </div>

                <div style="margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--border-color);">
                    <button wire:click="skipOtp" class="btn btn-ghost" style="font-size:0.9rem; color:var(--text-secondary);">
                        <i class="fa-solid fa-forward"></i> تخطي التحقق (اختياري)
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                showToast(event[0].message, event[0].type);
            });
        });
    </script>
</div>
