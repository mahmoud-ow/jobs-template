<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    public function loadAll(Request $request){
        // only Admins endpoint check
        if(!$request->request_by || $request->request_by != 'admin'){return;}
        // fetch
        $users = User::select('id','username', 'email', 'phone', 'state')->where('id', '>', 1)->get();
        return \Response::json( ['data' => $users] );
    }

    public function changeState(Request $request){
        $update_state = User::where('id', $request->id)->update([ 'state' => $request->newState ]);
        if(!$update_state){
            return response()->json([ 'status' => 'error', 'message' => 'هنالك بعض الاخطاء!' ]);
        }
        return response()->json([ 'status' => 'success', 'message' => 'تم تحديث الحالة' ]);
    }

    public function deleteUser(Request $request){
        $deleteUser = User::where('id',$request->id)->delete();
        if(!$deleteUser){
            return response()->json([  'status' => 'error', 'message' => "خطأ , حاول مجددا !"]);
        }
        return response()->json([ 'status' => 'success', 'message' => "تم الحذف بنجاح" ]);
    }

}