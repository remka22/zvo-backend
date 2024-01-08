<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SignupRequest;


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

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials, true)) {
            return response([
                'message' => 'Provided email or password is incorrect'
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value]);
        //$accessToken = $user->createToken('main');
        //$refreshToken = '';//$user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], config('sanctum.rt_expiration'))->accessToken;

        // $teachers = DB::connection('pgsql2')->select('
        //                                                 select * from GetCoursOfTeacher(3)
        //                                             ');
        //return response(compact('user', 'token'));

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
