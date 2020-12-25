<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use \App\Models\Message;
class MessageController extends Controller
{
    public function loadConversations(Request $request){
        
        $userId =  $request->user_id;
        $limit = 7;


        if($request->last_conversation_id){
            // load latest $limit conversation
            $last_conversation_id = $request->last_conversation_id;
            $last_conversation_row_id = $request ->last_conversation_row_id;
            
            // get conversations
            $conversations = \DB::table('messages')->select('*') 
            ->join(\DB::raw('(Select max(id) as id from messages group by conversation_id) LatestMessage'), function($join) {
                    $join->on('messages.id', '=', 'LatestMessage.id');
            })
            ->where('messages.id', '<', $last_conversation_row_id)->where(function($query) use($userId){
                $query->where('to_user_id', $userId)->orWhere('from_user_id', $userId);
            })->orderBy('created_at', 'desc')->take($limit)->get();


        } elseif ($request->top_conversation_id){
        

            // load latest $limit conversation
            $top_conversation_id = $request->top_conversation_id;
            $top_conversation_row_id = $request ->top_conversation_row_id;

            // get conversations
            $conversations = \DB::table('messages')->select('*') 
            ->join(\DB::raw('(Select max(id) as id from messages group by conversation_id) LatestMessage'), function($join) {
                    $join->on('messages.id', '=', 'LatestMessage.id');
            })
            ->where('messages.id', '>', $top_conversation_row_id)->where(function($query) use($userId){
                $query->where('to_user_id', $userId)->orWhere('from_user_id', $userId);
            })->orderBy('created_at', 'desc')->get();
        

        } else {


            // load latest $limit conversation
            $conversations = \DB::table('messages')->select('*') 
            ->join(\DB::raw('(Select max(id) as id from messages group by conversation_id) LatestMessage'), 
            function($join) {
                $join->on('messages.id', '=', 'LatestMessage.id');
            })
            ->where(function($query) use($userId){
                $query->where('to_user_id', $userId)->orWhere('from_user_id', $userId);
            })
            ->orderBy('created_at', 'desc')->take($limit)->get();

        }

      
        
        
        
        // this array will only be used if there is new incoming conversations
        $conversations_ids_array = [];

        $conversations_html_structure = '';
        if($conversations->count()>0){

            foreach($conversations as $conversation ){

                // check for partner account id
                if($conversation->to_user_id == $userId){
                    $chat_partner_id = $conversation->from_user_id;
                } else {
                    $chat_partner_id = $conversation->to_user_id;
                }

                // get partner info
                $chat_partner = User::where('id', $chat_partner_id)->first();

                
                // check if the partner account exists , will display conversation only if exists
                if($chat_partner){
                    if($chat_partner->count()>0){

                        array_push($conversations_ids_array, $conversation->conversation_id );

                        // get total unseen replies
                        $total_notseen_replies = Message::where('conversation_id', $conversation->conversation_id)->where('from_user_id', '!=', $userId)->where('seen', 0)->where('message', '<>' , '')->count();
                        
                        $partner_img = ($chat_partner->getMedia('avatar')->first()) ? $chat_partner->getMedia('avatar')->first()->getUrl('thumb'): url('/images//missing_avatar.svg');

                        $conversations_html_structure .= '
                        <div class="conversation-row" data-partner-id="'. $chat_partner->id .'" data-conversation-id="'. $conversation->conversation_id .'" data-conversation-row-id="'. $conversation->id .'">
                            <div class="conversation-row-partner-image">
                                <img src="'.  $partner_img .'" />
                            </div>
                            <div class="conversation-row-partner-name">'. $chat_partner->username .'</div>
                            <div class="conversation-row-notseen-messages-count"><span>'. $total_notseen_replies .'</span>&nbsp;:&nbsp;<i class="far fa-envelope"></i></div>
                        </div>
                        ';

                    }
                }/* /if(partner) */
            }/* /foreach */
        }/* /if(count) */

        
        $countUnread = 0;
        $countNotifications = 0;
        /* if( auth()->check() ){
            // check for messages counter
            $countUnread = \DB::table('messages')->distinct('conversation_id')->where('to_user_id', $user_id)->where('seen', 0)->count();
            
            // check for notification counter
            $countNotifications = \App\Notification::where('user_id', $user_id)->where('seen', 0)->count();
        } */

        return response()->json([
            'error' => 0,
            'message' => 'conversations loading success',
            'conversations' => $conversations_html_structure,
            'conversations_ids_array' => $conversations_ids_array,
            'countUnread' => $countUnread,
            'countNotifications' => $countNotifications
        ]);
        




    }/* /fetchConversations() */


    public function fetchConversation(Request $request){
      
        
        // check to see if the conversation exists to get the conversation id
        $user_id = $request->user_id;
        $partner_id = $request->partner_id;
       

        $check_conversation = Message::latest()->where(function($query) use($user_id, $partner_id){
            $query->where('from_user_id',  $user_id)->where('to_user_id', $partner_id);
        })->orWhere(function($query) use($user_id, $partner_id){
            $query->where('from_user_id', $partner_id)->where('to_user_id', $user_id);
        })->get();




        // check if this is a new chat cpnversation or existing one
        if( $check_conversation->count()){
            // existing conversation
            $conversation_id = $check_conversation[0]->conversation_id;
        } else {
            // new conversation
            $statement = DB::select("SHOW TABLE STATUS LIKE 'messages'");
            $nextId = $statement[0]->Auto_increment;

            $new_conversation = Message::create([
                'conversation_id' => $nextId.rand(1, 99),
                'to_user_id' => $partner_id,
                'from_user_id' => $user_id,
                'message' => '',
            ]);
            $conversation_id = $new_conversation->conversation_id;
        }


    
        

        $limit = 15;
        
        //$conversation = Message::where('conversation_id', $request->conversation_id)->latest()->take($limit)->get();
        $conversation = Message::where('conversation_id',  $conversation_id)->where('message', '!=', '')->latest()->take($limit)->get();
        $conversation = $conversation->sortBy('id');
        $conversation_html = '';

        if($conversation->count() > 0){

            Message::where('conversation_id',  $conversation_id)->where('from_user_id', '!=', $user_id)->update([
                'seen' => 1,
            ]);

            foreach($conversation as $reply){
                $rtl = ($this->isRtl($reply->message))? 'text-right': 'text-left' ;
                $replyBelongsTo = ($reply->from_user_id == $user_id)? 'current_user_reply' : 'partner_user_reply admin-view';

                $conversation_html .= '
                
                <div class="conversation_reply_row '.$rtl.' '. $replyBelongsTo .'" data-reply-id="'. $reply->id .'" data-reply-user-id="'. $reply->from_user_id .'">
                    <span>'. $reply->message .'</span>
                </div>

                ';
            }

        } else {
            $conversation_html = '
            <div class=""></div>
            ';
        }

        return response()->json([
            'error' => 0,
            'message' => 'ok',
            'replies' => $conversation_html ,
            'conversation_id' => $conversation_id,
        ]);

    }/* /fetchOneConversation() */
    

    public function addNewConversationReply(Request $request){
        
        
        
        $auth_id = $request->auth_id;
        $conversation_id = $request->conversation_id;
        $conversation_reply = $request->conversation_reply;
        

        if($conversation_reply){

            // get conversation info
            $conversation = Message::where('conversation_id', $conversation_id)->first();
            
            if($conversation->count()>0){

                
                if($conversation->from_user_id==$auth_id){
                    $to_user_id = $conversation->to_user_id;
                } else {
                    $to_user_id = $conversation->from_user_id;
                }


                $add_reply = Message::create([
                    'conversation_id' => $conversation_id,
                    'to_user_id'  => $to_user_id,
                    'from_user_id' => $auth_id,
                    'message' => $conversation_reply,
                ]);
                    
                if($add_reply){

                    $rtl = ($this->isRtl($conversation_reply))? 'text-right': 'text-left' ;
                    $replyBelongsTo = 'current_user_reply';
                    $reply_html = '
                    <div class="conversation_reply_row '.$rtl.' '. $replyBelongsTo .'"><span>'. $conversation_reply .'</span></div>
                    ';

                    return response()->json([
                        'error'   => 0,
                        'message' => 'ok',
                        'reply'   => $reply_html,
                    ]);
                } else {
                    return response()->json([
                        'error'   => 1,
                        'message' => 'Something went wrong',
                    ]);
                }

            }
            

        }
    }

    public function loadPreviousConversationReplies(Request $request){
        $limit = 15;
        $auth_id = $request->auth_id;
        $conversation_id = $request->id;
        $last_viewed_reply_id = $request->last_viewed_reply_id;

       
        $previous_replies = Message::where('conversation_id',$conversation_id)
                                            ->where('id', '<', $last_viewed_reply_id)->latest()->take($limit)->get();

       

        $previous_replies = $previous_replies->sortBy('id');
                                            
        if($previous_replies->count()>0){
            
            $replies_html = '';

            foreach($previous_replies as $reply){

                if($reply->message!= ''){
                    $rtl = ($this->isRtl($reply->message))? 'text-right': 'text-left' ;
                    $replyBelongsTo = ($reply->from_user_id == $auth_id)? 'current_user_reply' : 'partner_user_reply';

                    $replies_html .= '
                    <div class="conversation_reply_row '.$rtl.' '. $replyBelongsTo .'" data-reply-id="'. $reply->id .'" data-reply-user-id="'. $reply->from_user_id .'">
                        <span>'. $reply->message .'</span>
                    </div>
                    ';
                }
                
            }

            return response()->json([
                'error' => 0,
                'message' => 'ok',
                'replies' => $replies_html,
            ]);

        }
    }

    function isRtl($value) {
        $rtlChar = '/[\x{0590}-\x{083F}]|[\x{08A0}-\x{08FF}]|[\x{FB1D}-\x{FDFF}]|[\x{FE70}-\x{FEFF}]/u';
        return preg_match($rtlChar, $value) != 0;
    }

}
