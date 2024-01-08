<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\MetodistController;
use App\Http\Controllers\Api\DirectorController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\NotificationController;
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
        return $request->user();
    });


    Route::get('/director', function (Request $request) {
        return DirectorController::get($request);
    });
    Route::post('/director', function (Request $request) {
        return DirectorController::post($request);
    });


    Route::get('/metodist', function (Request $request) {
        return MetodistController::get($request);
    });
    Route::post('/metodist', function (Request $request) {
        return MetodistController::post($request);
    });


    Route::get('/teacher', function (Request $request) {
        return TeacherController::get($request);
    });
    Route::post('/teacher', function (Request $request) {
        return TeacherController::post($request);
    });


    Route::get('/student', function (Request $request) {
        return StudentController::get($request);
    });
    
    Route::get('/notify', function (Request $request) {
        return NotificationController::get($request);
    });
    Route::post('/notify', function (Request $request) {
        return NotificationController::create($request);
    });
    Route::put('/notify', function (Request $request) {
        return NotificationController::update($request);
    });

});
Route::post('/signup', [LoginController::class, 'signup']);
Route::post('/login', [LoginController::class, 'login']);




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
