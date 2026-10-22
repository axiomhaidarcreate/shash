<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'guest_name',
        'num_people',
        'barcode',
        'status',
        'is_subscription',
        'order_id',
        'validity_hours',
        'ticket_template_id',
        'selected_products',
        'times_used',
        'max_uses',
    ];

    protected $casts = [
        'selected_products' => 'array',
        'times_used'        => 'integer',
        'max_uses'          => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function ticketTemplate()
    {
        return $this->belongsTo(TicketTemplate::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * هل ما زالت هناك استخدامات متبقية؟
     */
    public function hasRemainingUses(): bool
    {
        return $this->status === 'active' && $this->times_used < $this->max_uses;
    }

    /**
     * عدد الاستخدامات المتبقية
     */
    public function remainingUses(): int
    {
        return max(0, $this->max_uses - $this->times_used);
    }

    /**
     * استخدام التذكرة مرة واحدة — يزيد العداد ويغلقها إذا اكتملت
     */
    public function useOnce(?int $orderId = null): void
    {
        $this->increment('times_used');
        $this->refresh();

        if ($this->times_used >= $this->max_uses) {
            $this->status   = 'used';
            $this->order_id = $orderId ?? $this->order_id;
            $this->save();
        }
    }
}
