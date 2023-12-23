<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\MetodistController;
use App\Http\Controllers\Api\DirectorController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
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

    //Route::apiResource('/users', UserController::class);
});

Route::post('/signup', [LoginController::class, 'signup']);
Route::post('/login', [LoginController::class, 'login']);

Route::get('/director', function(Request $request){
    return DirectorController::get($request);
});
Route::post('/director', function (Request $request){
    return DirectorController::post($request);
});
Route::get('/metodist', function(Request $request){
    return MetodistController::get($request);
});
Route::post('/metodist', function (Request $request){
    return MetodistController::post($request);
});
Route::get('/teacher', function(Request $request){
    return TeacherController::get($request);
});
Route::post('/teacher', function (Request $request){
    return TeacherController::post($request);
});
Route::get('/student', function(){
    return StudentController::get();
});
Route::post('/student', function (Request $request){
    return response();//TeacherController::post($request);
});
