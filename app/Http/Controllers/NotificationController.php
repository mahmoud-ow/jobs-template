<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function store(Request $request){
        
        

        if($request->notification_audience == 'universal'){
            $audience = User::all();
        } else {
            $audience = User::where('language', $request->notification_audience)->get();
        }
        
        if($audience->count()>0){

            $notification_token = \Str::random(20);
            $data=[];

            foreach($audience as $user){

                $data[] = array(
                    'user_id'      => $user->id , 
                    'content'      => $request->notification_content, 
                    'language'     => $request->notification_audience,
                    'notification_token' => $notification_token,
                    'source'       => $request->notification_source,
                    "created_at"   => date('Y-m-d H:i:s'), # new \Datetime()
                    "updated_at"   => date('Y-m-d H:i:s'), # new \Datetime()
                );
                
            }

            $create_notification = Notification::insert($data);
            if(!$create_notification){
                return response()->json([ 'status' => "error", 'message' =>  "خطأ , حاول مجددا !"]);
            }
            return response()->json([ 'status' => "success", 'message' =>  "تم الإرسال بنجاح"]);

        } else {
            return response()->json([ 'status' => "error", 'message' => 'لا يوجد جمهور حاليا لهذه اللغة' ]);
        }
        
    }/* /store() */


    
    public function fetchAll(Request $request){
        
        if($request->request_by=='admin'){
            
            // for admin datatable
            $all_notifications = Notification::where('source', 'admin')->get();
            $all_notifications = $all_notifications->groupBy('notification_token');
            
            $notifications_array=[];
            // constructing $notifications_array
            foreach($all_notifications as $notification){
                
                $audience = '';
                if( $notification[0]->language == 'ar' ){
                    $audience = 'اللغة العربية';
                } elseif ( $notification[0]->language == 'en' ){
                    $audience = 'اللغة الإنجليزية';
                } elseif ( $notification[0]->language == 'universal' ){
                    $audience = 'الجميع';
                }

                $notification_row;
                $notification_row['id']           = $notification[0]->id;
                $notification_row['notification_token'] = $notification[0]->notification_token;
                $notification_row['content']      = "<textarea disabled class='form-control' style='min-width:150px;height: 44px;min-height: 44px;text-align:center;'> ". $notification[0]->content ." </textarea>";
                $notification_row['count']        = $audience." (".$notification->count().")";
                $notification_row['views']        = $notification->where('seen', 1)->count();
                $notification_row['created_at']   = $notification[0]->created_at->calendar();
            
                $notifications_array[] = $notification_row;
            }
            return response()->json([ 'data' => $notifications_array ]);
            
        } elseif($request->request_by=='user') {

            // for user
            // this code will do the following
            // 1- load latest 20 notification for the user
            // 2- load more 20 notification if the user requested
            // 3- load new notifications ( interval ajax )
            




            $fetch_count = 100;

            if($request->previous_notifications=='true'){
                $notifications = Notification::where('user_id', auth()->user()->id)->where('id', '<', $request->bottom_notification)->latest()->skip(0)->take($fetch_count)->get();
            } else {
                $first_load       = $request->first_load; // true or false
                $top_notification = $request->top_notification; // 0 or > 0

                if($first_load != 'false'){
                    $notifications = Notification::where('user_id', auth()->user()->id)->latest()->skip(0)->take($fetch_count)->get();
                } else {
                    $notifications = Notification::where('id', '>', $top_notification)->where('user_id', auth()->user()->id)->latest()->get();
                    $notifications = $notifications->reverse();
                }
            }



     
            $notification_html_structure = '';

            if($notifications->count()>0){
                foreach($notifications as $notification){
                    
                    // set seen
                    if($notification->seen==1){
                        $seen_icon  = '<i class="far fa-bell"></i>';
                        $seen_class = '';
                    } else {
                        $seen_icon  = '<i class="fas fa-bell"></i>';
                        $seen_class = 'notSeen';
                    }

                    $notification_html_structure .= 
                    '<div data-id="'.$notification->id.'" class="header-notification-row '. $seen_class .'"><div class="notificationSeenIcon">'. $seen_icon .'</div><div><div class="notificationAndMessageloadingAnimation"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div><div>'. $notification->content .'</div></div></div>';
                    ;

                }
            }



            // check to see if there is unknown notifications for the user ( doesn't have to be seen ) = set new notifications counter
            $unseen_notifications_counter =  Notification::where('user_id', auth()->user()->id)->where('seen', 0)->count();
            
            return response()->json([ 'data' => $notification_html_structure, 'number_of_unseen_notifications' => $unseen_notifications_counter ]);

            
        }









    }/* /fetch() */


    
    public function fetchOne(Request $request){
        $notification = Notification::where('notification_token', $request->notification_token)->first();
        if($notification){
            if(!$request->request_by && !$request->request_by == 'admin'){
                $notification->update(['seen'=>1]);
            }
            return response()->json([ 'status' => "success", 'id' => $notification->id, 'content' => $notification->content , 'date' =>  $notification->created_at->calendar() ]);
        } else {
            return response()->json([ 'status' => "error", 'message' =>  "خطأ , حاول مجددا !" ]);
        }
        
    }/* /read() */


    public function update(Request $request){

        $token = $request->notification_token;
        $notification = $request->notification;

        $update = Notification::where('notification_token', $token)->update([
            'content' => $notification,
        ]);

        if(!$update){
            return response()->json([
                'status' => "error",
                'message' =>  "خطأ , حاول مجددا !"
            ]);
        }

        return response()->json([
            'status' => "success",
            'message' => 'تم التحديث بنجاح',
        ]);

    }/* /update() */


    public function delete(Request $request){


        $deleteNotification = Notification::where('notification_token',$request->notification_token)->delete();
        if(!$deleteNotification){
            return response()->json([
                'status' => "error",
                'message' =>  "خطأ , حاول مجددا !"
            ]);
        }
        return response()->json([
            'status' => "success",
            'message' => 'تم الحذف بنجاح',
        ]);
    }/* /delete() */



    
}