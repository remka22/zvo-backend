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

class MetodistController extends Controller
{
    public static function get($request){
        $id_metodist = $request->get('id');
        $metodist = User::find($id_metodist);
        $data = array('data' => array('metainfo' => array(
                                                            'id' => $metodist->id, 
                                                            'fio' => $metodist->fio,
                                                            'email' => $metodist->email,
                                                        ), 
                                        'groups' => array()));
        $groups = Group::where('metodist_id', $id_metodist)->get();
        foreach ($groups as $g){
            $data['data']['groups'] += [$g->id => array(
                                                        'short_name' => $g->short_name,
                                                        'year' => $g->year,
                                                        'number' => $g->number,
                                                        'subjects' => array()
                                                        )];
            $subject = Subject::where('group_id', $g->id)->get();
            foreach ($subject as $s){
                $data['data']['groups'][$g->id]['subjects'] += [$s->id => array('name' => $s->name,
                                                                                'id_teacher_subject' => $s->teacher_subject_id,
                                                                                'maby_subject' => array()
                                                                                )];
                $subject_teacher = SubjectTeacher::where('subject_id', $s->id)->get();
                foreach ($subject_teacher as $st){
                    $teacher = User::find($st->teacher_id);
                    $data['data']['groups'][$g->id]['subjects'][$s->id]['maby_subject'] += [$st->id => array(
                                                                                                            'teacher_id' => $teacher->id,
                                                                                                            'teacher_fio' => $teacher->fio,
                                                                                                            'id_teacher_course' => $st->teacher_course_id,
                                                                                                            'comment' => $st->comment,
                                                                                                            'course' => array(),
                                                                                                            'need_task' => array()
                                                                                                            )];
                    if ($st->teacher_course_id != null){
                        $t_course = TeacherCourse::find($st->teacher_course_id);
                        $course = MoodleCourse::find($t_course->course_id);
                        $data['data']['groups'][$g->id]['subjects'][$s->id]['maby_subject'][$st->id]['course'] += [$course->id => array(
                                                                                                                                    'name' => $course->name,
                                                                                                                                    'id_link' => $course->link_id,   
                                                                                                                                    )];
                        $task = NeedsTask::where('subject_id', $st->id)->get();
                        foreach ($task as $t){
                            $m_task = MoodleTask::find($t->task_id);
                            $data['data']['groups'][$g->id]['subjects'][$s->id]['maby_subject'][$st->id]['need_task'] += [$t->id => array(
                                                                                                                                        'name' => $m_task->name,
                                                                                                                                        'id_link' => $m_task->link_id,
                                                                                                                                        'type' => $m_task->type    
                                                                                                                                        )];
                        }   
                    } 
                }
            }
        }
        
        //return($data);
        //return(json_encode($data, JSON_UNESCAPED_UNICODE));
        return response([
            'response' => $data
        ], 200);
    }

    public static function post($request){
        $subjects = $request->input('subjects');

        foreach ($subjects as $key => $value){
            $subject = Subject::find($key);
            $subject->subject_teacher_id = $value;
            $subject->save();
        }


        return response([
            'response' => "course added to subject"
        ], 200);
    }
}
