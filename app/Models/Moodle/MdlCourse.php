<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlCourse extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_course";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [

        "category",
        "sortorder",
        "idnumber",
        "summary",
        "summaryformat",
        "format",
        "showgrades",
        "newsitems",
        "startdate",
        "enddate",
        "relativedatesmode",
        "marker",
        "maxbytes",
        "legacyfiles",
        "showreports",
        "visible",
        "visibleold",
        "groupmode",
        "groupmodeforce",
        "defaultgroupingid",
        "lang",
        "calendartype",
        "theme",
        "timecreated",
        "timemodified",
        "requested",
        "enablecompletion",
        "completionnotify",
        "cacherev",
    ];

    public function getAssign()
    {
        return $this->hasMany('App\Models\Moodle\MdlCourseModules', 'course', 'id')
            ->where('mdl_course_modules.module', 1);
    }
    // public function getNewAssign($assign_id_arr)
    // {
    //     return $this->hasMany('App\Models\Moodle\MdlCourseModules', 'course', 'id')
    //         ->join('mdl_assign', 'mdl_assign.id', '=', 'mdl_course_modules.instance')
    //         ->where('mdl_course_modules.module', 1)
    //         ->whereNotIn('mdl_course_modules.instance', $assign_id_arr);
    // }
    public function getQuiz()
    {
        return $this->hasMany('App\Models\Moodle\MdlCourseModules', 'course', 'id')
            ->where('mdl_course_modules.module', 17);;
    }
    // public function getNewQuiz($quize_id_arr)
    // {
    //     return $this->hasMany('App\Models\Moodle\MdlCourseModules', 'course', 'id')
    //         ->join('mdl_quiz', 'mdl_quiz.id', '=', 'mdl_course_modules.instance')
    //         ->where('mdl_course_modules.module', 17)
    //         ->whereNotIn('mdl_course_modules.instance', $quize_id_arr);
    // }
}


// ->join('mdl_modules', 'mdl_modules.id', '=', 'mdl_course_modules.module')
            // ->select('mdl_assign.id', 'mdl_modules.id', 'mdl_course_modules.id', 'mdl_course_modules.instance', 'mdl_assign.name')
            // ->where('mdl_modules.name', 'assign')
            // ->groupBy('mdl_assign.id', 'mdl_modules.id', 'mdl_course_modules.id', 'mdl_course_modules.instance', 'mdl_assign.name');