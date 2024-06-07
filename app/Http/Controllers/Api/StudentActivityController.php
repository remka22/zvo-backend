<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Moodle\MdlAssignGrades;
use App\Models\Moodle\MdlCourseModules;
use App\Models\Moodle\MdlQuizGrades;
use App\Models\NeedsTask;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\TeacherCourse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentActivityController extends Controller
{
    public static function get(Request $request)
    {
        $user = auth()->user();
        $group_id = $request->get("group_id");
        $subject_id = $request->get("subject_id");
        if ($group_id && $subject_id) {
            if ($subject = Subject::where([["group_id", $group_id], ['id', $subject_id]])->first()) {
                $arr_id_tasks = [];
                if ($subject->subject_teacher_id) {
                    $subject_teacher = SubjectTeacher::find($subject->subject_teacher_id);
                    $teacher = User::find($subject_teacher->teacher_id);
                    $t_course = TeacherCourse::where('id', $subject_teacher->teacher_course_id)->with('course')->first();
                    if ($t_course) {
                        $task = NeedsTask::where('subject_id', $subject_teacher->id)->with('task')->get();
                        $arr_tasks = [];
                        foreach ($task as $t) {
                            $arr_tasks[] = [
                                'name' => $t->task->name,
                                'id' => $t->task->link_id,
                                'type' => $t->task->type
                            ];
                            $arr_id_tasks[] = $t->task->link_id;
                        }
                    }
                }
                $students = User::where("group_id", $group_id)->get();
                $arr_students = [];
                foreach ($students as $student) {
                    // $grade_assign = [];
                    // $grade_quiz = [];
                    if ($arr_id_tasks != []) {
                        // $grade_assign = DB::connection('pgsql_moodle')->select("select mcm.id, mm.name as type, mag.grade from mdl_course_modules as mcm
                        //                                                 inner join mdl_assign_grades as mag on mag.assignment = mcm.instance
                        //                                                 inner join mdl_modules as mm on mm.id = mcm.module
                        //                                                 where mcm.id in (" . implode(",", $arr_id_tasks) . ") and mag.userid = " . $student->moodle_id);
                        // $grade_quiz = DB::connection('pgsql_moodle')->select("select mcm.id, mm.name, mqg.grade/20 from mdl_course_modules as mcm
                        //                                                 inner join mdl_quiz_grades as mqg on mqg.quiz  = mcm.instance
                        //                                                 inner join mdl_modules as mm on mm.id = mcm.module
                        //                                                 where mcm.id in (" . implode(",", $arr_id_tasks) . ") and mqg.userid = " . $student->moodle_id);
                        $grads = DB::connection('pgsql_moodle')->select("select mcm.id, mgi.itemmodule as type , mgg.finalgrade as grade, mgg.rawgrademax as maxgrade, mgg.usermodified as userid
                                                                        from mdl_course_modules as mcm
                                                                        inner join mdl_grade_items as mgi on mgi.iteminstance = mcm.instance
                                                                        inner join mdl_grade_grades as mgg on mgg.itemid = mgi.id
                                                                        where mcm.id in (" . implode(",", $arr_id_tasks) . ") and mgg.userid = " . $student->moodle_id);
                    }
                    $arr_students[] = [
                        "moodle_id" => $student->moodle_id,
                        "fio" => $student->fio,
                        'isLogined' => $student->isLogined,
                        "grade" => $grads ?? [] //array_merge($grade_assign, $grade_quiz),
                    ];
                }


                // $mdl_course_modeles = MdlCourseModules::whereIn('id', $arr_id_tasks)->with('type')->get();
                // $arr_id_assigns = [];
                // $arr_id_quizs = [];
                // foreach ($mdl_course_modeles as $mcm) {
                //     if ($mcm->type->name == 'assign') {
                //         $arr_id_assigns[] = $mcm->instance;
                //     } elseif ($mcm->type->name == 'quiz') {
                //         $arr_id_quizs[] = $mcm->instance;
                //     }
                // }

                // foreach ($students as $student) {
                //     if ($m_task->type == 'assign') {
                //         $c_modules = MdlCourseModules::whereIn('id', $arr_id_tasks)->with('assign')->first();
                //         if ($grade = MdlAssignGrades::where([['userid', '=', $user->moodle_id], ['assignment', '=', $c_modules->assign->id]])->first())
                //             $grade = $grade->grade / 20;
                //     } else if ($m_task->type == 'quiz') {
                //         $c_modules = MdlCourseModules::where('id', $m_task->link_id)->with('quiz')->first();
                //         if ($grade = MdlQuizGrades::where([['userid', '=', $user->moodle_id], ['assignment', '=', $c_modules->quiz->id]])->first())
                //             $grade = $grade->grade;
                //     }
                // }
                return $data = [
                    'subject' => $subject->name,
                    'course' => $t_course->course->name ?? null,
                    'teacher' => $teacher->fio ?? null,
                    'comment' => $subject_teacher->comment ?? "",
                    'tasks' => $arr_tasks ?? [],
                    'students' => $arr_students,

                ];
                // $students = User::where("group_id", $group_id)->get();
            }
        } else {
            if ($user->hasRole("Методист")) {
                $groups = Group::where("metodist_id", $user->id)->with('subjects')->get();
            } 
            else if (!$user->hasRole("Студент")) {
                $groups = Group::with('subjects')->get();
            }
            

            return $groups;
        }
    }
}
