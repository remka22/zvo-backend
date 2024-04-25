<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\MetodistController;
use App\Http\Controllers\Api\DirectorController;
use App\Http\Controllers\TeacherWorkloadController;
use App\Models\Moodle\MdlAssign;
use App\Models\Moodle\MdlCourse;
use App\Models\Moodle\MdlEnrol;
use App\Models\Moodle\MdlQuiz;
use App\Models\Moodle\MdlUser;
use App\Models\Moodle\MdlUserEnrolments;
use App\Models\Moodle\MdlUserInfoField;
use App\Models\MoodleCourse;
use App\Models\MoodleTask;
use App\Models\Role;
use App\Models\TeacherCourse;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return "hell world";
});

Route::get('/login', function () {
    return LoginController::login_web();
});

Route::get('/twl', function () {
    return TeacherWorkloadController::test_input();
});


Route::get('/test', function () {
    $t_course = TeacherCourse::where('user_id', '=', 6)->get();
        $course_id_arr = [];
        $assign_id_arr = [];
        $quiz_id_arr = [];
        foreach ($t_course as $tc) {
            $m_course = MoodleCourse::where('id', $tc->course_id)->get()->first();
            $course_id_arr[] = $m_course->link_id;
            $m_task = MoodleTask::where('course_id', $m_course->id)->get();
            foreach ($m_task as $mt) {
                if ($mt->type == 'assign')
                    $assign_id_arr[] = $mt->link_id;

                if ($mt->type == 'quiz')
                    $quiz_id_arr[] = $mt->link_id;
            }
        }
        // TeacherController::update_task($course_id_arr, $assign_id_arr,$quiz_id_arr);



        // dd($course_id_arr);
        $moodle_courses = MdlUserEnrolments:://select('id')->get();
        join('mdl_enrol', 'enrolid', '=', 'mdl_enrol.id')
            ->where('userid', 10)->whereNotIn('courseid', $course_id_arr) 
            ->with(
                ['getEnrole.getCourse.getNewAssign' => function($query){
                    $query->whereNotIn('id', 125291);
                }]
            )
            ->get();
    dd(
        //  json_decode(
        //     MdlUserEnrolments::
        //     join('mdl_enrol', 'enrolid', '=', 'mdl_enrol.id')
        //     ->where('userid', 10)->whereNotIn('courseid', [])
        //     // ->getEnrole()
        // //     MdlUser::where('id', 10)
        // // ->with(['getUserEnrole.getEnrole' => function ($query) {
        // //     $query->where('idi', '=', 125291);
        // // }])
        // // ->with('getEnrole.getCourse')
        // // ->with('getUserEnrole')
        // ->with(
        //     'getEnrole.getCourse.getAssign.getAssign',
        //     'getEnrole.getCourse.getAssign.getType',
        //     'getEnrole.getCourse.getQuiz.getQuiz',
        //     'getEnrole.getCourse.getQuiz.getType',
        // )
        // ->get()
        
        $moodle_courses
        // , JSON_UNESCAPED_UNICODE)
    );
    // dd(json_decode(MdlUserInfoField::where('shortname', 'kafedra')->get()->first()->getKafedra(2)->get(), JSON_UNESCAPED_UNICODE));
    //     .getAssign.getAssign',
    // .getAssign.getType',
    // .getQuiz.getQuiz',
    // .getQuiz.getType'

});
