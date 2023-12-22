<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;

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
                }
            }
        }
        
        //return($data);
        //return(json_encode($data, JSON_UNESCAPED_UNICODE));
        return response([
            'response' => $data
        ], 200);
    }
}
