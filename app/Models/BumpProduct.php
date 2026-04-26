<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Configurable add-on product offered at checkout (an "order bump").
 *
 * Linked to an Enrollment via order_items rows. Pricing is snapshotted into
 * order_items at checkout time; this row is the editable source of truth
 * for what's offered going forward, NOT for what was sold historically.
 */
class BumpProduct extends Model
{
    use HasFactory, HasUuids;

    public const TYPE_PLAYBOOK_COURSE = 'playbook_course';
    public const TYPE_GROUP_MEMBERSHIP = 'group_membership';
    public const TYPE_MANUAL = 'manual';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'price',
        'original_price',
        'deliverable_type',
        'deliverable_ref_id',
        'course_id',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForCourse(Builder $query, ?string $courseId): Builder
    {
        if ($courseId === null) {
            return $query->whereNull('course_id');
        }
        return $query->where(function (Builder $q) use ($courseId) {
            $q->where('course_id', $courseId)->orWhereNull('course_id');
        });
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
