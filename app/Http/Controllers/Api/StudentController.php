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
use App\Models\Student;

class StudentController extends Controller
{
    public static function get($request)
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)->get()->first();
        $group = Group::where('id', $student->group_id)->get()->first();
        if ($group->metodist_id != null) {
            $m = User::find($group->metodist_id);
            $metodist_id = $m->id;
            $metodist_fio = $m->fio;
            $metodist_email = $m->email;
        } else {
            $metodist_id = null;
            $metodist_fio = null;
            $metodist_email = null;
        }
        $arr_group = [
            'name' => $group->id,
            'metodist_id' => $metodist_id,
            'metodist_fio' => $metodist_fio,
            'metodist_email' => $metodist_email,
            'year' => $group->year,
            'number' => $group->number
        ];
        $subjects = Subject::where('group_id', $group->id)->get();
        $arr_sabjects = [];
        foreach ($subjects as $sub) {
            if ($sub->subject_teacher_id != null) {
                $t_subject = SubjectTeacher::find($sub->subject_teacher_id);
                $t_course = TeacherCourse::find($t_subject->teacher_course_id);
                $course = MoodleCourse::find($t_course->course_id);
                $need_task = NeedsTask::where('subject_id', $t_subject->id)->get();
                $arr_need_task = [];
                foreach ($need_task as $nt) {
                    $m_task = MoodleTask::find($nt->task_id);
                    $arr_need_task[] = [
                        'name' => $m_task->name,
                        'id_link' => $m_task->link_id,
                        'type' => $m_task->type
                    ];
                }
                $arr_sabjects[] = [
                    'name' => $sub->name,
                    'number_course' => $sub->number_course,
                    'name_course' => $course->name,
                    'id_link' => $course->link_id,
                    'need_task' => $arr_need_task
                ];
            } else {
                $arr_sabjects[] = ['name' => $sub->name, 'number_course' => $sub->number_course];
            }
        }
        $data = [
            'group' => $arr_group,
            'subjects' => $arr_sabjects
        ];
        // json_encode($data, JSON_UNESCAPED_UNICODE)
        //dd($data);
        return $data; //(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
