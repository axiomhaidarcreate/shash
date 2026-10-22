<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\TicketTemplate;
use Carbon\Carbon;

class AdminTicketReportViewer extends Component
{
    public bool $show = false;
    
    // UI State: 'days', 'templates', 'tickets', 'details'
    public string $viewState = 'days'; 
    
    public $selectedDate = null;
    public $selectedTemplateId = null;
    public $selectedTicketId = null;

    // Data properties
    public $availableDays = [];
    public $templatesForDay = [];
    public $ticketsForTemplate = [];
    public $ticketDetails = null;

    public function mount()
    {
        // Not loading immediately to save resources. Will load when modal opens.
    }

    protected $listeners = ['openShiftReport' => 'showShiftReport'];

    public function showReport()
    {
        $this->selectedDate = 'current_shift';
        $this->selectedTemplateId = null;
        $this->selectedTicketId = null;
        
        // Skip 'days' view, go directly to templates for current active shift
        $this->selectDay('current_shift');
        
        $this->show = true;
    }

    public function showShiftReport($reportId)
    {
        $reportId = is_array($reportId) && isset($reportId['reportId']) ? $reportId['reportId'] : $reportId;
        
        $this->selectedDate = 'shift_' . $reportId;
        $this->selectedTemplateId = null;
        $this->selectedTicketId = null;
        
        // Skip 'days' view, go directly to templates for this specific shift
        $this->selectDay($this->selectedDate);
        
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    // STEP 1: Load last 7 days summary
    public function loadAvailableDays()
    {
        // Generate last 7 days including today
        $days = collect();
        
        // Add current active shift as a special entry at the top
        $activeStats = Ticket::where('is_daily_closed', 0)
            ->selectRaw('COUNT(*) as total_tickets, 
                         SUM(CASE WHEN status = "used" THEN 1 ELSE 0 END) as sold_tickets,
                         SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_tickets')
            ->first();
            
        $days->push([
            'date' => 'current_shift',
            'day_name' => 'الفترة المحاسبية الحالية (مفتوحة)',
            'total_tickets' => $activeStats->total_tickets ?? 0,
            'sold_tickets' => $activeStats->sold_tickets ?? 0,
            'cancelled_tickets' => $activeStats->cancelled_tickets ?? 0,
        ]);
        
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            
            // Get stats for this day (historical, so we just use the date)
            $stats = Ticket::whereDate('created_at', $date)
                ->selectRaw('COUNT(*) as total_tickets, 
                             SUM(CASE WHEN status = "used" THEN 1 ELSE 0 END) as sold_tickets,
                             SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_tickets')
                ->first();
                
            $days->push([
                'date' => $date,
                'day_name' => Carbon::parse($date)->translatedFormat('l') . ' (' . $date . ')',
                'total_tickets' => $stats->total_tickets ?? 0,
                'sold_tickets' => $stats->sold_tickets ?? 0,
                'cancelled_tickets' => $stats->cancelled_tickets ?? 0,
            ]);
        }
        
        $this->availableDays = $days->toArray();
    }

    // STEP 2: Select a day -> show templates
    public function selectDay($date)
    {
        $this->selectedDate = $date;
        $this->viewState = 'templates';
        
        if ($date === 'current_shift') {
            $tickets = Ticket::where('is_daily_closed', 0)
                ->with('ticketTemplate')
                ->get();
        } elseif (str_starts_with($date, 'shift_')) {
            $reportId = str_replace('shift_', '', $date);
            $report = \App\Models\TicketDailyReport::find($reportId);
            if ($report) {
                // Also check opened_at/closed_at on TicketDailyReport
                $tickets = Ticket::whereBetween('created_at', [$report->opened_at, $report->closed_at])
                    ->with('ticketTemplate')
                    ->get();
            } else {
                $tickets = collect();
            }
        } else {
            $tickets = Ticket::whereDate('created_at', $date)
                ->with('ticketTemplate')
                ->get();
        }
            
        $grouped = $tickets->groupBy('ticket_template_id');
        
        $templates = [];
        foreach ($grouped as $templateId => $templateTickets) {
            $template = $templateTickets->first()->ticketTemplate;
            $templates[] = [
                'id' => $templateId,
                'name' => $template ? $template->name : 'قالب محذوف',
                'max_items' => $template ? $template->max_items : '-',
                'total_tickets' => $templateTickets->count(),
                'sold_tickets' => $templateTickets->where('status', 'used')->count(),
                'active_tickets' => $templateTickets->where('status', 'active')->count(),
                'cancelled_tickets' => $templateTickets->where('status', 'cancelled')->count(),
            ];
        }
        
        $this->templatesForDay = $templates;
    }

    // STEP 3: Select a template -> show tickets
    public function selectTemplate($templateId)
    {
        $this->selectedTemplateId = $templateId;
        $this->viewState = 'tickets';
        
        $query = Ticket::where('ticket_template_id', $templateId);
        
        if ($this->selectedDate === 'current_shift') {
            $query->where('is_daily_closed', 0);
        } elseif (str_starts_with($this->selectedDate, 'shift_')) {
            $reportId = str_replace('shift_', '', $this->selectedDate);
            $report = \App\Models\TicketDailyReport::find($reportId);
            if ($report) {
                $query->whereBetween('created_at', [$report->opened_at, $report->closed_at]);
            }
        } else {
            $query->whereDate('created_at', $this->selectedDate);
        }
        
        $this->ticketsForTemplate = $query
            ->with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // STEP 4: Select a ticket -> show details
    public function selectTicket($ticketId)
    {
        $this->selectedTicketId = $ticketId;
        
        // Usually we might just load a modal or side panel, 
        // but let's just fetch details if we need them explicitly.
        $this->ticketDetails = Ticket::with(['user', 'ticketTemplate', 'order', 'order.orderItems', 'order.orderItems.product'])
            ->find($ticketId);
    }
    
    public function closeTicketDetails()
    {
        $this->selectedTicketId = null;
        $this->ticketDetails = null;
    }

    public function goBack()
    {
        if ($this->viewState === 'tickets') {
            $this->viewState = 'templates';
            $this->selectedTemplateId = null;
        }
    }

    public function render()
    {
        return view('livewire.admin-ticket-report-viewer');
    }
}
