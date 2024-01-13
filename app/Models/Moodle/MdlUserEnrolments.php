<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlUserEnrolments extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_user_enrolments";
    protected $connection = 'pgsql_moodle';

    protected $hidden =[
        "id",
        "status",
        "enrolid",
        "userid",
        "timestart",
        "timeend",
        "modifierid",
        "timecreated",
        "timemodified",
    ];

    public function getEnrole()
    {
        return $this->hasOne('App\Models\Moodle\MdlEnrol', 'id', 'enrolid');
    }
}
