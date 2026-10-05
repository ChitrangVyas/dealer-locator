<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers;
use App\Http\Requests\Admin\DealerUserCreateRequest;
use App\Http\Requests\Admin\DealerUserUpdateRequest;
use App\Mail\Welcome;
use App\Models\Category;
use App\Models\Country;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductDiscount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use View;
class DealerUserController extends MainController
{
    public function __construct() {
        parent::__construct();
        View::share ( 'title', 'Dealer' );
        View::share ( 'elementActive', 'dealer' );        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('dealer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::get();
        $categories = Category::Where('status',1)->get();
        return view('dealer.create',compact('countries','categories'));
    }

    public function login(){
        return view('auth.login',['user_group'=>'2']);
    }

    public function forgotPassword(){
        return view('auth.passwords.email',['user_group'=>'2']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DealerUserCreateRequest $request)
    {   
        $request_data = $request->all();
        try{
            $user = User::create([
                'name' => $request_data['name'],
                'email' => $request_data['email'],
                'mobile' => $request_data['mobile'],
                'users_group_id' => $request_data['user_group'],
                'created_by' => $request_data['created_by'],
                'address' => $request_data['address'],
                'country_id' => $request_data['country_id'],
                'city_id' => $request_data['city'],
                'state_id' => $request_data['state'],
                'zipcode' => $request_data['zipcode'],
                'status' => (!empty($request_data['status']))?1:0,
                'latitude' => $request_data['latitude'],
                'longitude' => $request_data['longitude'],
            ]);
            if((!empty($user)) && (!empty($request_data['categories']))){
                $user->dealerCategory()->sync($request_data['categories']);
            }
        }catch(Exception $e){
            return redirect(route('dealer.create'))->with('exception', $e->getMessage());
        }
        return redirect(route('dealer.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $user = User::findOrFail($id);       
        $countries = Country::get(); 
        $categories = Category::Where('status',1)->get();
        $dealer_category = $user->dealerCategory()->pluck('category_id')->toArray();
        return view('dealer.create',compact('user','countries','categories','dealer_category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DealerUserUpdateRequest $request, $id)
    {
        $user_data = User::findOrFail($id);
        $request_data = $request->all();
        $upload_data =[
            'name' => $request_data['name'],
            'email' => $request_data['email'],
            'mobile' => $request_data['mobile'],
            'users_group_id' => $request_data['user_group'],
            'created_by' => $request_data['created_by'],
            'address' => $request_data['address'],
            'country_id' => $request_data['country_id'],
            'city_id' => $request_data['city'],
            'state_id' => $request_data['state'],
            'zipcode' => $request_data['zipcode'],
            'status' => (!empty($request_data['status']))?1:0,
            'latitude' => $request_data['latitude'],
            'longitude' => $request_data['longitude'],
        ];
        $user = User::where('id',$id)->first();
        if((!empty($user->update($upload_data))) && (!empty($request_data['categories']))){
            $user->dealerCategory()->sync($request_data['categories']);
        }
        return redirect(route('dealer.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        if(!empty($id)){
            $user = User::findOrFail($id);
            $user->delete();
        }
    }
    public function storeImage($request,$fileName,$input){
        if((!empty($fileName)) && (!empty($input))) {
            $filePath = $request->file($input)->storeAs('uploads/userlogo', $fileName, 'public');
            return '/storage/' . $filePath;
        }
        return '';
    }
    public function listAjaxData(Request $request){
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'users_group_id = 2';
        $where .= (!empty($search)) ? " AND (users.name like '%$search%' OR email like '%$search%' OR mobile like '%$search%' OR cities.name like '%$search%' OR states.name like '%$search%')":'';
        $users = DB::table('users')
        ->selectRaw('users.id,users.name,email,mobile,cities.name as city, states.name as state,users.status,users.zipcode')
        ->join('cities','users.city_id','=','cities.id')
        ->join('states','users.state_id','=','states.id')
        ->whereRaw($where)
        ->where('deleted_at', NULL)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();

        $total_users = DB::table('users')
        ->selectRaw('users.id,users.name,email,mobile,cities.name as city, states.name as state,users.status')
        ->join('cities','users.city_id','=','cities.id')
        ->join('states','users.state_id','=','states.id')->whereRaw($where)->count();
        $data = [];
        foreach($users as $user){
            $city = (!empty($user->city))?$user->city:'';
            $state = (!empty($user->state))?$user->state:'';
            $indiVidual=  User::where('id','=',$user->id)->first();
            $action_html = "<span><a href=".route('dealer.edit',$user->id)." class='btn btn-primary'><i class='fas fa-pen' title='Edit'></i></a></span>";
            $child_user =  Helpers::getChildUserCount($user->id);
            $status = (!empty($user->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status" class="form-control change-status" data-id="'.$user->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $action_html .= "<span class='ml-3'><button type=submit data-user=$user->id class='btn btn-danger' onClick=deleteUser($user->id)><i class='fas fa-trash' title='Delete'></i></button></span>";
            $data[] = [$user->id,$user->name,$user->zipcode,$indiVidual->getMobileWithCountryCode(),$city,$state,$status_html,$action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_users;
        $return_array['recordsFiltered'] = $total_users;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }
}
