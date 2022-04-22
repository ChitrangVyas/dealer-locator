<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Settings;
use App\Models\State;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\User;

use View;
use Route;
use Illuminate\Support\Facades\DB;
class MainController extends Controller
{
    public $country = 101;
    public $allowed_country = [101];
    public $settingJson = [];
    public $allowedcountry=[];
    public $currency= false;
    public $msg = array(
        'inquiry_assign'=>'Hello {name} You are assigned a new Inquiry No. {inquiry_no} Please login to your account to view more details. - Kashyap Technology EWTIND',
        'inquiry_customer_assigned'=>'Hello {name},\nyour Inquiry No. {inquiry_no} has been assigned.\n{user_type} : {fullname},\nM: {mobile}\nEmail : {email}\nKashyap Technology\nEWTIND',
    );
    
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
            $this->allowed_country = (!empty($this->settingJson['allowed_countries'])) ? $this->settingJson['allowed_countries'] : [101];
            if(!empty($this->settingJson)){
              
                if(!empty($this->settingJson['timezone'])){
                    config(['app.timezone' => $this->settingJson['timezone']]);
                }
                config(['app.name' => $this->settingJson['site_name']]);
                if(!empty($this->settingJson['logo'])){
                config(['logo' => $this->settingJson['logo']]);
                }
                config(['adminlte.title'=>$this->settingJson['site_name']]);
                if(!empty($this->settingJson['icon_logo'])){
                    config(['icon.logo' => $this->settingJson['icon_logo']]);
                }
                if(!empty($this->settingJson['currency'])){
                    $this->currency=Currency::find($this->settingJson['currency']);                    
                }else{
                    $this->currency=Currency::find(1);
                }
                $state = State::find($this->settingJson['state']);
                $city = City::find($this->settingJson['city']);
                $country = Country::find($this->settingJson['country']);
                
                $this->settingJson['state_name']=(!empty($state))?$state->name:'';
                $this->settingJson['city_name']=(!empty($city))?$city->name:'';
                $this->settingJson['country_name']=(!empty($country))?$country->name:'';
                $mailConfig['transport'] = $this->settingJson['mail_mailer'];
                if($this->settingJson['mail_mailer']=="sendmail"){
                    $mailConfig['path']='/usr/sbin/sendmail -bs';
                }else{
                    $mailConfig = array_merge($mailConfig,[                    
                        'host' => $this->settingJson['mail_host'],
                        'port' => $this->settingJson['mail_port'],
                        'encryption' => ($this->settingJson['mail_encryption']=='null') ? null :$this->settingJson['mail_encryption'],
                        'username' => $this->settingJson['mail_username'],
                        'password' => $this->settingJson['mail_password'],
                        'timeout' => null
                    ]);
                }
                config(['mail.mailers.smtp' => $mailConfig]);            
                config(['mail.from.address' => $this->settingJson['mail_from_email']]);
                config(['mail.from.name' => $this->settingJson['mail_from_name']]);
                $allowedcountry=$this->getAllowedCountries($this->allowed_country);
            }
        }
        
        View::share('country',$this->country);
        View::share('allowedcountry',$allowedcountry);        
        View::share('setting',$this->settingJson);  
        View::share('currency',$this->currency);
        
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
    public function getAllowedCountries($countryalllowed){      
      return  Country::whereIn('id',$countryalllowed)->get();   
    }
    public function getMsg($key){
        return $this->msg[$key];
    }
    public function currency_list()
    {
        $currency_list = array();
        $currency_data = DB::select('select * from currencies where status = 1'); 
        foreach ($currency_data as $key => $currency) {
            $currency_list[$key]['id'] = $currency->id;
            $currency_list[$key]['name'] = $currency->name;
        }
        return $currency_list;
    }

}
