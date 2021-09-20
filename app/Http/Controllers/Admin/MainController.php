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

}
