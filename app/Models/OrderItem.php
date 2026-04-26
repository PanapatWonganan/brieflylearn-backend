<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A line item attached to an Enrollment representing a purchased bump.
 *
 * `name_snapshot` and `price_snapshot` are immutable once created — they
 * preserve what was sold even if the BumpProduct row is later edited or
 * deactivated. delivery_meta holds the audit trail produced by the
 * fulfillment service (e.g. the playbook enrollment id created on success).
 */
class OrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'enrollment_id',
        'bump_product_id',
        'name_snapshot',
        'price_snapshot',
        'delivered_at',
        'delivery_meta',
    ];

    protected function casts(): array
    {
        return [
            'price_snapshot' => 'decimal:2',
            'delivered_at' => 'datetime',
            'delivery_meta' => 'array',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function bumpProduct(): BelongsTo
    {
        return $this->belongsTo(BumpProduct::class);
    }

    public function isDelivered(): bool
    {
        return $this->delivered_at !== null;
    }
}
