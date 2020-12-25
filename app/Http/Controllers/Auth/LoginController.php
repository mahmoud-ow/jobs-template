<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

// required
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class LoginController extends Controller
{
    public function login(Request $request){
        
        $errors = [];
        
        if(!request('email')){
            $errors[] = ' البريد الإلكترونى مطلوب';
        }
        if(!request('password')){
            $errors[] = 'كلمة المرور مطلوبة';
        }

        if(count($errors)){
            return response()->json([
                'status' => 'error',
                'message' => 'هنالك بعض الاخطاء!',
            ]);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            // auth token for stateless api
            $user = auth()->user();
            $user->auth_token = \Str::random(10).auth()->user()->id.\Str::random(10);
            $user->save();

            $user_info = [
                'id' => auth()->user()->id,
                'username' => auth()->user()->username,
                'logged_in' => true,
                'user_type' => auth()->user()->user_type,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'نجحت عملية تسجيل الدخول',
                'user' => $user_info
            ]);
            
            
        }

        // if the code reaches this line = login info is incorrect redirect to login with message
        return response()->json([
            'status' => 'error',
            'login' => 'fail',
            'message' => "البيانات التى أدخلتها غير صحيحة",
        ]);

    }



    public function logout(){
        Auth::logout();
        return redirect('/login/?source=logout')->with('logout', 'تم تسجيل الخروج بنجاح');
    }
}