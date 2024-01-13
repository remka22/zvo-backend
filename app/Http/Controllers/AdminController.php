<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public static function add_director($request){
        $teacher = $request->user();
        if ($teacher->role_id != 1) {
            return response(
                ['massage' => 'Ограничены права доступа'],
                500
            );
        }
        User::create([
            'role_id' => 5,
            'fio' => $request->fio,
            'email' => $request->login,
            'password' => bcrypt($request->password), //need generate
            'mira_id' => $request->mira_id
        ]);
        return response([
            'message' => 'Director added done!'
        ], 200);
    }

    public static function add_metodist($request){
        $teacher = $request->user();
        if ($teacher->role_id != 1) {
            return response(
                ['massage' => 'Ограничены права доступа'],
                500
            );
        }
        $metodists = $request->metodists;
        foreach ($metodists as $met){
            User::create([
                'role_id' => 3,
                'fio' => $met->fio,
                'email' => $met->login,
                'password' => bcrypt($met->password), //need generate
                'mira_id' => $met->mira_id
            ]);
        }
        return response([
            'message' => 'Metodists added done!'
        ], 200);
    }
}
