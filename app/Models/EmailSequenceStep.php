<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSequenceStep extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'sequence_id',
        'step_number',
        'subject',
        'body_html',
        'delay_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'delay_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function sequence()
    {
        return $this->belongsTo(EmailSequence::class, 'sequence_id');
    }
}
