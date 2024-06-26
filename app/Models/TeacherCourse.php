<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherCourse extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "teacher_course";
    function course(){
        return $this->hasOne('App\Models\MoodleCourse', 'id', 'course_id');
    }
}
