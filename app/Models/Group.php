<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property string|null $zoom_link
 * @property string|null $meeting_schedule
 * @property array|null $resources
 * @property int|null $max_members
 * @property bool $is_active
 */
class Group extends Model
{
    use HasFactory, HasUuids;

    public const TYPE_COACHING = 'coaching';
    public const TYPE_COMMUNITY = 'community';
    public const TYPE_COHORT = 'cohort';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'type',
        'zoom_link',
        'meeting_schedule',
        'resources',
        'max_members',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'resources' => 'array',
            'max_members' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class)->where('status', 'active');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot(['role', 'status', 'joined_at', 'enrollment_id'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isFull(): bool
    {
        if ($this->max_members === null) {
            return false;
        }

        return $this->activeMembers()->count() >= $this->max_members;
    }
}
