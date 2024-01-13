<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlAssignGrades extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_assign_grades";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [
        'id',
        'assignment',
        'userid',
        'attemptnumber',
    ];
}
