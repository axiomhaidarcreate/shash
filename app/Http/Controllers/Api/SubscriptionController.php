<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::where('is_active', true)->get();
        return response()->json([
            'status' => 'success',
            'data' => [
                'packages' => $packages
            ]
        ]);
    }

    public function subscribe(Request $request, $packageId)
    {
        $package = SubscriptionPackage::findOrFail($packageId);
        $customer = $request->user();

        // Check if there is already a pending request
        $existingRequest = SubscriptionRequest::where('customer_phone', $customer->phone)
            ->where('subscription_package_id', $packageId)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'status' => 'error',
                'message' => 'لديك طلب اشتراك قيد المراجعة لهذه الباقة.'
            ], 400);
        }

        $subscriptionRequest = SubscriptionRequest::create([
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'request_type' => 'subscription',
            'subscription_package_id' => $package->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال طلب الاشتراك بنجاح. سيتم التواصل معك قريباً.',
            'data' => [
                'subscription_request' => $subscriptionRequest
            ]
        ]);
    }
}
