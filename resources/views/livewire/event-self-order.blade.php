<div class="flex flex-col items-center justify-center min-h-screen py-12 px-4 sm:px-6">
    <!-- Header -->
    <div class="w-full max-w-xl mb-10 text-center animate-[float_6s_ease-in-out_infinite]">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary/10 border border-primary/30 shadow-[0_0_30px_rgba(212,175,55,0.3)] mb-4 backdrop-blur-sm">
            <i class="fa-solid fa-martini-glass-citrus text-3xl text-primary"></i>
        </div>
        <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-primary via-yellow-200 to-primary mb-3 drop-shadow-lg font-outfit tracking-wide">
            VIP LOUNGE
        </h1>
        <p class="text-gray-300 text-sm md:text-base font-medium tracking-wide">
            {{ $welcomeMsg ?? 'اطلب من مكانك بكل راحة ورفاهية' }}
        </p>
    </div>

    <!-- Error/Success Messages -->
    <div class="w-full max-w-xl mb-6">
        @if (session()->has('error'))
            <div class="glass border-l-4 border-l-red-500 text-red-100 p-4 rounded-xl shadow-lg flex items-center gap-4">
                <div class="bg-red-500/20 p-2 rounded-full">
                    <i class="fa-solid fa-circle-exclamation text-red-400 text-xl"></i>
                </div>
                <p class="font-medium text-sm">{{ session('error') }}</p>
            </div>
        @endif
        @if (session()->has('success'))
            <div class="glass border-l-4 border-l-emerald-500 text-emerald-100 p-4 rounded-xl shadow-lg flex items-center gap-4">
                <div class="bg-emerald-500/20 p-2 rounded-full">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
                </div>
                <p class="font-medium text-sm">{{ session('success') }}</p>
            </div>
        @endif
    </div>

    @if ($step === 'verify')
        <!-- VERIFICATION STEP -->
        <div class="w-full max-w-xl glass-card rounded-3xl p-6 md:p-10">
            <h2 class="text-2xl font-bold mb-8 text-white flex items-center border-b border-white/10 pb-4">
                <i class="fa-solid fa-fingerprint text-primary ml-3 text-2xl"></i>تأكيد الحضور
            </h2>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">اسم المشترك الكامل</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <input type="text" wire:model="customerName" placeholder="مثال: أحمد محمد" class="glass-input w-full rounded-xl pr-12 pl-4 py-3.5 focus:ring-1 focus:ring-primary font-medium">
                    </div>
                    @error('customerName') <span class="text-red-400 text-xs font-bold mt-2 block"><i class="fa-solid fa-triangle-exclamation ml-1"></i>{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">رمز التذكرة (Barcode)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-barcode"></i>
                        </div>
                        <input type="text" wire:model="ticketBarcode" placeholder="أدخل رقم الباركود هنا" class="glass-input w-full rounded-xl pr-12 pl-4 py-3.5 focus:ring-1 focus:ring-primary uppercase tracking-wider font-bold">
                    </div>
                    @error('ticketBarcode') <span class="text-red-400 text-xs font-bold mt-2 block"><i class="fa-solid fa-triangle-exclamation ml-1"></i>{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">رقم الطاولة</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-couch"></i>
                        </div>
                        <input type="text" wire:model="physicalTableNumber" placeholder="مثال: VIP-1 أو 15" class="glass-input w-full rounded-xl pr-12 pl-4 py-3.5 focus:ring-1 focus:ring-primary font-bold">
                    </div>
                    @error('physicalTableNumber') <span class="text-red-400 text-xs font-bold mt-2 block"><i class="fa-solid fa-triangle-exclamation ml-1"></i>{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">رقم الكرسي</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-chair"></i>
                        </div>
                        <input type="text" wire:model="physicalChairNumber" placeholder="مثال: 1 أو 2" class="glass-input w-full rounded-xl pr-12 pl-4 py-3.5 focus:ring-1 focus:ring-primary font-bold">
                    </div>
                    @error('physicalChairNumber') <span class="text-red-400 text-xs font-bold mt-2 block"><i class="fa-solid fa-triangle-exclamation ml-1"></i>{{ $message }}</span> @enderror
                </div>

                @if($requireGeo)
                    <div class="bg-blue-900/10 border border-blue-500/20 rounded-2xl p-5 mb-4 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-blue-500/5 group-hover:bg-blue-500/10 transition-colors"></div>
                        <div class="flex items-start gap-4 relative z-10">
                            <div class="bg-blue-500/20 p-2 rounded-lg">
                                <i class="fa-solid fa-location-dot text-blue-400 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-blue-100 font-bold mb-1">التحقق الجغرافي مطلوب</p>
                                <p class="text-xs text-blue-200/60 leading-relaxed mb-3">يجب السماح بتحديد الموقع لضمان تواجدك في منطقة الـ VIP.</p>
                                @if(!$latitude || !$longitude)
                                    <button type="button" onclick="requestLocation()" class="text-xs bg-blue-600/80 hover:bg-blue-500 text-white font-bold px-4 py-2 rounded-lg transition-all shadow-[0_0_15px_rgba(37,99,235,0.3)]">
                                        <i class="fa-solid fa-location-crosshairs ml-1"></i> السماح بتحديد الموقع
                                    </button>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-xs font-bold">
                                        <i class="fa-solid fa-check"></i> تم تحديد الموقع
                                    </div>
                                @endif
                                @error('geo') <p class="text-red-400 text-xs font-bold mt-2"><i class="fa-solid fa-triangle-exclamation ml-1"></i>{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <button type="button" wire:click="verifyAndProceed" wire:loading.attr="disabled" class="btn-premium w-full py-4 rounded-xl mt-6 flex justify-center items-center gap-3 text-lg">
                    <span wire:loading.remove wire:target="verifyAndProceed">متابعة للطلبات</span>
                    <span wire:loading wire:target="verifyAndProceed">جاري التحقق من بياناتك...</span>
                    <i class="fa-solid fa-arrow-left" wire:loading.remove wire:target="verifyAndProceed"></i>
                </button>
            </div>
        </div>

        @if($requireGeo)
            <script>
                function requestLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                @this.setLocation(position.coords.latitude, position.coords.longitude);
                            },
                            function(error) {
                                alert("تعذر الوصول للموقع. يرجى تفعيل الـ GPS وصلاحيات الموقع في متصفحك.");
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    } else {
                        alert("متصفحك لا يدعم تحديد الموقع.");
                    }
                }
            </script>
        @endif

    @elseif ($step === 'order')
        <!-- ORDER STEP -->
        <div class="w-full max-w-3xl glass-card rounded-3xl p-5 md:p-8">
            <!-- Order Header -->
            <div class="flex justify-between items-center border-b border-white/10 pb-5 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-primary/20 border border-primary/50 flex items-center justify-center">
                        <i class="fa-solid fa-crown text-primary text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white tracking-wide">{{ $resolvedTicket['guest_name'] }}</h2>
                        <p class="text-xs text-gray-400 mt-1 font-medium">
                            طاولة VIP: <span class="text-primary">{{ $physicalTableNumber }}</span> |
                            الرصيد المتبقي: <span class="text-primary font-black">{{ $resolvedTicket['remaining'] }}</span>
                        </p>
                    </div>
                </div>
                <button wire:click="startNewOrder" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 hover:bg-white/10 text-gray-400 hover:text-white transition-all flex items-center justify-center" title="خروج">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </div>

            <!-- Categories / Products Selection -->
            @if(empty($orderItems) || $this->getTotalItemsCount() < $resolvedTicket['remaining'])
                <div class="mb-8">
                    @php 
                        $grouped = $products->groupBy('category'); 
                    @endphp

                    @if (!$selectedCategory)
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-primary"></i> الفئات المتاحة:
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($grouped as $cat => $items)
                                @php
                                    $displayCatName = in_array($cat, ['معسل', 'الشيش', 'الشيشة']) ? 'الشيشة' : $cat;
                                    $catIcon = match($cat) {
                                        'الشيشة', 'معسل', 'الشيش' => 'fa-solid fa-wind',
                                        'رؤوس الشيشة', 'رأس شيشة' => 'fa-solid fa-fire-burner',
                                        'طبقات إضافية' => 'fa-solid fa-layer-group',
                                        'مشروبات' => 'fa-solid fa-mug-hot',
                                        'مستلزمات' => 'fa-solid fa-screwdriver-wrench',
                                        default => 'fa-solid fa-cube',
                                    };
                                    $catColor = match($cat) {
                                        'الشيشة', 'معسل', 'الشيش' => 'linear-gradient(135deg, rgba(212,175,55,0.15), rgba(212,175,55,0.05))',
                                        'رؤوس الشيشة', 'رأس شيشة' => 'linear-gradient(135deg, rgba(255,152,0,0.15), rgba(255,152,0,0.05))',
                                        'طبقات إضافية' => 'linear-gradient(135deg, rgba(233,30,99,0.15), rgba(233,30,99,0.05))',
                                        'مشروبات' => 'linear-gradient(135deg, rgba(33,150,243,0.15), rgba(33,150,243,0.05))',
                                        'مستلزمات' => 'linear-gradient(135deg, rgba(156,39,176,0.15), rgba(156,39,176,0.05))',
                                        default => 'linear-gradient(135deg, rgba(158,158,158,0.15), rgba(158,158,158,0.05))',
                                    };
                                    $catBorder = match($cat) {
                                        'الشيشة', 'معسل', 'الشيش' => '#d4af37',
                                        'رؤوس الشيشة', 'رأس شيشة' => '#FF9800',
                                        'طبقات إضافية' => '#E91E63',
                                        'مشروبات' => '#2196F3',
                                        'مستلزمات' => '#9C27B0',
                                        default => '#4b5563',
                                    };
                                @endphp
                                <button wire:click="selectCategory('{{ $cat }}')" 
                                        class="item-card rounded-xl text-white font-bold w-full flex flex-col items-center justify-center gap-2 text-center"
                                        style="background: {{ $catColor }}; border: 1px solid {{ $catBorder }}; padding: 1.2rem 0.8rem; min-height: 100px; transition: all 0.25s ease;">
                                    <i class="{{ $catIcon }}" style="font-size: 1.8rem; color: {{ $catBorder }};"></i>
                                    <span class="text-[0.95rem] md:text-[1.1rem] mt-1">{{ $displayCatName }}</span>
                                    <span class="text-xs text-gray-400">{{ $items->count() }} صنف</span>
                                </button>
                            @endforeach
                        </div>
                    @else
                        @php 
                            $selectedCat = $selectedCategory; 
                            $displayCatName = in_array($selectedCat, ['معسل', 'الشيش', 'الشيشة']) ? 'الشيشة' : $selectedCat;
                            $selectedIcon = match($selectedCat) {
                                'الشيشة', 'معسل', 'الشيش' => 'fa-solid fa-wind',
                                'رؤوس الشيشة', 'رأس شيشة' => 'fa-solid fa-fire-burner',
                                'طبقات إضافية' => 'fa-solid fa-layer-group',
                                'مشروبات' => 'fa-solid fa-mug-hot',
                                'مستلزمات' => 'fa-solid fa-screwdriver-wrench',
                                default => 'fa-solid fa-cube',
                            };
                            $selectedIconColor = match($selectedCat) {
                                'الشيشة', 'معسل', 'الشيش' => '#d4af37',
                                'رؤوس الشيشة', 'رأس شيشة' => '#FF9800',
                                'طبقات إضافية' => '#E91E63',
                                'مشروبات' => '#2196F3',
                                'مستلزمات' => '#9C27B0',
                                default => '#9ca3af',
                            };
                        @endphp
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2" style="color: {{ $selectedIconColor }};">
                                <i class="{{ $selectedIcon }}"></i> اختر من: {{ $displayCatName }}
                            </h3>
                            <button wire:click="backToCategories" class="glass px-4 py-2 rounded-xl text-sm font-semibold text-gray-300 hover:text-white hover:bg-white/5 transition-all">
                                <i class="fa-solid fa-arrow-right ml-1"></i> رجوع للتصنيفات
                            </button>
                        </div>
                        
                        @if(isset($grouped[$selectedCat]))
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 animate-[fadeInUp_0.3s_ease-out]">
                                @foreach($grouped[$selectedCat] as $product)
                                    @php
                                        $isShishaOrMoassel = in_array($selectedCat, ['معسل', 'الشيشة', 'الشيش']);
                                        $isAlreadyAdded = collect($orderItems)->contains('name', $product->name);
                                    @endphp
                                    <div class="flex flex-col h-full bg-black/40 border {{ $isAlreadyAdded ? 'border-primary shadow-[0_0_10px_rgba(212,175,55,0.2)]' : 'border-white/10' }} rounded-xl overflow-hidden item-card">
                                        <button wire:click="addProduct('{{ $product->name }}', {{ $product->grams_per_order }}, false)" 
                                                class="flex-1 p-3 text-center flex flex-col items-center justify-center gap-2 min-h-[90px] hover:bg-white/5 transition-all w-full">
                                            @if($product->image)
                                                <img src="{{ route('file.serve', ['path' => $product->image]) }}" class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-white/5 flex items-center justify-center">
                                                    <i class="fa-solid fa-box text-gray-400 text-xl"></i>
                                                </div>
                                            @endif
                                            <span class="font-bold text-white text-sm mt-1 leading-tight">{{ $product->name }}</span>
                                            @if($product->price > 0)
                                                <span class="text-xs text-emerald-400 font-bold mt-1">{{ number_format($product->price, 0) }} ر.ي</span>
                                            @endif
                                        </button>
                                        
                                        @if($isShishaOrMoassel)
                                            @php
                                                $isHeadAdded = collect($orderItems)->contains('name', $product->name . ' (رأس فقط)');
                                            @endphp
                                            <div class="border-t border-white/5 flex">
                                                <button wire:click="addProduct('{{ $product->name }}', 20, true)" 
                                                        class="w-full text-xs font-bold p-2 text-center transition-all 
                                                               {{ $isHeadAdded ? 'bg-orange-500/20 text-orange-400 border-t border-orange-500/50' : 'text-orange-500 hover:bg-orange-500/10 bg-orange-500/5' }}">
                                                    رأس فقط
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-400 p-4">لا توجد أصناف في هذا التصنيف.</div>
                        @endif
                    @endif
                </div>
            @else
                <div class="bg-primary/10 border border-primary/30 text-primary p-4 rounded-xl mb-8 text-sm font-bold text-center shadow-[0_0_15px_rgba(212,175,55,0.15)] flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check text-lg"></i> لقد وصلت للحد الأقصى المسموح به في تذكرتك.
                </div>
            @endif

            <!-- Selected Items Cart -->
            @if(count($orderItems) > 0)
                <div class="glass border border-white/10 rounded-2xl p-5 mb-8">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-basket-shopping text-primary"></i> طلباتك الحالية:
                    </h3>
                    <div class="space-y-3">
                        @foreach ($orderItems as $index => $item)
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center bg-black/40 p-4 rounded-xl border border-white/5 gap-4">
                                <div class="flex-1">
                                    <p class="font-bold text-white text-base">{{ $item['name'] }}</p>
                                    @if ($item['notes'])
                                        <p class="text-xs text-primary/80 mt-1 font-medium"><i class="fa-solid fa-info-circle ml-1"></i>{{ $item['notes'] }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                                    <div class="flex items-center bg-black/60 rounded-lg border border-white/10 p-1">
                                        <button wire:click="decrementProductQty({{ $index }})" class="w-8 h-8 rounded text-gray-400 hover:text-white hover:bg-white/10 flex items-center justify-center transition-colors"><i class="fa-solid fa-minus"></i></button>
                                        <span class="w-10 text-center text-white font-bold">{{ $item['quantity'] }}</span>
                                        <button wire:click="incrementProductQty({{ $index }})" class="w-8 h-8 rounded text-primary hover:bg-primary/20 flex items-center justify-center transition-colors"><i class="fa-solid fa-plus"></i></button>
                                    </div>
                                    <button wire:click="removeProduct({{ $index }})" class="w-10 h-10 flex items-center justify-center bg-red-500/10 text-red-400 border border-red-500/20 rounded-lg hover:bg-red-500 hover:text-white transition-colors shadow-sm">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button wire:click="submitOrder" wire:loading.attr="disabled" class="btn-premium w-full py-4 rounded-xl flex justify-center items-center gap-3 text-lg shadow-[0_10px_30px_rgba(212,175,55,0.3)]">
                    <span wire:loading.remove wire:target="submitOrder">اعتماد وإرسال للمطبخ</span>
                    <span wire:loading wire:target="submitOrder">جاري إرسال طلبك...</span>
                    <i class="fa-solid fa-bell-concierge" wire:loading.remove wire:target="submitOrder"></i>
                </button>
            @endif
        </div>

        <!-- Modifiers Modal -->
        @if ($showModifierModal)
            <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-md transition-opacity">
                <div class="glass-card rounded-t-3xl sm:rounded-3xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] sm:max-h-[85vh] animate-[fadeInUp_0.3s_ease-out]">
                    <div class="p-5 border-b border-white/10 flex justify-between items-center bg-black/20">
                        <h3 class="font-bold text-white text-lg flex items-center gap-2"><i class="fa-solid fa-sliders text-primary"></i> تخصيص: {{ $activeProductForModifiers['name'] ?? '' }}</h3>
                        <button wire:click="cancelModifiers" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-colors"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    
                    <div class="p-5 overflow-y-auto flex-1 space-y-6">
                        @foreach ($productModifiersData as $group)
                            <div class="bg-black/30 rounded-2xl p-5 border border-white/5">
                                <div class="flex justify-between items-center mb-4 border-b border-white/5 pb-3">
                                    <h4 class="font-bold text-white text-base">{{ $group['name'] }}</h4>
                                    @if ($group['is_required'])
                                        <span class="text-xs bg-primary/20 text-primary px-2.5 py-1 rounded-md border border-primary/30 font-bold">إجباري</span>
                                    @else
                                        <span class="text-xs bg-white/5 text-gray-400 px-2.5 py-1 rounded-md font-bold">اختياري</span>
                                    @endif
                                </div>
                                
                                <div class="space-y-2.5">
                                    @foreach ($group['modifiers'] as $mod)
                                        <label class="flex items-center justify-between p-3.5 rounded-xl border cursor-pointer transition-all {{ ($group['is_multiple'] ? ($selectedModifiers[$group['id']][$mod['id']] ?? false) : ($selectedModifiers[$group['id']] == $mod['id'])) ? 'bg-primary/10 border-primary/50 shadow-[0_0_15px_rgba(212,175,55,0.15)]' : 'bg-black/40 border-white/5 hover:border-white/20' }}">
                                            <div class="flex items-center gap-4 w-full">
                                                <div class="flex-shrink-0">
                                                    @if ($group['is_multiple'])
                                                        <div class="w-5 h-5 rounded border flex items-center justify-center transition-colors {{ ($selectedModifiers[$group['id']][$mod['id']] ?? false) ? 'bg-primary border-primary' : 'border-gray-500 bg-transparent' }}">
                                                            @if($selectedModifiers[$group['id']][$mod['id']] ?? false)
                                                                <i class="fa-solid fa-check text-black text-xs"></i>
                                                            @endif
                                                        </div>
                                                        <input type="checkbox" wire:model="selectedModifiers.{{ $group['id'] }}.{{ $mod['id'] }}" class="hidden">
                                                    @else
                                                        <div class="w-5 h-5 rounded-full border flex items-center justify-center transition-colors {{ ($selectedModifiers[$group['id']] == $mod['id']) ? 'border-primary' : 'border-gray-500' }}">
                                                            @if($selectedModifiers[$group['id']] == $mod['id'])
                                                                <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                                                            @endif
                                                        </div>
                                                        <input type="radio" wire:model="selectedModifiers.{{ $group['id'] }}" value="{{ $mod['id'] }}" name="mod_group_{{ $group['id'] }}" class="hidden">
                                                    @endif
                                                </div>
                                                <div class="flex-1 flex justify-between items-center">
                                                    <span class="text-sm font-bold text-gray-200 {{ ($group['is_multiple'] ? ($selectedModifiers[$group['id']][$mod['id']] ?? false) : ($selectedModifiers[$group['id']] == $mod['id'])) ? 'text-white' : '' }}">{{ $mod['name'] }}</span>
                                                    @if($mod['price'] > 0)
                                                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded">+{{ $mod['price'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('modifier_'.$group['id']) <span class="text-red-400 text-xs font-bold mt-3 block"><i class="fa-solid fa-triangle-exclamation ml-1"></i>{{ $message }}</span> @enderror
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="p-5 border-t border-white/10 bg-black/40 flex gap-4">
                        <button wire:click="cancelModifiers" class="flex-1 px-4 py-3.5 glass hover:bg-white/10 text-white rounded-xl font-bold transition-all text-sm">إلغاء</button>
                        <button wire:click="confirmModifiers" class="flex-1 px-4 py-3.5 btn-premium text-black rounded-xl font-bold shadow-lg transition-all text-sm">تأكيد الإضافة</button>
                    </div>
                </div>
            </div>
        @endif

    @elseif ($step === 'success')
        <!-- SUCCESS STEP -->
        <div class="w-full max-w-lg glass-card rounded-3xl p-10 text-center relative overflow-hidden">
            <!-- decorative background element -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="w-24 h-24 bg-emerald-500/20 border-2 border-emerald-500/50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-[0_0_30px_rgba(16,185,129,0.3)] animate-[float_4s_ease-in-out_infinite]">
                    <i class="fa-solid fa-check text-5xl text-emerald-400"></i>
                </div>
                
                <h2 class="text-3xl font-black text-white mb-3 tracking-wide">تم استلام طلبك!</h2>
                <p class="text-gray-300 text-sm md:text-base mb-10 font-medium leading-relaxed">
                    تم تحويل طلبك مباشرة إلى فريقنا. سيتم تحضيره بعناية فائقة وتقديمه لطاولتك قريباً. شكراً لثقتكم.
                </p>

                <button wire:click="startNewOrder" class="w-full glass hover:bg-white/5 border-white/20 hover:border-primary/50 text-white font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate-left"></i> تقديم طلب آخر
                </button>
            </div>
        </div>
    @endif
</div>
