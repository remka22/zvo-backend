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
        $user = $request->user();
        if ($user->role_id != 5) {
            return response(
                ['massage' => 'ограничены права доступа'],
                500
            );
        }
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
        $data = [];
        if ($role != null) {
            $data["metodists"] = User::where('role_id', $role->id)->with('getMetodistsGroups')->get();
            $data["groups"] = Group::where('metodist_id', null)->get();
        }
        return $data;
    }

    public static function post($request)
    {
        $user = $request->user();
        if ($user->role_id != 5) {
            return response(
                ['massage' => 'ограничены права доступа'],
                500
            );
        }
        $data = $request->input('metodists');
        foreach ($data as $value) {
            foreach ($value['add'] as $va) {
                $group = Group::find($va);
                $group->metodist_id = $value['id'];
                $group->save();
            }
            foreach ($value['delete'] as $vd) {
                $group = Group::find($vd);
                if ($group->metodist_id == $value['id']) {
                    $group->metodist_id = null;
                    $group->save();
                }
            }
        }
        return response([
            'response' => "Нагрузка по методистам указанным методистам зафиксированна"
        ], 200);
    }
}
