<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id', 'question_id',
        'selected_option_ids', 'is_correct', 'earned_points',
    ];

    protected function casts(): array
    {
        return [
            'selected_option_ids' => 'array',
            'is_correct'          => 'boolean',
            'earned_points'       => 'decimal:2',
        ];
    }

    public function attempt(): BelongsTo  { return $this->belongsTo(EvaluationAttempt::class, 'attempt_id'); }
    public function question(): BelongsTo { return $this->belongsTo(Question::class); }
}
