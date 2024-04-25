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
    // public function login(LoginRequest $request)
    public static function login(Request $request)
    {
        if (User::where('email', $request->email)->get()->count() != 0) {
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {
                return response([
                    'message' => 'Provided email or password is incorrect'
                ], 422);
            }
        } else {
            $campus_user = Campus::where('login', $request->email)->get();
            if ($campus_user->count() == 0) {
                return response([
                    'message' => 'Undefind user'
                ], 422);
            } else if ($campus_user->count() == 1) {
                $campus_user = $campus_user->first();
                $login = $campus_user->login;

                $moodle_user = MdlUser::where('email', $login)->get();
                if ($moodle_user->count() == 1) {
                    $moodle_user = $moodle_user->first();
                } else {
                    $moodle_user = [];
                }

                $user = new User();
                $l = $campus_user->last_name;
                $f = $campus_user->first_name;
                if ($campus_user->cohort == 'Преподаватель') {
                    //todo
                } else {
                    $user->fio = "$f $l";
                    $user->email = $campus_user->nomz;
                    $user->moodle_id = $moodle_user->id ?? null;
                    $user->role_id = 2;
                    $user->mira_id = $campus_user->miraid;
                    $user->password = bcrypt('tasar232');
                    $user->save();

                    $group = Group::where('short_name', $campus_user->cohort)->get();
                    if ($group->count() == 1) {
                        $group = $group->first();
                        $student = new Student();
                        $student->user_id = $user->id;
                        $student->group_id = $group->id;
                        $student->nomz = $campus_user->nomz;
                        $student->save();
                    } else {
                        return response([
                            'message' => 'Group student error, report administrator'
                        ], 422);
                    }
                }
            } else {
                return response([
                    'message' => 'Multiuser error, report administrator'
                ], 422);
            }
        }

        //$credentials = $request->validated();
        // if (!Auth::attempt($credentials, true)) {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {
            return response([
                'message' => 'Provided email or password is incorrect'
            ], 422);
        }
        /** @var \App\Models\User $user */
        $user = User::where('id', Auth::user()->id)->with('role')->get()->first();

        $accessToken = Auth::user()->createToken('access_token', [TokenAbility::ACCESS_API->value]);

        return response([
            'user' => $user,
            'token' => $accessToken->plainTextToken,
            // 'token_access_info' => $accessToken->accessToken,
            // 'refresh_token' => $refreshToken,
        ], 200);
    }

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

    public static function login_web()
    {
        dd(campus_auth());
    }
}

function campus_auth()
{
    # переменные
    $return = false;

    $APP = [
        'ID' => '',
        'CODE' => ''
    ];
    # ЭТАП 1 - авторизация учетной записи в ЛИЧНОМ КАБИНЕТЕ
    # редирект на страницу авторизации
    # редирект обратно после успешной авторизации
    if (!isset($_REQUEST['code'])) {
        header('HTTP 302 Found');
        header('Location: https://int.istu.edu/oauth/authorize/?client_id=' . $APP['ID']);
        exit;
    }
    # ЭТАП 2 - авторизация приложения
    if (isset($_REQUEST['code'])) {
        # формирование параметров запроса
        $url = implode('&', [
            'https://int.istu.edu/oauth/token/?grant_type=authorization_code',
            'code=' . $_REQUEST['code'],
            'client_id=' . $APP['ID'],
            'client_secret=' . $APP['CODE']
        ]);

        # выполнение запроса и обработка ответа

        $data = @file_get_contents($url);

        if (explode(' ', $http_response_header[0])[1] !== '200') return false;
        $data = json_decode($data, true);
    }
    # ЭТАП 3 - запрос данных по учетной записи
    if (isset($data['client_endpoint']) && isset($data['access_token'])) {
        # формирование параметров запроса
        $url = $data['client_endpoint'] . 'user.info.json?auth=' . $data['access_token'];
        # выполнение запроса и обработка ответа
        $data = @file_get_contents($url);
        if (explode(' ', $http_response_header[0])[1] !== '200') return false;
        $data = json_decode($data, true);
        # проверка наличия структуры данных
        if (isset($data['result']['email'])) $return = $data['result'];
    }
    # возврат
    return $return;
}
