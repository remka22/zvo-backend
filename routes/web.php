<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\MetodistController;
use App\Http\Controllers\Api\DirectorController;
use App\Http\Controllers\TeacherWorkloadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    //return redirect('/login');
});

Route::get('/login', function(){
    return LoginController::login_web();
});

Route::get('/twl', function(){
    return TeacherWorkloadController::input_teacher_workload();
    //return TeacherWorkloadController::teachers_insystem();
});

Route::get('/student', function(){
    return StudentController::get();
});
Route::get('/metodist', function(){
    return MetodistController::get();
});
Route::get('/director', function(){
    return DirectorController::get();
});