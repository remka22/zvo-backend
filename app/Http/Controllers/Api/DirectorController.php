<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;

class DirectorController extends Controller
{
    public static function get($request){
        $id_director = $request->get('id');
        $director = User::find($id_director);
        $data = array('data' => array('metainfo' => array(
                                                        'id' => $director->id, 
                                                        'fio' => $director->fio,
                                                        'email' => $director->email,
                                                        ), 'metodists' => array(), 'groups' => array()));
        $metodists = User::where('role_id', 4)->get();
        foreach ($metodists as $met){
            $data['data']['metodists'] += [$met->id => array(
                                                                'fio' => $met->fio,
                                                                'groups' => array()
                                                            )];
            $groups = Group::where('metodist_id', $met->id)->get();
            foreach($groups as $g){
                $data['data']['metodists'][$met->id]['groups'] += [$g->id =>array(
                                                                        'short_name' => $g->short_name,
                                                                        'year' => $g->year,
                                                                        'number' => $g->number
                                                                        )];
            }
        }
        $groups = Group::query()->get();
        foreach ($groups as $g){
            if ($g->metodist_id == null)
            $data['data']['groups'] += [$g->id => array(
                                                                'short_name' => $g->short_name,
                                                                'year' => $g->year,
                                                                'number' => $g->number
                                                            )];
        }
        //return($data);
        return(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public static function post($request){
        $data = $request->input('metodists');

        foreach($data as $key => $value){
            foreach($value as $v){
                $group = Group::find($v);
                $group->metodist_id = $key;
                $group->save();
            }
        }

        return response([
            'response' => "ok"
        ], 200);
    }
}
