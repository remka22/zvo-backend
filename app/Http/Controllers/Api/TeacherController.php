<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Moodle\MdlCourse;
use App\Models\Moodle\MdlUser;
use App\Models\Moodle\MdlUserEnrolments;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;
use App\Models\TeacherCourse;
use App\Models\MoodleCourse;
use App\Models\MoodleTask;
use App\Models\NeedsTask;

class TeacherController extends Controller
{
    public static function get($request)
    {
        $teacher = $request->user();
        if ($teacher->role_id != 3) {
            return response(
                ['massage' => 'ограничены права доступа'],
                500
            );
        }
        $s_teachers = SubjectTeacher::where('teacher_id', $teacher->id)->get();
        $arr_subjects = [];
        foreach ($s_teachers as $st) {
            $subject = Subject::find($st->subject_id);
            $group = Group::find($subject->group_id);

            $year = $group->year + $subject->number_course;
            if (date("m") > 9) {
                $year--;
            }
            if (date("Y") == ($year + 2000)) {
                
                $arr_need_task = [];
                $t_course = null;
                if ($st->teacher_course_id != null) {
                    $n_task = NeedsTask::where('subject_id', $st->id)->get();
                    foreach ($n_task as $nt) {
                        $task = MoodleTask::find($nt->task_id);
                        $arr_need_task[] = [
                            'id' => $nt->task_id,
                            'name' => $task->name,
                            'type' => $task->type
                        ];
                    }
                    $m_course_id = TeacherCourse::find($st->teacher_course_id)->course_id;
                    $t_course = MoodleCourse::find($m_course_id);
                }

                $arr_subjects[] = [
                    'id' => $st->id,
                    'name' => $subject->name,
                    'number_course' => $subject->number_course,
                    'id_teacher_course' => $st->teacher_course_id,
                    'course_name' => $t_course->name ?? null,
                    'id_link' => $t_course->link_id ?? null,
                    'short_name' => $group->short_name,
                    'comment' => $st->comment,
                    'need_task' => $arr_need_task
                ];
            }
        }
        $t_courses = TeacherCourse::where('user_id', $teacher->id)->get();
        $arr_courses = [];
        foreach ($t_courses as $tc) {
            $course = MoodleCourse::find($tc->course_id);
            $task = MoodleTask::where('course_id', $course->id)->get();
            $arr_task = [];
            foreach ($task as $t) {
                $arr_task[] = [
                    'id' => $t->id,
                    'name' => $t->name,
                    'id_link' => $t->link_id,
                    'type' => $t->type
                ];
            }
            $arr_courses[] = [
                'id' => $tc->id,
                'name' => $course->name,
                'id_link' => $course->link_id,
                'tasks' => $arr_task
            ];
        }
        $data = array('data' => array(
            'user' => array(
                'id' => $teacher->id,
                'fio' => $teacher->fio,
                'email' => $teacher->email,
            ),
            'subjects' => $arr_subjects,
            'courses' => $arr_courses
        ));
        return $data;
        // response([
        //     'response' => $data
        // ], 200);
    }

    public static function post($request)
    {
        // return response([
        //     'otvet' => $request->input('data')
        // ], 200);
        $teacher = $request->user();
        if ($teacher->role_id != 3) {
            return response(
                ['massage' => 'ограничены права доступа'],
                500
            );
        }
        $data = $request->input('data');

        // foreach ($r_subjects as $value) {
        if ($t_subject = SubjectTeacher::where([['id', '=', $data['subject']['id']], ['teacher_id', '=', $teacher->id]])->get()->first()) {
            if ($t_course = TeacherCourse::where([['id', '=', $data['course']['id']], ['user_id', '=', $teacher->id]])->get()->first()) {
                if ($m_course = MoodleCourse::find($t_course->course_id)) {
                    $nt_id_req = [];
                    foreach ($data['tasks'] as $task) {
                        $nt_id_req[] = $task['id'];
                    }
                    if ($m_tasks = MoodleTask::where('course_id', $m_course->id)->whereIn('id', $nt_id_req)->get()) {
                        $nt_id_m = [];
                        foreach ($m_tasks as $task) {
                            $nt_id_m[] = $task->id;
                        }
                        NeedsTask::where('subject_id', $t_subject->id)->whereNotIn('task_id', $nt_id_m)->delete();
                        $n_tasks = NeedsTask::where('subject_id', $t_subject->id)->get();
                        $nt_id_n = [];
                        foreach ($n_tasks as $task) {
                            $nt_id_n[] = $task->task_id;
                        }
                        if ($n_tasks->count() < $m_tasks->count()) {
                            $nt_id_add = array_diff($nt_id_m, $nt_id_n);
                            foreach ($nt_id_add as $task) {
                                $nt = new NeedsTask();
                                $nt->subject_id = $t_subject->id;
                                $nt->task_id = $task;
                                $nt->save();
                            }
                        }
                        $t_subject->teacher_course_id = $t_course->id;
                        $t_subject->comment = $data['comment'];
                        $t_subject->save();
                    } else {
                        return response([
                            'response' => 'Задания не найдены, попробуйте позже'
                        ], 500);
                    }
                } else {
                    return response([
                        'response' => 'Курс не найден, попробуйте позже'
                    ], 500);
                }
            } else {
                return response([
                    'response' => 'Предмент не найден, попробуйте позже'
                ], 500);
            }




            // $t_subject->teacher_course_id = $t_course->id;
            // $t_subject->save();

            // $nt_id = [];
            // foreach ($data['tasks'] as $task) {
            //     $nt_id +=  $task['id'];
            // }
            // $m_tasks = MoodleTask::where('coure_id', $m_course->id)->whereIn('id', $nt_id);
            // return response([
            //         'otvet' => $m_tasks
            //     ], 200);


            // if (NeedsTask::where('task_id', $task)->get()->count() == 0) {
            //     $n_task = new NeedsTask;
            //     $n_task->subject_id = $t_subject->id;
            //     $n_task->task_id = $task;
            //     $n_task->save();
            // }
            // }
            // foreach ($value['need_task']['delete'] as $task) {
            //     $n_task = NeedsTask::where('task_id', $task);
            //     if ($n_task->get() != null) {
            //         $n_task->delete();
            //     }
            // }
        }


        return response([
            'response' => "Изменения успешно сохранены"
        ], 200);
    }

    public static function update_courses($request)
    {
        $teacher = $request->user();
        if ($teacher->role_id != 3) {
            return response(
                ['massage' => 'ограничены права доступа'],
                500
            );
        }



        $t_course = TeacherCourse::where('user_id', '=', $teacher->id)->get();
        $course_id_arr = [];
        $course_name_arr = [];
        $assign_id_arr = [];
        $quiz_id_arr = [];
        foreach ($t_course as $tc) {
            $m_course = MoodleCourse::where('id', $tc->course_id)->get()->first();
            $course_id_arr[] = $m_course->link_id;
            $course_name_arr[] = $m_course->name;
            $m_task = MoodleTask::where('course_id', $m_course->id)->get();
            foreach ($m_task as $mt) {
                if ($mt->type == 'assign')
                    $assign_id_arr[] = $mt->link_id;

                if ($mt->type == 'quiz')
                    $quiz_id_arr[] = $mt->link_id;
            }
        }
        TeacherController::update_task($course_id_arr, $assign_id_arr, $quiz_id_arr);

        $mdl_courses = MdlCourse::whereIn('id', $course_id_arr)->get();
        foreach ($mdl_courses as $mdl_course) {
            if (!in_array($mdl_course->fullname, $course_name_arr)) {
                $mcourse = MoodleCourse::where('link_id', $mdl_course->id)->get()->first();
                $mcourse->name = $mdl_course->fullname;
                $mcourse->save();
            }
        }

        // dd($course_id_arr);
        $moodle_courses = MdlUserEnrolments::join('mdl_enrol', 'enrolid', '=', 'mdl_enrol.id')
            ->where('userid', 10)->whereNotIn('courseid', $course_id_arr)
            ->with(
                'getEnrole.getCourse.assign.assign',
                'getEnrole.getCourse.assign.type',
                'getEnrole.getCourse.quiz.quiz',
                'getEnrole.getCourse.quiz.type',
            )
            ->get();

        // dd(json_decode($moodle_courses, JSON_UNESCAPED_UNICODE));
        foreach ($moodle_courses as $mc) {
            // dd($mc);
            $course = $mc->getEnrole->getCourse;
            $m_course = MoodleCourse::where('link_id', $course->id)->get()->first();
            // dd($course);
            if ($m_course == null) {
                $m_course = new MoodleCourse;
                $m_course->link_id = $course->id;
                $m_course->name = $course->fullname;
                $m_course->save();
                // dd($course);    
                foreach ($course->assign as $ca) {
                    $m_task = new MoodleTask;
                    $m_task->link_id = $ca->id;
                    $m_task->name = $ca->assign->name;
                    $m_task->type = $ca->type->name;
                    $m_task->course_id = $m_course->id;
                    $m_task->save();
                }
                foreach ($course->quiz as $cq) {
                    $m_task = new MoodleTask;
                    $m_task->link_id = $cq->id;
                    $m_task->name = $cq->quiz->name;
                    $m_task->type = $cq->type->name;
                    $m_task->course_id = $m_course->id;
                    $m_task->save();
                }
            }

            $t_course = new TeacherCourse;
            $t_course->user_id = $teacher->id;
            $t_course->course_id = $m_course->id;
            $t_course->save();
        }
        return response(
            ['massage' => 'Courses updated!'],
            200
        );
    }

    public static function update_task($course_id_arr, $assign_id_arr, $quiz_id_arr)
    {
        // $teacher = $request->user();

        // $t_course = TeacherCourse::where('user_id', '=', $teacher->id)->get();
        // $course_id_arr = [];
        // $assign_id_arr = [];
        // $quiz_id_arr = [];
        // foreach ($t_course as $tc) {
        //     $m_course = MoodleCourse::where('id', $tc->course_id)->get()->first();
        //     $course_id_arr[] = $m_course->link_id;
        //     $m_task = MoodleTask::where('course_id', $m_course->id)->get();
        //     foreach ($m_task as $mt) {
        //         if ($mt->type == 'assign')
        //             $assign_id_arr[] = $mt->link_id;

        //         if ($mt->type == 'quiz')
        //             $quiz_id_arr[] = $mt->link_id;
        //     }
        // }

        // $course_task = MdlCourse::whereIn('id', $course_id_arr)->get();
        // // dd(json_decode($course_task, JSON_UNESCAPED_UNICODE));
        // foreach ($course_task as $ct) {
        //     $course = MoodleCourse::where('link_id', $ct->id)->get()->first();
        //     $new_assign = $ct->getNewAssign($assign_id_arr)->get();
        //     dd(json_decode($new_assign, JSON_UNESCAPED_UNICODE));
        //     foreach ($new_assign as $na) {
        //         $m_task = new MoodleTask;
        //         $m_task->link_id = $na->id;
        //         $m_task->name = $na->name;
        //         $m_task->type = 'assign';
        //         $m_task->course_id = $course->id;
        //         $m_task->save();
        //     }
        //     $new_quiz = $ct->getNewQuiz($quiz_id_arr)->get();
        //     foreach ($new_quiz as $nq) {
        //         $m_task = new MoodleTask;
        //         $m_task->link_id = $nq->id;
        //         $m_task->name = $nq->name;
        //         $m_task->type = 'quiz';
        //         $m_task->course_id = $course->id;
        //         $m_task->save();
        //     }
        // }
        $moodle_courses = MdlUserEnrolments::join('mdl_enrol', 'enrolid', '=', 'mdl_enrol.id')
            ->where('userid', 10)->whereIn('courseid', $course_id_arr)
            ->with(
                'getEnrole.getCourse.assign.assign',
                'getEnrole.getCourse.assign.type',
                'getEnrole.getCourse.quiz.quiz',
                'getEnrole.getCourse.quiz.type',
            )
            ->get();

        // dd(json_decode($moodle_courses, JSON_UNESCAPED_UNICODE));
        foreach ($moodle_courses as $mc) {
            // dd($mc);
            $course = $mc->getEnrole->getCourse;
            $m_course = MoodleCourse::where('link_id', $course->id)->get()->first();
            // dd($course);
            if ($m_course != null) {
                foreach ($course->assign as $ca) {
                    if (!in_array($ca->id, $assign_id_arr)) {
                        $m_task = new MoodleTask;
                        $m_task->link_id = $ca->id;
                        $m_task->name = $ca->assign->name;
                        $m_task->type = $ca->type->name;
                        $m_task->course_id = $m_course->id;
                        $m_task->save();
                    }
                }
                foreach ($course->quiz as $cq) {
                    if (!in_array($cq->id, $quiz_id_arr)) {
                        $m_task = new MoodleTask;
                        $m_task->link_id = $cq->id;
                        $m_task->name = $cq->quiz->name;
                        $m_task->type = $cq->type->name;
                        $m_task->course_id = $m_course->id;
                        $m_task->save();
                    }
                }
            }
        }

        // dd(json_decode($course_task, JSON_UNESCAPED_UNICODE));
    }
}
