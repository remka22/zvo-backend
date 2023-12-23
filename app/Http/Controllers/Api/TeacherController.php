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
    public static function get($request){
        $id_teacher = $request->get('id');
        $teacher = User::find($id_teacher);
        $data = array('data' => array('metainfo' => array(
                                                        'id' => $teacher->id, 
                                                        'fio' => $teacher->fio,
                                                        'email' => $teacher->email,
                                                    ), 
                                        'subjects' => array(),
                                        'courses' => array()));
        $s_teachers = SubjectTeacher::where('teacher_id', $teacher->id)->get();
        foreach ($s_teachers as $st){
            $subject = Subject::find($st->subject_id);
            $group = Group::find($subject->group_id);
            $data['data']['subjects'] += [$st->id => array(
                                                        'name' => $subject->name,
                                                        'number_course' => $subject->number_course,
                                                        'id_teacher_course' => $st->teacher_course_id,
                                                        'short_name' => $group->short_name
                                                        )];
            if ($st->teacher_course_id != null){
                $n_task = NeedsTask::where('subject_id', $st->id)->get();
                $arr_task = [];
                foreach ($n_task as $nt){
                    $arr_task[] = $nt->task_id;
                }
                $data['data']['subjects'][$st->id] += array('need_task' => $arr_task);
            }
        }
        $t_courses = TeacherCourse::where('user_id', $teacher->id)->get();
        foreach ($t_courses as $tc){
            $course = MoodleCourse::find($tc->course_id);
            $data['data']['courses'] += [$tc->id => array(
                                                        'name' => $course->name,
                                                        'id_link' => $course->link_id,
                                                        'tasks' => array()
                                                        )];
            $task = MoodleTask::where('course_id', $course->id)->get();
            foreach ($task as $t){
                $data['data']['courses'][$tc->id]['tasks'] += [$t->id => array(
                                                                                'name' => $t->name,
                                                                                'id_link' => $t->link_id,
                                                                                'type' => $t->type    
                                                                                )];
            }
        }
        return response([
            'response' => $data
        ], 200);
    }

    public static function post($request){
        $id_teacher = $request->input('id');
        $r_subjects = $request->input('subjects');

        foreach ($r_subjects as $key => $value){
            // $t_subject_id[] = $key;
            // $data[$key][] = $value;
            $t_subject = SubjectTeacher::find($key);
            $t_subject->teacher_course_id = $value['id_teacher_course'];
            $t_subject->save();

            foreach ($value['need_task'] as $task){
                 $n_task = new NeedsTask;
                 $n_task->subject_id = $t_subject->id;
                 $n_task->task_id = $task;
                 $n_task->save();
            }
        }


        return response([
            'response' => "course added to subject"
        ], 200);
    }
    
}
