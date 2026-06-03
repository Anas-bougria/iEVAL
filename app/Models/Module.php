<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'semester_id', 'teacher_id'];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('position');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'module_student', 'module_id', 'student_id')
                    ->withTimestamps();
    }

    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(Question::class, Evaluation::class);
    }
}
