<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $group_id
 * @property string $user_id
 * @property string $role
 * @property string $status
 * @property \Illuminate\Support\Carbon $joined_at
 * @property string|null $enrollment_id
 */
class GroupMember extends Model
{
    use HasFactory, HasUuids;

    public const ROLE_MEMBER = 'member';
    public const ROLE_ADMIN = 'admin';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING = 'pending';
    public const STATUS_REMOVED = 'removed';

    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'status',
        'joined_at',
        'enrollment_id',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
