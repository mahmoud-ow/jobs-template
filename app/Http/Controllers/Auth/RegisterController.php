<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// required
use Illuminate\Http\Request;

class RegisterController extends Controller
{
   
    protected function register(Request $request)
    {   
        $errors = [];
        if(!request('username')){ $errors[] = 'الإسم / اللقب - مطلوب'; }
        if(!request('email')){ $errors[] = ' البريد الإلكترونى مطلوب'; }
        if(!request('password')){ $errors[] = 'كلمة المرور مطلوبة'; }
        if(!request('password_confirmation')){ $errors[] = 'تأكيد كلمة المرور مطلوب'; }
        if(!request('phone')){ $errors[] = 'رقم الهاتف مطلوب'; }
        if (strlen(request('password')) < 6) { $errors[] = "كلمة المرور أقل من 6 حرف/رقم"; }
        if( request('password') !=  request('password_confirmation')){$errors[] = 'كلمات المرور غير متطابقة';}
        
        

        $checkEmail = User::where('email', request('email'))->count();
        if($checkEmail > 0){ $errors[] = 'هذا البريد مسجل بالفعل';}

        
        if(count($errors)){
            return response()->json([
                'status' => 'error',
                'message' => 'هنالك بعض الاخطاء!',
                'valid_email' => 'false',
            ]);
        }


        $verification_key = \Str::random(10);

        $data = array(
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'verification_key' => $verification_key,
        );

        $user =  User::create($data);

        if( $user ){

            //Mail::to($request->email)->send( new EmailVerification($data) );
            //return redirect('/users/register/email/verification/'.$request->email);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنشاء الحساب بنجاح',
            ]);

        } else {
            return response()->json([
                'status' => 'error',
                'message' => '!فشت عملية التسجيل, حاول مجددا',
            ]);
        }
    }
}