<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use HasFactory;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CLOSED    = 'closed';

    protected $fillable = [
        'title', 'description', 'instructions',
        'module_id', 'teacher_id',
        'duration_minutes', 'max_attempts',
        'shuffle_questions', 'shuffle_options', 'show_results_immediately',
        'status', 'opens_at', 'closes_at',
    ];

    protected function casts(): array
    {
        return [
            'shuffle_questions'         => 'boolean',
            'shuffle_options'           => 'boolean',
            'show_results_immediately'  => 'boolean',
            'opens_at'                  => 'datetime',
            'closes_at'                 => 'datetime',
        ];
    }

    // ---------- Relations ----------
    public function module(): BelongsTo  { return $this->belongsTo(Module::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }
    public function questions(): HasMany { return $this->hasMany(Question::class)->orderBy('position'); }
    public function attempts(): HasMany  { return $this->hasMany(EvaluationAttempt::class); }

    // ---------- Scopes ----------
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeOpenNow(Builder $q): Builder
    {
        $now = now();
        return $q->published()
            ->where(fn ($w) => $w->whereNull('opens_at')->orWhere('opens_at', '<=', $now))
            ->where(fn ($w) => $w->whereNull('closes_at')->orWhere('closes_at', '>=', $now));
    }

    // ---------- Helpers ----------
    public function totalPoints(): float
    {
        return (float) $this->questions()->sum('points');
    }

    public function isOpen(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) return false;
        $now = now();
        if ($this->opens_at  && $this->opens_at->gt($now))  return false;
        if ($this->closes_at && $this->closes_at->lt($now)) return false;
        return true;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT     => 'Brouillon',
            self::STATUS_PUBLISHED => 'Publiée',
            self::STATUS_CLOSED    => 'Clôturée',
            default                => $this->status,
        };
    }

    public function attemptsByStudent(int $studentId): int
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->count();
    }

    public function canBeAttemptedBy(User $student): bool
    {
        if (!$this->isOpen()) return false;
        return $this->attemptsByStudent($student->id) < $this->max_attempts;
    }
}
