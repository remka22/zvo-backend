<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher_course extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "teacher_course";
}
