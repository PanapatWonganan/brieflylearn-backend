<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSequence extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function steps()
    {
        return $this->hasMany(EmailSequenceStep::class, 'sequence_id')->orderBy('step_number');
    }

    public function activeSteps()
    {
        return $this->hasMany(EmailSequenceStep::class, 'sequence_id')
            ->where('is_active', true)
            ->orderBy('step_number');
    }

    public function subscriptions()
    {
        return $this->hasMany(EmailSequenceSubscription::class, 'sequence_id');
    }

    public function activeSubscriptions()
    {
        return $this->hasMany(EmailSequenceSubscription::class, 'sequence_id')
            ->where('status', 'active');
    }

    /**
     * Get the default sequence for auto-subscribing new users.
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }

    /**
     * Subscribe a user to this sequence.
     */
    public function subscribe(User $user): EmailSequenceSubscription
    {
        $firstStep = $this->activeSteps()->first();

        return EmailSequenceSubscription::firstOrCreate(
            [
                'user_id' => $user->id,
                'sequence_id' => $this->id,
            ],
            [
                'current_step' => 0,
                'next_send_at' => now()->addDays($firstStep?->delay_days ?? 3),
                'status' => 'active',
                'started_at' => now(),
            ]
        );
    }
}
