<?php

namespace App\Http;

use App\Models\Inquiry;
use App\Models\Notification;
use App\Models\Settings;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Helpers {
    public static function storeNotification($user_id='',$message='',$is_read=0,$icon_id=0){
        if(!empty($user_id)||!empty($message)){
            $user_id = (!isset($user_id)) ? NULL :$user_id;
            try{
                $notification = Notification::create([
                    'user_id'=> $user_id,
                    'message'=> $message,
                    'is_read'=> $is_read,
                    'icon_id'=> $icon_id,
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

    public static function getNotification($user_id='',$id=''){
        if(!empty($user_id) ){
            return (Notification::where('id',$id)->where('user_id',$user_id));
        }else{
            return (Notification::whereNull('user_id')->where('id',$id));
        }
        return false;
    }

    public static function getChildUserCount($user_id){
        if(!empty($user_id) ){
            return (User::where('parent_id',$user_id)->count());
        }
        return 0;
    }

    public static function getAssignedRetailerInquiryCount($user_id){
        if(!empty($user_id) ){
            return (Inquiry::where('retailer_id',$user_id)->where('status','<>',5)->count());
        }
        return 0;
    }

    public static function getAssignedDealerInquiryCount($user_id){
        if(!empty($user_id) ){
            return (Inquiry::where('dealer_id',$user_id)->where('status','<>',5)->count());
        }
        return 0;
    }

    public static function getCountData($table,$where,$is_raw=0){
            if(empty($is_raw)){
                return DB::table($table)->where($where)->count();
            }
            return (DB::table($table)->whereRaw($where)->count());
    }
    public static function number_format($amt){
        $number_format = Session::get('number_format');
        if(!empty($number_format)){
            if($number_format == 1){
                return number_format($amt,2,'.',',');
            }else if($number_format == 2){
                return number_format($amt,2,',','.');
            }
        }else{
            return $amt;
        }
    }
    public static function dateTime_format($date){
        $date_format = Session::get('date_format');
        if(!empty($date_format)){
            if($date_format == 1){
                return date('d-m-Y H:i:s',strtotime($date));
            }elseif($date_format == 2){
                return date('m-d-Y H:i:s',strtotime($date));
            }elseif($date_format == 3){
                return date('Y-m-d H:i:s',strtotime($date));
            }elseif($date_format == 4){
                return date('d-M-Y H:i:s',strtotime($date));
            }elseif($date_format == 5){
                return date('M-d-Y H:i:s',strtotime($date));
            }elseif($date_format == 6){
                return date('Y-M-d H:i:s',strtotime($date));
            }
        }else{
            return $date;
        }
    }
    public static function dateOnly_format($date){
        $date_format = Session::get('date_format');
        if(!empty($date_format)){
            if($date_format == 1){
                return date('d-m-Y',strtotime($date));
            }elseif($date_format == 2){
                return date('m-d-Y',strtotime($date));
            }elseif($date_format == 3){
                return date('y-m-d',strtotime($date));
            }elseif($date_format == 4){
                return date('d-M-Y',strtotime($date));
            }elseif($date_format == 5){
                return date('M-d-Y',strtotime($date));
            }elseif($date_format == 6){
                return date('Y-M-d',strtotime($date));
            }
        }else{
            return $date;
        }
    }
}