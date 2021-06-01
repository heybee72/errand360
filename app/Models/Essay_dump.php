<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Essay_dump extends Model
{
    use HasFactory;

    protected $fillable = ['email','exam_year_id','user_id','description','document'];
}
