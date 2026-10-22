<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Table;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Component
{
    public $filterStatus = 'all';
    public $previousApprovalCount = 0;
    public $ticketGuestName = '';
    public $ticketNumPeople = 1;

    // Counter Details Modal
    public $showDetailsModal = false;
    public $detailsModalTitle = '';
    public $detailsModalType = '';
    public $detailsModalData = [];

    public function showCounterDetails($type)
    {
        $this->detailsModalType = $type;
        $this->detailsModalData = [];
        $shiftStart = \App\Models\WorkShift::getActiveShiftStart();
        
        if ($type === 'ordersToday') {
            $this->detailsModalTitle = 'تفاصيل طلبات اليوم';
            $this->detailsModalData = Order::with(['table', 'user'])->where('created_at', '>=', $shiftStart)->where('status', '!=', 'cancelled')->get()->toArray();
        } elseif ($type === 'activeOrders') {
            $this->detailsModalTitle = 'تفاصيل الطلبات النشطة';
            $this->detailsModalData = Order::with(['table', 'user'])->whereIn('status', ['pending', 'prep'])->get()->toArray();
        } elseif ($type === 'completedOrders') {
            $this->detailsModalTitle = 'تفاصيل الطلبات المكتملة';
            $this->detailsModalData = Order::with(['table', 'user'])->where('status', 'delivered')->where('created_at', '>=', $shiftStart)->get()->toArray();
        } elseif ($type === 'todayTickets') {
            $this->detailsModalTitle = 'إجمالي تذاكر اليوم';
            $this->detailsModalData = Ticket::with(['user', 'ticketTemplate'])->where('status', '!=', 'cancelled')->where('created_at', '>=', $shiftStart)->get()->toArray();
        } elseif ($type === 'todayTicketsActive') {
            $this->detailsModalTitle = 'التذاكر النشطة في الانتظار';
            $this->detailsModalData = Ticket::with(['user', 'ticketTemplate'])->where('status', 'active')->where('created_at', '>=', $shiftStart)->get()->toArray();
        } elseif ($type === 'todayTicketsUsed') {
            $this->detailsModalTitle = 'التذاكر المستخدمة';
            $this->detailsModalData = Ticket::with(['user', 'ticketTemplate'])->where('status', 'used')->where('created_at', '>=', $shiftStart)->get()->toArray();
        } elseif ($type === 'todayTotalPeople') {
            $this->detailsModalTitle = 'طلبات الضيوف الموافق عليها';
            $this->detailsModalData = Order::with(['table', 'user'])->whereNotNull('guest_name')->where('manager_approval_status', 'approved')->where('created_at', '>=', $shiftStart)->get()->toArray();
        }

        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->detailsModalData = [];
    }
    public $lastAdminTicket  = null;

    public function incrementTicketPeople() { $this->ticketNumPeople = min(50, $this->ticketNumPeople + 1); }
    public function decrementTicketPeople() { $this->ticketNumPeople = max(1, $this->ticketNumPeople - 1); }

    public function adminIssueTicket()
    {
        $this->validate([
            'ticketGuestName'  => 'nullable|string|max:191',
            'ticketNumPeople'  => 'required|integer|min:1|max:50',
        ]);
        $maxBarcode = Ticket::selectRaw('MAX(CAST(barcode AS UNSIGNED)) as max_barcode')->value('max_barcode');
        $barcode = (string)($maxBarcode ? max(1000, $maxBarcode + 1) : 1000);
        $ticket = Ticket::create([
            'user_id'    => Auth::id(),
            'guest_name' => $this->ticketGuestName ?: null,
            'num_people' => $this->ticketNumPeople,
            'barcode'    => $barcode,
            'status'     => 'active',
        ]);

        $this->lastAdminTicket  = $ticket;
        $this->dispatch('admin-print-ticket', ticketId: $ticket->id);
        session()->flash('success', "تم إصدار التذكرة #{$ticket->id} — الباركود: {$barcode}");
        $this->ticketGuestName = '';
        $this->ticketNumPeople = 1;
    }

    public function adminCancelTicket($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        if ($ticket && $ticket->status === 'active') {
            $ticket->status = 'cancelled';
            $ticket->save();
            session()->flash('success', 'تم إلغاء التذكرة.');
        }
    }
    public function approveCancellation($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->cancel_requested) {
            $order->status = 'cancelled';
            $order->cancel_requested = false;
            $order->save();

            // Free the table only if no other active orders exist
            if ($order->releaseTableAndChairIfEmpty()) {
                session()->flash('success', 'تم قبول طلب الإلغاء وتحرير الطاولة.');
            } else {
                session()->flash('success', 'تم قبول طلب الإلغاء. (يوجد طلبات أخرى نشطة لنفس الطاولة)');
            }
        }
    }

    public function rejectCancellation($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->cancel_requested = false;
            $order->save();
            session()->flash('success', 'تم رفض طلب الإلغاء، الطلب لا يزال نشطاً.');
        }
    }

    public function approveCancelDuplicate($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->cancel_duplicate_requested) {
            $order->status = 'cancelled';
            $order->cancel_duplicate_requested = false;
            $order->save();
            
            if ($order->releaseTableAndChairIfEmpty()) {
                session()->flash('success', 'تم قبول طلب إلغاء التكرار وتحرير الطاولة.');
            } else {
                session()->flash('success', 'تم قبول طلب إلغاء التكرار وتم إلغاء الطلب بنجاح.');
            }
        }
    }

    public function rejectCancelDuplicate($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->cancel_duplicate_requested) {
            $order->cancel_duplicate_requested = false;
            $order->save();
            session()->flash('success', 'تم رفض طلب إلغاء التكرار، الطلب لا يزال نشطاً.');
        }
    }

    public function approveSpecialOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->manager_approval_status === 'pending') {
            // Find all orders in the same batch that need approval
            $batchOrders = Order::where('batch_id', $order->batch_id)
                                ->where('manager_approval_status', 'pending')
                                ->get();
                                
            foreach($batchOrders as $bOrder) {
                $bOrder->manager_approval_status = 'approved';
                $bOrder->approved_by = auth()->id();
                $bOrder->save();
            }

            if ($order->table) {
                $order->table->status = 'order_prep';
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
                                
            foreach($batchOrders as $bOrder) {
                $bOrder->manager_approval_status = 'rejected';
                $bOrder->status = 'cancelled';
                $bOrder->save();
            }

            session()->flash('success', 'تم رفض الطلب الخاص بنجاح.');
        }
    }

    public function render()
    {
        $shiftStart = \App\Models\WorkShift::getActiveShiftStart();

        $ordersToday = Order::where('created_at', '>=', $shiftStart)->where('status', '!=', 'cancelled')->count();
        $activeOrders = Order::whereIn('status', ['pending', 'prep'])->count();
        $completedOrders = Order::where('status', 'delivered')->where('created_at', '>=', $shiftStart)->count();
        
        $mostRequestedShisha = Order::select('shisha_type')
            ->where('status', '!=', 'cancelled')
            ->selectRaw('count(*) as total')
            ->groupBy('shisha_type')
            ->orderBy('total', 'desc')
            ->first();

        $mostActiveTable = Order::select('table_id')
            ->where('status', '!=', 'cancelled')
            ->selectRaw('count(*) as total')
            ->groupBy('table_id')
            ->orderBy('total', 'desc')
            ->first();

        $activeTableNumber = $mostActiveTable ? Table::find($mostActiveTable->table_id)?->number : 'لا يوجد';

        $tables = Table::all();
        $orders = Order::with('table', 'user')->where('status', '!=', 'cancelled')->orderBy('created_at', 'desc')->take(20)->get();
        $cancelRequests = Order::with('table', 'user')
            ->where('cancel_requested', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $duplicateCancelRequests = Order::with('table', 'user')
            ->where('cancel_duplicate_requested', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $approvalRequests = Order::with('table', 'user')
            ->where('manager_approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $currentApprovalCount = $approvalRequests->count();
        if ($currentApprovalCount > $this->previousApprovalCount) {
            $this->dispatch('play-approval-sound');
        }
        $this->previousApprovalCount = $currentApprovalCount;

        // --- Inventory Summary ---
        $totalStockIn = StockEntry::where('type', 'addition')->sum('quantity_grams');
        $totalWaste = StockEntry::where('type', 'waste')->sum('quantity_grams');
        $totalConsumed = Order::whereNotIn('status', ['cancelled'])
            ->selectRaw('SUM(quantity * grams_per_unit) as total_grams')
            ->value('total_grams') ?? 0;
        $totalRemaining = $totalStockIn - $totalConsumed - $totalWaste;

        // --- Detailed Today's Table Inventory Report ---
        $todayOrders = Order::with(['table', 'user'])
            ->where('created_at', '>=', $shiftStart)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'asc')
            ->get();

        $tableReport = [];
        $todayProductTotals = [];

        foreach ($todayOrders as $order) {
            $tableNumber = $order->table ? $order->table->number : 'أخرى';
            $sectionName = ($order->table && $order->table->section) ? $order->table->section : '';
            
            $key = $tableNumber . ($sectionName ? " ($sectionName)" : "");
            if (!isset($tableReport[$key])) {
                $tableReport[$key] = [
                    'table_number' => $tableNumber,
                    'section' => $sectionName,
                    'orders' => [],
                ];
            }
            
            $tableReport[$key]['orders'][] = [
                'shisha_type' => $order->shisha_type,
                'quantity' => $order->quantity,
                'waiter' => $order->user ? $order->user->name : 'غير معروف',
                'time' => $order->created_at->format('h:i A'),
            ];

            // Summarize total items served today
            if (!isset($todayProductTotals[$order->shisha_type])) {
                $todayProductTotals[$order->shisha_type] = 0;
            }
            $todayProductTotals[$order->shisha_type] += $order->quantity;
        }

        // --- Today's Tickets ---
        $todayTickets = Ticket::with('user')
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $shiftStart)
            ->orderBy('created_at', 'desc')
            ->get();

        // --- Reception Stats ---
        $todayTicketsActive = $todayTickets->where('status', 'active')->count();
        $todayTicketsUsed   = $todayTickets->where('status', 'used')->count();
        
        // Count guests (complimentary orders) that have manager approval
        $todayTotalPeople   = Order::whereNotNull('guest_name')
            ->where('manager_approval_status', 'approved')
            ->where('created_at', '>=', $shiftStart)
            ->count();


        return view('livewire.admin-dashboard', [
            'ordersToday' => $ordersToday,
            'activeOrders' => $activeOrders,
            'completedOrders' => $completedOrders,
            'mostRequestedShisha' => $mostRequestedShisha ? $mostRequestedShisha->shisha_type : 'لا يوجد',
            'activeTableNumber' => $activeTableNumber,
            'tables' => $tables,
            'orders' => $orders,
            'cancelRequests' => $cancelRequests,
            'duplicateCancelRequests' => $duplicateCancelRequests,
            'approvalRequests' => $approvalRequests,
            'totalStockIn' => $totalStockIn,
            'totalConsumed' => $totalConsumed,
            'totalWaste' => $totalWaste,
            'totalRemaining' => $totalRemaining,
            
            // Passing detailed report variables
            'tableReport'           => $tableReport,
            'todayProductTotals'    => $todayProductTotals,
            'todayTickets'          => $todayTickets,
            'lastAdminTicket'       => $this->lastAdminTicket,

            // Reception Stats
            'todayTicketsActive'    => $todayTicketsActive,
            'todayTicketsUsed'      => $todayTicketsUsed,
            'todayTotalPeople'      => $todayTotalPeople,
        ])->layout('components.layouts.admin');
    }
}
