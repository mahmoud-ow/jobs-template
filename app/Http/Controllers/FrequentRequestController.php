<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Message;

class FrequentRequestController extends Controller
{
    public function FrequentRequest(Request $request){
        
        $auth_id = $request->auth_id;
        $activeConversation_id = $request->activeConversation_id;
        $conversation_partner_id = $request->conversation_partner_id;
        $conversation_last_partner_reply_id = $request->conversation_last_partner_reply_id;



        // check for conversation partner's new replies
        $partner_new_replies = Message::where('conversation_id', $activeConversation_id)
                                            ->where('from_user_id', '<>', $auth_id)
                                            ->where('id', '>', $conversation_last_partner_reply_id)->get();

                                            
        if($partner_new_replies->count()>0){
            
            Message::where('conversation_id', $activeConversation_id)->where('from_user_id', '!=', $auth_id)->update([
                'seen' => 1,
            ]);

            $replies_html = '';

            foreach($partner_new_replies as $reply){
                $rtl = ($this->isRtl($reply->message))? 'text-right': 'text-left';
                $replyBelongsTo = 'partner_user_reply';

                $replies_html .= '
                <div class="conversation_reply_row '.$rtl.' '. $replyBelongsTo .'" data-reply-id="'. $reply->id .'" data-reply-user-id="'. $reply->from_user_id .'">
                    <span>'. $reply->message .'</span>
                </div>
                ';
            }
            
            return response()->json([
                'error' => 0,
                'activeConversation_id' => $activeConversation_id,
                'conversation_last_partner_reply_id' => $conversation_last_partner_reply_id,
                'conversation_partner_id'=> $conversation_partner_id,
                'new_replies' => $replies_html,
            ]);

        }
        
    }


    function isRtl($value) {
        $rtlChar = '/[\x{0590}-\x{083F}]|[\x{08A0}-\x{08FF}]|[\x{FB1D}-\x{FDFF}]|[\x{FE70}-\x{FEFF}]/u';
        return preg_match($rtlChar, $value) != 0;
    }

}/* /CLASS */
