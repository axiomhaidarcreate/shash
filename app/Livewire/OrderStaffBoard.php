<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Table;
use App\Models\Order;
use App\Models\Product;
use App\Models\Section;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;

class OrderStaffBoard extends Component
{
    public $tableSearch = '';
    public $selectedTable = null;
    public $selectedChair = null;
    public $guestsCount = 1;

    // Single product list: array of selected items [{name, quantity, notes, grams, targetKitchen, price}]
    public $orderItems = [];

    public $editingOrderId = null;
    public $printKitchenReceipt = true;
    public $guestName = '';

    // Reception ticket barcode (single per submission)
    public $ticketBarcode = '';
    public $resolvedTicket = null;

    // Table section filtering state
    public $selectedSection = null;

    // Category selection (single — no index needed since one list)
    public $selectedCategory = null;

    // Order Mode
    public $orderMode = 'ticket'; // 'ticket' or 'normal'

    // Loyalty Link
    public $loyaltySearch = '';
    public $loyaltySearchResults = [];
    public $linkedCustomer = null;
    public $loyaltyMessage = '';

    // Multi-ticket tracking
    public $submittedTicketsCount = 0; // how many tickets/orders already submitted for this table

    // Modifiers State
    public $showModifierModal = false;
    public $activeProductForModifiers = null;
    public $productModifiersData = []; 
    public $selectedModifiers = []; 

    public function mount()
    {
        $this->orderItems = [];
        $this->selectedSection = null;
    }

    public function setGuestsCount($value)
    {
        $value = (int)$value;
        if ($value < 1) $value = 1;
        
        $this->guestsCount = $value;
    }

    public function selectTable($id)
    {
        $table = Table::find($id);
        // We no longer block occupied tables entirely since we can add to empty chairs
        // We will just let them select the table, then select an empty chair.

        $this->resetValidation('selectedTable');
        $this->selectedTable = $id;
        $this->selectedChair = null; // Reset chair when table changes
        $this->guestsCount = 1;
        $this->submittedTicketsCount = 0;
        $this->orderItems = [];
        $this->selectedCategory = null;
        $this->clearTicket();
        $this->guestName = '';
    }

    public function selectChair($chairId)
    {
        $chair = \App\Models\Chair::find($chairId);
        if ($chair && $chair->status !== 'empty' && !$this->editingOrderId) {
            $this->addError('selectedChair', 'هذا الكرسي مشغول حالياً.');
            return;
        }
        $this->resetValidation('selectedChair');
        $this->selectedChair = $chairId;
    }

    public function backToChairs()
    {
        $this->selectedChair = null;
    }

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
    }

    public function backToCategories()
    {
        $this->selectedCategory = null;
    }

    /**
     * Get total quantity of all items in the current order
     */
    public function getTotalItemsCount()
    {
        $total = 0;
        foreach ($this->orderItems as $item) {
            $total += $item['quantity'];
        }
        return $total;
    }

    /**
     * Get the max allowed items based on ticket (if in ticket mode)
     */
    public function getMaxAllowedItems()
    {
        if ($this->orderMode === 'ticket' && $this->resolvedTicket) {
            return $this->resolvedTicket['remaining'];
        }
        return 9999; // No limit in normal mode
    }

    public function addProduct($productName, $grams = 20, $isHeadOnly = false)
    {
        $product = Product::with('modifierGroups.modifiers')->where('name', $productName)->first();
        if (!$product) return;
        
        $price = $product->price;
        $targetKitchen = $product->target_kitchen;
        
        $displayName = $isHeadOnly ? $productName . ' (رأس فقط)' : $productName;
        $actualGrams = $isHeadOnly ? 20 : $grams;

        // Check ticket limit
        $maxItems = $this->getMaxAllowedItems();
        $currentTotal = $this->getTotalItemsCount();
        if ($currentTotal >= $maxItems) {
            session()->flash('error', "التذكرة تسمح بـ {$maxItems} طلبات فقط. لا يمكن إضافة المزيد.");
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
            
            // initialize selected modifiers
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
        // validate required groups
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

        // Process notes string and total extra price
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
        // Check if same item with SAME NOTES exists, increment quantity
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
            session()->flash('error', "التذكرة تسمح بـ {$maxItems} طلبات فقط. لا يمكن إضافة المزيد.");
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

    public function editOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order && $order->status === 'pending') {
            $this->editingOrderId = $order->id;
            $this->selectedTable = $order->table_id;
            $this->guestsCount = 1;
            $this->submittedTicketsCount = 0;
            
            $notes = $order->notes;
            // Clean up legacy ice level note if present
            if (strpos($notes, 'ثلج: ') === 0) {
                $parts = explode(' | ', $notes);
                if (count($parts) > 1) {
                    $notes = $parts[1];
                } else {
                    $notes = '';
                }
            }

            $product = Product::where('name', str_replace(' (رأس فقط)', '', $order->shisha_type))->first();
            $this->orderItems = [
                [
                    'name' => $order->shisha_type,
                    'quantity' => $order->quantity,
                    'grams' => $order->grams_per_unit,
                    'targetKitchen' => $product ? $product->target_kitchen : 'shisha',
                    'price' => $product ? $product->price : 0,
                    'notes' => $notes,
                ]
            ];
        } else {
            session()->flash('error', 'لا يمكن تعديل هذا الطلب (ربما بدأ تحضيره).');
        }
    }

    public function lookupTicket()
    {
        $barcode = strtoupper(trim($this->ticketBarcode));
        if (!$barcode) {
            $this->resolvedTicket = null;
            return;
        }

        $ticket = Ticket::where('barcode', $barcode)->latest()->first();

        if (!$ticket) {
            $this->addError('ticketBarcode', 'لم يتم العثور على تذكرة بهذا الرمز.');
            $this->resolvedTicket = null;
            return;
        }

        if ($ticket->status === 'cancelled') {
            $this->addError('ticketBarcode', 'هذه التذكرة ملغاة ولا يمكن استخدامها.');
            $this->resolvedTicket = null;
            return;
        }

        if (!$ticket->hasRemainingUses()) {
            $this->addError('ticketBarcode', 'هذه التذكرة مستخدمة بالكامل (' . $ticket->times_used . '/' . $ticket->max_uses . ' مرات).');
            $this->resolvedTicket = null;
            return;
        }

        $this->resetValidation('ticketBarcode');
        $this->resolvedTicket = [
            'id'            => $ticket->id,
            'barcode'       => $ticket->barcode,
            'guest_name'    => $ticket->guest_name,
            'num_people'    => $ticket->num_people,
            'remaining'     => $ticket->remainingUses(),
            'max_uses'      => $ticket->max_uses,
        ];

        // Pre-fill guest name if empty
        if ($ticket->guest_name && empty($this->guestName)) {
            $this->guestName = $ticket->guest_name;
        }
    }

    public function clearTicket()
    {
        $this->ticketBarcode = '';
        $this->resolvedTicket = null;
        $this->resetValidation('ticketBarcode');
    }

    public function cancelEdit()
    {
        $this->reset(['editingOrderId', 'selectedTable', 'tableSearch', 'guestName', 'ticketBarcode']);
        $this->resolvedTicket = null;
        $this->orderItems = [];
        $this->selectedCategory = null;
        $this->submittedTicketsCount = 0;
        $this->guestsCount = 1;
        $this->clearLoyaltyCustomer();
    }

    public function setOrderMode($mode)
    {
        $this->orderMode = $mode;
        $this->clearTicket();
        $this->clearLoyaltyCustomer();
        $this->orderItems = [];
        $this->selectedCategory = null;
        $this->submittedTicketsCount = 0;
    }

    public function updatedLoyaltySearch()
    {
        if (strlen(trim($this->loyaltySearch)) >= 2) {
            $this->loyaltySearchResults = Customer::with('loyaltyOffer')
                ->where('name', 'like', '%' . trim($this->loyaltySearch) . '%')
                ->orWhere('phone', 'like', '%' . trim($this->loyaltySearch) . '%')
                ->orWhere('subscription_number', 'like', '%' . trim($this->loyaltySearch) . '%')
                ->take(5)
                ->get();
        } else {
            $this->loyaltySearchResults = [];
        }
    }

    public function selectLoyaltyCustomer($customerId)
    {
        $customer = Customer::with('loyaltyOffer')->find($customerId);
        if ($customer) {
            $this->linkedCustomer = $customer;
            $this->loyaltySearch = '';
            $this->loyaltySearchResults = [];
            $remaining = $customer->total_points - $customer->used_points;
            $this->loyaltyMessage = "✅ تم اختيار {$customer->name} | النقاط الحالية: {$remaining}";
        }
    }

    public function clearLoyaltyCustomer()
    {
        $this->loyaltySearch = '';
        $this->loyaltySearchResults = [];
        $this->linkedCustomer = null;
        $this->loyaltyMessage = '';
    }

    public function requestCancel($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order) {
            $order->cancel_requested = true;
            $order->save();
            session()->flash('success', 'تم إرسال طلب إلغاء الطلبية للمدير.');
        }
    }

    public function requestCancelDuplicate($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order && in_array($order->status, ['pending', 'prep'])) {
            $order->cancel_duplicate_requested = true;
            $order->save();
            session()->flash('success', 'تم إرسال طلب إلغاء التكرار للمدير بنجاح.');
        } else {
            session()->flash('error', 'لا يمكن إلغاء التكرار لهذا الطلب.');
        }
    }

    public function rushOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order && in_array($order->status, ['pending', 'prep'])) {
            // Check if 10 minutes have passed since creation
            if ($order->created_at->gt(now()->subMinutes(10))) {
                session()->flash('error', 'لا يمكن تسريع الطلب إلا بعد مرور 10 دقائق من إنشائه.');
                return;
            }

            $order->is_rushed = true;
            $order->save();
            $this->dispatch('order-rushed');
            session()->flash('success', 'تم إرسال تنبيه تسريع للمطبخ بنجاح!');
        }
    }

    public function deliverOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order && $order->status === 'ready') {
            $order->status = 'delivered';
            $order->save();
            
            $table = \App\Models\Table::find($order->table_id);
            if ($table && $table->status === 'order_ready') {
                // If there are no other ready orders, maybe reset to customer_seated or delivered.
                // We can set it to delivered.
                $table->status = 'delivered';
                $table->save();
            }
            
            session()->flash('success', 'تم تسليم الطلب للعميل بنجاح.');
        }
    }

    public function triggerUrgentAlert($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order && in_array($order->status, ['pending', 'prep'])) {
            $order->increment('kitchen_alert_count');
            $this->dispatch('urgent-alert-triggered');
            session()->flash('success', 'تم إرسال نداء استعجال طارئ للمطبخ! 🚨');
        }
    }

    public function selectSection($name)
    {
        $this->selectedSection = $name;
    }

    public function submitOrder()
    {
        if (!$this->selectedTable) {
            $this->addError('selectedTable', 'يرجى اختيار الطاولة');
            return;
        }

        if (empty($this->orderItems)) {
            session()->flash('error', 'يرجى اختيار صنف واحد على الأقل.');
            return;
        }

        // Resolve ticket in ticket mode
        $ticket = null;
        if ($this->orderMode === 'ticket') {
            if ($this->resolvedTicket) {
                $ticket = Ticket::find($this->resolvedTicket['id']);
                if ($ticket && !$ticket->hasRemainingUses()) {
                    $ticket = null;
                }
            } elseif (!empty(trim($this->ticketBarcode))) {
                $found = Ticket::where('barcode', strtoupper(trim($this->ticketBarcode)))->latest()->first();
                if ($found && $found->hasRemainingUses()) {
                    $ticket = $found;
                }
            }

            if (!$ticket) {
                $this->addError('ticketBarcode', 'يرجى إدخال أو تصوير تذكرة الاستقبال لإرسال الطلب.');
                return;
            }

            // Validate total items don't exceed ticket remaining uses
            $totalQty = $this->getTotalItemsCount();
            $remaining = $ticket->remainingUses();
            if ($totalQty > $remaining) {
                session()->flash('error', "عدد الأصناف ({$totalQty}) يتجاوز رصيد التذكرة ({$remaining} استخدامات). يرجى تقليل العدد.");
                return;
            }
        }

        $batchId = date('YmdHis') . rand(100, 999);
        $itemsProcessed = 0;

        foreach ($this->orderItems as $item) {
            $searchName = str_replace(' (رأس فقط)', '', $item['name']);
            $product = Product::where('name', $searchName)->first();
            $targetKitchen = $product ? $product->target_kitchen : 'shisha';
            
            $grams = 0;
            if (str_contains($item['name'], 'رأس فقط')) {
                $grams = 20;
            } elseif ($product) {
                $grams = $product->grams_per_order;
            }

            for ($q = 0; $q < $item['quantity']; $q++) {
                if ($ticket) {
                    $ticket->refresh();
                    if (!$ticket->hasRemainingUses()) {
                        session()->flash('error', "نفدت استخدامات التذكرة أثناء المعالجة.");
                        return;
                    }
                }

                $notesStr = trim($item['notes'] ?? '');
                $order = Order::create([
                    'table_id'                => $this->selectedTable,
                    'chair_id'                => $this->selectedChair,
                    'shisha_type'             => $item['name'],
                    'quantity'                => 1,
                    'grams_per_unit'          => $grams,
                    'notes'                   => $notesStr,
                    'user_id'                 => Auth::id(),
                    'status'                  => 'pending',
                    'batch_id'                => $batchId,
                    'manager_approval_status' => null,
                    'guest_name'              => trim($this->guestName),
                    'ticket_id'               => $ticket?->id,
                    'customer_id'             => $this->linkedCustomer ? $this->linkedCustomer->id : null,
                    'target_kitchen'          => $targetKitchen,
                ]);
                
                if ($ticket) {
                    $ticket->useOnce($order->id);
                }
                
                $itemsProcessed++;
            }
        }

        // إضافة نقاط الولاء للطلبات العادية
        if ($this->orderMode === 'normal' && $this->linkedCustomer && $this->linkedCustomer->loyaltyOffer) {
            $offer = $this->linkedCustomer->loyaltyOffer;
            $pointsToAdd = $offer->points_per_order;
            
            if ($pointsToAdd > 0) {
                $this->linkedCustomer->increment('total_points', $pointsToAdd);
                
                LoyaltyTransaction::create([
                    'customer_id'  => $this->linkedCustomer->id,
                    'points_added' => $pointsToAdd,
                    'description'  => "طلب عادي (بدون تذكرة) - أضيفت {$pointsToAdd} نقطة",
                ]);

                $whatsapp = app(WhatsAppService::class);
                $this->linkedCustomer->refresh();
                $whatsapp->sendPointsUpdate($this->linkedCustomer, $pointsToAdd, "طلب جديد");
            }
        }

        // Update Chair and Table status
        if ($this->selectedTable && $this->selectedChair) {
            $table = Table::find($this->selectedTable);
            $chair = \App\Models\Chair::find($this->selectedChair);
            
            if ($chair) {
                $chair->update(['status' => 'occupied']);
            }
            if ($table && $table->status == 'empty') {
                $table->status = 'customer_seated';
                $table->save();
            }
        }

        $this->submittedTicketsCount++;
        
        $this->dispatch('order-sent');

        // Check if all guests have been served
        if ($this->submittedTicketsCount >= $this->guestsCount) {
            // All done — fully reset
            session()->flash('success', "تم إرسال جميع الطلبات ({$this->guestsCount} تذاكر) للمطابخ بنجاح! الطاولة محجوزة.");
            $this->cancelEdit();
        } else {
            // More guests remaining — clear items & ticket, keep table
            $remaining = $this->guestsCount - $this->submittedTicketsCount;
            session()->flash('success', "✅ تم إرسال طلب الشخص {$this->submittedTicketsCount} بنجاح! — متبقي {$remaining} شخص آخر.");
            $this->orderItems = [];
            $this->selectedCategory = null;
            $this->clearTicket();
            $this->guestName = '';
        }
    }

    public function submitOrderWithApproval()
    {
        if (!$this->selectedTable) {
            $this->addError('selectedTable', 'يرجى اختيار الطاولة');
            return;
        }

        if (empty(trim($this->guestName))) {
            $this->addError('guestName', 'يرجى إدخال اسم الضيف (مطلوب للطلبات بدون فاتورة كاشير).');
            return;
        }

        if (empty($this->orderItems)) {
            session()->flash('error', 'يرجى اختيار صنف واحد على الأقل.');
            return;
        }

        // Resolve ticket if barcode was provided
        $ticket = null;
        if ($this->orderMode === 'ticket') {
            if ($this->resolvedTicket) {
                $found = Ticket::find($this->resolvedTicket['id']);
                if ($found && $found->hasRemainingUses()) {
                    $ticket = $found;
                }
            } elseif (!empty(trim($this->ticketBarcode))) {
                $found = Ticket::where('barcode', strtoupper(trim($this->ticketBarcode)))->latest()->first();
                if ($found && $found->hasRemainingUses()) {
                    $ticket = $found;
                }
            }
            
            if ($ticket) {
                // Validate total items don't exceed ticket remaining uses
                $totalQty = $this->getTotalItemsCount();
                $remaining = $ticket->remainingUses();
                if ($totalQty > $remaining) {
                    session()->flash('error', "عدد الأصناف ({$totalQty}) يتجاوز رصيد التذكرة ({$remaining} استخدامات). يرجى تقليل العدد.");
                    return;
                }
            }
        }

        $batchId = date('YmdHis') . rand(100, 999);
        $itemsProcessed = 0;

        foreach ($this->orderItems as $item) {
            $searchName = str_replace(' (رأس فقط)', '', $item['name']);
            $product = Product::where('name', $searchName)->first();
            $targetKitchen = $product ? $product->target_kitchen : 'shisha';
            
            $grams = 0;
            if (str_contains($item['name'], 'رأس فقط')) {
                $grams = 20;
            } elseif ($product) {
                $grams = $product->grams_per_order;
            }

            for ($q = 0; $q < $item['quantity']; $q++) {
                if ($ticket) {
                    $ticket->refresh();
                    if (!$ticket->hasRemainingUses()) {
                        session()->flash('error', "نفدت استخدامات التذكرة أثناء المعالجة.");
                        return;
                    }
                }

                $notesStr = trim($item['notes'] ?? '');
                $order = Order::create([
                    'table_id'                => $this->selectedTable,
                    'chair_id'                => $this->selectedChair,
                    'shisha_type'             => $item['name'],
                    'quantity'                => 1,
                    'grams_per_unit'          => $grams,
                    'notes'                   => $notesStr,
                    'user_id'                 => Auth::id(),
                    'status'                  => 'pending',
                    'batch_id'                => $batchId,
                    'manager_approval_status' => 'pending',
                    'guest_name'              => trim($this->guestName),
                    'ticket_id'               => $ticket?->id,
                    'customer_id'             => $this->linkedCustomer ? $this->linkedCustomer->id : null,
                    'target_kitchen'          => $targetKitchen,
                ]);
                
                if ($ticket) {
                    $ticket->useOnce($order->id);
                }
                
                $itemsProcessed++;
            }
        }

        // إضافة نقاط الولاء للطلبات العادية
        if ($this->orderMode === 'normal' && $this->linkedCustomer && $this->linkedCustomer->loyaltyOffer) {
            $offer = $this->linkedCustomer->loyaltyOffer;
            $pointsToAdd = $offer->points_per_order; 
            if ($pointsToAdd > 0) {
                $this->linkedCustomer->increment('total_points', $pointsToAdd);
                LoyaltyTransaction::create([
                    'customer_id'  => $this->linkedCustomer->id,
                    'points_added' => $pointsToAdd,
                    'description'  => "طلب عادي (بانتظار موافقة المدير) - أضيفت {$pointsToAdd} نقطة",
                ]);
                $whatsapp = app(WhatsAppService::class);
                $this->linkedCustomer->refresh();
                $whatsapp->sendPointsUpdate($this->linkedCustomer, $pointsToAdd, "طلب بانتظار الموافقة");
            }
        }

        // Update Chair and Table status
        if ($this->selectedTable && $this->selectedChair) {
            $table = Table::find($this->selectedTable);
            $chair = \App\Models\Chair::find($this->selectedChair);
            
            if ($chair) {
                $chair->update(['status' => 'occupied']);
            }
            if ($table && $table->status == 'empty') {
                $table->status = 'customer_seated';
                $table->save();
            }
        }

        $this->submittedTicketsCount++;
        $this->dispatch('order-sent');

        // Check if all guests have been served
        if ($this->submittedTicketsCount >= $this->guestsCount) {
            session()->flash('success', "تم إرسال جميع الطلبات ({$this->guestsCount} تذاكر) للمدير للموافقة بنجاح!");
            $this->cancelEdit();
        } else {
            $remaining = $this->guestsCount - $this->submittedTicketsCount;
            session()->flash('success', "✅ تم إرسال طلب الشخص {$this->submittedTicketsCount} للمدير! — متبقي {$remaining} شخص آخر.");
            $this->orderItems = [];
            $this->selectedCategory = null;
            $this->clearTicket();
            $this->guestName = '';
        }
    }

    public function render()
    {
        $tablesQuery = Table::query()->with(['eventBookings' => function($q) {
            $q->where('status', 'approved')
              ->whereHas('event', function($q2) {
                  $q2->whereDate('event_date', '>=', now()->toDateString());
              });
        }]);
        
        if ($this->tableSearch) {
            $tablesQuery->where('number', 'like', '%' . $this->tableSearch . '%');
        }
        
        if ($this->selectedSection !== null) {
            $tablesQuery->where('section', $this->selectedSection);
            $tables = $tablesQuery->orderBy('sort_order')->orderBy('id')->get();
        } else {
            $tables = collect();
        }

        // Fetch products based on categories visible in the orders page
        $visibleCategories = Category::where('is_visible', true)->pluck('name')->toArray();
        $products = Product::where('is_active', true)
            ->whereIn('category', $visibleCategories)
            ->orderBy('category')->orderBy('name')->get();
            
        $productGrams = $products->pluck('grams_per_order', 'name');
        
        $shiftStart = \App\Models\WorkShift::getActiveShiftStart();
        $recentOrders = Order::with('table')
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $shiftStart)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $delayedOrders = Order::with('table')
            ->whereIn('status', ['pending', 'prep'])
            ->where('created_at', '<=', now()->subMinutes(10))
            ->get();

        $sections = Section::orderBy('name')->get();

        return view('livewire.order-staff-board', [
            'tables'       => $tables,
            'products'     => $products,
            'productGrams' => $productGrams,
            'recentOrders' => $recentOrders,
            'delayedOrders'=> $delayedOrders,
            'sections'     => $sections,
        ])->layout('components.layouts.app');
    }
}
