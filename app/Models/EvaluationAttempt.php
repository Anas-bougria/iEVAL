<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class EvaluationAttempt extends Model
{
    use HasFactory;

    public const STATUS_IN_PROGRESS    = 'in_progress';
    public const STATUS_SUBMITTED      = 'submitted';
    public const STATUS_AUTO_SUBMITTED = 'auto_submitted';
    public const STATUS_CANCELLED      = 'cancelled';

    protected $fillable = [
        'evaluation_id', 'student_id',
        'started_at', 'submitted_at',
        'score', 'max_score', 'grade_20', 'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'submitted_at' => 'datetime',
            'score'        => 'decimal:2',
            'max_score'    => 'decimal:2',
            'grade_20'     => 'decimal:2',
        ];
    }

    public function evaluation(): BelongsTo { return $this->belongsTo(Evaluation::class); }
    public function student(): BelongsTo    { return $this->belongsTo(User::class, 'student_id'); }
    public function answers(): HasMany      { return $this->hasMany(StudentAnswer::class, 'attempt_id'); }

    public function deadline(): ?Carbon
    {
        if (!$this->started_at || !$this->evaluation) return null;
        return $this->started_at->copy()->addMinutes((int) $this->evaluation->duration_minutes);
    }

    public function isExpired(): bool
    {
        $d = $this->deadline();
        return $d !== null && now()->gt($d);
    }

    public function secondsRemaining(): int
    {
        $d = $this->deadline();
        if (!$d) return 0;
        return max(0, $d->diffInSeconds(now(), false) * -1);
    }

    /**
     * Compute and persist score from the answers table.
     * Returns the resulting score / max_score / grade_20.
     */
    public function recomputeScore(): self
    {
        $earned = (float) $this->answers()->sum('earned_points');
        $max    = (float) $this->evaluation->totalPoints();
        $grade  = $max > 0 ? round(($earned / $max) * 20, 2) : 0.0;

        $this->update([
            'score'     => $earned,
            'max_score' => $max,
            'grade_20'  => $grade,
        ]);

        return $this;
    }

    /**
     * Save student-submitted answers (array of [question_id => [option_id, …]]) and
     * compute correctness for each one in a single transaction.
     */
    public function recordAnswers(array $answers): void
    {
        DB::transaction(function () use ($answers) {
            foreach ($this->evaluation->questions as $question) {
                $selected = $answers[$question->id] ?? [];
                if (!is_array($selected)) {
                    $selected = $selected === null ? [] : [$selected];
                }
                $selected = array_values(array_filter(array_map('intval', $selected)));

                $eval = $question->evaluateAnswer($selected);

                StudentAnswer::updateOrCreate(
                    ['attempt_id' => $this->id, 'question_id' => $question->id],
                    [
                        'selected_option_ids' => $selected,
                        'is_correct'          => $eval['is_correct'],
                        'earned_points'       => $eval['earned_points'],
                    ]
                );
            }
        });
    }

    public function submit(string $status = self::STATUS_SUBMITTED): self
    {
        $this->update([
            'submitted_at' => now(),
            'status'       => $status,
        ]);
        return $this->recomputeScore();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_IN_PROGRESS    => 'En cours',
            self::STATUS_SUBMITTED      => 'Soumise',
            self::STATUS_AUTO_SUBMITTED => 'Soumise (temps écoulé)',
            self::STATUS_CANCELLED      => 'Annulée',
            default                     => $this->status,
        };
    }
}
