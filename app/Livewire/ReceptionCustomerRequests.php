<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EventBooking;
use App\Models\Event;
use Livewire\Attributes\Layout;

class ReceptionCustomerRequests extends Component
{
    public $filterDate;
    public $isAdminRoute = false;

    // Drill-down State
    public $selectedEventId = null;
    public $selectedTableId = null;
    public $selectedBookingForChair = null; // Will hold the EventBooking model for customer details
    
    // For manual override of chair/table if needed (optional)
    public $isTableSelectorModalOpen = false;
    public $activeBookingId = null;

    // Edit Location
    public $editTableNumber = '';
    public $editChairNumber = '';
    public $editAvailableChairs = [];

    // Add Customer
    public $showAddCustomerModal = false;
    public $newCustomerName = '';
    public $newCustomerPhone = '';
    public $newCustomerGender = 'male';
    public $newCustomerTable = '';
    public $newCustomerChair = '';
    public $newAvailableChairs = [];

    public function mount()
    {
        $this->filterDate = date('Y-m-d');
        $this->isAdminRoute = request()->is('admin/*');
    }

    public function approveBooking($bookingId)
    {
        $booking = EventBooking::find($bookingId);
        if ($booking) {
            $booking->status = 'approved';
            $booking->save();
            session()->flash('success', 'تم قبول الحجز بنجاح!');
        }
    }

    public function rejectBooking($bookingId)
    {
        $booking = EventBooking::find($bookingId);
        if ($booking) {
            $booking->status = 'rejected';
            $booking->save();
            session()->flash('success', 'تم رفض الحجز.');
        }
    }

    // --- Drill Down Navigation ---
    
    public function selectEvent($eventId)
    {
        $this->selectedEventId = $eventId;
        $this->selectedTableId = null;
        $this->selectedBookingForChair = null;
    }

    public function selectTable($tableId)
    {
        $this->selectedTableId = $tableId;
        $this->selectedBookingForChair = null;
    }

    public function selectChairBooking($bookingId)
    {
        $this->selectedBookingForChair = EventBooking::find($bookingId);
        if ($this->selectedBookingForChair) {
            $this->editTableNumber = $this->selectedBookingForChair->table_number;
            $this->updatedEditTableNumber($this->editTableNumber);
            $this->editChairNumber = $this->selectedBookingForChair->chair_number;
        }
    }

    public function updatedEditTableNumber($value)
    {
        $table = \App\Models\Table::where('number', $value)->first();
        if ($table && $this->selectedEventId) {
            $bookedChairNumbers = EventBooking::where('event_id', $this->selectedEventId)
                ->where('table_number', $value)
                ->where('id', '!=', $this->selectedBookingForChair->id ?? null)
                ->pluck('chair_number')
                ->toArray();
                
            $this->editAvailableChairs = $table->chairs->map(function($chair) use ($bookedChairNumbers) {
                return [
                    'number' => $chair->number,
                    'is_booked' => in_array($chair->number, $bookedChairNumbers)
                ];
            })->toArray();
        } else {
            $this->editAvailableChairs = [];
        }
        // $this->editChairNumber = '';
    }

    public function updatedNewCustomerTable($value)
    {
        $table = \App\Models\Table::where('number', $value)->first();
        if ($table && $this->selectedEventId) {
            $bookedChairNumbers = EventBooking::where('event_id', $this->selectedEventId)
                ->where('table_number', $value)
                ->pluck('chair_number')
                ->toArray();
                
            $this->newAvailableChairs = $table->chairs->map(function($chair) use ($bookedChairNumbers) {
                return [
                    'number' => $chair->number,
                    'is_booked' => in_array($chair->number, $bookedChairNumbers)
                ];
            })->toArray();
        } else {
            $this->newAvailableChairs = [];
        }
        $this->newCustomerChair = '';
    }
    
    public function updateBookingLocation()
    {
        $this->validate([
            'editTableNumber' => 'required',
            'editChairNumber' => 'required',
        ]);

        if ($this->selectedBookingForChair) {
            $this->selectedBookingForChair->table_number = $this->editTableNumber;
            $this->selectedBookingForChair->chair_number = $this->editChairNumber;
            
            $realTable = \App\Models\Table::where('number', $this->editTableNumber)->first();
            $this->selectedBookingForChair->table_id = $realTable ? $realTable->id : null;
            
            if ($realTable) {
                $realChair = \App\Models\Chair::where('table_id', $realTable->id)->where('number', $this->editChairNumber)->first();
                $this->selectedBookingForChair->chair_id = $realChair ? $realChair->id : null;
            } else {
                $this->selectedBookingForChair->chair_id = null;
            }

            $this->selectedBookingForChair->save();
            session()->flash('success', 'تم تعديل الطاولة والكرسي بنجاح!');
            $this->closeCustomerDetails();
        }
    }
    
    public function openAddCustomerModal()
    {
        $this->showAddCustomerModal = true;
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
        $this->newCustomerGender = 'male';
        $this->newCustomerTable = '';
        $this->newCustomerChair = '';
        $this->newAvailableChairs = [];
    }

    public function closeAddCustomerModal()
    {
        $this->showAddCustomerModal = false;
    }

    public function addCustomerToEvent()
    {
        $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerPhone' => 'required|string|max:50',
            'newCustomerTable' => 'required',
            'newCustomerChair' => 'required',
        ], [
            'newCustomerName.required' => 'اسم العميل مطلوب',
            'newCustomerPhone.required' => 'رقم الجوال مطلوب',
            'newCustomerTable.required' => 'رقم الطاولة مطلوب',
            'newCustomerChair.required' => 'رقم الكرسي مطلوب',
        ]);

        $realTable = \App\Models\Table::where('number', $this->newCustomerTable)->first();
        $realChair = null;
        if ($realTable) {
            $realChair = \App\Models\Chair::where('table_id', $realTable->id)->where('number', $this->newCustomerChair)->first();
        }

        EventBooking::create([
            'event_id' => $this->selectedEventId,
            'customer_name' => $this->newCustomerName,
            'customer_phone' => $this->newCustomerPhone,
            'gender' => $this->newCustomerGender,
            'table_number' => $this->newCustomerTable,
            'table_id' => $realTable ? $realTable->id : null,
            'chair_number' => $this->newCustomerChair,
            'chair_id' => $realChair ? $realChair->id : null,
            'status' => 'approved',
        ]);

        session()->flash('success', 'تم إضافة العميل للحفلة بنجاح!');
        $this->closeAddCustomerModal();
    }

    public function closeCustomerDetails()
    {
        $this->selectedBookingForChair = null;
    }

    public function backToEvents()
    {
        $this->selectedEventId = null;
        $this->selectedTableId = null;
        $this->selectedBookingForChair = null;
    }

    public function backToTables()
    {
        $this->selectedTableId = null;
        $this->selectedBookingForChair = null;
    }
    public function render()
    {
        // 1. Get Events that have bookings (approved OR pending)
        $eventsQuery = Event::withCount(['bookings as approved_bookings_count' => function ($query) {
            $query->where('status', 'approved');
        }, 'bookings as pending_bookings_count' => function ($query) {
            $query->where('status', 'pending');
        }])->whereHas('bookings', function ($query) {
            $query->whereIn('status', ['approved', 'pending']);
        });
        
        $events = $eventsQuery->orderBy('event_date', 'desc')->get();

        // 2. Pending bookings for selected event
        $pendingBookings = collect();
        
        // 3. If an event is selected, get its tables (approved) AND pending bookings
        $tables = collect();
        $selectedEvent = null;
        if ($this->selectedEventId) {
            $selectedEvent = Event::find($this->selectedEventId);
            
            // Get pending bookings for this event
            $pendingBookings = EventBooking::where('event_id', $this->selectedEventId)
                                            ->where('status', 'pending')
                                            ->orderBy('created_at', 'desc')
                                            ->get();
            
            // Group approved bookings by table_id
            $bookingsForEvent = EventBooking::where('event_id', $this->selectedEventId)
                                            ->where('status', 'approved')
                                            ->with('table')
                                            ->get();
                                            
            $tables = $bookingsForEvent->groupBy(function($item) {
                return $item->table_id ?: ($item->table_number ?: 'بدون_طاولة');
            });
        }

        // 4. If a table is selected, get its chairs (bookings)
        $chairs = collect();
        if ($this->selectedEventId && $this->selectedTableId) {
            $chairs = EventBooking::where('event_id', $this->selectedEventId)
                                  ->where('status', 'approved')
                                  ->where(function($q) {
                                      if (is_numeric($this->selectedTableId)) {
                                          $q->where('table_id', $this->selectedTableId)
                                            ->orWhere('table_number', $this->selectedTableId);
                                      } else {
                                          $q->where('table_number', $this->selectedTableId)
                                            ->orWhereNull('table_id');
                                      }
                                  })
                                  ->get();
        }

        // 5. Get Event Tables for dropdowns
        $eventTables = \App\Models\Table::where('section', 'صالة الحفلات')->orderBy('number')->get();

        $layout = $this->isAdminRoute ? 'components.layouts.admin' : 'components.layouts.app';

        return view('livewire.reception-customer-requests', [
            'events' => $events,
            'selectedEvent' => $selectedEvent,
            'tables' => $tables,
            'chairs' => $chairs,
            'eventTables' => $eventTables,
            'pendingBookings' => $pendingBookings,
        ])->layout($layout);
    }
}

