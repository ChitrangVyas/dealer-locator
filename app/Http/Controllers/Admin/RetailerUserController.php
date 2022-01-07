<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\MainController;
use App\Http\Helpers;
use App\Http\Requests\Admin\RetailerUserCreateRequest;
use App\Http\Requests\Admin\RetailerUserUpdateRequest;
use App\Mail\Welcome;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Illuminate\Support\Str;
use View;

class RetailerUserController extends MainController
{
    public function __construct() {
        parent::__construct();
        View::share ( 'title', 'Retailer' );
        View::share ( 'elementActive', 'retailer' );        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('retailer.index');
    }

    public function login(){
        return view('auth.login',['user_group'=>'3']);
    }
    
    public function forgotPassword(){
        return view('auth.passwords.email',['user_group'=>'3']);
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('retailer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RetailerUserCreateRequest $request)
    {   
        //
        $request_data = $request->all();
        $dealer = (auth()->user()->users_group_id == 2)?auth()->user()->id:$request_data['dealer'];
        if($request_data['user_group'] == 3 && (!empty($dealer))){
        try{
            if(!empty($request->file('logo'))){
                $fileName = time().'_'. Str::snake($request->logo->getClientOriginalName());
                $filepath = $this->storeImage($request,$fileName,'logo');
                $request_data['logo'] = $filepath;
            }
            $user = User::create([
                'parent_id' => $dealer,
                'name' => $request_data['name'],
                'email' => $request_data['email'],
                'password' => Hash::make($request_data['password']),
                'mobile' => $request_data['mobile'],
                'users_group_id' => $request_data['user_group'],
                'created_by' => $request_data['created_by'],
                'address' => $request_data['address'],
                'country_id' => $request_data['country_id'],
                'city_id' => $request_data['city'],
                'state_id' => $request_data['state'],
                'zipcode' => $request_data['zipcode'],
                'status' => (!empty($request_data['status']))?1:0,
                'gstin' => $request_data['gstin'],
                'logo' => $request_data['logo'],
                'footer_text' => $request_data['footer_text'],
            ]);
            Mail::to($user->email)->send(new Welcome($user,"Welcome to ".config('app.name'),$request_data['password'],url(route('dealer.login'))));
        }catch(Exception $e){
            return redirect(route('retailer.create'))->with('exception', $e->getMessage());
        }
    }
        return redirect(route('retailer.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //'
        $parent_user = auth()->user();
        $user = User::find($id);
        if($parent_user && (empty($parent_user->users_group_id) || $parent_user->users_group_id == 2)){
            if($parent_user->users_group_id == 2){
                if($parent_user->id == $user->parent_id){
                    if(!empty($user)){
                        return view('retailer.create',compact('user'));
                    }
                }else{
                    return abort(404);    
                }
            }else{
                return view('retailer.create',compact('user'));
                
            }
            
        }
        return abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RetailerUserUpdateRequest $request, $id)
    {
        //
        $request_data = $request->all();
        $request_data['status'] = (!empty($request_data['status']))?1:0;
        if($request_data['user_group'] == 3){
            if(!empty($request->file('logo'))){
                $fileName = time().'_'. Str::snake($request->logo->getClientOriginalName());
                $filepath = $this->storeImage($request,$fileName,'logo');
                $request_data['logo'] = $filepath;
            }
            $user = User::where('id',$id)->update([
                'parent_id' => $request_data['dealer'],
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
                'status' => $request_data['status'],
                'gstin' => $request_data['gstin'],
                'logo' => $request_data['logo'],
                'footer_text' => $request_data['footer_text'],
            ]);
        }
        return redirect(route('retailer.index'));
    }
    public function storeImage($request,$fileName,$input){
        if((!empty($fileName)) && (!empty($input))) {
            $filePath = $request->file($input)->storeAs('uploads/userlogo', $fileName, 'public');
            return '/storage/' . $filePath;
        }
        return '';
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

    public function listAjaxData(Request $request){
        $user =  auth()->user();
        $parent_id = '';
        if((!empty($user->userGroup)) && $user->userGroup->id == '2'){
            $parent_id = $user->id;
        }

        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'users_group_id = 3';
        $where .= (!empty($parent_id))?" AND parent_id=$parent_id":'';
        $where .= (!empty($search)) ? " AND (users.name like '$search%' OR email like '$search%' OR mobile like '$search%' OR cities.name like '$search%' OR states.name like '$search%')":'';
        $users = DB::table('users')
        ->selectRaw('users.id,users.name,email,mobile,cities.name as city, states.name as state,users.status')
        ->join('cities','users.city_id','=','cities.id')
        ->join('states','users.state_id','=','states.id')
        ->whereRaw($where)
        ->where('deleted_at', NULL)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();

        $total_users = User::where('users_group_id','=','3')->count();
        $data = [];
        foreach($users as $user){
            $city = (!empty($user->city))?$user->city:'';
            $state = (!empty($user->state))?$user->state:'';
            $total_assigned_inquiries =  Helpers::getAssignedRetailerInquiryCount($user->id);
            $status = (!empty($user->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status" class="form-control change-status" data-id="'.$user->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $action_html = "<span><a href=".route('retailer.edit',$user->id)." class='btn btn-primary'>Edit</a></span>";
            if(empty($total_assigned_inquiries)){
                $action_html .= "<span class='ml-3'><button type=submit data-user=$user->id class='btn btn-danger' onClick=deleteUser($user->id)>Delete</button></span>";
            }else{
                $action_html .= "<span class='ml-3'><span class='badge badge-info' title='Inquires'>$total_assigned_inquiries</span></span>";
            }
            $data[] = [$user->id,$user->name,$user->email,$user->mobile,$city,$state,$status_html,$action_html];
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
