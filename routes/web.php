<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\MetodistController;
use App\Http\Controllers\Api\DirectorController;
use App\Http\Controllers\TeacherWorkloadController;
use App\Models\Group;
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
use App\Models\Student;
use App\Models\TeacherCourse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    // $groups = Group::all();
    // // $mdl_student = [];
    // foreach ($groups as $group) {
    //     $mdl_student = DB::connection('pgsql_moodle')->select("SELECT mdl_user.id, mdl_user.firstname, mdl_user.lastname,  mdl_user_info_data.data
    //                                                             FROM mdl_user_info_data
    //                                                             INNER join mdl_user ON mdl_user.id = mdl_user_info_data.userid
    //                                                             INNER JOIN mdl_user_info_field ON mdl_user_info_data.fieldid = mdl_user_info_field.id
    //                                                             WHERE mdl_user_info_field.shortname = 'cohort' AND mdl_user_info_data.data = '".$group->short_name."'");
    //     foreach ($mdl_student as $student) {
    //         $user = User::where([['fio', $student->lastname." ".$student->firstname], ['group_id', $group->id]])->first();
    //         if(!$user){
    //             $user = new User();
    //             $user->fio = $student->lastname." ".$student->firstname;
    //             $user->moodle_id = $student->id;
    //             $user->group_id = $group->id;
    //             $user->role_id = 2;
    //             $user->save();
    //         }
    //     }
    // }

    // }
    // $mdl_student = MdlUser::join('mdl_user_info_data', 'mdl_user.id', '=', 'userid')
    //     ->join('mdl_user_info_field', 'mdl_user_info_data.fieldid', '=', 'mdl_user_info_field.id')
    //     ->where([
    //         ['mdl_user_info_field.shortname', '=', 'cohort'],
    //         ['mdl_user_info_data.data', '=', "НБз-20-1"],
    //     ])->get();

    // $mdl_student = DB::connection('pgsql_moodle')->select("SELECT mdl_user.id, mdl_user.firstname, mdl_user.lastname,  mdl_user_info_data.data
    //                                                             FROM mdl_user_info_data
    //                                                             INNER join mdl_user ON mdl_user.id = mdl_user_info_data.userid
    //                                                             INNER JOIN mdl_user_info_field ON mdl_user_info_data.fieldid = mdl_user_info_field.id
    //                                                             WHERE mdl_user_info_field.shortname = 'cohort' AND mdl_user_info_data.data = 'НБз-20-1'");
    // $mdl_student = DB::connection('pgsql_moodle')->select("SELECT mdl_user.id, mdl_user_info_data.data
    //                                                             FROM mdl_user_info_data
    //                                                             INNER join mdl_user ON mdl_user.id = mdl_user_info_data.userid
    //                                                             INNER JOIN mdl_user_info_field ON mdl_user_info_data.fieldid = mdl_user_info_field.id
    //                                                             WHERE mdl_user_info_field.shortname = 'cohort' AND mdl_user_info_data.data = 'НБз-20-1'
    //                                                             AND mdl_user.firstname = 'Студент3 Отчество3' AND mdl_user.lastname = 'Фамилия3'");
    // dd($mdl_student[0]->id);
    // $arr = [18162, 18164, 18165];
    // $arr2 = [12, 14];
    // dump($mdl_student = DB::connection('pgsql_moodle')->select("select mcm.id, mm.name, mag.grade from mdl_course_modules as mcm
    //                                                             inner join mdl_assign_grades as mag on mag.assignment = mcm.instance
    //                                                             inner join mdl_modules as mm on mm.id = mcm.module
    //  where mcm.id in (".implode(",", $arr).") and mag.userid = 607"));
    // dump(array_merge($arr, $arr2));    
    return view('test');                                               
    return "hell world";
});

Route::post('/', function (Request $request) {
    return TeacherWorkloadController::input_teacher_workload($request);
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
    $moodle_courses = MdlUserEnrolments:: //select('id')->get();
        join('mdl_enrol', 'enrolid', '=', 'mdl_enrol.id')
        ->where('userid', 10)->whereNotIn('courseid', $course_id_arr)
        ->with(
            ['getEnrole.getCourse.getNewAssign' => function ($query) {
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
