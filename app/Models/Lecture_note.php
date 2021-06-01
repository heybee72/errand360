<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture_note extends Model
{
    use HasFactory;

    protected $fillable = ['lecture_note_title','lecture_note','course_id','topic_id'];
}
