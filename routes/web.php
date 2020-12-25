<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/lang/{lang}', 'App\Http\Controllers\LanguageController@setLanguage');


Route::group(['middleware' => 'Lang' ], function(){
    
    // auth
    Route::prefix('/auth')->group(function () {
        Route::post('/register', [RegisterController::class , 'register']);
        Route::post('/login', [LoginController::class,'login']);
        Route::post('/update-password', [ResetPasswordController::class,'update']);
        Route::post('/logout', [LoginController::class,'logout']);
    });
    
    

    // Vue Router
    Route::get('/{any}', 'App\Http\Controllers\SinglePageController@index')->where('any', '.*');
    
    // Auth::routes();
    
    Route::get('/home', [App\Http\Controllers\SinglePageController::class, 'index'])->name('home');
    
});