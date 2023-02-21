<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollsQuestionAnswer extends Model
{
    use HasFactory;

    protected $table = 'polls_question_answer';
}
