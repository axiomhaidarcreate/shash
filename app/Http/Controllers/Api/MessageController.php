<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsappLog;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $customer = $request->user();
        
        // Assuming messages sent to the customer's phone are in WhatsappLog
        $messages = WhatsappLog::where('phone', $customer->phone)
            ->orWhere('phone', '+' . $customer->phone)
            ->orWhere('phone', ltrim($customer->phone, '0'))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'messages' => $messages
            ]
        ]);
    }
}
