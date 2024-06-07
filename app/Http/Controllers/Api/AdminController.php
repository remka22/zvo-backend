<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\NeedsTask;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\TeacherCourse;
use PHPUnit\Metadata\Api\Groups;

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
    public static function users($request){
        $users = User::with('role','group')->get();
        
        return response([
            'users' => $users
        ], 200);
    }

    public static function roles($request){
        $roles = Role::all();
        $groups = Group::all();
        
        return response([
            'roles' => $roles,
            'groups' => $groups
        ], 200);
    }

    public static function user($request){
        $user_id = $request->get('id');
        $user = User::find($user_id);
        if ($user){
            $user->role();
            if($user->role->name == 'Студент'){
                $group = Group::find($user->group_id);
            }
            return response([
                'user' => $user,
                'group' => $group ?? null
            ], 200);
        }
        return response([
            'message' => 'Пользователь не найден'
        ], 200);
        
    }

    // id: userChange.value.id ?? false,
    // fio: userChange.value.f + " " + userChange.value.i + " " + userChange.value.o,
    // email: userChange.value.login,
    // password: userChange.value.password,
    // mira_id: userChange.value.mira_id,
    // moodle_id: userChange.value.moodle_id,
    // role_id: role.value.name,
    // group_id: group.value.id,
    public static function add_user($request){
        $PASSWORD_TOKEN = 'ul6fx5pnAWnmvW5CX7Z0';
        $id = $request->input('id');
        $fio = $request->input('fio');
        $email = $request->input('email') ?? null;
        $password = $request->input('password') ?? null;
        $mira_id = $request->input('mira_id') ?? null;
        $moodle_id = $request->input('moodle_id') ?? null;
        $role_id = $request->input('role_id');
        $group_id = $request->input('group_id') ?? null;

        if($id){
            $user = User::find($id);
            if(!$user){
                return response([
                    'message' => 'Пользователь не найден'
                ], 420);
            }
        }
        else{
            $user = new User();
        }

        $user->fio = $fio;
        $user->email = $email;
        $user->mira_id = $mira_id;
        $user->moodle_id = $moodle_id;
        $user->role_id = $role_id;
        $user->group_id = $group_id;

        if($role_id == 1){
            if($request->input('pass_check'))
            $user->password = bcrypt($password);
        }
        else{
            $user->password = bcrypt($PASSWORD_TOKEN);
        }

        $user->save();
        $user = User::with('role','group')->find($user->id);

        return response([
            'message' => 'Данные пользователя сохранены!',
            'user' => $user
        ], 200);
    }

    public static function delete_user($request){
        $id = $request->input('id');        
        $user = User::find($id);
        if(!$user){
            return response([
                'message' => 'Пользователь не найден'
            ], 420);
        }
        $role = Role::find($user->role_id);
        switch ($role->name) {
            case 'Преподаватель':
                teacher_delete($user->id);
                $user->delete();
                break;
            case 'Методист':
                notify_delete($user->id);
                metodist_delete($user->id);
                $user->delete();
                break;
            case 'Директор':
                notify_delete($user->id);
                teacher_delete($user->id);
                $user->delete();
                break;
            case 'Студент':
                $user->delete();
                break;
            case 'Администратор':
                notify_delete($user->id);
                $user->delete();
                break;
        }


        return response([
            'message' => 'Данные пользователя удалены!',
        ], 200);
    }

}

function teacher_delete($user_id){
    $s_t = SubjectTeacher::where('teacher_id', $user_id)->get();
    foreach ($s_t as $st){
        $s = Subject::where('subject_teacher_id', $st->id)->first();
        $s->subject_teacher_id = null;
        $s->save();
        $n_t = NeedsTask::where('subject_id', $st->id)->delete();
    }
    $s_t = SubjectTeacher::where('teacher_id', $user_id)->delete();
    $t_c = TeacherCourse::where('user_id', $user_id)->delete();
}

function metodist_delete($user_id){
    $g = Group::where('metodist_id', $user_id)->get();
    foreach($g as $group){
        $group->metodist_id = null;
        $group->save();
    }
}

function notify_delete($user_id){
    $n = Notification::where([['user_send_id', $user_id],['user_rec_id', $user_id]])->delete();
}