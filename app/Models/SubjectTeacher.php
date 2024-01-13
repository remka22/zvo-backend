<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectTeacher extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "subject_teacher";

    function getTeacher(){
        return $this->hasOne('App\Models\User', 'id', 'teacher_id');//, $this->hasOne('App\Models\TeacherCourse', 'id', 'teacher_course_id'));
    }
    function getTeacherCourse(){
        return $this->hasOne('App\Models\TeacherCourse', 'id', 'teacher_course_id');
    }
    function getNeedTask(){
        return $this->hasMany('App\Models\NeedsTask', 'subject_id', 'id');
    }
    function getSubject($number){
        return $this->hasOne('App\Models\Subject', 'id', 'subject_id')->where('number_course', $number);
    }
}
