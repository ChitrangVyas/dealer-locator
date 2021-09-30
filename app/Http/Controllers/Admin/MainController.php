<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Settings;
use App\Models\State;
use Illuminate\Http\Request;
use App\Models\User;
use View;
use Route;
use Illuminate\Support\Facades\DB;
class MainController extends Controller
{
    public $country = 101;
    public $settingJson = [];
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
            $this->settingJson = json_decode($settings->json_data,true);
            $this->country = (!empty($this->settingJson['country'])) ? $this->settingJson['country'] : 101;
            if(!empty($this->settingJson)){
                config(['app.timezone' => $this->settingJson['timezone']]);
                config(['app.name' => $this->settingJson['site_name']]);
                config(['logo' => $this->settingJson['logo']]);
                config(['icon.logo' => $this->settingJson['icon_logo']]);
                $state = State::find($this->settingJson['state']);
                $city = City::find($this->settingJson['city']);
                $this->settingJson['state_name']=$state->name;
                $this->settingJson['city_name']=$city->name;
                $mailConfig = [
                    'transport' => $this->settingJson['mail_mailer'],
                    'host' => $this->settingJson['mail_host'],
                    'port' => $this->settingJson['mail_port'],
                    'encryption' => ($this->settingJson['mail_encryption']=='null') ? null :$this->settingJson['mail_encryption'],
                    'username' => $this->settingJson['mail_username'],
                    'password' => $this->settingJson['mail_password'],
                    'timeout' => null
                ];
                config(['mail.mailers.smtp' => $mailConfig]);            
                config(['mail.from.address' => $this->settingJson['mail_from_email']]);
                config(['mail.from.name' => $this->settingJson['mail_from_name']]);
            }
        }
        View::share('country',$this->country);        
        View::share('setting',$this->settingJson);        
        
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
