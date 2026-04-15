<?php

namespace App\Models;

use Database\Factories\ExamAttemptFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'score',
    'total_questions',
    'correct_answers',
    'incorrect_answers',
    'answers',
])]
class ExamAttempt extends Model
{
    use HasFactory;

    protected $casts = [
        'answers' => AsArrayObject::class,
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPassing(): bool
    {
        return $this->score >= 80;
    }
}
