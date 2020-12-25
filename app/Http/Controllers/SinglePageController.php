<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SinglePageController extends Controller
{
    public function index() {
        
        if( auth()->check() ){
            $user = [
                'id' => auth()->user()->id,
                'username' => auth()->user()->username,
                'logged_in' => true,
                'user_type' => auth()->user()->user_type,
            ];
        } else { 
            $user = [
                'id' => null,
                'username'=> 'Guest_'.\Str::random(10),
                'logged_in' => false,
                'user_type' => 'guests',
            ];
        }
        return view('index', compact('user'));
        
    }
}