<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollsRespondent extends Model
{
    use HasFactory;

    protected $table = 'polls_respondent';
    public $timestamps = false;
}
