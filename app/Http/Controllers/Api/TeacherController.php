<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
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
            $arr_need_task = [];
            if ($st->teacher_course_id != null) {
                $n_task = NeedsTask::where('subject_id', $st->id)->get();
                foreach ($n_task as $nt) {
                    $arr_need_task[] = $nt->task_id;
                }
            }
            $arr_subjects[] = [
                'id' => $st->id,
                'name' => $subject->name,
                'number_course' => $subject->number_course,
                'id_teacher_course' => $st->teacher_course_id,
                'short_name' => $group->short_name,
                'need_task' => $arr_need_task
            ];
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
        $teacher = $request->user();
        if ($teacher->role_id != 3) {
            return response(
                ['massage' => 'ограничены права доступа'],
                500
            );
        }
        $r_subjects = $request->input('subjects');

        foreach ($r_subjects as $value) {
            $t_subject = SubjectTeacher::where([['id', '=', $value['id_subject_teacher']], ['teacher_id', '=', $teacher->id]])->get()->first();
            $t_subject->teacher_course_id = $value['id_teacher_course'];
            $t_subject->save();

            foreach ($value['need_task']['add'] as $task) {
                if (NeedsTask::where('task_id', $task)->get()->count() == 0) {
                    $n_task = new NeedsTask;
                    $n_task->subject_id = $t_subject->id;
                    $n_task->task_id = $task;
                    $n_task->save();
                }
            }
            foreach ($value['need_task']['delete'] as $task) {
                $n_task = NeedsTask::where('task_id', $task);
                if ($n_task->get() != null) {
                    $n_task->delete();
                }
            }
        }


        return response([
            'response' => "task added to subject"
        ], 200);
    }
}
