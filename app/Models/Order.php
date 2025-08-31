<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_INVOICED = 'invoiced';
    public const STATUS_CANCELLED = 'cancelled';

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
        return $this->status === self::STATUS_COMPLETED
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

    /**
     * Check if order is in draft status
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if order is invoiced
     */
    public function isInvoiced(): bool
    {
        return $this->status === self::STATUS_INVOICED;
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if order is ready for invoicing
     */
    public function isReadyForInvoicing(): bool
    {
        return $this->canBeInvoiced();
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayText(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Borrador',
            self::STATUS_COMPLETED => 'Completada',
            self::STATUS_INVOICED => 'Facturada',
            self::STATUS_CANCELLED => 'Cancelada',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get status badge classes
     */
    public function getStatusBadgeClasses(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'bg-gray-100 text-gray-800',
            self::STATUS_COMPLETED => 'bg-blue-100 text-blue-800',
            self::STATUS_INVOICED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
