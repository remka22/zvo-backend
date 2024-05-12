<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;
use App\Models\Notification;
use App\Models\Role;

class DirectorController extends Controller
{
    public static function get($request)
    {
        // $metodists = User::where('role_id', 4)->get();
        // $arr_metodists = [];
        // foreach ($metodists as $met) {
        //     $groups = Group::where('metodist_id', $met->id)->get();
        //     $arr_groups = [];
        //     foreach ($groups as $g) {
        //         $arr_groups[] = [
        //             'id' => $g->id,
        //             'short_name' => $g->short_name,
        //             'year' => $g->year,
        //             'number' => $g->number
        //         ];
        //     }
        //     $arr_metodists[] = [
        //         'fio' => $met->fio,
        //         'id' => $met->id,
        //         'groups' => $arr_groups
        //     ];
        // }
        // $arr_groups = [];
        // $groups = Group::all();
        // foreach ($groups as $g) {
        //     if ($g->metodist_id == null) {
        //         $arr_groups[] = [
        //             'id' => $g->id,
        //             'short_name' => $g->short_name,
        //             'year' => $g->year,
        //             'number' => $g->number
        //         ];
        //     }
        // }
        // $notif = Notification::where([['user_rec_id', '=', $director->id], ['is_read', '=', false]])->get();
        // $arr_notif = [];
        // foreach ($notif as $n) {
        //     $user_send = User::find($n->user_send_id);
        //     $arr_notif[] = [
        //         'id' => $n->id,
        //         'user_send_id' => $user_send->id,
        //         'user_rec_fio' => $user_send->fio,
        //         'content' => $n->content,
        //         'send_date' => $n->send_date,
        //         'is_read' => $n->is_read
        //     ];
        // }
        // $data = array('data' => array(
        //     'user' => array(
        //         'id' => $director->id,
        //         'id_role' => $director->role_id,
        //         'fio' => $director->fio,
        //         'email' => $director->email,
        //     ),
        //     'metodists' => $arr_metodists,
        //     'groups' => $arr_groups,
        //     'notification' => $arr_notif
        // ));
        // //return($data);
        // //return(json_encode($data, JSON_UNESCAPED_UNICODE));
        $role = Role::where('name', 'Методист')->get()->first();
        $metodists = User::where('role_id', $role->id)->with('groups')->get();
        $metodists_arr = [];
        foreach ($metodists as $m) {
            $prof_group = [];
            foreach ($m->groups as $g) {
                $prof_group[] = ['short_name' => explode('-', $g->short_name)[0]];
            }
            $metodists_arr[] = [
                'fio' => $m->fio,
                'id' => $m->id,
                'groups' => array_values(array_unique($prof_group, SORT_REGULAR)),
            ];
        }
        $groups = Group::where('metodist_id', null)->get();
        $prof_group = [];
        foreach ($groups as $g) {
            $prof_group[] = ['short_name' => explode('-', $g->short_name)[0]];
        }


        $data = [];
        if ($role != null) {
            $data["metodists"] = $metodists_arr;
            $data["groups"] = array_values(array_unique($prof_group, SORT_REGULAR));
        }
        return $data;
    }

    public static function post($request)
    {
        $data = $request->input('data');
        $metodist = User::find($data['metodist']['id']);
        $groups = Group::where('metodist_id', $metodist->id)->get();
            foreach ($groups as $g) {
                $g->metodist_id = null;
                $g->save();
            }
        foreach ($data['metodist']['groups'] as $value) {
            $value['short_name'];
            $groups = Group::where('short_name', 'like', '%'.$value['short_name'].'%')->get();
            foreach ($groups as $g) {
                $g->metodist_id = $metodist->id;
                $g->save();
            }
        }
        return response([
            'response' => "Нагрузка по методистам указанным методистам зафиксированна"
        ], 200);
    }
}
