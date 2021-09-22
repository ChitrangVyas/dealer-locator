<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use View;
use Route;
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
