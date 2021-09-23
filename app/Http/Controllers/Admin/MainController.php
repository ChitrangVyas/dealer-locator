<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use App\Models\User;
use View;
use Route;
use Illuminate\Support\Facades\DB;
class MainController extends Controller
{
    
    public function __construct() {                
        list(, $action) = explode('@', Route::getCurrentRoute()->getActionName());        
        if($action=="create" || $action=="store"){
            View::share ( 'action_title', 'Add' );
        }else if($action=="edit" || $action=="update"){
            View::share ( 'action_title', 'Edit' );
        }
        View::share ( 'select2Old', new User());
        $settings = Settings::find(1);
        if(!empty($settings)){
            $setting = json_decode($settings->json_data);
            config(['app.timezone' => $setting->timezone]);
            config(['app.name' => $setting->site_name]);

            $mailConfig = [
                'transport' => $setting->mail_mailer,
                'host' => $setting->mail_host,
                'port' => $setting->mail_port,
                'encryption' => $setting->mail_encryption,
                'username' => $setting->mail_username,
                'password' => $setting->mail_password,
                'timeout' => null
            ];
            config(['mail.mailers.smtp' => $mailConfig]);            
            config(['mail.from.address' => $setting->mail_from_email]);
            config(['mail.from.name' => $setting->mail_from_name]);
        }        
        
    }


    public function timezone_list() {
        $zones_array = array();
        $timestamp = time();
        $dummy_datetime_object = new \DateTime();
        foreach(timezone_identifiers_list() as $key => $zone) {
        date_default_timezone_set($zone);
        $zones_array[$key]['zone'] = $zone;
        $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
    
        $tz = new \DateTimeZone($zone);
        $zones_array[$key]['offset'] = $tz->getOffset($dummy_datetime_object);
        }
  
        return $zones_array;
    }

}
