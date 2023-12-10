<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public static function login(){
        return view('login');
    }

    public static function login_token($request){
        
    }
}
