<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlCourseModules extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_course_modules";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [
        'section',
        'idnumber',
        'added',
        'score',
        'indent',
        'visible',
        'visibleoncoursepage',
        'visibleold',
        'groupmode',
        'groupingid',
        'completion',
        'completiongradeitemnumber',
        'completionview',
        'completionexpected',
        'showdescription',
        'availability',
        'deletioninprogress',
    ];

    public function assign()
    {
        return $this->hasOne('App\Models\Moodle\MdlAssign', 'id', 'instance');
    }
    public function quiz()
    {
        return $this->hasOne('App\Models\Moodle\MdlQuiz', 'id', 'instance');
    }
    public function type()
    {
        return $this->hasOne('App\Models\Moodle\MdlModules', 'id', 'module');
    }
}
