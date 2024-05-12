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
    public function metodist(){
        return $this->hasOne('App\Models\User', 'id', 'metodist_id');
    }

    public function students(){
        return $this->hasMany('App\Models\User','group_id', 'id');
    }
}
