<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.event')]
class EventSelfOrder extends Component
{
    // Steps: 'verify', 'order', 'success'
    public $step = 'verify';

    // Verify Step Fields
    public $customerName = '';
    public $ticketBarcode = '';
    public $physicalTableNumber = '';
    public $physicalChairNumber = '';
    public $latitude = null;
    public $longitude = null;

    // Order Step Fields
    public $resolvedTicket = null;
    public $orderItems = [];
    public $selectedCategory = null;
    
    // Modifiers State
    public $showModifierModal = false;
    public $activeProductForModifiers = null;
    public $productModifiersData = []; 
    public $selectedModifiers = []; 

    // Settings
    public $requireGeo = false;
    public $geoLat = 0;
    public $geoLng = 0;
    public $geoRadius = 0;
    public $welcomeMsg = '';
    public $isEnabled = true;

    public function mount()
    {
        $this->isEnabled = filter_var(Setting::where('key', 'self_order_enabled')->value('value') ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $this->requireGeo = filter_var(Setting::where('key', 'self_order_require_geo')->value('value') ?? 'false', FILTER_VALIDATE_BOOLEAN);
        $this->geoLat = (float) (Setting::where('key', 'self_order_geo_lat')->value('value') ?? 0);
        $this->geoLng = (float) (Setting::where('key', 'self_order_geo_lng')->value('value') ?? 0);
        $this->geoRadius = (int) (Setting::where('key', 'self_order_geo_radius')->value('value') ?? 200);
        $this->welcomeMsg = Setting::where('key', 'self_order_welcome_msg')->value('value') ?? 'مرحباً بك في نظام الطلب الذاتي';

        if (!$this->isEnabled) {
            abort(403, 'نظام الطلب الذاتي غير مفعل حالياً.');
        }
    }

    public function verifyAndProceed()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'ticketBarcode' => 'required|string|max:255',
            'physicalTableNumber' => 'required|string|max:50',
            'physicalChairNumber' => 'required|string|max:50',
        ], [
            'customerName.required' => 'يرجى إدخال اسمك.',
            'ticketBarcode.required' => 'يرجى إدخال رمز التذكرة.',
            'physicalTableNumber.required' => 'يرجى إدخال رقم الطاولة التي تجلس عليها.',
            'physicalChairNumber.required' => 'يرجى إدخال رقم الكرسي الذي تجلس عليه.',
        ]);

        // 1. Verify Geo Location if required
        if ($this->requireGeo) {
            if (!$this->latitude || !$this->longitude) {
                $this->addError('geo', 'يرجى السماح بصلاحية الموقع الجغرافي للتحقق من تواجدك في موقع الحفلة.');
                $this->dispatch('request-location');
                return;
            }

            $distance = $this->calculateDistance($this->latitude, $this->longitude, $this->geoLat, $this->geoLng);
            if ($distance > $this->geoRadius) {
                $this->addError('geo', 'يبدو أنك لست في موقع الحفلة. المسافة: ' . round($distance) . ' متر.');
                return;
            }
        }

        // 2. Verify Ticket (Primary validation)
        $barcode = strtoupper(trim($this->ticketBarcode));
        $ticket = Ticket::where('barcode', $barcode)->latest()->first();

        if (!$ticket) {
            $this->addError('ticketBarcode', 'لم يتم العثور على تذكرة بهذا الرمز.');
            return;
        }

        if ($ticket->status === 'cancelled') {
            $this->addError('ticketBarcode', 'هذه التذكرة ملغاة ولا يمكن استخدامها.');
            return;
        }

        if (!$ticket->hasRemainingUses()) {
            $this->addError('ticketBarcode', 'هذه التذكرة مستخدمة بالكامل (' . $ticket->times_used . '/' . $ticket->max_uses . ' مرات).');
            return;
        }

        // 3. Try to find matching customer (optional - not required)
        $customer = Customer::where('name', 'like', '%' . trim($this->customerName) . '%')->first();
        $customerId = $customer ? $customer->id : null;

        // Validation passed!
        $this->resolvedTicket = [
            'id'            => $ticket->id,
            'barcode'       => $ticket->barcode,
            'guest_name'    => $ticket->guest_name ?: $this->customerName,
            'num_people'    => $ticket->num_people,
            'remaining'     => $ticket->remainingUses(),
            'max_uses'      => $ticket->max_uses,
            'customer_id'   => $customerId,
        ];

        $this->step = 'order';
        $this->orderItems = [];
        $this->selectedCategory = null;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Haversine formula
        $earthRadius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        return $distance;
    }

    public function setLocation($lat, $lng)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        // Optionally auto-verify if they were waiting for location
        if ($this->getErrorBag()->has('geo')) {
            $this->resetValidation('geo');
            $this->verifyAndProceed();
        }
    }

    // ==========================================
    // Order Step Methods (Copied from OrderStaffBoard)
    // ==========================================

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
    }

    public function backToCategories()
    {
        $this->selectedCategory = null;
    }

    public function getTotalItemsCount()
    {
        $total = 0;
        foreach ($this->orderItems as $item) {
            $total += $item['quantity'];
        }
        return $total;
    }

    public function getMaxAllowedItems()
    {
        if ($this->resolvedTicket) {
            return $this->resolvedTicket['remaining'];
        }
        return 0;
    }

    public function addProduct($productName, $grams = 20, $isHeadOnly = false)
    {
        $product = Product::with('modifierGroups.modifiers')->where('name', $productName)->first();
        if (!$product) return;
        
        $price = $product->price;
        $targetKitchen = $product->target_kitchen;
        
        $displayName = $isHeadOnly ? $productName . ' (رأس فقط)' : $productName;
        $actualGrams = $isHeadOnly ? 20 : $grams;

        $maxItems = $this->getMaxAllowedItems();
        $currentTotal = $this->getTotalItemsCount();
        if ($currentTotal >= $maxItems) {
            session()->flash('error', "تذكرتك تسمح بـ {$maxItems} طلبات فقط. لا يمكن إضافة المزيد.");
            $this->selectedCategory = null;
            return;
        }

        if ($product->modifierGroups->count() > 0 && !$isHeadOnly) {
            $this->activeProductForModifiers = [
                'name' => $displayName,
                'grams' => $actualGrams,
                'targetKitchen' => $targetKitchen,
                'price' => $price,
            ];
            $this->productModifiersData = $product->modifierGroups->toArray();
            
            $this->selectedModifiers = [];
            foreach ($this->productModifiersData as $group) {
                if ($group['is_multiple']) {
                    $this->selectedModifiers[$group['id']] = [];
                } else {
                    $this->selectedModifiers[$group['id']] = null;
                }
            }
            $this->showModifierModal = true;
            return;
        }

        $this->addOrderToCart($displayName, $actualGrams, $targetKitchen, $price, '');
    }

    public function confirmModifiers()
    {
        $this->resetErrorBag();
        foreach ($this->productModifiersData as $group) {
            if ($group['is_required']) {
                $selection = $this->selectedModifiers[$group['id']] ?? null;
                if ($group['is_multiple']) {
                    $hasSelection = false;
                    foreach ((array)$selection as $val) {
                        if ($val) $hasSelection = true;
                    }
                    if (!$hasSelection) {
                        $this->addError('modifier_'.$group['id'], 'يرجى اختيار خيار واحد على الأقل من: ' . $group['name']);
                        return;
                    }
                } else {
                    if (empty($selection)) {
                        $this->addError('modifier_'.$group['id'], 'يرجى اختيار: ' . $group['name']);
                        return;
                    }
                }
            }
        }

        $notesStr = '';
        $extraPrice = 0;
        foreach ($this->productModifiersData as $group) {
            $selection = $this->selectedModifiers[$group['id']] ?? null;
            $selectedNames = [];
            if ($group['is_multiple']) {
                foreach ((array)$selection as $modId => $isChecked) {
                    if ($isChecked) {
                        $mod = collect($group['modifiers'])->firstWhere('id', $modId);
                        if ($mod) {
                            $selectedNames[] = $mod['name'];
                            $extraPrice += (float)$mod['price'];
                        }
                    }
                }
            } else {
                if (!empty($selection)) {
                    $modId = $selection;
                    $mod = collect($group['modifiers'])->firstWhere('id', $modId);
                    if ($mod) {
                        $selectedNames[] = $mod['name'];
                        $extraPrice += (float)$mod['price'];
                    }
                }
            }
            
            if (count($selectedNames) > 0) {
                if (!empty($notesStr)) $notesStr .= ' | ';
                $notesStr .= $group['name'] . ': ' . implode('، ', $selectedNames);
            }
        }

        $price = $this->activeProductForModifiers['price'] + $extraPrice;
        
        $this->addOrderToCart(
            $this->activeProductForModifiers['name'],
            $this->activeProductForModifiers['grams'],
            $this->activeProductForModifiers['targetKitchen'],
            $price,
            $notesStr
        );

        $this->cancelModifiers();
    }

    public function cancelModifiers()
    {
        $this->showModifierModal = false;
        $this->activeProductForModifiers = null;
        $this->productModifiersData = [];
        $this->selectedModifiers = [];
        $this->selectedCategory = null;
    }

    private function addOrderToCart($displayName, $actualGrams, $targetKitchen, $price, $notes)
    {
        foreach ($this->orderItems as $i => $item) {
            if ($item['name'] === $displayName && $item['notes'] === $notes) {
                $this->orderItems[$i]['quantity']++;
                $this->selectedCategory = null;
                return;
            }
        }

        $this->orderItems[] = [
            'name' => $displayName,
            'quantity' => 1,
            'grams' => $actualGrams,
            'targetKitchen' => $targetKitchen,
            'price' => $price,
            'notes' => $notes,
        ];
        $this->selectedCategory = null;
    }

    public function removeProduct($index)
    {
        unset($this->orderItems[$index]);
        $this->orderItems = array_values($this->orderItems);
    }

    public function incrementProductQty($index)
    {
        $maxItems = $this->getMaxAllowedItems();
        $currentTotal = $this->getTotalItemsCount();
        
        if ($currentTotal >= $maxItems) {
            session()->flash('error', "تذكرتك تسمح بـ {$maxItems} طلبات فقط. لا يمكن إضافة المزيد.");
            return;
        }
        $this->orderItems[$index]['quantity']++;
    }

    public function decrementProductQty($index)
    {
        if ($this->orderItems[$index]['quantity'] > 1) {
            $this->orderItems[$index]['quantity']--;
        }
    }

    public function submitOrder()
    {
        if (empty($this->orderItems)) {
            session()->flash('error', 'يرجى اختيار صنف واحد على الأقل.');
            return;
        }

        if (!$this->resolvedTicket) {
            $this->step = 'verify';
            return;
        }

        $ticket = Ticket::find($this->resolvedTicket['id']);
        if (!$ticket || !$ticket->hasRemainingUses()) {
            session()->flash('error', 'هذه التذكرة لم تعد صالحة للاستخدام.');
            $this->step = 'verify';
            return;
        }

        $totalQty = $this->getTotalItemsCount();
        $remaining = $ticket->remainingUses();
        if ($totalQty > $remaining) {
            session()->flash('error', "عدد الأصناف ({$totalQty}) يتجاوز رصيد التذكرة ({$remaining} استخدامات). يرجى تقليل العدد.");
            return;
        }

        $batchId = date('YmdHis') . rand(100, 999);
        $itemsProcessed = 0;

        foreach ($this->orderItems as $item) {
            $searchName = str_replace(' (رأس فقط)', '', $item['name']);
            $product = Product::where('name', $searchName)->first();
            $targetKitchen = $product ? $product->target_kitchen : 'shisha';
            
            for ($q = 0; $q < $item['quantity']; $q++) {
                $ticket->refresh();
                if (!$ticket->hasRemainingUses()) {
                    session()->flash('error', "نفدت استخدامات التذكرة أثناء المعالجة.");
                    return;
                }

                $notesStr = trim($item['notes'] ?? '');
                // Prepend physical table info to notes if there's no actual table
                $fullNotes = "طاولة العميل: " . $this->physicalTableNumber . " | كرسي: " . $this->physicalChairNumber;
                if ($notesStr) {
                    $fullNotes .= " | " . $notesStr;
                }

                $order = Order::create([
                    'table_id'                => null, // Self orders don't use system tables
                    'shisha_type'             => $item['name'],
                    'quantity'                => 1,
                    'grams_per_unit'          => $item['grams'],
                    'notes'                   => $fullNotes,
                    'user_id'                 => null, // Null because self service
                    'status'                  => 'pending',
                    'batch_id'                => $batchId,
                    'manager_approval_status' => null,
                    'guest_name'              => trim($this->customerName),
                    'ticket_id'               => $ticket->id,
                    'customer_id'             => $this->resolvedTicket['customer_id'],
                    'target_kitchen'          => $targetKitchen,
                    'source'                  => 'self_service',
                ]);
                
                $ticket->useOnce($order->id);
                $itemsProcessed++;
            }
        }

        $this->dispatch('order-sent-to-kitchen');
        $this->step = 'success';
    }

    public function startNewOrder()
    {
        $this->step = 'verify';
        $this->customerName = '';
        $this->ticketBarcode = '';
        $this->physicalTableNumber = '';
        $this->resolvedTicket = null;
        $this->orderItems = [];
        $this->selectedCategory = null;
    }

    public function render()
    {
        $visibleCategories = Category::where('is_visible', true)->pluck('name')->toArray();
        $products = Product::where('is_active', true)
            ->whereIn('category', $visibleCategories)
            ->orderBy('category')->orderBy('name')->get();
            
        $productGrams = $products->pluck('grams_per_order', 'name');

        return view('livewire.event-self-order', [
            'products' => $products,
            'productGrams' => $productGrams,
        ]);
    }
}
