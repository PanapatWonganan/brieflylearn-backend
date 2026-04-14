<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'exam_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'points',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'points' => 'integer',
            'order_index' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
