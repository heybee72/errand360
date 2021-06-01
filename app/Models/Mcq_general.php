<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mcq_general extends Model
{
    use HasFactory;
    
    protected $fillable = ['question','opt_a','opt_b','opt_c','opt_d','answer','reason'];
}
