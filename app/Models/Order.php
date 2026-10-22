<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function chair()
    {
        return $this->belongsTo(Chair::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Call this when the order is delivered or cancelled
     * to check and free the chair and table if they are no longer in use.
     */
    public function releaseTableAndChairIfEmpty()
    {
        // 1. Check chair
        if ($this->chair_id) {
            $hasActiveOrdersOnChair = Order::where('chair_id', $this->chair_id)
                ->whereIn('status', ['pending', 'prep', 'ready'])
                ->exists();
                
            if (!$hasActiveOrdersOnChair) {
                $chair = Chair::find($this->chair_id);
                if ($chair) {
                    $chair->update(['status' => 'empty']);
                }
            }
        }

        // 2. Check table
        if ($this->table_id) {
            $hasActiveOrdersOnTable = Order::where('table_id', $this->table_id)
                ->whereIn('status', ['pending', 'prep', 'ready'])
                ->exists();
                
            $table = Table::find($this->table_id);
            if ($table && !$hasActiveOrdersOnTable) {
                // Also verify no occupied chairs before setting table to empty
                $hasOccupiedChairs = $table->chairs()->where('status', '!=', 'empty')->exists();
                if (!$hasOccupiedChairs) {
                    $table->status = 'empty';
                    $table->save();
                }
            }
        }
    }
}
