<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flash_card extends Model
{
    use HasFactory;

    protected $fillable = ['flash_card_question','flash_card_answer','course_id','topic_id'];
}
