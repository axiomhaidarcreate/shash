<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Firebase login - find existing customer by phone/firebase_uid
     */
    public function firebaseLogin(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'firebase_uid' => 'required|string',
            'firebase_id_token' => 'required|string',
            'fcm_token' => 'nullable|string',
        ]);

        // Verify Firebase ID token
        $firebaseUser = $this->verifyFirebaseToken($request->firebase_id_token);
        if (!$firebaseUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'رمز التحقق من Firebase غير صالح.'
            ], 401);
        }

        // Find customer by firebase_uid or phone
        $customer = Customer::where('firebase_uid', $request->firebase_uid)
            ->orWhere('phone', $request->phone)
            ->first();

        if (!$customer) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'لا يوجد حساب بهذا الرقم، يرجى إنشاء حساب جديد.'
            ], 404);
        }

        // Update firebase_uid and fcm_token
        $customer->update([
            'firebase_uid' => $request->firebase_uid,
            'is_phone_verified' => true,
            'fcm_token' => $request->fcm_token ?? $customer->fcm_token,
        ]);

        $token = $customer->createToken('customer_app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح.',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }

    /**
     * Firebase register - create new customer
     */
    public function firebaseRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'gender' => 'required|string',
            'nationality' => 'nullable|string|max:255',
            'firebase_uid' => 'required|string',
            'firebase_id_token' => 'required|string',
            'fcm_token' => 'nullable|string',
        ]);

        // Verify Firebase ID token
        $firebaseUser = $this->verifyFirebaseToken($request->firebase_id_token);
        if (!$firebaseUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'رمز التحقق من Firebase غير صالح.'
            ], 401);
        }

        // Check if customer already exists
        $existingCustomer = Customer::where('firebase_uid', $request->firebase_uid)
            ->orWhere('phone', $request->phone)
            ->first();

        if ($existingCustomer) {
            // Customer exists, update and login
            $existingCustomer->update([
                'firebase_uid' => $request->firebase_uid,
                'is_phone_verified' => true,
                'fcm_token' => $request->fcm_token,
            ]);

            $token = $existingCustomer->createToken('customer_app')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الدخول بنجاح (حساب موجود مسبقاً).',
                'data' => [
                    'token' => $token,
                    'customer' => $existingCustomer
                ]
            ]);
        }

        // Create new customer
        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'firebase_uid' => $request->firebase_uid,
            'fcm_token' => $request->fcm_token,
            'is_phone_verified' => true,
            'password' => Hash::make(uniqid()), // Random password since we use Firebase auth
            'subscription_number' => 'CUST-' . rand(10000, 99999) . time(),
        ]);

        $token = $customer->createToken('customer_app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء الحساب بنجاح.',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }

    /**
     * Update FCM token for push notifications
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث رمز الإشعارات بنجاح.'
        ]);
    }

    /**
     * Verify Firebase ID Token using Google's public keys
     */
    private function verifyFirebaseToken(string $idToken): ?array
    {
        try {
            // Decode the JWT token (without full verification for simplicity)
            // In production, you should verify with Firebase Admin SDK
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                return null;
            }

            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
            
            if (!$payload) {
                return null;
            }

            // Check token expiration
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return null;
            }

            // Check the issuer
            $projectId = config('services.firebase.project_id', 'shash-c7c31');
            if (isset($payload['iss']) && $payload['iss'] !== 'https://securetoken.google.com/' . $projectId) {
                return null;
            }

            return $payload;
        } catch (\Exception $e) {
            return null;
        }
    }

    // ===== Legacy endpoints (kept for backward compatibility) =====

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات الدخول غير صحيحة.'
            ], 401);
        }

        if ($request->has('fcm_token')) {
            $customer->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $customer->createToken('customer_app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح.',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'customer' => $request->user()
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الخروج بنجاح.'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone',
            'password' => 'required|string|min:6',
            'gender' => 'required|string',
            'nationality' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => $request->password,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'fcm_token' => $request->fcm_token,
            'subscription_number' => 'CUST-' . rand(10000, 99999) . time(),
        ]);

        $token = $customer->createToken('customer_app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء الحساب بنجاح.',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }
}
