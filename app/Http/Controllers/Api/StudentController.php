<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;

class StudentController extends Controller
{
    public static function get(){
        // $data = DB::connection('pgsql')->select("
        //     select count(id) 
        //     from public.mdl_user 
        //     where lastname = '$last_name' and firstname = '$first_name';
        // ")
        
        $str_group = 'АСУбз-21-1';
        $data = array($str_group => array('metainfo' => array(), 'subjects' => array()));
        $group = Group::where('short_name', '=', $str_group)->get()->first();
        $data[$str_group]['metainfo'] = array(
            'metodist' => $group->metodist_id,
            'year' => $group->year,
            'number' => $group->number
        );
        $subjects = Subject::where('group_id', $group->id)->get();
        foreach ($subjects as $sub){
            //$subject_teachers
            $data[$str_group]['subjects'] += [$sub->name => array()];
           // array_merge($data[$str_group]['subjects'], [$sub->name => array()]);
        }
        // json_encode($data, JSON_UNESCAPED_UNICODE)
        dd($data);

    }
}
