<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\CustomerNotification;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function categories()
    {
        $categories = Category::where('is_visible', true)->get();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories
            ]
        ]);
    }

    public function products()
    {
        $products = Product::where('is_active', true)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'products' => $products
            ]
        ]);
    }

    /**
     * إنشاء طلب من تطبيق العملاء
     * يدعم: items[] مع product_id/quantity/notes، ticket_barcode، table_id
     * ينشئ Order لكل وحدة (نفس منطق OrderStaffBoard)
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
            'table_id' => 'nullable|integer',
            'ticket_barcode' => 'nullable|string',
        ]);

        $customer = $request->user();
        $ticket = null;

        // التحقق من التذكرة إذا تم إرسال باركود
        if ($request->filled('ticket_barcode')) {
            $barcode = strtoupper(trim($request->ticket_barcode));
            $ticket = Ticket::where('barcode', $barcode)->latest()->first();

            if (!$ticket) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على تذكرة بهذا الرمز.'
                ], 422);
            }

            if ($ticket->status === 'cancelled') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'هذه التذكرة ملغاة ولا يمكن استخدامها.'
                ], 422);
            }

            if (!$ticket->hasRemainingUses()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'هذه التذكرة مستخدمة بالكامل (' . $ticket->times_used . '/' . $ticket->max_uses . ' مرات).'
                ], 422);
            }

            // حساب إجمالي الكمية والتحقق من رصيد التذكرة
            $totalQty = 0;
            foreach ($request->items as $item) {
                $totalQty += $item['quantity'];
            }

            $remaining = $ticket->remainingUses();
            if ($totalQty > $remaining) {
                return response()->json([
                    'status' => 'error',
                    'message' => "عدد الأصناف ({$totalQty}) يتجاوز رصيد التذكرة ({$remaining} استخدامات)."
                ], 422);
            }
        }

        $batchId = date('YmdHis') . rand(100, 999);
        $createdOrders = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $targetKitchen = $product->target_kitchen ?? 'kitchen';
            $grams = $product->grams_per_order ?? 0;
            $displayName = $product->name;
            $notes = trim($item['notes'] ?? '');

            for ($q = 0; $q < $item['quantity']; $q++) {
                // التحقق من رصيد التذكرة أثناء المعالجة
                if ($ticket) {
                    $ticket->refresh();
                    if (!$ticket->hasRemainingUses()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'نفدت استخدامات التذكرة أثناء المعالجة.'
                        ], 422);
                    }
                }

                $order = Order::create([
                    'table_id'                => $request->table_id,
                    'shisha_type'             => $displayName,
                    'quantity'                => 1,
                    'grams_per_unit'          => $grams,
                    'notes'                   => $notes,
                    'status'                  => 'pending',
                    'batch_id'                => $batchId,
                    'customer_id'             => $customer->id,
                    'ticket_id'               => $ticket?->id,
                    'target_kitchen'          => $targetKitchen,
                    'source'                  => 'customer_app',
                    'guest_name'              => $customer->name,
                ]);

                if ($ticket) {
                    $ticket->useOnce($order->id);
                }

                $createdOrders[] = $order;
            }
        }

        // إرسال إشعار للعميل
        CustomerNotification::create([
            'customer_id' => $customer->id,
            'title' => 'تم استلام طلبك',
            'message' => 'تم إرسال طلبك (' . count($createdOrders) . ' أصناف) للمطبخ بنجاح. سيتم إخطارك عندما يكون جاهزاً.',
            'type' => 'order',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال طلبك بنجاح للمطبخ.',
            'data' => [
                'orders' => $createdOrders,
                'batch_id' => $batchId,
            ]
        ]);
    }

    /**
     * التحقق من التذكرة بالباركود
     */
    public function checkTicket(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        $barcode = strtoupper(trim($request->barcode));
        $ticket = Ticket::where('barcode', $barcode)->latest()->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'لم يتم العثور على تذكرة بهذا الرمز.'
            ], 404);
        }

        if ($ticket->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'هذه التذكرة ملغاة.'
            ], 422);
        }

        if (!$ticket->hasRemainingUses()) {
            return response()->json([
                'status' => 'error',
                'message' => 'هذه التذكرة مستخدمة بالكامل (' . $ticket->times_used . '/' . $ticket->max_uses . ').'
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'remaining' => $ticket->remainingUses(),
                'max_uses' => $ticket->max_uses,
                'times_used' => $ticket->times_used,
                'guest_name' => $ticket->guest_name,
            ]
        ]);
    }

    public function myOrders(Request $request)
    {
        $customer = $request->user();
        
        $orders = Order::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'orders' => $orders
            ]
        ]);
    }
}
