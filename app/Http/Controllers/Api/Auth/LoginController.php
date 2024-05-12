<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SignupRequest;
use App\Models\Campus;
use App\Models\Group;
use App\Models\Moodle\MdlUser;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function signup(SignupRequest $request)
    {
        $data = $request->validated();
        /** @var \App\Models\User $user */
        $user = User::create([
            'fio' => $data['name'],
            'role_id' => '1',
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;
        return response(compact('user', 'token'));
    }

    public static function check_auth()
    {
        if (Auth::check()) {
            $user = Auth::user();
        }
    }

    public static function login(Request $request)
    {
        $muser = MdlUser::where('username', $request->email)->first();

        $return = [];
        $return['mira_id'][0] = $muser->id;
        $return['last_name'] = $muser->lastname;
        $return['name'] = $muser->firstname;;
        $return['second_name'] = "";
        if (str_contains($muser->username, 's') === true) {
            $return['is_student'] = 1;
            $return['is_teacher'] = 0;
            $return['data_student']['nomz'] = $muser->id;
            $return['data_student']['grup'] = "НБз-20-1";
            $return['data_teacher'] = [];
        }
        elseif (str_contains($muser->username, 'd') === true) {
            $return['is_student'] = 0;
            $return['is_teacher'] = 1;
            $return['data_student'] = [];
            $return['data_teacher']['dep'] = 'Дирекция института заочно-вечернего обучения';
        }
        elseif (str_contains($muser->username, 'm') === true) {
            $return['is_student'] = 0;
            $return['is_teacher'] = 1;
            $return['data_student'] = [];
            $return['data_teacher']['dep'] = 'Дирекция института заочно-вечернего обучения';
        }
        elseif (str_contains($muser->username, 'p') === true) {
            $return['is_student'] = 0;
            $return['is_teacher'] = 1;
            $return['data_student'] = [];
            $return['data_teacher']['dep'] = 'хз';
        }
        $return['email'] = $muser->username;
        return auth($return);
    }

    public static function user($request) {
        return get_data_user($request->user());
    }

    // // public function login(LoginRequest $request)
    // public static function login(Request $request)
    // {
    //     if (User::where('email', $request->email)->get()->count() != 0) {
    //         if (!Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {
    //             return response([
    //                 'message' => 'Provided email or password is incorrect'
    //             ], 422);
    //         }
    //     } else {
    //         $campus_user = Campus::where('login', $request->email)->get();
    //         if ($campus_user->count() == 0) {
    //             $moodle_user = MdlUser::where('username', $request->email)->get();
    //             if ($moodle_user->count() == 1) {
    //                 $moodle_user = $moodle_user->first();
    //             } else {
    //                 $moodle_user = [];
    //             }

    //             $user = new User();
    //             $l = $moodle_user->lastname;
    //             $f = $moodle_user->firstname;
    //             $user->fio = "$f $l";
    //             $user->email = $request->email;
    //             $user->moodle_id = $moodle_user->id ?? null;
    //             $user->role_id = 2;
    //             $user->mira_id = null;
    //             $user->password = $moodle_user->password;
    //             $user->save();
    //             // return response([
    //             //     'message' => 'Undefind user'
    //             // ], 422);
    //         } else if ($campus_user->count() == 1) {
    //             $campus_user = $campus_user->first();
    //             $login = $campus_user->login;

    //             $moodle_user = MdlUser::where('email', $login)->get();
    //             if ($moodle_user->count() == 1) {
    //                 $moodle_user = $moodle_user->first();
    //             } else {
    //                 $moodle_user = [];
    //             }

    //             $user = new User();
    //             $l = $campus_user->last_name;
    //             $f = $campus_user->first_name;
    //             if ($campus_user->cohort == 'Преподаватель') {
    //                 //todo
    //             } else {
    //                 $user->fio = "$f $l";
    //                 $user->email = $campus_user->nomz;
    //                 $user->moodle_id = $moodle_user->id ?? null;
    //                 $user->role_id = 2;
    //                 $user->mira_id = $campus_user->miraid;
    //                 $user->password = bcrypt('tasar232');
    //                 $user->save();

    //                 $group = Group::where('short_name', $campus_user->cohort)->get();
    //                 if ($group->count() == 1) {
    //                     $group = $group->first();
    //                     $student = new Student();
    //                     $student->user_id = $user->id;
    //                     $student->group_id = $group->id;
    //                     $student->nomz = $campus_user->nomz;
    //                     $student->save();
    //                 } else {
    //                     return response([
    //                         'message' => 'Group student error, report administrator'
    //                     ], 422);
    //                 }
    //             }
    //         } else {
    //             return response([
    //                 'message' => 'Multiuser error, report administrator'
    //             ], 422);
    //         }
    //     }

    //     //$credentials = $request->validated();
    //     // if (!Auth::attempt($credentials, true)) {
    //     if (!Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {
    //         return response([
    //             'message' => 'Provided email or password is incorrect'
    //         ], 422);
    //     }

    //     /** @var \App\Models\User $user */
    //     $user = User::where('id', Auth::user()->id)->with('role')->get()->first();

    //     $accessToken = Auth::user()->createToken('access_token', [TokenAbility::ACCESS_API->value]);

    //     return response([
    //         'user' => $user,
    //         'token' => $accessToken->plainTextToken,
    //         // 'token_access_info' => $accessToken->accessToken,
    //         // 'refresh_token' => $refreshToken,
    //     ], 200);
    // }

    public function logout(Request $request)
    {
        return response([
            'message' => $request
        ], 204);
        /** @var \App\Models\User $user */
        $user = $request->user()->currentAccessToken()->delete();
        return response([
            'message' => 'Good bay'
        ], 204);
    }

    // public static function login_web()
    // {
    //     // dd(campus_auth());
    // }

    public static function campus_auth($request)
    {
        # переменные
        $return = false;

        $APP = [
            'ID' => '',
            'CODE' => ''
        ];
        # todo Это уеедет на клиент
        # ЭТАП 1 - авторизация учетной записи в ЛИЧНОМ КАБИНЕТЕ
        # редирект на страницу авторизации
        # редирект обратно после успешной авторизации
        // if (!isset($_REQUEST['code'])) {
        if (!$request->get('code')) {
            //header('HTTP 302 Found');
            return header('Location: https://int.istu.edu/oauth/authorize/?client_id=' . $APP['ID']);
            exit;
        }


        # ЭТАП 2 - авторизация приложения
        $client = new \GuzzleHttp\Client();
        if ($request->get('code')) {
            # формирование параметров запроса
            $url = implode('&', [
                'https://int.istu.edu/oauth/token/?grant_type=authorization_code',
                'code=' . $_REQUEST['code'],
                'client_id=' . $APP['ID'],
                'client_secret=' . $APP['CODE']
            ]);

            # выполнение запроса и обработка ответа
            $res = $client->get($url);
            $data = (string) $res->getBody();
            //$data = @file_get_contents($url);

            //if (explode(' ', $http_response_header[0])[1] !== '200') return false;
            $data = json_decode($data, true);
        }
        # ЭТАП 3 - запрос данных по учетной записи
        if (isset($data['client_endpoint']) && isset($data['access_token'])) {
            # формирование параметров запроса
            $url = $data['client_endpoint'] . 'user.info.json?auth=' . $data['access_token'];

            # выполнение запроса и обработка ответа
            $res = $client->get($url);
            $data = (string) $res->getBody();
            //$data = @file_get_contents($url);

            //if (explode(' ', $http_response_header[0])[1] !== '200') return false;
            $data = json_decode($data, true);
            # проверка наличия структуры данных
            if (isset($data['result']['email'])) $return = $data['result'];
        }
        # возврат
        //return $return;
        // dd($return);
        return auth($return);
    }
}


function auth($return)
{
    $user = User::where('mira_id', $return['mira_id'][0])->first();
    if (!$user) {
        if ($return['is_student']) {
            $group = Group::where('short_name', $return['data_student']['grup'])->first();
            if (!$group) {
                return response([
                    'message' => 'Ваша группа не найдена, возможно вы не ученик заочного обучения, обратитесь к администратору'
                ], 422);
            }
            $user = User::where([['fio', $return['last_name']." ".$return['name']], ['group_id', $group->id]])->first();
            // $user = User::where([['fio', $return['last_name']." ".$return['name']." ".$return['second_name']], ['group_id', $group->id]])->first();
            //todo ЭТО НАДО ИЗМЕНИТь ПРИ ДЕПЛО!!!
            if($user){

            }
            else{
                $mdl_student = DB::connection('pgsql_moodle')->select("SELECT mdl_user.id, mdl_user_info_data.data
                                                                FROM mdl_user_info_data
                                                                INNER join mdl_user ON mdl_user.id = mdl_user_info_data.userid
                                                                INNER JOIN mdl_user_info_field ON mdl_user_info_data.fieldid = mdl_user_info_field.id
                                                                WHERE mdl_user_info_field.shortname = 'cohort' AND mdl_user_info_data.data = '".$group->short_name."'
                                                                AND mdl_user.firstname = '".$return['name']."' AND mdl_user.lastname = '".$return['last_name']."'");
                                                                //todo ДОБАВИТЬ ПРИ ДЕПЛОЕ к firstname mdl_user.firstname = '".$return['name']." ".$return['second_name']."
                $user = new User();
                $user->role_id = 2;
                $user->group_id = $group->id;
                $user->moodle_id = $mdl_student[0]->id;
                $user->fio = $return['last_name']." ".$return['name']." ".$return['second_name'];
            }
        }
        if ($return['is_teacher']) {
            if (strpos($return['data_teacher']['dep'], 'Дирекция института заочно-вечернего обучения') === true) {
                return response([
                    'message' => 'Ваша учетная запись не найдена, если вы работник института ЗВО, обратитесь к администратору'
                ], 422);
            } else {
                // $fio = $return['last_name']." ".mb_substr($return['name'],0,1).".".mb_substr($return['second_name'],0,1).".";
                //todo ЭТО НАДО ИЗМЕНИТь ПРИ ДЕПЛО!!!
                $fio = $return['last_name']." ".$return['name'];
                $user = User::where('fio', $fio)->first();
                if (!$user) {
                    return response([
                        'message' => 'Ваша учетная запись не найдена, обратитесь к администратору'
                    ], 422);
                }
                //todo ЭТО НАДО ИЗМЕНИТь ПРИ ДЕПЛО!!!
                // $muser = MdlUser::where([['lastname', '=',$return['last_name']], ['firstname', '=', $return['name']." ".$return['second_name']]])->first();
                $muser = MdlUser::where([['lastname', '=',$return['last_name']], ['firstname', '=', $return['name']]])->first();
                if (!$muser) {
                    return response([
                        'message' => 'Ваша учетная запись в мудл не найдена, обратитесь к администратору'
                    ], 422);
                }
                $user->moodle_id = $muser->id;
            }
        }
        $user->email = $return['email'];
        $user->password = bcrypt('AzSxDc132!');
        $user->mira_id = $return['mira_id'][0];
        $user->islogined = true;
        $user->save();
    }

    if (!Auth::attempt(['email' => $return['email'], 'password' => 'AzSxDc132!'])) {
        return response([
            'message' => 'Чтото пошло не так, обратитесь к администратору'
        ], 422);
    }

    // $user = User::where('id', Auth::user()->id)->with('role')->first();
    $user = get_data_user(Auth::user());

    $accessToken = Auth::user()->createToken('access_token', [TokenAbility::ACCESS_API->value]);

    return response([
        'user' => $user,
        'token' => $accessToken->plainTextToken,
        // 'token_access_info' => $accessToken->accessToken,
        // 'refresh_token' => $refreshToken,
    ], 200);
}


function get_data_user($auth_user){
    $user = null;
    if ($auth_user->hasRole('Студент')) {
        $group = Group::find($auth_user->group_id)?->with('metodist')->first();
        $self_data = [
            'group_name' => $group->short_name ?? null,
            'metodist_fio' => $group->metodist->fio ?? null,
            'metodist_email' => $group->metodist->email ?? null,
        ];
    }
    else {
        
    }
    $user = [
        'email' => $auth_user->email,
        'fio' => $auth_user->fio,
        'id' => $auth_user->id,
        'role' => $auth_user->role->name,
        'moodle_id' => $auth_user->moodle_id,
        'self_data' => $self_data ?? []
    ];
    return $user;
}