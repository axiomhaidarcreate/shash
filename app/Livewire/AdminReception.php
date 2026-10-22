<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\TicketTemplate;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class AdminReception extends Component
{
    // Template Creation
    public $newTemplateName = '';
    public $newTemplateMaxItems = 1;
    public $newTemplateProducts = [];
    public $customProductInput = '';

    // Ticket Issuing
    public $selectedTemplateId = null;
    public $selectedProducts = [];
    public $lastTicket = null;

    // Report Filters
    public $filterStartDate = '';
    public $filterEndDate = '';
    public $filterStartTime = '';
    public $filterEndTime = '';

    public $reportStartDate;
    public $reportEndDate;

    protected $rules = [
        'newTemplateName' => 'required|string|max:255',
        'newTemplateMaxItems' => 'required|integer|min:1',
        'newTemplateProducts' => 'required|array|min:1',
    ];

    public $editTemplateId = null;

    public function addCustomProduct()
    {
        $this->validate(['customProductInput' => 'required|string|max:100']);
        $productName = trim($this->customProductInput);
        
        if (!in_array($productName, $this->newTemplateProducts)) {
            $this->newTemplateProducts[] = $productName;
        }
        
        $this->customProductInput = '';
    }

    public function removeProduct($productName)
    {
        $this->newTemplateProducts = array_values(array_diff($this->newTemplateProducts, [$productName]));
    }

    public function createTemplate()
    {
        $this->validate([
            'newTemplateName' => 'required|string|max:255',
            'newTemplateMaxItems' => 'required|integer|min:1|max:10',
            'newTemplateProducts' => 'required|array|min:1',
        ]);

        TicketTemplate::create([
            'name' => $this->newTemplateName,
            'max_items' => $this->newTemplateMaxItems,
            'products' => array_values($this->newTemplateProducts),
        ]);

        session()->flash('success_template', 'تم إنشاء قالب التذكرة بنجاح.');
        $this->cancelEditTemplate();
    }

    public function editTemplate($id)
    {
        $template = TicketTemplate::find($id);
        if ($template) {
            $this->editTemplateId = $template->id;
            $this->newTemplateName = $template->name;
            $this->newTemplateMaxItems = $template->max_items;
            $this->newTemplateProducts = is_array($template->products) ? $template->products : [];
            $this->customProductInput = '';
        }
    }

    public function updateTemplate()
    {
        $this->validate([
            'newTemplateName' => 'required|string|max:255',
            'newTemplateMaxItems' => 'required|integer|min:1|max:10',
            'newTemplateProducts' => 'required|array|min:1',
        ]);

        $template = TicketTemplate::find($this->editTemplateId);
        if ($template) {
            $template->update([
                'name' => $this->newTemplateName,
                'max_items' => $this->newTemplateMaxItems,
                'products' => array_values($this->newTemplateProducts),
            ]);

            session()->flash('success_template', 'تم تحديث قالب التذكرة بنجاح.');
            if ($this->selectedTemplateId == $template->id) {
                $this->selectedProducts = [];
            }
        }

        $this->cancelEditTemplate();
    }

    public function cancelEditTemplate()
    {
        $this->editTemplateId = null;
        $this->newTemplateName = '';
        $this->newTemplateMaxItems = 1;
        $this->newTemplateProducts = [];
        $this->customProductInput = '';
    }

    public function deleteTemplate($id)
    {
        $template = TicketTemplate::find($id);
        if ($template) {
            if (Ticket::where('ticket_template_id', $id)->exists()) {
                session()->flash('error', 'لا يمكن حذف القالب لوجود تذاكر مرتبطة به.');
                return;
            }
            $template->delete();
            session()->flash('success_template', 'تم حذف قالب التذكرة بنجاح.');
            
            if ($this->selectedTemplateId == $id) {
                $this->selectedTemplateId = null;
                $this->selectedProducts = [];
            }
        }
    }

    public function selectTemplate($templateId)
    {
        $this->selectedTemplateId = $templateId;
        $this->selectedProducts = []; // Reset selected products
    }

    public function toggleProduct($productName)
    {
        $template = TicketTemplate::find($this->selectedTemplateId);
        if (!$template) return;

        if (in_array($productName, $this->selectedProducts)) {
            // Remove
            $this->selectedProducts = array_diff($this->selectedProducts, [$productName]);
        } else {
            // Add if within limit
            if (count($this->selectedProducts) < $template->max_items) {
                $this->selectedProducts[] = $productName;
            } else {
                session()->flash('error', "عذراً، أقصى عدد للطلبات في هذه التذكرة هو " . $template->max_items);
            }
        }
    }

    public function issueTicket()
    {
        $template = TicketTemplate::find($this->selectedTemplateId);
        if (!$template) {
            session()->flash('error', 'يرجى اختيار قالب تذكرة أولاً.');
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
            'ticket_template_id' => $template->id,
            'selected_products'  => $this->selectedProducts,
            'barcode'            => $barcode,
            'status'             => 'active',
            'validity_hours'     => 12,
            'times_used'         => 0,
            'max_uses'           => $template->max_items, // ← يأخذ من القالب
        ]);

        $this->lastTicket = $ticket;
        $this->dispatch('print-ticket', ticketId: $ticket->id, productsCount: count($this->selectedProducts) ?: 1);

        session()->flash('success', "تم إصدار التذكرة #{$ticket->id} بنجاح! الباركود: {$barcode}" . ($template->max_items > 1 ? " (تذكرة ابو {$template->max_items} طلبات)" : ''));

        $this->selectedTemplateId = null;
        $this->selectedProducts = [];
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

    public function mount()
    {
        $this->filterStartDate = '';
        $this->filterEndDate = '';
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $templates = TicketTemplate::all();
        $products = Product::where('is_active', true)->get();

        $query = Ticket::with(['user', 'ticketTemplate'])->where('status', '!=', 'cancelled');
        
        if ($this->filterStartDate && $this->filterEndDate) {
            $query->whereBetween('created_at', [
                $this->filterStartDate . ' 00:00:00',
                $this->filterEndDate . ' 23:59:59'
            ]);
        } elseif ($this->filterStartDate) {
            $query->whereDate('created_at', '>=', $this->filterStartDate);
        } elseif ($this->filterEndDate) {
            $query->whereDate('created_at', '<=', $this->filterEndDate);
        } else {
            $shiftStart = \App\Models\WorkShift::getActiveShiftStart();
            $query->where('created_at', '>=', $shiftStart);
        }

        $todayTickets = $query->orderBy('created_at', 'desc')->get();

        // Cancelled tickets (separate query with same date filters)
        $cancelledQuery = Ticket::with(['user', 'ticketTemplate'])->where('status', 'cancelled');
        if ($this->filterStartDate && $this->filterEndDate) {
            $cancelledQuery->whereBetween('created_at', [
                $this->filterStartDate . ' 00:00:00',
                $this->filterEndDate . ' 23:59:59'
            ]);
        } elseif ($this->filterStartDate) {
            $cancelledQuery->whereDate('created_at', '>=', $this->filterStartDate);
        } elseif ($this->filterEndDate) {
            $cancelledQuery->whereDate('created_at', '<=', $this->filterEndDate);
        } else {
            $shiftStart = \App\Models\WorkShift::getActiveShiftStart();
            $cancelledQuery->where('created_at', '>=', $shiftStart);
        }
        $cancelledTickets = $cancelledQuery->orderBy('updated_at', 'desc')->get();

        $stats = [
            'total'     => $todayTickets->count(),
            'active'    => $todayTickets->where('status', 'active')->count(),
            'used'      => $todayTickets->where('status', 'used')->count(),
            'cancelled' => $cancelledTickets->count(),
        ];

        $dailyReportsQuery = \App\Models\TicketDailyReport::with('user');
        if ($this->reportStartDate && $this->reportEndDate) {
            $startDate = \Carbon\Carbon::parse($this->reportStartDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->reportEndDate)->endOfDay();
            $dailyReportsQuery->whereBetween('closed_at', [$startDate, $endDate]);
        }
        $dailyReports = $dailyReportsQuery->orderBy('created_at', 'desc')->get();

        return view('livewire.admin-reception', [
            'todayTickets'     => $todayTickets,
            'cancelledTickets' => $cancelledTickets,
            'stats'            => $stats,
            'templates'        => $templates,
            'products'         => $products,
            'filterStartDate'  => $this->filterStartDate,
            'filterEndDate'    => $this->filterEndDate,
            'filterStartTime'  => $this->filterStartTime,
            'filterEndTime'    => $this->filterEndTime,
            'dailyReports'     => $dailyReports,
        ])->layout('components.layouts.admin', ['title' => 'الاستقبال']);
    }
}
