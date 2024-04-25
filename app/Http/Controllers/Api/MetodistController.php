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
    public static function get($request)
    {
        $user = $request->user();
        if ($user->role_id != 4) { 
            return response(
                ['massage' => 'ограничены права доступа'],
                500
            ); 
        }
        return get_groups($user->id); 
        // return get_groups($user->id);
        //return(json_encode($data, JSON_UNESCAPED_UNICODE));
        // return $data;
        // return json_encode(User::with(
        //     'getMetodistsGroups.getSubjects.getSubjectTeachers.getTeacher',
        //     'getMetodistsGroups.getSubjects.getSubjectTeachers.getTeacherCourse.getCourse',
        //     'getMetodistsGroups.getSubjects.getSubjectTeachers.getNeedTask.getTask',
        // )->where('id', $user->id)->get(), JSON_UNESCAPED_UNICODE);
    }

    public static function get_metodist_groups($request){
        $user_id = $request->get('metodist');
        return get_groups($user_id);
    }

    public static function post($request)
    {
        $user = $request->user();
        if ($user->role_id != 4) {
            return response(
                ['massage' => 'ограничены права доступа'],
                500
            );
        }

        $subjects = $request->input('subjects');

        foreach ($subjects as $value) {
            $check = $user->join('groups', 'users.id', '=', 'metodist_id')
                ->join('subject', 'groups.id', '=', 'group_id')
                ->join('subject_teacher', 'subject.id', '=', 'subject_id')
                ->where([
                    ['groups.id', $value['group_id']],
                    ['subject.id', $value['subject_id']],
                    ['subject_teacher.id', $value['subject_teacher_id']],
                ])->get()->first();
            // return $check;
            if ($check->count() != 0) {
                $subject = Subject::find($value['subject_id']);
                $subject->subject_teacher_id = $value['subject_teacher_id'];
                $subject->save();
            } else {
                return response([
                    'response' => "No"
                ], 300);
            }
        }
        return response([
            'response' => "course added to subject"
        ], 200);
    }
}


function get_groups($user_id){
    $groups = Group::where('metodist_id', $user_id)->get();
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
        $arr_groups[] = [
            'id' => $g->id,
            'short_name' => $g->short_name,
            'year' => $g->year,
            'number' => $g->number,
            'subjects' => $arr_subjects
        ];
    }
    return $arr_groups;
}