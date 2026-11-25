<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Artist;
use App\Models\Musician;
use App\Models\Event;
use App\Models\ArtistBooking;
use App\Models\EventTemplate;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class ArtistsManager extends Component
{
    use WithFileUploads;

    // Navigation state: 'main', 'artists', 'musicians'
    public $currentView = 'main';

    // Modals visibility
    public $isArtistModalOpen = false;
    public $isMusicianModalOpen = false;
    public $isEventModalOpen = false;
    public $isBookingModalOpen = false;
    public $isEventDetailsModalOpen = false;
    public $isEndEventModalOpen = false;
    public $isTemplateModalOpen = false;
    public $isAddToEventModalOpen = false;
    public $isPostShareModalOpen = false;
    public $isUploadPostImageModalOpen = false;

    // View Event State
    public $viewingEvent = null;
    public $viewingEventBookings = [];

    // Post Share State
    public $postShareEvent = null;
    public $postShareEventId = null;

    // Upload Post Image State
    public $uploadEventId = null;
    public $postUploadImage;

    // Editing IDs
    public $editingArtistId = null;
    public $editingMusicianId = null;
    public $editingEventId = null;
    public $editingTemplateId = null;

    // --- Artist Fields ---
    public $artist_real_name, $artist_stage_name, $artist_nationality, $artist_license_number, $artist_phone, $artist_image;
    public $artist_gender = 'male';
    public $existingArtistImage = '';

    public $musician_real_name, $musician_stage_name, $musician_instrument, $musician_license_number, $musician_phone, $musician_image, $musician_nationality;
    public $musician_instrument_custom = '';
    public $existingMusicianImage = '';

    // --- Event Fields ---
    public $event_title, $event_description, $event_date, $event_time, $event_is_active = true, $event_image;
    public $existingEventImage = '';

    // --- Booking Fields ---
    public $booking_event_title, $booking_event_description, $booking_event_date, $booking_event_time, $booking_event_image;
    public $booking_artist_id_1;
    public $booking_artist_id_2;
    public $booking_musician_id_1;
    public $booking_musician_id_2;
    public $booking_price, $booking_notes;
    public $booking_status = 'approved';
    public $booking_template_id = '';

    // --- Template Fields ---
    public $template_title, $template_description, $template_time, $template_image;
    public $existingTemplateImage = '';

    // --- Add to Event Fields ---
    public $addToEventId = null;
    public $addToEvent_artist_id;
    public $addToEvent_musician_id;
    public $addToEvent_price;
    public $addToEvent_notes;
    
    // Conflict handling
    public $conflictWarning = false;
    public $conflictArtistName = '';

    // --- Accounts Fields ---
    public $accounts_date_from = '';
    public $accounts_date_to = '';
    public $accounts_selected_artist_id = '';
    public $accounts_selected_event_id = '';
    public $accounts_filter_status = '';

    // Income editing
    public $editingIncomeEventId = null;
    public $editingIncomeValue = 0;

    // Ending Event Modal State
    public $endingEventId = null;
    public $endingEventIncome = 0;
    public $endingEventOtherExpenses = 0;
    public $endingEventArtistPrices = [];
    public $endingEventMusicianPrices = [];

    // --- Booking Filters ---
    public $filter_booking_artist_id = '';
    public $filter_booking_musician_id = '';
    public $filter_booking_date_from = '';
    public $filter_booking_date_to = '';
    public $filter_booking_status = '';

    // --- Artist Filters ---
    public $filter_artist_nationality = '';
    public $filter_artist_gender = '';

    // ========================================
    // Navigation
    // ========================================
    public function goToView($view) {
        $this->currentView = $view;
    }

    // ========================================
    // 1. Artist CRUD
    // ========================================
    public function openArtistModal() {
        $this->resetArtistFields();
        $this->isArtistModalOpen = true;
    }

    private function resetArtistFields() {
        $this->editingArtistId = null;
        $this->artist_real_name = '';
        $this->artist_stage_name = '';
        $this->artist_nationality = '';
        $this->artist_license_number = '';
        $this->artist_phone = '';
        $this->artist_gender = 'male';
        $this->artist_image = null;
        $this->existingArtistImage = '';
    }

    public function editArtist($id) {
        $artist = Artist::find($id);
        if (!$artist) return;
        $this->editingArtistId = $artist->id;
        $this->artist_real_name = $artist->real_name;
        $this->artist_stage_name = $artist->stage_name ?? '';
        $this->artist_nationality = $artist->nationality ?? '';
        $this->artist_license_number = $artist->license_number ?? '';
        $this->artist_phone = $artist->phone ?? '';
        $this->artist_gender = $artist->gender ?? 'male';
        $this->existingArtistImage = $artist->image ?? '';
        $this->artist_image = null;
        $this->isArtistModalOpen = true;
    }

    public function saveArtist() {
        $this->validate(['artist_real_name' => 'required|string|max:255']);
        $data = [
            'real_name' => $this->artist_real_name,
            'stage_name' => $this->artist_stage_name,
            'nationality' => $this->artist_nationality,
            'license_number' => $this->artist_license_number,
            'phone' => $this->artist_phone,
            'gender' => $this->artist_gender,
        ];
        if ($this->artist_image) {
            $data['image'] = $this->artist_image->store('artists', 'public');
        }
        if ($this->editingArtistId) {
            Artist::find($this->editingArtistId)->update($data);
            $this->dispatch('notify', ['message' => 'تم تحديث بيانات الفنان بنجاح!', 'type' => 'success']);
        } else {
            Artist::create($data);
            $this->dispatch('notify', ['message' => 'تم إضافة الفنان بنجاح!', 'type' => 'success']);
        }
        $this->isArtistModalOpen = false;
    }

    public function deleteArtist($id) {
        Artist::destroy($id);
        $this->dispatch('notify', ['message' => 'تم حذف الفنان بنجاح!', 'type' => 'success']);
    }

    // ========================================
    // 2. Musician CRUD
    // ========================================
    public function openMusicianModal() {
        $this->resetMusicianFields();
        $this->isMusicianModalOpen = true;
    }

    private function resetMusicianFields() {
        $this->editingMusicianId = null;
        $this->musician_real_name = '';
        $this->musician_stage_name = '';
        $this->musician_instrument = '';
        $this->musician_instrument_custom = '';
        $this->musician_license_number = '';
        $this->musician_phone = '';
        $this->musician_image = null;
        $this->musician_nationality = '';
        $this->existingMusicianImage = '';
    }

    public function editMusician($id) {
        $musician = Musician::find($id);
        if (!$musician) return;
        $this->editingMusicianId = $musician->id;
        $this->musician_real_name = $musician->real_name;
        $this->musician_stage_name = $musician->stage_name ?? '';
        
        $knownInstruments = ['عود', 'طبل', 'أورج', 'كمان', 'قيثارة', 'ناي', 'دف'];
        if ($musician->instrument && !in_array($musician->instrument, $knownInstruments)) {
            $this->musician_instrument = 'أخرى';
            $this->musician_instrument_custom = $musician->instrument;
        } else {
            $this->musician_instrument = $musician->instrument ?? '';
            $this->musician_instrument_custom = '';
        }
        
        $this->musician_license_number = $musician->license_number ?? '';
        $this->musician_phone = $musician->phone ?? '';
        $this->musician_nationality = $musician->nationality ?? '';
        $this->existingMusicianImage = $musician->image ?? '';
        $this->musician_image = null;
        $this->isMusicianModalOpen = true;
    }

    public function saveMusician() {
        $this->validate(['musician_real_name' => 'required|string|max:255']);
        
        $instrument = $this->musician_instrument;
        if ($instrument === 'أخرى' && !empty($this->musician_instrument_custom)) {
            $instrument = $this->musician_instrument_custom;
        }
        
        $data = [
            'real_name' => $this->musician_real_name,
            'stage_name' => $this->musician_stage_name,
            'instrument' => $instrument,
            'nationality' => $this->musician_nationality,
            'license_number' => $this->musician_license_number,
            'phone' => $this->musician_phone,
        ];
        if ($this->musician_image) {
            $data['image'] = $this->musician_image->store('musicians', 'public');
        }
        if ($this->editingMusicianId) {
            Musician::find($this->editingMusicianId)->update($data);
            $this->dispatch('notify', ['message' => 'تم تحديث بيانات العازف بنجاح!', 'type' => 'success']);
        } else {
            Musician::create($data);
            $this->dispatch('notify', ['message' => 'تم إضافة العازف بنجاح!', 'type' => 'success']);
        }
        $this->isMusicianModalOpen = false;
    }

    public function deleteMusician($id) {
        Musician::destroy($id);
        $this->dispatch('notify', ['message' => 'تم حذف العازف بنجاح!', 'type' => 'success']);
    }

    // ========================================
    // 3. Event
    // ========================================
    public function openEventModal() {
        $this->reset(['editingEventId', 'event_title', 'event_description', 'event_date', 'event_time', 'event_is_active', 'event_image', 'existingEventImage']);
        $this->event_is_active = true;
        $this->isEventModalOpen = true;
    }

    public function editEvent($id) {
        $event = Event::find($id);
        if (!$event) return;
        $this->editingEventId = $event->id;
        $this->event_title = $event->title;
        $this->event_description = $event->description;
        $this->event_date = $event->event_date;
        $this->event_time = $event->event_time;
        $this->event_is_active = $event->is_active;
        $this->existingEventImage = $event->artist_image ?? '';
        $this->event_image = null;
        $this->isEventModalOpen = true;
    }

    public function deleteEvent($id) {
        Event::destroy($id);
        $this->dispatch('notify', ['message' => 'تم حذف الحفلة بنجاح!', 'type' => 'success']);
    }

    public function saveEvent() {
        $this->validate(['event_title' => 'required|string|max:255', 'event_date' => 'nullable|date']);
        $data = [
            'title' => $this->event_title,
            'description' => $this->event_description ?: null,
            'event_date' => $this->event_date ?: null,
            'event_time' => $this->event_time ?: null,
            'is_active' => $this->event_is_active,
        ];
        if ($this->event_image) {
            $data['artist_image'] = $this->event_image->store('site/events', 'public');
        }
        
        if ($this->editingEventId) {
            Event::find($this->editingEventId)->update($data);
            $this->dispatch('notify', ['message' => 'تم تحديث الحفلة بنجاح!', 'type' => 'success']);
        } else {
            $event = Event::create($data);
            
            $customers = \App\Models\Customer::all();
            $notifications = [];
            $fcmTokens = [];

            foreach($customers as $customer) {
                $title = 'حفلة جديدة! 🎉';
                $body = 'تم الإعلان عن حفلة: ' . $event->title;
                
                $notifications[] = [
                    'customer_id' => $customer->id,
                    'title' => $title,
                    'message' => $body,
                    'type' => 'event',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (!empty($customer->fcm_token)) {
                    $fcmTokens[] = $customer->fcm_token;
                }
            }

            if(count($notifications) > 0) {
                \App\Models\CustomerNotification::insert($notifications);
            }

            if(count($fcmTokens) > 0) {
                \App\Services\FirebasePushService::send(
                    $fcmTokens,
                    'حفلة جديدة! 🎉',
                    'تم الإعلان عن حفلة: ' . $event->title,
                    ['type' => 'event', 'event_id' => (string)$event->id]
                );
            }

            $this->dispatch('notify', ['message' => 'تم إضافة الحفلة وإشعار العملاء بنجاح!', 'type' => 'success']);
        }
        $this->isEventModalOpen = false;
    }

    public function viewEventDetails($id) {
        $this->viewingEvent = Event::find($id);
        if (!$this->viewingEvent) return;
        
        $this->viewingEventBookings = ArtistBooking::with(['artist', 'musician'])->where('event_id', $id)->get();
        $this->isEventDetailsModalOpen = true;
    }

    // ========================================
    // 4. Booking
    // ========================================
    public function openBookingModal() {
        $this->reset([
            'booking_event_title', 'booking_event_description', 'booking_event_date', 'booking_event_time', 'booking_event_image',
            'booking_artist_id_1', 'booking_artist_id_2', 'booking_musician_id_1', 'booking_musician_id_2', 'booking_price', 'booking_notes',
            'conflictWarning', 'conflictArtistName', 'booking_template_id', 'booking_status'
        ]);
        $this->booking_status = 'approved';
        $this->isBookingModalOpen = true;
    }

    public function updatedBookingTemplateId($value) {
        if ($value) {
            $template = EventTemplate::find($value);
            if ($template) {
                $this->booking_event_title = $template->title;
                $this->booking_event_description = $template->description;
                $this->booking_event_time = $template->event_time;
            }
        }
    }

    public function proceedWithBooking() {
        $this->conflictWarning = false;
        $this->executeBookingSave();
    }

    public function cancelBookingConflict() {
        $this->conflictWarning = false;
        // Optionally reset the selections, but we can leave them for the user to change.
    }

    public function saveBooking() {
        $this->validate([
            'booking_event_title' => 'nullable|string|max:255',
            'booking_event_date' => 'required|date',
        ], [
            'booking_event_date.required' => 'تاريخ الحفلة مطلوب',
        ]);

        if (!$this->conflictWarning) {
            $artistIdsToCheck = array_filter([$this->booking_artist_id_1, $this->booking_artist_id_2]);
            $conflictingArtist = null;
            
            foreach ($artistIdsToCheck as $artistId) {
                $exists = ArtistBooking::where('artist_id', $artistId)
                    ->whereDate('booking_date', $this->booking_event_date)
                    ->exists();

                if ($exists) {
                    $artist = Artist::find($artistId);
                    $conflictingArtist = $artist ? $artist->real_name : 'فنان محدد';
                    break;
                }
            }

            if ($conflictingArtist) {
                $this->conflictArtistName = $conflictingArtist;
                $this->conflictWarning = true;
                return; // Wait for user decision
            }
        }

        $this->executeBookingSave();
    }

    private function executeBookingSave() {
        // 1. Create Event
        $eventData = [
            'title' => $this->booking_event_title ?: 'حفلة بدون عنوان',
            'description' => $this->booking_event_description ?: null,
            'event_date' => $this->booking_event_date ?: null,
            'event_time' => $this->booking_event_time ?: null,
            'is_active' => true,
        ];

        if ($this->booking_event_image) {
            $eventData['artist_image'] = $this->booking_event_image->store('site/events', 'public');
        }

        $event = Event::create($eventData);

        // 2. Create Booking(s)
        $artists = array_values(array_filter(array_unique([$this->booking_artist_id_1, $this->booking_artist_id_2])));
        $musicians = array_values(array_filter(array_unique([$this->booking_musician_id_1, $this->booking_musician_id_2])));
        
        $count = max(count($artists), count($musicians));
        
        if ($count == 0) {
            ArtistBooking::create([
                'artist_id' => null,
                'musician_id' => null,
                'event_id' => $event->id,
                'booking_date' => $this->booking_event_date,
                'price' => $this->booking_price ?? 0,
                'notes' => $this->booking_notes,
                'status' => $this->booking_status,
            ]);
        } else {
            for ($i = 0; $i < $count; $i++) {
                ArtistBooking::create([
                    'artist_id' => $artists[$i] ?? null,
                    'musician_id' => $musicians[$i] ?? null,
                    'event_id' => $event->id,
                    'booking_date' => $this->booking_event_date,
                    'price' => $i == 0 ? ($this->booking_price ?? 0) : 0,
                    'notes' => $this->booking_notes,
                    'status' => $this->booking_status,
                ]);
            }
        }

        // 3. Send Notifications
        $customers = \App\Models\Customer::all();
        $notifications = [];
        $fcmTokens = [];

        foreach($customers as $customer) {
            $title = 'حفلة جديدة! 🎉';
            $body = 'تم الإعلان عن حفلة: ' . $event->title;
            
            $notifications[] = [
                'customer_id' => $customer->id,
                'title' => $title,
                'message' => $body,
                'type' => 'event',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (!empty($customer->fcm_token)) {
                $fcmTokens[] = $customer->fcm_token;
            }
        }

        if(count($notifications) > 0) {
            \App\Models\CustomerNotification::insert($notifications);
        }

        if(count($fcmTokens) > 0) {
            \App\Services\FirebasePushService::send(
                $fcmTokens,
                'حفلة جديدة! 🎉',
                'تم الإعلان عن حفلة: ' . $event->title,
                ['type' => 'event', 'event_id' => (string)$event->id]
            );
        }

        $this->isBookingModalOpen = false;
        $this->dispatch('notify', ['message' => 'تم الحجز وإنشاء الحفلة بنجاح!', 'type' => 'success']);
        $this->booking_template_id = '';
    }

    // ========================================
    // 4b. Add Artist/Musician to Existing Event
    // ========================================
    public function openAddToEventModal($eventId) {
        $this->addToEventId = $eventId;
        $this->addToEvent_artist_id = null;
        $this->addToEvent_musician_id = null;
        $this->addToEvent_price = null;
        $this->addToEvent_notes = null;
        $this->isAddToEventModalOpen = true;
    }

    public function saveAddToEvent() {
        $this->validate([
            'addToEvent_artist_id' => 'required',
        ], [
            'addToEvent_artist_id.required' => 'الرجاء اختيار الفنان',
        ]);

        $event = Event::find($this->addToEventId);
        if (!$event) return;

        ArtistBooking::create([
            'artist_id' => $this->addToEvent_artist_id,
            'musician_id' => $this->addToEvent_musician_id ?: null,
            'event_id' => $event->id,
            'booking_date' => $event->event_date ?? now()->toDateString(),
            'price' => $this->addToEvent_price ?? 0,
            'notes' => $this->addToEvent_notes,
        ]);

        $this->isAddToEventModalOpen = false;
        $this->dispatch('notify', ['message' => 'تم إضافة الفنان/العازف للحفلة بنجاح!', 'type' => 'success']);
    }

    public function deleteBooking($bookingId) {
        $booking = ArtistBooking::find($bookingId);
        if (!$booking) return;
        
        $booking->delete();
        
        // Refresh viewing event bookings if modal is open
        if ($this->viewingEvent) {
            $this->viewingEventBookings = ArtistBooking::with(['artist', 'musician'])->where('event_id', $this->viewingEvent->id)->get();
        }
        
        $this->dispatch('notify', ['message' => 'تم حذف الحجز بنجاح!', 'type' => 'success']);
    }

    public function approveBooking($bookingId) {
        $booking = ArtistBooking::find($bookingId);
        if (!$booking) return;
        $booking->status = 'approved';
        $booking->save();
        $this->dispatch('notify', ['message' => 'تم اعتماد الحجز بنجاح!', 'type' => 'success']);
    }

    public function hideEvent($id) {
        $event = Event::find($id);
        if (!$event) return;
        $event->is_hidden = true;
        $event->save();
        $this->dispatch('notify', ['message' => 'تم إخفاء الحفلة من القائمة بنجاح!', 'type' => 'success']);
    }

    // ========================================
    // Post Share Modal
    // ========================================
    public function openPostShareModal($eventId) {
        $event = Event::find($eventId);
        if (!$event) return;
        $this->postShareEvent = $event;
        $this->postShareEventId = $eventId;
        $this->isPostShareModalOpen = true;
    }

    // ========================================
    // Upload Post Image Modal
    // ========================================
    public function openUploadPostImageModal($eventId) {
        $this->uploadEventId = $eventId;
        $this->postUploadImage = null;
        $this->isUploadPostImageModalOpen = true;
    }

    public function savePostImage() {
        $this->validate([
            'postUploadImage' => 'required|image|max:5120',
        ], [
            'postUploadImage.required' => 'الرجاء اختيار صورة',
            'postUploadImage.image' => 'الملف يجب أن يكون صورة',
            'postUploadImage.max' => 'حجم الصورة يجب ألا يتجاوز 5MB',
        ]);

        $event = Event::find($this->uploadEventId);
        if (!$event) return;

        $path = $this->postUploadImage->store('site/events', 'public');
        $event->artist_image = $path;
        $event->save();

        $this->isUploadPostImageModalOpen = false;
        $this->postUploadImage = null;
        $this->dispatch('notify', ['message' => 'تم رفع صورة البوست بنجاح!', 'type' => 'success']);
    }

    public function updateBookingArtist($bookingId, $newArtistId) {
        $booking = ArtistBooking::find($bookingId);
        if (!$booking || !$newArtistId) return;
        $booking->artist_id = $newArtistId;
        $booking->save();
        $this->dispatch('notify', ['message' => 'تم تغيير الفنان بنجاح!', 'type' => 'success']);
    }

    public function updateBookingMusician($bookingId, $newMusicianId) {
        $booking = ArtistBooking::find($bookingId);
        if (!$booking) return;
        $booking->musician_id = $newMusicianId ?: null;
        $booking->save();
        $this->dispatch('notify', ['message' => 'تم تغيير العازف بنجاح!', 'type' => 'success']);
    }

    // ========================================
    // 4c. Event Templates CRUD
    // ========================================
    public function openTemplateModal() {
        $this->resetTemplateFields();
        $this->isTemplateModalOpen = true;
    }

    private function resetTemplateFields() {
        $this->editingTemplateId = null;
        $this->template_title = '';
        $this->template_description = '';
        $this->template_time = '';
        $this->template_image = null;
        $this->existingTemplateImage = '';
    }

    public function editTemplate($id) {
        $template = EventTemplate::find($id);
        if (!$template) return;
        $this->editingTemplateId = $template->id;
        $this->template_title = $template->title;
        $this->template_description = $template->description;
        $this->template_time = $template->event_time;
        $this->existingTemplateImage = $template->artist_image ?? '';
        $this->template_image = null;
        $this->isTemplateModalOpen = true;
    }

    public function saveTemplate() {
        $this->validate(['template_title' => 'required|string|max:255']);
        $data = [
            'title' => $this->template_title,
            'description' => $this->template_description ?: null,
            'event_time' => $this->template_time ?: null,
        ];
        if ($this->template_image) {
            $data['artist_image'] = $this->template_image->store('site/templates', 'public');
        }
        if ($this->editingTemplateId) {
            EventTemplate::find($this->editingTemplateId)->update($data);
            $this->dispatch('notify', ['message' => 'تم تحديث القالب بنجاح!', 'type' => 'success']);
        } else {
            EventTemplate::create($data);
            $this->dispatch('notify', ['message' => 'تم إضافة القالب بنجاح!', 'type' => 'success']);
        }
        $this->isTemplateModalOpen = false;
    }

    public function deleteTemplate($id) {
        EventTemplate::destroy($id);
        $this->dispatch('notify', ['message' => 'تم حذف القالب بنجاح!', 'type' => 'success']);
    }

    // ========================================
    // 5. Accounts & Reports
    // ========================================
    public function updateEventIncome($eventId) {
        $event = Event::find($eventId);
        if($event) {
            $event->total_income = $this->editingIncomeValue;
            $event->save();
            $this->editingIncomeEventId = null;
            $this->dispatch('notify', ['message' => 'تم تحديث دخل الحفلة بنجاح!', 'type' => 'success']);
        }
    }

    public function openIncomeEdit($eventId, $currentIncome) {
        $this->editingIncomeEventId = $eventId;
        $this->editingIncomeValue = $currentIncome;
    }

    public function openEndEventModal($eventId) {
        $event = Event::find($eventId);
        if(!$event) return;
        $this->endingEventId = $eventId;
        $this->endingEventIncome = $event->total_income ?? 0;
        $this->endingEventOtherExpenses = $event->other_expenses ?? 0;
        
        $bookings = ArtistBooking::where('event_id', $eventId)->get();
        $this->endingEventArtistPrices = [];
        $this->endingEventMusicianPrices = [];
        foreach($bookings as $b) {
            if ($b->artist_id) {
                $this->endingEventArtistPrices[$b->id] = $b->price ?? 0;
            }
            if ($b->musician_id) {
                $this->endingEventMusicianPrices[$b->id] = $b->musician_price ?? 0;
            }
        }
        
        $this->isEndEventModalOpen = true;
    }

    public function confirmEndEvent() {
        $event = Event::find($this->endingEventId);
        if($event) {
            $event->total_income = $this->endingEventIncome;
            $event->other_expenses = $this->endingEventOtherExpenses;
            $event->is_ended = true;
            $event->save();
            
            $bookings = ArtistBooking::where('event_id', $this->endingEventId)->get();
            foreach($bookings as $b) {
                if ($b->artist_id && isset($this->endingEventArtistPrices[$b->id])) {
                    $b->price = $this->endingEventArtistPrices[$b->id];
                }
                if ($b->musician_id && isset($this->endingEventMusicianPrices[$b->id])) {
                    $b->musician_price = $this->endingEventMusicianPrices[$b->id];
                }
                $b->save();
            }
            $this->dispatch('notify', ['message' => 'تم إنهاء الحفلة بنجاح وحفظ التفاصيل!', 'type' => 'success']);
        }
        $this->isEndEventModalOpen = false;
    }
    public function resetFilters() {
        $this->filter_booking_artist_id = '';
        $this->filter_booking_musician_id = '';
        $this->filter_booking_date_from = '';
        $this->filter_booking_date_to = '';
        $this->filter_booking_status = '';
    }

    public function filterToday() {
        $today = now()->toDateString();
        $this->filter_booking_date_from = $today;
        $this->filter_booking_date_to = $today;
        $this->filter_booking_status = '';
        $this->filter_booking_artist_id = '';
        $this->filter_booking_musician_id = '';
    }

    public function filterThisMonth() {
        $this->filter_booking_date_from = now()->startOfMonth()->toDateString();
        $this->filter_booking_date_to = now()->endOfMonth()->toDateString();
        $this->filter_booking_status = '';
        $this->filter_booking_artist_id = '';
        $this->filter_booking_musician_id = '';
    }

    public function filterNextMonth() {
        $this->filter_booking_date_from = now()->addMonth()->startOfMonth()->toDateString();
        $this->filter_booking_date_to = now()->addMonth()->endOfMonth()->toDateString();
        $this->filter_booking_status = '';
        $this->filter_booking_artist_id = '';
        $this->filter_booking_musician_id = '';
    }

    public function resetAccountsFilters() {
        $this->accounts_date_from = '';
        $this->accounts_date_to = '';
        $this->accounts_selected_artist_id = '';
        $this->accounts_selected_event_id = '';
        $this->accounts_filter_status = '';
    }

    public function accountsFilterToday() {
        $today = now()->toDateString();
        $this->accounts_date_from = $today;
        $this->accounts_date_to = $today;
        $this->accounts_filter_status = '';
        $this->accounts_selected_artist_id = '';
        $this->accounts_selected_event_id = '';
    }

    public function accountsFilterThisMonth() {
        $this->accounts_date_from = now()->startOfMonth()->toDateString();
        $this->accounts_date_to = now()->endOfMonth()->toDateString();
        $this->accounts_filter_status = '';
        $this->accounts_selected_artist_id = '';
        $this->accounts_selected_event_id = '';
    }

    public function accountsFilterNextMonth() {
        $this->accounts_date_from = now()->addMonth()->startOfMonth()->toDateString();
        $this->accounts_date_to = now()->addMonth()->endOfMonth()->toDateString();
        $this->accounts_filter_status = '';
        $this->accounts_selected_artist_id = '';
        $this->accounts_selected_event_id = '';
    }

    public function setAccountsStatus($status) {
        $this->accounts_filter_status = ($this->accounts_filter_status === $status) ? '' : $status;
    }

    public function render()
    {
        $eventsQuery = Event::with(['artistBookings.artist', 'artistBookings.musician']);

        // Filter by Date
        if ($this->filter_booking_date_from) {
            $eventsQuery->whereDate('event_date', '>=', $this->filter_booking_date_from);
        }
        if ($this->filter_booking_date_to) {
            $eventsQuery->whereDate('event_date', '<=', $this->filter_booking_date_to);
        }

        // Filter by Status
        if ($this->filter_booking_status === 'completed') {
            $eventsQuery->where('is_ended', true);
        } elseif ($this->filter_booking_status === 'approved') {
            $eventsQuery->where(function($q) {
                $q->where('is_ended', false)
                  ->orWhereNull('is_ended');
            })->whereDoesntHave('artistBookings', function($q) {
                $q->where('status', 'suggested');
            });
        } elseif ($this->filter_booking_status === 'suggested') {
            $eventsQuery->where(function($q) {
                $q->where('is_ended', false)
                  ->orWhereNull('is_ended');
            })->whereHas('artistBookings', function($q) {
                $q->where('status', 'suggested');
            });
        }

        // Filter by Artist
        if ($this->filter_booking_artist_id) {
            $eventsQuery->whereHas('artistBookings', function($q) {
                $q->where('artist_id', $this->filter_booking_artist_id);
            });
        }

        // Filter by Musician
        if ($this->filter_booking_musician_id) {
            $eventsQuery->whereHas('artistBookings', function($q) {
                $q->where('musician_id', $this->filter_booking_musician_id);
            });
        }

        // Hide is_hidden events only when NO filters are active
        $hasActiveFilters = $this->filter_booking_date_from || $this->filter_booking_date_to || $this->filter_booking_status || $this->filter_booking_artist_id || $this->filter_booking_musician_id;
        if (!$hasActiveFilters) {
            $eventsQuery->where(function($q) {
                $q->where('is_hidden', false)->orWhereNull('is_hidden');
            });
        }

        $eventsWithBookings = $eventsQuery->orderBy('event_date', 'asc')->get();

        // Filter artists by nationality and gender if filters are set
        $artistsQuery = Artist::latest();
        if ($this->filter_artist_nationality) {
            $artistsQuery->where('nationality', $this->filter_artist_nationality);
        }
        if ($this->filter_artist_gender) {
            $artistsQuery->where('gender', $this->filter_artist_gender);
        }
        $filteredArtists = $artistsQuery->get();

        // Get unique nationalities for dropdown
        $artistNationalities = Artist::whereNotNull('nationality')->where('nationality', '!=', '')->distinct()->pluck('nationality');

        $renderData = [
            'artists' => Artist::latest()->get(),
            'filteredArtists' => $filteredArtists,
            'artistNationalities' => $artistNationalities,
            'musicians' => Musician::latest()->get(),
            'events' => Event::orderByRaw("DATE(event_date) = ? DESC", [now()->toDateString()])->orderBy('event_date', 'desc')->get(),
            'bookings' => ArtistBooking::with(['artist', 'musician', 'event'])->orderBy('booking_date', 'desc')->get(),
            'eventTemplates' => EventTemplate::latest()->get(),
            'eventsWithBookings' => $eventsWithBookings,
        ];

        // Accounts data
        if ($this->currentView === 'accounts') {
            $eventsQuery = Event::orderBy('event_date', 'desc');

            if ($this->accounts_date_from) {
                $eventsQuery->whereDate('event_date', '>=', $this->accounts_date_from);
            }
            if ($this->accounts_date_to) {
                $eventsQuery->whereDate('event_date', '<=', $this->accounts_date_to);
            }
            if ($this->accounts_selected_event_id) {
                $eventsQuery->where('id', $this->accounts_selected_event_id);
            }

            if ($this->accounts_selected_artist_id) {
                $eventIds = ArtistBooking::where('artist_id', $this->accounts_selected_artist_id)->pluck('event_id')->unique();
                $eventsQuery->whereIn('id', $eventIds);
            }

            // Filter by Status for Accounts
            if ($this->accounts_filter_status === 'completed') {
                $eventsQuery->where('is_ended', true);
            } elseif ($this->accounts_filter_status === 'approved') {
                $eventsQuery->where(function($q) {
                    $q->where('is_ended', false)
                      ->orWhereNull('is_ended');
                })->whereDoesntHave('artistBookings', function($q) {
                    $q->where('status', 'suggested');
                });
            }

            $accountEvents = $eventsQuery->get();

            // Calculate stats for each event
            $accountEventsData = [];
            $totalIncomeAll = 0;
            $totalExpenseAll = 0;
            $totalProfitAll = 0;
            $totalArtistsAll = 0;
            $totalMusiciansAll = 0;

            foreach ($accountEvents as $ev) {
                $artistBookings = ArtistBooking::with(['artist', 'musician'])->where('event_id', $ev->id)->get();
                $customerBookings = \App\Models\EventBooking::where('event_id', $ev->id)->get();
                $expense = $artistBookings->sum('price') + $artistBookings->sum('musician_price') + ($ev->other_expenses ?? 0);
                $income = $ev->total_income ?? 0;
                $profit = $income - $expense;
                $customerBookingsCount = $customerBookings->count();

                $artistsCount = $artistBookings->whereNotNull('artist_id')->count();
                $musiciansCount = $artistBookings->whereNotNull('musician_id')->count();

                $accountEventsData[] = [
                    'event' => $ev,
                    'artist_bookings' => $artistBookings,
                    'customer_bookings_count' => $customerBookingsCount,
                    'expense' => $expense,
                    'income'  => $income,
                    'profit'  => $profit,
                ];

                $totalExpenseAll += $expense;
                $totalIncomeAll += $income;
                $totalProfitAll += $profit;
                $totalArtistsAll += $artistsCount;
                $totalMusiciansAll += $musiciansCount;
            }

            // Artist profitability summary
            $artistsQuery = ArtistBooking::with('artist');
            
            if ($this->accounts_date_from) {
                $artistsQuery->whereDate('booking_date', '>=', $this->accounts_date_from);
            }
            if ($this->accounts_date_to) {
                $artistsQuery->whereDate('booking_date', '<=', $this->accounts_date_to);
            }
            if ($this->accounts_selected_artist_id) {
                $artistsQuery->where('artist_id', $this->accounts_selected_artist_id);
            }
            if ($this->accounts_selected_event_id) {
                $artistsQuery->where('event_id', $this->accounts_selected_event_id);
            }
            if ($this->accounts_filter_status) {
                $artistsQuery->whereIn('event_id', $accountEvents->pluck('id'));
            }

            $allArtistBookings = $artistsQuery->get();
            $artistSummary = $allArtistBookings->groupBy('artist_id')->map(function($group) {
                $artist = $group->first()->artist;
                $totalCost = $group->sum('price') + $group->sum('musician_price');
                
                // Calculate income brought by this artist (sum of total_income of events they participated in)
                $eventIds = $group->pluck('event_id')->unique();
                $events = Event::whereIn('id', $eventIds)->get();
                $totalIncome = $events->sum('total_income');
                $otherExpenses = $events->sum('other_expenses');
                $totalProfit = $totalIncome - ($totalCost + $otherExpenses);

                return [
                    'artist' => $artist,
                    'total_events' => $eventIds->count(),
                    'total_cost' => $totalCost,
                    'total_income' => $totalIncome,
                    'total_profit' => $totalProfit,
                ];
            })->values();

            $renderData['accountEventsData'] = $accountEventsData;
            $renderData['totalExpense'] = $totalExpenseAll;
            $renderData['totalIncome'] = $totalIncomeAll;
            $renderData['totalProfit'] = $totalIncomeAll - $totalExpenseAll;
            $renderData['totalArtists'] = $totalArtistsAll;
            $renderData['totalMusicians'] = $totalMusiciansAll;
            $renderData['artistSummary'] = $artistSummary;
        }

        return view('livewire.admin.artists-manager', $renderData);
    }
}
