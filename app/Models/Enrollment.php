<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory, HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
        'completed_at',
        'progress',
        'status',
        'payment_status',
        'amount_paid',
        'payment_date',
        'payment_method',
        'transaction_id',
        'order_no',
        'payment_gateway',
        'gateway_response',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'payment_date' => 'datetime',
            'gateway_response' => 'array',
        ];
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Total amount of all attached order-bump items, in baht.
     * Returns 0 when no items are loaded/attached.
     */
    public function getBumpsTotalAttribute(): float
    {
        return (float) $this->orderItems->sum('price_snapshot');
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
