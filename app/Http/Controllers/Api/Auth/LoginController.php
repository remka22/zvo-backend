<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public static function login(){
        return view('login');
    }

    public static function login_token($request){
        
    }
}
