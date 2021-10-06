<?php

namespace App\Http;

use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\DB;

class Helpers {
    public static function storeNotification($user_id='',$message='',$is_read=0){
        if(!empty($user_id)||!empty($message)){
            $user_id = (!isset($user_id)) ? NULL :$user_id;
            try{
                $notification = Notification::create([
                    'user_id'=> $user_id,
                    'message'=>$message,
                    'is_read'=>$is_read
                ]);
                return ["code"=>1,"message"=>"","data"=>$notification];
            }catch(Exception $e){
                return ["code"=>0,"message"=>$e->getMessage(),"data"=>''];
            }
        }
        return ["code"=>0,"message"=>"Unable to save notification","data"=>''];
    }

    public static function getUserNotification($user_id='', $is_admin = 0){
        if(!empty($user_id) || ($is_admin == "1")){
            $user_id = (!isset($user_id)) ? $user_id : NULL;
            try{
                $notifications = Notification::where(function($query) use($user_id) {
                    if(!empty($user_id)){
                        return $query->where('user_id',$user_id);
                    }else{
                        return $query->whereNull('user_id');
                    }
                })->orderBy('id','DESC')->get();
                return ["code"=>1,"message"=>"","data"=>$notifications];
            }catch(Exception $e){
                return ["code"=>0,"message"=>$e->getMessage(),"data"=>''];
            }
        }
        return ["code"=>0,"message"=>"Unable to find notification","data"=>''];
    }
    
    public static function getUnreadNotification($user_id='', $is_admin = 0){
        if(!empty($user_id) || ($is_admin == "1")){
                $notifications = Notification::where(function($query) use($user_id) {
                    if(!empty($user_id)){
                        return $query->where('user_id',$user_id)->where('is_read',0);
                    }else{
                        return $query->whereNull('user_id')->where('is_read',0);
                    }
                })->orderBy('id','DESC');
                return $notifications;
        }
        return false;
    }
}