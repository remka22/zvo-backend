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
use App\Models\NeedsTask;
use App\Models\MoodleTask;
use App\Models\Notification;


class MetodistController extends Controller
{
    // public static function get($request)
    // {
    //     if ($user_id = $request->get('metodist_id')) {
    //         return get_groups($user_id);
    //     } else {
    //         return get_groups($request->user()->id);
    //     }
    // }
    public static function get_groups($request)
    {
        if ($request->user()->hasRole("Директор")) {
            $user = User::find($request->get('metodist_id'));
        } else {
            $user = $request->user();
        }

        $groups = Group::where('metodist_id', $user->id)->get();
        $arr_groups = [];
        foreach ($groups as $g) {
            $subjects = Subject::where('group_id', $g->id)->get();
            $arr_subjects = [];
            foreach ($subjects as $s) {
                $subject_teacher = SubjectTeacher::where('subject_id', $s->id)->get();
                $arr_maby_subject = [];
                foreach ($subject_teacher as $st) {
                    $teacher = User::find($st->teacher_id);
                    $arr_course = [];
                    $arr_need_task = [];
                    if ($st->teacher_course_id != null) {
                        $t_course = TeacherCourse::find($st->teacher_course_id);
                        $course = MoodleCourse::find($t_course->course_id);
                        $arr_course = [
                            'id' => $course->id,
                            'name' => $course->name,
                            'id_link' => $course->link_id,
                        ];
                        $task = NeedsTask::where('subject_id', $st->id)->get();
                        foreach ($task as $t) {
                            $m_task = MoodleTask::find($t->task_id);
                            $arr_need_task[] = [
                                'id' => $t->id,
                                'name' => $m_task->name,
                                'id_link' => $m_task->link_id,
                                'type' => $m_task->type
                            ];
                        }
                    }
                    $arr_maby_subject[] = [
                        'id' => $st->id,
                        'teacher_id' => $teacher->id,
                        'teacher_fio' => $teacher->fio,
                        'id_teacher_course' => $st->teacher_course_id,
                        'comment' => $st->comment,
                        'new' => $st->new,
                        'course' => $arr_course,
                        'need_task' => $arr_need_task
                    ];
                }
                $arr_subjects[] = [
                    'id' => $s->id,
                    'name' => $s->name,
                    'id_teacher_subject' => $s->subject_teacher_id,
                    'maby_subject' => $arr_maby_subject
                ];
            }
            $arr_students = [];
            $students = User::where([['role_id', 2], ['group_id', $g->id]])->get();
            foreach ($students as $student) {
                $arr_students[] = [
                    'id' => $student->id,
                    'fio' => $student->fio,
                    'isLogined' => $student->isLogined,
                ];
            }
            $arr_groups[] = [
                'id' => $g->id,
                'short_name' => $g->short_name,
                'year' => $g->year,
                'number' => $g->number,
                'subjects' => $arr_subjects,
                'students' => $arr_students
            ];
        }
        return $arr_groups;
    }

    public static function get_subject_group($request)
    {
    }

    public static function post($request)
    {
        if ($user_id = $request->get('metodist_id')) {
            $user = User::find($user_id);
        } else {
            $user = $request->user();
        }

        $data = $request->input('data');
        $subject_teacher = SubjectTeacher::find($data['subject_teacher_id']);
        $subject = Subject::find($subject_teacher->subject_id);
        $group = Group::find($subject->group_id);
        if ($group->metodist_id == $user->id) {
            $subject->subject_teacher_id = $subject_teacher->id;
            $subject->save();
            set_show($subject->id);
            return response([
                'response' => "Изменения сохранены"
            ], 200);
        }
        return response([
            'response' => "Группа для которого предназначается этот предмет, не под вашем курированием"
        ], 300);
    }
}

function set_show($subject_id)
{
    $t_subject = SubjectTeacher::where('subject_id', $subject_id)->get();
    foreach ($t_subject as $t) {
        $t->new = false;
        $t->save();
    }
}
