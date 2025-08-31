<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'issuer_id',
        'recipient_id',
        'status',
        'subtotal',
        'discounts',
        'total',
        'paid',
        'due',
        'invoice_id',
    ];

    protected $attributes = [
        'status' => 'draft',
        'subtotal' => 0,
        'discounts' => 0,
        'total' => 0,
        'paid' => 0,
        'due' => 0,
        'invoice_id' => null,
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discounts' => 'decimal:2',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
        'due' => 'decimal:2',
        'invoice_id' => 'string',
    ];

    /**
     * Get the issuer (company) of the order.
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'issuer_id');
    }

    /**
     * Get the recipient (customer) of the order.
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'recipient_id');
    }

    /**
     * Get the order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate order totals based on items.
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $discounts = $this->items->sum(function ($item) {
            return ($item->quantity * $item->unit_price * $item->discount_percentage) / 100;
        });

        $total = $subtotal - $discounts;
        $due = $total - $this->paid;

        $this->update([
            'subtotal' => $subtotal,
            'discounts' => $discounts,
            'total' => $total,
            'due' => $due,
        ]);
    }

    /**
     * Check if order can be invoiced
     */
    public function canBeInvoiced(): bool
    {
        return $this->status === 'completed'
            && !$this->invoice_id
            && $this->items->isNotEmpty()
            && $this->issuer
            && $this->recipient
            && $this->issuer->fiscalapiId
            && $this->recipient->fiscalapiId;
    }

    /**
     * Check if all products have FiscalAPI IDs
     */
    public function allProductsHaveFiscalApiId(): bool
    {
        return $this->items->every(function ($item) {
            return $item->product && $item->product->fiscalapiId;
        });
    }
}
