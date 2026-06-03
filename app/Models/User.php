<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN   = 'admin';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STUDENT = 'student';

    protected $fillable = [
        'name', 'first_name', 'last_name',
        'email', 'matricule', 'class', 'phone', 'birth_date',
        'role', 'password', 'avatar_path', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password'   => 'hashed',
            'is_active'  => 'boolean',
        ];
    }

    /**
     * Compose the full display name from first/last when available,
     * falling back to the legacy `name` field.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name || $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        return $this->name ?? '';
    }

    // ---------- Role helpers ----------
    public function isAdmin():   bool { return $this->role === self::ROLE_ADMIN; }
    public function isTeacher(): bool { return $this->role === self::ROLE_TEACHER; }
    public function isStudent(): bool { return $this->role === self::ROLE_STUDENT; }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN   => 'admin.dashboard',
            self::ROLE_TEACHER => 'teacher.dashboard',
            default            => 'student.dashboard',
        };
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $i = '';
        foreach ($parts as $p) {
            if ($p !== '') $i .= mb_strtoupper(mb_substr($p, 0, 1));
            if (mb_strlen($i) >= 2) break;
        }
        return $i ?: 'U';
    }

    // ---------- Relations ----------
    public function taughtModules(): HasMany
    {
        return $this->hasMany(Module::class, 'teacher_id');
    }

    public function enrolledModules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'module_student', 'student_id', 'module_id')
                    ->withTimestamps();
    }

    public function authoredEvaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'teacher_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(EvaluationAttempt::class, 'student_id');
    }
}
