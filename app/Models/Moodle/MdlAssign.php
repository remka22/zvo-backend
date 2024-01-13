<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlAssign extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_assign";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [
        "course",
        "intro",
        "introformat",
        "alwaysshowdescription",
        "nosubmissions",
        "submissiondrafts",
        "sendnotifications",
        "sendlatenotifications",
        "duedate",
        "allowsubmissionsfromdate",
        "grade",
        "timemodified",
        "requiresubmissionstatement",
        "completionsubmit",
        "cutoffdate",
        "gradingduedate",
        "teamsubmission",
        "requireallteammemberssubmit",
        "teamsubmissiongroupingid",
        "blindmarking",
        "hidegrader",
        "revealidentities",
        "attemptreopenmethod",
        "maxattempts",
        "markingworkflow",
        "markingallocation",
        "sendstudentnotifications",
        "preventsubmissionnotingroup",
    ];
}
