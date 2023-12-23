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

class StudentController extends Controller
{
    public static function get(){
        // $data = DB::connection('pgsql')->select("
        //     select count(id) 
        //     from public.mdl_user 
        //     where lastname = '$last_name' and firstname = '$first_name';
        // ")
        
        $str_group = 'ХТбз-23-1';
        $data = array($str_group => array('metainfo' => array(), 'subjects' => array()));
        $group = Group::where('short_name', '=', $str_group)->get()->first();
        $data[$str_group]['metainfo'] = array(
            'metodist' => $group->metodist_id,
            'year' => $group->year,
            'number' => $group->number
        );
        $subjects = Subject::where('group_id', $group->id)->get();
        foreach ($subjects as $sub){
            if($sub->subject_teacher_id != null){
                $t_subject = SubjectTeacher::find($sub->subject_teacher_id);
                $t_course = TeacherCourse::find($t_subject->teacher_course_id);
                $course = MoodleCourse::find($t_course->course_id);
                $data[$str_group]['subjects'] += [$sub->name => array(
                                                                        'name' => $course->name,
                                                                        'id_link' => $course->link_id,
                                                                        'need_task' => array())];
                $need_task = NeedsTask::where('subject_id', $t_subject->id)->get();
                foreach ($need_task as $nt){
                    $m_task = MoodleTask::find($nt->task_id);
                    $data[$str_group]['subjects'][$sub->name]['need_task'] += array(
                                                                                     'name' => $m_task->name,
                                                                                      'id_link' => $m_task->link_id,
                                                                                      'type' => $m_task->type);
                }
            }
            else{
                $data[$str_group]['subjects'] += [$sub->name => array()];
            }
        }
        // json_encode($data, JSON_UNESCAPED_UNICODE)
        //dd($data);
        return(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
