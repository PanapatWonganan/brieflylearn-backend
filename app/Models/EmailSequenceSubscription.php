<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSequenceSubscription extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'sequence_id',
        'current_step',
        'next_send_at',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'current_step' => 'integer',
            'next_send_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sequence()
    {
        return $this->belongsTo(EmailSequence::class, 'sequence_id');
    }

    /**
     * Get the next step to send for this subscription.
     */
    public function getNextStep(): ?EmailSequenceStep
    {
        return $this->sequence
            ->activeSteps()
            ->where('step_number', $this->current_step + 1)
            ->first();
    }

    /**
     * Advance to the next step after sending.
     */
    public function advance(): void
    {
        $this->current_step += 1;

        $nextStep = $this->getNextStep();

        if ($nextStep) {
            $this->next_send_at = now()->addDays($nextStep->delay_days);
        } else {
            // No more steps — sequence completed
            $this->status = 'completed';
            $this->completed_at = now();
            $this->next_send_at = null;
        }

        $this->save();
    }

    /**
     * Unsubscribe from this sequence.
     */
    public function unsubscribe(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'next_send_at' => null,
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDueToSend($query)
    {
        return $query->active()->where('next_send_at', '<=', now());
    }
}
