<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleCourse extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "moodle_course";
}
