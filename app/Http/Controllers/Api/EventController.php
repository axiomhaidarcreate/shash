<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventBooking;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('is_active', true)
            ->where('is_ended', false)
            ->where(function($query) {
                $query->whereDate('event_date', '>', now()->toDateString())
                      ->orWhere(function($q) {
                          $q->whereDate('event_date', '=', now()->toDateString())
                            ->whereTime('event_time', '>=', now()->toTimeString());
                      });
            })
            ->orderByRaw("DATE(event_date) = ? DESC", [now()->toDateString()])
            ->orderBy('event_date', 'asc')
            ->get();

        // إرجاع رابط الصورة الكامل لكل حفلة
        $events->transform(function ($event) {
            if ($event->artist_image) {
                if (filter_var($event->artist_image, FILTER_VALIDATE_URL)) {
                    $event->artist_image_url = $event->artist_image;
                } else {
                    // Use a special API route to bypass CORS on Flutter Web
                    $event->artist_image_url = url('api/image/' . ltrim($event->artist_image, '/'));
                }
            } else {
                $event->artist_image_url = null;
            }
            return $event;
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'events' => $events
            ]
        ]);
    }

    public function tables($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        $tables = \App\Models\Table::with('chairs')->where('section', 'صالة الحفلات')->get();
        
        // جلب معرفات الكراسي المحجوزة مسبقاً لهذه الحفلة (باستثناء الحجوزات المرفوضة)
        $bookedChairsIds = EventBooking::where('event_id', $eventId)
            ->whereNotNull('chair_id')
            ->where('status', '!=', 'rejected')
            ->pluck('chair_id')
            ->toArray();

        return response()->json([
            'status' => 'success',
            'data' => [
                'tables' => $tables,
                'booked_chairs' => $bookedChairsIds
            ]
        ]);
    }

    public function book(Request $request, $eventId)
    {
        $request->validate([
            'gender' => 'required|string',
            'table_id' => 'required|exists:tables,id',
            'chair_id' => 'required|exists:chairs,id',
        ]);

        $event = Event::findOrFail($eventId);
        $customer = $request->user();

        // Check if chair is already booked for this event
        $isChairBooked = EventBooking::where('event_id', $eventId)
            ->where('chair_id', $request->chair_id)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($isChairBooked) {
            return response()->json([
                'status' => 'error',
                'message' => 'هذا الكرسي محجوز بالفعل لهذه الحفلة. يرجى اختيار كرسي آخر.'
            ], 400);
        }

        $table = \App\Models\Table::find($request->table_id);
        $chair = \App\Models\Chair::find($request->chair_id);

        $booking = EventBooking::create([
            'event_id' => $event->id,
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'gender' => $request->gender,
            'table_id' => $table->id,
            'table_number' => $table->number,
            'chair_id' => $chair->id,
            'chair_number' => $chair->number,
            'status' => 'pending', 
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال طلب الحجز بنجاح إلى موظف الاستقبال.',
            'data' => [
                'booking' => $booking
            ]
        ]);
    }
}
