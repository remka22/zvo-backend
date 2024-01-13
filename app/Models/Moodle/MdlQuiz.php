<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlQuiz extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_quiz";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [
        "course",
        "intro",
        "introformat",
        "timeopen",
        "timeclose",
        "timelimit",
        "overduehandling",
        "graceperiod",
        "preferredbehaviour",
        "canredoquestions",
        "attempts",
        "attemptonlast",
        "grademethod",
        "decimalpoints",
        "questiondecimalpoints",
        "reviewattempt",
        "reviewcorrectness",
        "reviewmarks",
        "reviewspecificfeedback",
        "reviewgeneralfeedback",
        "reviewrightanswer",
        "reviewoverallfeedback",
        "questionsperpage",
        "navmethod",
        "shuffleanswers",
        "sumgrades",
        "grade",
        "timecreated",
        "timemodified",
        "password",
        "subnet",
        "browsersecurity",
        "delay1",
        "delay2",
        "showuserpicture",
        "showblocks",
        "completionattemptsexhausted",
        "completionpass",
        "allowofflineattempts",
    ];
}
