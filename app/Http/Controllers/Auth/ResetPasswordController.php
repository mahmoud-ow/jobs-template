<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{

    public function update(Request $request){
       
        if(!auth()->check()){
            return response()->json([
                'error' => 1,
                'message' => 'Login Required',
            ]);
        }

        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $new_password_confirmation = $request->new_password_confirmation;
        
        if(!$old_password || ! $new_password || ! $new_password_confirmation){
            return response()->json([ 'error' => 1, 'message' => 'بعض الحقول مطلوبة']);
        }
        if($new_password != $new_password_confirmation){
            return response()->json([ 'error' => 1, 'message' => 'كلمة المرور الجديدة غير متشابهه']);
        }
        if(strlen(trim($new_password)) < 6){
            return response()->json([ 'error' => 1, 'message' => "كلمة المرور أقل من 6 حرف/رقم"]);
        }


        $user_id = auth()->user()->id;
        $current_password = auth()->user()->password;
        
        // check to see if the old password is correct
        if( Hash::check($old_password, $current_password) ){
            // old pass is okay now update password
            $update = User::where("id", '=', $user_id)->update(['password' => Hash::make($new_password)]);
            if($update){
                return response()->json([ 'error' => 0, 'message' =>  'تم التحديث بنجاح' ]);    
            }
            return response()->json([ 'error' => 1, 'message' => "فشلت العملية" ]);
        }

        return response()->json([ 'error' => 1, 'message' => "البيانات التى أدخلتها غير صحيحة" ]);

    }


}