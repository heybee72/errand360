<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test_score extends Model
{
    use HasFactory;

    protected $fillable = ['time','score','user_id','topic_id','course_id','type'];
}
