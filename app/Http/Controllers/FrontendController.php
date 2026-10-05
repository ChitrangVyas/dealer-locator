<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MainController;
use App\Models\Category;
use App\Models\City;
use App\Models\DealerCategory;
use App\Models\State;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class FrontendController extends MainController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $title = 'Symphony Limited';
        $categories = Category::withoutTrashed()->where('status',1)->get();
        return view('frontend.index', compact('title','categories'));
    }
    public function getState(Request $request)
    {
        $states = DealerCategory::Select('states.*')->leftJoin('users','users.id','=','dealer_category.dealer_id')->leftJoin('states','states.id','=','users.state_id')->where('dealer_category.category_id',$request->get('id'))->groupBy('users.state_id')->orderBy('states.name','ASC')->get();
        //dd($states);
        echo '<option value="">Please select State</option>';
        foreach($states as $state){
            echo "<option value='".$state->id."'>".$state->name."</option>";
        }       
    }
    public function getValue(Request $request)
    {
        $cities =  DealerCategory::Select('cities.*')->leftJoin('users','users.id','=','dealer_category.dealer_id')->leftJoin('cities','cities.id','=','users.city_id')->where('users.state_id',$request->get('id'))->where('dealer_category.category_id',$request->get('category_id'))->groupBy('users.city_id')->orderBy('cities.name','ASC')->get();
        echo '<option value="">Please select City</option>';
        foreach($cities as $city){
            $cityname = ucwords(strtolower($city->name));
            echo "<option value='".$city->id."'>".$cityname."</option>";
        }       
    }
    public function getDealers(Request $request)
    {
        
        if(!empty($request->input('lat')) &&  !empty($request->input('long'))){
            $lat = round($request->input('lat'),7);
            $long = round($request->input('long'),7);
            $radius = $request->input('radius');
            $dealers = User::select(DB::raw('users.name,address,mobile,latitude,longitude,city_id,cities.name as cname,( 6367 * acos( cos( radians('.$lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$long.') ) + sin( radians('.$lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))->leftJoin('dealer_category','dealer_category.dealer_id','=','users.id')->leftJoin('cities','cities.id','=','users.city_id')->where('dealer_category.category_id',$request->get('category_id'))->having('distance', '<', $request->input('radius'))->orderBy('distance')->get();
            //dd( $dealers);
            $total = count($dealers);
            
        }
        
        return view('frontend.dealer',compact('dealers','total','radius'));
    }
}
