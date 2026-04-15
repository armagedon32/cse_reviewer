<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'question_text',
    'category',
    'difficulty',
    'option_a',
    'option_b',
    'option_c',
    'option_d',
    'correct_option',
])]
class Question extends Model
{
    public const DIFFICULTIES = ['easy', 'medium', 'hard'];
    public const CORRECT_OPTIONS = ['A', 'B', 'C', 'D'];
}
