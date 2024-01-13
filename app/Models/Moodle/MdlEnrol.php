<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlEnrol extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_enrol";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [
        "enrol",
        "status",
        "sortorder",
        "name",
        "enrolperiod",
        "enrolstartdate",
        "enrolenddate",
        "expirynotify",
        "expirythreshold",
        "notifyall",
        "password",
        "cost",
        "currency",
        "roleid",
        "customint1",
        "customint2",
        "customint3",
        "customint4",
        "customint5",
        "customint6",
        "customint7",
        "customint8",
        "customchar1",
        "customchar2",
        "customchar3",
        "customdec1",
        "customdec2",
        "customtext1",
        "customtext2",
        "customtext3",
        "customtext4",
        "timecreated",
        "timemodified",
    ];

    public function getCourse()
    {
        return $this->hasOne('App\Models\Moodle\MdlCourse', 'id', 'courseid');
    }
}
