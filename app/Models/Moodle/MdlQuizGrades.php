<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlQuizGrades extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_quize";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [
        'id',
        'quiz',
        'userid',
        'timemodified',
    ];
}
