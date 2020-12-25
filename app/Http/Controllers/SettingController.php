<?php

namespace App\Http\Controllers;
use App\Models\Social;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function fetchSettings(Request $request){
        $settings['social'] = Social::find(1);
        return response()->json($settings);
    }

    public function updateSettings(Request $request){
        
        Social::where('id', 1)->update([
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'youtube' => $request->youtube,
            'instagram' => $request->instagram,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث البيانات',
        ]);
    }
}
