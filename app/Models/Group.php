<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "groups";

    public function subjects(){
        return $this->hasMany('App\Models\Subject', 'group_id', 'id');
    }

    // for metodist post
    public function getSubject($subject_id){
        return $this->hasOne('App\Models\Subject', 'group_id', 'id')->where('id', $subject_id);
    }
}
