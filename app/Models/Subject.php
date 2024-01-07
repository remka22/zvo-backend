<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "subject";

    public function getSubjectTeachers(){
        return $this->hasMany('App\Models\SubjectTeacher', 'subject_id', 'id');
    }
}
