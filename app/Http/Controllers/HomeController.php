<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $elementActive="home";
        $title = 'Dashboard';
        $user = auth()->user();
        $total_order = Order::where('type','sale')->where('from_user',auth()->user()->id)->where('deleted_at',NULL)->count();
        $total_po=0;
        $total_dealer = 0;
        $total_dealer = User::where('users_group_id',2)->where('deleted_at',NULL)->count();
        $total_retailer = User::where('users_group_id',3)->where('deleted_at',NULL)->count();
        if(empty($user->userGroup) || $user->userGroup->id == '1'){
            $total_po = Order::where('type','po')->where('from_user',$user->id,function($query){
                return $query->orWhere('for_manufacturer', 1);
            })->where('deleted_at',NULL)->count();
        }else{
            $total_po = Order::where('type','po')->where('from_user',$user->id)->where('deleted_at',NULL)->count();
        }
        return view('home',compact('elementActive','title','total_order','total_po','total_dealer','total_retailer'));
    }
}
