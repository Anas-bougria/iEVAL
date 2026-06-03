<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    public const TYPE_SINGLE   = 'single';
    public const TYPE_MULTIPLE = 'multiple';

    protected $fillable = [
        'evaluation_id', 'chapter_id', 'statement',
        'type', 'points', 'position',
    ];

    protected function casts(): array
    {
        return ['points' => 'decimal:2'];
    }

    public function evaluation(): BelongsTo { return $this->belongsTo(Evaluation::class); }
    public function chapter(): BelongsTo    { return $this->belongsTo(Chapter::class); }
    public function options(): HasMany      { return $this->hasMany(AnswerOption::class)->orderBy('position'); }

    public function correctOptionIds(): array
    {
        return $this->options()->where('is_correct', true)->pluck('id')->map(fn ($v) => (int) $v)->all();
    }

    /**
     * Evaluate a set of selected option IDs against the correct ones.
     * Returns ['is_correct' => bool, 'earned_points' => float].
     * - single   : full points only if exactly the one correct option is selected
     * - multiple : full points only if the selected set equals the correct set
     */
    public function evaluateAnswer(array $selectedIds): array
    {
        $selected = array_values(array_unique(array_map('intval', $selectedIds)));
        sort($selected);

        $correct = $this->correctOptionIds();
        sort($correct);

        $isCorrect = ($selected === $correct);

        return [
            'is_correct'    => $isCorrect,
            'earned_points' => $isCorrect ? (float) $this->points : 0.0,
        ];
    }
}
