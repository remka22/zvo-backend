<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\TokenAbility;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\MetodistController;
use App\Http\Controllers\Api\DirectorController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\StudentActivityController;
use App\Http\Controllers\TeacherWorkloadController;
use App\Http\Requests\LoginRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return LoginController::user($request);
    });

    Route::prefix('director')->middleware('role:Директор')->group(function () {
        Route::get('/', function (Request $request) {
            return DirectorController::get($request);
        });
        Route::post('/', function (Request $request) {
            return DirectorController::post($request);
        });
        Route::post('/workload', function (Request $request) {
            return TeacherWorkloadController::input_teacher_workload($request);
        });
    });

    Route::prefix('metodist')->middleware('role:Методист,Директор')->group(function () {
        Route::get('/', function (Request $request) {
            return MetodistController::get_groups($request);
        });
        Route::post('/', function (Request $request) {
            return MetodistController::post($request);
        });
    });

    Route::prefix('teacher')->middleware('role:Преподаватель')->group(function () {
        Route::get('/', function (Request $request) {
            return TeacherController::get($request);
        });
        Route::post('/', function (Request $request) {
            return TeacherController::post($request);
        });
        Route::get('/update_course', function (Request $request) {
            return TeacherController::update_courses($request);
        });
    });

    Route::prefix('student')->middleware('role:Студент')->group(function () {
        Route::get('/', function (Request $request) {
            return StudentController::get_subjects($request);
        });
        Route::get('/subject', function (Request $request) {
            return StudentController::get_subject_data($request);
        });
    });

    Route::prefix('notify')->middleware('role:Методист,Директор,Преподаватель,Студент,Администратор')->group(function () {
        Route::get('/users', function (Request $request) {
            return NotificationController::get_users($request);
        });
        Route::get('/messages', function (Request $request) {
            return NotificationController::get_messages($request);
        });
        Route::get('/last', function (Request $request) {
            return NotificationController::get_last($request);
        });
        Route::post('/', function (Request $request) {
            return NotificationController::create($request);
        });
        Route::put('/', function (Request $request) {
            return NotificationController::update($request);
        });
    });

    Route::prefix('student_activity')->middleware('role:Методист,Директор,Администратор')->group(function () {
        Route::get('/', function (Request $request) {
            return StudentActivityController::get($request);
        });
    });

    Route::prefix('admin')->middleware('role:Администратор')->group(function () {
        Route::get('/users', function (Request $request) {
            return AdminController::users($request);
        });
        // Route::get('/user', function (Request $request) {
        //     return AdminController::user($request);
        // });
        Route::get('/roles', function (Request $request) {
            return AdminController::roles($request);
        });
        Route::post('/add_user', function (Request $request) {
            return AdminController::add_user($request);
        });
        Route::post('/delete_user', function (Request $request) {
            return AdminController::delete_user($request);
        });
    });
});
Route::post('/login', function (Request $request) {
    return LoginController::login_admin($request);
});

Route::post('/login_admin', function (Request $request) {
    return LoginController::login_admin($request);
});


// Route::post('/signup', [LoginController::class, 'signup']);
// Route::post('/refresh-token', function (Request $request) {
    //     $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], config('sanctum.expiration'));
    
    //     return ['token' => $accessToken->plainTextToken];
    // })->middleware([
    //     'ability:'.TokenAbility::ISSUE_ACCESS_TOKEN->value,
    // ]);




// Route::get('/test', function () {
//     //return User::where('role_id', 5)->with('getMetodists')->get();
//     //$data = [];
//     $data["subjects"] = User::where('id', 609)->with('getStudent')->get();
//     // $data["groups"] = Group::where('metodist_id', null)->get();
//     return $data;
//     // $user = User::find(1)->getMetodistsGroups()->get();
//     // $user->getSubjects()->get();
//     // $user->getSubjectTeachers()->get();
//     // $user->getTeacherAndCourse()->get();
//     // return (json_encode($user, JSON_UNESCAPED_UNICODE));
// });
