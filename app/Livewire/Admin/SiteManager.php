<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SiteLink;
use App\Models\Setting;
use App\Models\Event;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class SiteManager extends Component
{
    use WithFileUploads;

    // Site Settings
    public $site_name = '';
    public $site_bio = '';
    public $site_logo = '';
    public $site_cover_image = '';
    public $site_location_name = '';
    public $site_location_address = '';
    public $site_location_lat = '';
    public $site_location_lng = '';
    public $site_location_url = '';
    public $site_show_events = true;
    public $site_show_packages = true;
    
    // Custom Texts
    public $site_announcements_title = '';
    public $site_location_title = '';
    public $site_footer_text = '';

    // Self Order Settings
    public $self_order_enabled = true;
    public $self_order_require_geo = false;
    public $self_order_geo_lat = '';
    public $self_order_geo_lng = '';
    public $self_order_geo_radius = 200;
    public $self_order_welcome_msg = '';

    // New/Edit Link
    public $isModalOpen = false;
    public $editingId = null;
    public $title = '';
    public $subtitle = '';
    public $url = '';
    public $icon = '';
    public $icon_bg_color = '#333333';
    public $type = 'link';
    public $description = '';
    public $sort_order = 0;
    public $is_active = true;
    public $linkImage = null;
    public $existingImage = '';

    // Upload fields
    public $newLogo = null;
    public $newCoverImage = null;

    // Events Management
    public $isEventModalOpen = false;
    public $editingEventId = null;
    public $event_title = '';
    public $event_description = '';
    public $event_date = '';
    public $event_time = '';
    public $event_is_active = true;
    public $newEventImage = null;
    public $existingEventImage = '';

    public function selectIcon($class, $color)
    {
        $this->icon = $class;
        $this->icon_bg_color = $color;
    }

    public function mount()
    {
        $this->site_name = Setting::where('key', 'site_name')->value('value') ?? '';
        $this->site_bio = Setting::where('key', 'site_bio')->value('value') ?? '';
        $this->site_logo = Setting::where('key', 'site_logo')->value('value') ?? '';
        $this->site_cover_image = Setting::where('key', 'site_cover_image')->value('value') ?? '';
        $this->site_location_name = Setting::where('key', 'site_location_name')->value('value') ?? '';
        $this->site_location_address = Setting::where('key', 'site_location_address')->value('value') ?? '';
        $this->site_location_lat = Setting::where('key', 'site_location_lat')->value('value') ?? '';
        $this->site_location_lng = Setting::where('key', 'site_location_lng')->value('value') ?? '';
        $this->site_location_url = Setting::where('key', 'site_location_url')->value('value') ?? '';
        
        $this->site_show_events = filter_var(Setting::where('key', 'site_show_events')->value('value') ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $this->site_show_packages = filter_var(Setting::where('key', 'site_show_packages')->value('value') ?? 'true', FILTER_VALIDATE_BOOLEAN);
        
        $this->site_announcements_title = Setting::where('key', 'site_announcements_title')->value('value') ?? 'إعلانات وحفلات';
        $this->site_location_title = Setting::where('key', 'site_location_title')->value('value') ?? 'موقعنا';
        $this->site_footer_text = Setting::where('key', 'site_footer_text')->value('value') ?? 'تم تطوير النظام بواسطة م. مازن أحمد حيدر';

        $this->self_order_enabled = filter_var(Setting::where('key', 'self_order_enabled')->value('value') ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $this->self_order_require_geo = filter_var(Setting::where('key', 'self_order_require_geo')->value('value') ?? 'false', FILTER_VALIDATE_BOOLEAN);
        $this->self_order_geo_lat = Setting::where('key', 'self_order_geo_lat')->value('value') ?? '';
        $this->self_order_geo_lng = Setting::where('key', 'self_order_geo_lng')->value('value') ?? '';
        $this->self_order_geo_radius = (int) (Setting::where('key', 'self_order_geo_radius')->value('value') ?? 200);
        $this->self_order_welcome_msg = Setting::where('key', 'self_order_welcome_msg')->value('value') ?? 'مرحباً بك في نظام الطلب الذاتي';
    }

    public function saveSiteSettings()
    {
        $settings = [
            'site_name' => $this->site_name,
            'site_bio' => $this->site_bio,
            'site_location_name' => $this->site_location_name,
            'site_location_address' => $this->site_location_address,
            'site_location_lat' => $this->site_location_lat,
            'site_location_lng' => $this->site_location_lng,
            'site_location_url' => $this->site_location_url,
            'site_show_events' => $this->site_show_events ? 'true' : 'false',
            'site_show_packages' => $this->site_show_packages ? 'true' : 'false',
            'site_announcements_title' => $this->site_announcements_title,
            'site_location_title' => $this->site_location_title,
            'site_footer_text' => $this->site_footer_text,
            'self_order_enabled' => $this->self_order_enabled ? 'true' : 'false',
            'self_order_require_geo' => $this->self_order_require_geo ? 'true' : 'false',
            'self_order_geo_lat' => $this->self_order_geo_lat,
            'self_order_geo_lng' => $this->self_order_geo_lng,
            'self_order_geo_radius' => $this->self_order_geo_radius,
            'self_order_welcome_msg' => $this->self_order_welcome_msg,
        ];

        if ($this->newLogo) {
            $path = $this->newLogo->store('site', 'public');
            $settings['site_logo'] = $path;
            $this->site_logo = $path;
            $this->newLogo = null;
        }

        if ($this->newCoverImage) {
            $path = $this->newCoverImage->store('site', 'public');
            $settings['site_cover_image'] = $path;
            $this->site_cover_image = $path;
            $this->newCoverImage = null;
        }

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->dispatch('notify', ['message' => 'تم حفظ إعدادات الموقع بنجاح!', 'type' => 'success']);
    }

    public function openModal()
    {
        $this->resetLink();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetLink();
    }

    private function resetLink()
    {
        $this->editingId = null;
        $this->title = '';
        $this->subtitle = '';
        $this->url = '';
        $this->icon = 'fa-solid fa-link';
        $this->icon_bg_color = '#333333';
        $this->type = 'link';
        $this->description = '';
        $this->sort_order = 0;
        $this->is_active = true;
        $this->linkImage = null;
        $this->existingImage = '';
    }

    public function editLink($id)
    {
        $link = SiteLink::find($id);
        if (!$link) return;

        $this->editingId = $link->id;
        $this->title = $link->title;
        $this->subtitle = $link->subtitle ?? '';
        $this->url = $link->url;
        $this->icon = $link->icon ?? 'fa-solid fa-link';
        $this->icon_bg_color = $link->icon_bg_color;
        $this->type = $link->type;
        $this->description = $link->description ?? '';
        $this->sort_order = $link->sort_order;
        $this->is_active = $link->is_active;
        $this->existingImage = $link->image ?? '';
        $this->linkImage = null;
        $this->isModalOpen = true;
    }

    public function saveLink()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:2048',
            'type' => 'required|in:link,social,announcement,menu,whatsapp,location',
        ]);

        $data = [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'url' => $this->url,
            'icon' => $this->icon,
            'icon_bg_color' => $this->icon_bg_color,
            'type' => $this->type,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->linkImage) {
            $data['image'] = $this->linkImage->store('site/links', 'public');
        }

        if ($this->editingId) {
            SiteLink::find($this->editingId)->update($data);
            $this->dispatch('notify', ['message' => 'تم تحديث الرابط بنجاح!', 'type' => 'success']);
        } else {
            SiteLink::create($data);
            $this->dispatch('notify', ['message' => 'تم إضافة الرابط بنجاح!', 'type' => 'success']);
        }

        $this->closeModal();
    }

    public function deleteLink($id)
    {
        SiteLink::destroy($id);
        $this->dispatch('notify', ['message' => 'تم حذف الرابط بنجاح!', 'type' => 'success']);
    }

    public function toggleActive($id)
    {
        $link = SiteLink::find($id);
        if ($link) {
            $link->update(['is_active' => !$link->is_active]);
        }
    }

    public function updateSortOrder($id, $direction)
    {
        $link = SiteLink::find($id);
        if (!$link) return;

        if ($direction === 'up') {
            $link->update(['sort_order' => max(0, $link->sort_order - 1)]);
        } else {
            $link->update(['sort_order' => $link->sort_order + 1]);
        }
    }

    // ==========================================
    // Events Management Methods
    // ==========================================
    public function openEventModal()
    {
        $this->resetEvent();
        $this->isEventModalOpen = true;
    }

    public function closeEventModal()
    {
        $this->isEventModalOpen = false;
        $this->resetEvent();
    }

    private function resetEvent()
    {
        $this->editingEventId = null;
        $this->event_title = '';
        $this->event_description = '';
        $this->event_date = '';
        $this->event_time = '';
        $this->event_is_active = true;
        $this->newEventImage = null;
        $this->existingEventImage = '';
    }

    public function editEvent($id)
    {
        $event = Event::find($id);
        if (!$event) return;

        $this->editingEventId = $event->id;
        $this->event_title = $event->title;
        $this->event_description = $event->description ?? '';
        $this->event_date = $event->event_date ?? '';
        $this->event_time = $event->event_time ?? '';
        $this->event_is_active = $event->is_active;
        $this->existingEventImage = $event->artist_image ?? '';
        $this->newEventImage = null;
        $this->isEventModalOpen = true;
    }

    public function saveEvent()
    {
        $this->validate([
            'event_title' => 'required|string|max:255',
            'event_date' => 'nullable|date',
        ]);

        $data = [
            'title' => $this->event_title,
            'description' => $this->event_description ?: null,
            'event_date' => $this->event_date ?: null,
            'event_time' => $this->event_time ?: null,
            'is_active' => $this->event_is_active,
        ];

        if ($this->newEventImage) {
            $data['artist_image'] = $this->newEventImage->store('site/events', 'public');
        }

        if ($this->editingEventId) {
            Event::find($this->editingEventId)->update($data);
            $this->dispatch('notify', ['message' => 'تم تحديث الحفلة بنجاح!', 'type' => 'success']);
        } else {
            $event = Event::create($data);
            
            // Notify all customers
            $customers = \App\Models\Customer::all();
            $notifications = [];
            foreach($customers as $customer) {
                $notifications[] = [
                    'customer_id' => $customer->id,
                    'title' => 'حفلة جديدة! 🎉',
                    'message' => 'تم الإعلان عن حفلة: ' . $event->title,
                    'type' => 'event',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if(count($notifications) > 0) {
                \App\Models\CustomerNotification::insert($notifications);
            }

            $this->dispatch('notify', ['message' => 'تم إضافة الحفلة وإشعار العملاء بنجاح!', 'type' => 'success']);
        }

        $this->closeEventModal();
    }

    public function deleteEvent($id)
    {
        Event::destroy($id);
        $this->dispatch('notify', ['message' => 'تم حذف الحفلة بنجاح!', 'type' => 'success']);
    }

    public function toggleEventActive($id)
    {
        $event = Event::find($id);
        if ($event) {
            $event->update(['is_active' => !$event->is_active]);
        }
    }

    public function render()
    {
        $links = SiteLink::orderBy('sort_order')->orderBy('id')->get();
        $events = Event::orderBy('event_date', 'desc')->get();

        return view('livewire.admin.site-manager', [
            'links' => $links,
            'events' => $events,
        ]);
    }
}
