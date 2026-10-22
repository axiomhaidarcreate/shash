<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\TicketTemplate;
use App\Models\Product;
use App\Models\Order;
use App\Models\Table;
use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Models\SubscriptionPackage;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;

class ReceptionBoard extends Component
{
    // Ticket Issuing (staff cannot create templates)
    public $selectedTemplateId = null;
    public $selectedProducts = [];
    public $validityHours = 12;

    // Manager Approval PIN Modal
    public $showPinModal = false;
    public $pendingOrderIdForApproval = null;
    public $enteredPin = '';
    public $lastTicket = null;

    // Report Filters
    public $filterStartDate;
    public $filterEndDate;
    public $filterStartTime = '';
    public $filterEndTime = '';

    // Manager Approval Requests
    public $previousApprovalCount = 0;

    // Loyalty Customer Link
    public $loyaltySearch = '';
    public $loyaltySearchResults = [];
    
    // Customer details popup modal after print
    public $showCustomerDetailsModal = false;
    public $printedCustomerDetails = null;
    public $linkedCustomer = null;
    public $loyaltyMessage = '';

    // Subscription
    public $subscriptionSearch = '';
    public $subscriptionSearchResults = [];
    public $subscriptionCustomer = null;
    public $selectedSubscriptionPackageId = null;
    public $showSubscriptionModal = false;
    
    public $isCreatingNewCustomer = false;
    public $newCustomerName = '';
    public $newCustomerPhone = '';

    public function mount()
    {
        $this->filterStartDate = now()->format('Y-m-d');
        $this->filterEndDate = now()->format('Y-m-d');
    }

    public function closeDay()
    {
        $todayTickets = Ticket::where('is_daily_closed', false)
            ->get();

        if ($todayTickets->isEmpty()) {
            session()->flash('error', 'لا توجد تذاكر نشطة أو ملغية لإغلاقها اليوم.');
            return;
        }

        $totalTickets = $todayTickets->count();
        $cancelledTickets = $todayTickets->where('status', 'cancelled')->count();
        $soldTickets = $totalTickets - $cancelledTickets;

        $activeShift = \App\Models\WorkShift::where('status', 'open')->first();

        // Create Report
        \App\Models\TicketDailyReport::create([
            'user_id' => Auth::id(),
            'total_tickets' => $totalTickets,
            'sold_tickets' => $soldTickets,
            'cancelled_tickets' => $cancelledTickets,
            'opened_at' => $activeShift ? $activeShift->opened_at : $todayTickets->min('created_at'),
            'closed_at' => now(),
        ]);

        // Mark tickets as closed
        Ticket::whereIn('id', $todayTickets->pluck('id'))->update(['is_daily_closed' => true]);

        session()->flash('success', 'تم إغلاق التذاكر وتصفير العدادات وإصدار التقرير بنجاح.');
    }

    public function selectTemplate($templateId)
    {
        $this->selectedTemplateId = $templateId;
        $this->selectedProducts = [];
    }

    public function toggleProduct($productName)
    {
        $template = TicketTemplate::find($this->selectedTemplateId);
        if (!$template) return;

        if (in_array($productName, $this->selectedProducts)) {
            $this->selectedProducts = array_values(array_diff($this->selectedProducts, [$productName]));
        } else {
            if (count($this->selectedProducts) < $template->max_items) {
                $this->selectedProducts[] = $productName;
            } else {
                session()->flash('error', "أقصى عدد للطلبات في هذه التذكرة هو " . $template->max_items);
            }
        }
    }

    // ---- Loyalty Customer Methods ----

    public function updatedLoyaltySearch()
    {
        if (strlen(trim($this->loyaltySearch)) >= 2) {
            $this->loyaltySearchResults = Customer::with(['loyaltyOffer', 'subscriptionPackage'])
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
        $customer = Customer::with(['loyaltyOffer', 'subscriptionPackage'])->find($customerId);
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

    public function issueTicket()
    {
        $template = TicketTemplate::find($this->selectedTemplateId);
        if (!$template) {
            session()->flash('error', 'يرجى اختيار نوع التذكرة أولاً.');
            return;
        }

        if (count($this->selectedProducts) !== $template->max_items) {
            session()->flash('error', "يجب اختيار {$template->max_items} طلبات لهذه التذكرة.");
            return;
        }
        $maxBarcode = Ticket::selectRaw('MAX(CAST(barcode AS UNSIGNED)) as max_barcode')->value('max_barcode');
        $barcode = (string)($maxBarcode ? max(1000, $maxBarcode + 1) : 1000);

        $ticket = Ticket::create([
            'user_id'            => Auth::id(),
            'customer_id'        => $this->linkedCustomer ? $this->linkedCustomer->id : null,
            'ticket_template_id' => $template->id,
            'selected_products'  => $this->selectedProducts,
            'barcode'            => $barcode,
            'status'             => 'active',
            'validity_hours'     => 12,
            'times_used'         => 0,
            'max_uses'           => $template->max_items,
        ]);

        // Auto-add loyalty points if customer is linked
        if ($this->linkedCustomer && $this->linkedCustomer->loyaltyOffer) {
            $offer = $this->linkedCustomer->loyaltyOffer;
            $pointsToAdd = $offer->points_per_visit + $offer->points_per_order;

            if ($pointsToAdd > 0) {
                $this->linkedCustomer->increment('total_points', $pointsToAdd);

                LoyaltyTransaction::create([
                    'customer_id'  => $this->linkedCustomer->id,
                    'points_added' => $pointsToAdd,
                    'description'  => "تذكرة #{$ticket->id} — زيارة ({$offer->points_per_visit}) + طلب ({$offer->points_per_order})",
                ]);

                // Send WhatsApp notification
                $whatsapp = app(WhatsAppService::class);
                $this->linkedCustomer->refresh();
                $whatsapp->sendPointsUpdate($this->linkedCustomer, $pointsToAdd, "تذكرة #{$ticket->id}");

                session()->flash('success', "تم إصدار التذكرة #{$ticket->id} بنجاح! ✅ تم إضافة {$pointsToAdd} نقطة للعميل {$this->linkedCustomer->name}");
            } else {
                session()->flash('success', "تم إصدار التذكرة #{$ticket->id} بنجاح! الباركود: {$barcode}");
            }
        } else {
            session()->flash('success', "تم إصدار التذكرة #{$ticket->id} بنجاح! الباركود: {$barcode}");
        }

        $this->lastTicket = $ticket;
        $productsCount = max(1, count($this->selectedProducts));
        $this->dispatch('print-ticket', ticketId: $ticket->id, productsCount: $productsCount);

        if ($this->linkedCustomer) {
            $this->printedCustomerDetails = Customer::with(['loyaltyOffer.rewardTiers', 'subscriptionPackage'])->find($this->linkedCustomer->id);
            $this->showCustomerDetailsModal = true;
        }

        $this->selectedTemplateId = null;
        $this->selectedProducts = [];
        $this->clearLoyaltyCustomer();
    }

    public function openCustomerDetailsModal($customerId)
    {
        $customer = Customer::with(['loyaltyOffer.rewardTiers', 'subscriptionPackage'])->find($customerId);
        if ($customer) {
            $this->printedCustomerDetails = $customer;
            $this->showCustomerDetailsModal = true;
        }
    }

    public function closeCustomerDetailsModal()
    {
        $this->showCustomerDetailsModal = false;
        $this->printedCustomerDetails = null;
    }

    // ---- Subscription Methods ----

    public function updatedSubscriptionSearch()
    {
        if (strlen(trim($this->subscriptionSearch)) >= 2) {
            $this->subscriptionSearchResults = Customer::with('subscriptionPackage')
                ->where('name', 'like', '%' . trim($this->subscriptionSearch) . '%')
                ->orWhere('phone', 'like', '%' . trim($this->subscriptionSearch) . '%')
                ->orWhere('subscription_number', 'like', '%' . trim($this->subscriptionSearch) . '%')
                ->take(5)
                ->get();
        } else {
            $this->subscriptionSearchResults = [];
        }
    }

    public function openSubscriptionModal()
    {
        $this->showSubscriptionModal = true;
        $this->subscriptionSearch = '';
        $this->subscriptionSearchResults = [];
        $this->subscriptionCustomer = null;
        $this->selectedSubscriptionPackageId = null;
        $this->isCreatingNewCustomer = false;
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
    }

    public function closeSubscriptionModal()
    {
        $this->showSubscriptionModal = false;
        $this->subscriptionSearch = '';
        $this->subscriptionSearchResults = [];
        $this->subscriptionCustomer = null;
        $this->selectedSubscriptionPackageId = null;
    }

    public function selectSubscriptionCustomer($customerId)
    {
        $this->subscriptionCustomer = Customer::with('subscriptionPackage')->find($customerId);
        $this->subscriptionSearch = '';
        $this->subscriptionSearchResults = [];
        $this->isCreatingNewCustomer = false;
    }

    public function toggleCreateCustomer()
    {
        $this->isCreatingNewCustomer = !$this->isCreatingNewCustomer;
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
        $this->subscriptionSearch = '';
        $this->subscriptionSearchResults = [];
    }

    public function saveNewCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerPhone' => 'nullable|string|max:20',
        ], [
            'newCustomerName.required' => 'اسم العميل مطلوب.',
        ]);

        $subNumber = $this->generateSubscriptionNumber();

        $customer = Customer::create([
            'name' => $this->newCustomerName,
            'phone' => $this->newCustomerPhone,
            'subscription_number' => $subNumber,
        ]);

        $this->subscriptionCustomer = Customer::with('subscriptionPackage')->find($customer->id);
        $this->isCreatingNewCustomer = false;
        
        session()->flash('success', 'تم إضافة العميل بنجاح! يمكنك الآن اختيار الباقة له.');
    }

    private function generateSubscriptionNumber()
    {
        do {
            $number = mt_rand(100000, 999999);
        } while (Customer::where('subscription_number', $number)->exists());
        return (string) $number;
    }

    public function activateSubscription()
    {
        if (!$this->subscriptionCustomer) {
            session()->flash('error', 'يرجى اختيار العميل أولاً.');
            return;
        }
        if (!$this->selectedSubscriptionPackageId) {
            session()->flash('error', 'يرجى اختيار الباقة.');
            return;
        }

        $package = SubscriptionPackage::find($this->selectedSubscriptionPackageId);
        if (!$package) {
            session()->flash('error', 'الباقة غير موجودة.');
            return;
        }

        $startDate = now();
        $this->subscriptionCustomer->update([
            'subscription_package_id' => $package->id,
            'subscription_start_date' => $startDate->format('Y-m-d'),
            'subscription_end_date' => $startDate->copy()->addDays($package->duration_days)->format('Y-m-d'),
            'last_subscription_usage_date' => null,
            'daily_subscription_usage_count' => 0,
        ]);

        session()->flash('success', "✅ تم تفعيل باقة '{$package->name}' للعميل {$this->subscriptionCustomer->name} — صالحة حتى " . $startDate->copy()->addDays($package->duration_days)->format('Y-m-d'));
        
        // Auto-link customer to the ticket section
        $this->linkedCustomer = Customer::with(['loyaltyOffer', 'subscriptionPackage'])->find($this->subscriptionCustomer->id);

        $this->closeSubscriptionModal();
    }

    public function issueTicketViaSubscription()
    {
        if (!$this->linkedCustomer) {
            session()->flash('error', 'يرجى ربط العميل أولاً.');
            return;
        }

        $customer = Customer::with('subscriptionPackage')->find($this->linkedCustomer->id);
        if (!$customer || !$customer->hasActiveSubscription()) {
            session()->flash('error', 'اشتراك العميل غير ساري أو منتهي.');
            return;
        }

        if (!$customer->canUseSubscriptionToday()) {
            $limit = $customer->subscriptionPackage->daily_order_limit ?? 1;
            session()->flash('error', "العميل وصل الحد اليومي ({$limit} طلب). يرجى المحاولة غداً.");
            return;
        }

        $template = TicketTemplate::find($this->selectedTemplateId);
        if (!$template) {
            session()->flash('error', 'يرجى اختيار نوع التذكرة أولاً.');
            return;
        }

        if (count($this->selectedProducts) !== $template->max_items) {
            session()->flash('error', "يجب اختيار {$template->max_items} طلبات لهذه التذكرة.");
            return;
        }

        // Check selected products are in subscription's included_products
        $package = $customer->subscriptionPackage;
        $allowedProducts = is_array($package->included_products) ? $package->included_products : [];
        if (!empty($allowedProducts)) {
            foreach ($this->selectedProducts as $prod) {
                if (!in_array($prod, $allowedProducts)) {
                    session()->flash('error', "الصنف '{$prod}' غير مشمول في باقة '{$package->name}'. الأصناف المتاحة: " . implode('، ', $allowedProducts));
                    return;
                }
            }
        }

        // Issue ticket
        $maxBarcode = Ticket::selectRaw('MAX(CAST(barcode AS UNSIGNED)) as max_barcode')->value('max_barcode');
        $barcode = (string)($maxBarcode ? max(1000, $maxBarcode + 1) : 1000);

        $ticket = Ticket::create([
            'user_id'            => Auth::id(),
            'customer_id'        => $customer->id,
            'ticket_template_id' => $template->id,
            'selected_products'  => $this->selectedProducts,
            'barcode'            => $barcode,
            'status'             => 'active',
            'is_subscription'    => true,
            'validity_hours'     => 12,
            'times_used'         => 0,
            'max_uses'           => $template->max_items,
        ]);

        // Update subscription usage
        $today = now()->format('Y-m-d');
        if ($customer->last_subscription_usage_date === $today) {
            $customer->increment('daily_subscription_usage_count');
        } else {
            $customer->update([
                'last_subscription_usage_date' => $today,
                'daily_subscription_usage_count' => 1,
            ]);
        }

        $this->lastTicket = $ticket;
        $productsCount = max(1, count($this->selectedProducts));
        $this->dispatch('print-ticket', ticketId: $ticket->id, productsCount: $productsCount);

        $remainingDays = now()->diffInDays(\Carbon\Carbon::parse($customer->subscription_end_date));
        session()->flash('success', "✅ تم إصدار تذكرة #{$ticket->id} عبر الاشتراك! (متبقي {$remainingDays} يوم)");

        $this->printedCustomerDetails = Customer::with(['loyaltyOffer.rewardTiers', 'subscriptionPackage'])->find($customer->id);
        $this->showCustomerDetailsModal = true;

        $this->selectedTemplateId = null;
        $this->selectedProducts = [];
        $this->clearLoyaltyCustomer();
    }

    public function cancelTicket($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        if ($ticket && $ticket->status === 'active') {
            $ticket->status = 'cancelled';
            $ticket->save();
            session()->flash('success', 'تم إلغاء التذكرة بنجاح.');
        }
    }

    public function markTicketAsUsed($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        if ($ticket && $ticket->status === 'active') {
            $ticket->status = 'used';
            $ticket->save();
            session()->flash('success', 'تم استخدام التذكرة بنجاح.');
        }
    }

    public function openPinModal($orderId)
    {
        $this->pendingOrderIdForApproval = $orderId;
        $this->enteredPin = '';
        $this->showPinModal = true;
    }

    public function closePinModal()
    {
        $this->showPinModal = false;
        $this->pendingOrderIdForApproval = null;
        $this->enteredPin = '';
    }

    public function verifyPinAndApprove()
    {
        // Define the manager PIN here (could also be from config or env)
        $managerPin = \App\Models\Setting::where('key', 'manager_pin')->value('value') ?? env('MANAGER_PIN', '7925');

        if ($this->enteredPin === $managerPin) {
            $this->approveSpecialOrder($this->pendingOrderIdForApproval);
            $this->closePinModal();
        } else {
            $this->enteredPin = '';
            session()->flash('error', 'رمز المدير غير صحيح!');
        }
    }

    public function approveSpecialOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->manager_approval_status === 'pending') {
            $batchOrders = Order::where('batch_id', $order->batch_id)
                                ->where('manager_approval_status', 'pending')
                                ->get();
            foreach ($batchOrders as $bOrder) {
                $bOrder->manager_approval_status = 'approved';
                $bOrder->status = 'pending';
                $bOrder->save();
            }
            if ($order->table) {
                // Keep the table status appropriate for pending orders
                $order->table->status = 'customer_seated';
                $order->table->save();
            }
            session()->flash('success', 'تمت الموافقة على الطلب الخاص بنجاح.');
        }
    }

    public function rejectSpecialOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->manager_approval_status === 'pending') {
            $batchOrders = Order::where('batch_id', $order->batch_id)
                                ->where('manager_approval_status', 'pending')
                                ->get();
            foreach ($batchOrders as $bOrder) {
                $bOrder->manager_approval_status = 'rejected';
                $bOrder->status = 'cancelled';
                $bOrder->save();
            }
            session()->flash('success', 'تم رفض الطلب الخاص بنجاح.');
        }
    }

    public function render()
    {
        $templates = TicketTemplate::all();
        $products = Product::where('is_active', true)->get();

        $todayTickets = Ticket::with(['user', 'ticketTemplate'])
            ->where('status', '!=', 'cancelled')
            ->where('is_daily_closed', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Cancelled tickets (separate from main list)
        $cancelledTickets = Ticket::with(['user', 'ticketTemplate'])
            ->where('status', 'cancelled')
            ->where('is_daily_closed', false)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Manager approval requests
        $approvalRequests = Order::with(['table', 'user'])
            ->where('manager_approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $currentApprovalCount = $approvalRequests->count();
        if ($currentApprovalCount > $this->previousApprovalCount) {
            $this->dispatch('play-approval-sound');
        }
        $this->previousApprovalCount = $currentApprovalCount;

        $packages = SubscriptionPackage::where('is_active', true)->get();

        return view('livewire.reception-board', [
            'todayTickets'      => $todayTickets,
            'cancelledTickets'  => $cancelledTickets,
            'templates'         => $templates,
            'products'          => $products,
            'approvalRequests'  => $approvalRequests,
            'packages'          => $packages,
        ])->layout('components.layouts.app');
    }
}
