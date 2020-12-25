<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MetaTagController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FrequentRequest;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::prefix('/path')->group(function () {});



Route::prefix('/users')->group(function () {
    Route::get('', [UserController::class,'loadAll']);
    Route::post('/change-state', [UserController::class,'changeState']);
    Route::delete('/delete/{id}', [UserController::class,'deleteUser']);
    Route::post('/{id}/messages', [MessageController::class,'sendMessageToUser']);

});




Route::prefix('/meta-tags')->group(function () {
    Route::get('', [MetaTagController::class,'loadAll']);
    Route::post('', [MetaTagController::class,'addMetaTag']);
    Route::get('/{id}', [MetaTagController::class,'fetchOne']);
    Route::put('/{id}', [MetaTagController::class,'update']);
    Route::delete('/{id}', [MetaTagController::class,'delete']);
});




Route::prefix('/settings')->group(function () {
    Route::get('/', [SettingController::class, 'fetchSettings']);
    Route::post('/', [SettingController::class, 'updateSettings']);
});




Route::prefix('/notifications')->group(function () {
    Route::post('/', [NotificationController::class, 'store']);
    Route::get('/', [NotificationController::class, 'fetchAll']);
    Route::get('/{notification_token}', [NotificationController::class, 'fetchOne']);
    Route::put('/{notification_token}', [NotificationController::class, 'update']);
    Route::delete('/{notification_token}', [NotificationController::class,'delete']);
});




Route::prefix('/conversations')->group(function () {

    Route::get('/', [MessageController::class,'loadConversations']);
    Route::get('/{id}', [MessageController::class,'fetchConversation']);
    Route::post('/{id}', [MessageController::class,'addNewConversationReply']);
    Route::get('{id}/previous_replies', [MessageController::class, 'loadPreviousConversationReplies']);
    Route::get('{id}/new_replies/{last_partner_reply_id}', [MessageController::class, 'loadPartnerNewConversationReplies']);
    /*  
    Route::get('/conversations/admin', 'ChatMessageController@fetchAllForAdmin');
    // open conversation
    // add reply + new replies + previous replies
    Route::get('/conversations/new_replies/{conversation_id}/{last_partner_reply}', 'ChatMessageController@checkForPartnerReplies');
    
    Route::get('/conversations/admin/read/{conversation_id}' , 'ChatMessageController@fetchOneConversationForAdmin');
    
    Route::post('/conversations/{conversation_id}/add_image', 'ChatMessageController@addImage');
 */
});



Route::get('/frequent-request', [FrequentRequest::class,'frequentRequest']);