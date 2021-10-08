<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers;
use App\Http\Requests\Admin\DealerUserCreateRequest;
use App\Http\Requests\Admin\DealerUserUpdateRequest;
use App\Mail\Welcome;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        //
        return view('dealer.create');
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
        //
        $request_data = $request->all();
        try{
            $user = User::create([
                'name' => $request_data['name'],
                'email' => $request_data['email'],
                'password' => Hash::make($request_data['password']),
                'mobile' => $request_data['mobile'],
                'users_group_id' => $request_data['user_group'],
                'created_by' => $request_data['created_by'],
                'address' => $request_data['address'],
                'city_id' => $request_data['city'],
                'state_id' => $request_data['state'],
                'zipcode' => $request_data['zipcode'],
            ]);
            Mail::to($user->email)->send(new Welcome($user,"Welcome to ".config('app.name'),$request_data['password'],url(route('dealer.login'))));
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
        //
        $user = User::findOrFail($id);        
        return view('dealer.create',compact('user'));
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
        //
        $request_data = $request->all();
        $request_data['status'] = (!empty($request_data['status']))?1:0;
        $user = User::where('id',$id)->update([
            'name' => $request_data['name'],
            'email' => $request_data['email'],
            'mobile' => $request_data['mobile'],
            'users_group_id' => $request_data['user_group'],
            'created_by' => $request_data['created_by'],
            'address' => $request_data['address'],
            'city_id' => $request_data['city'],
            'state_id' => $request_data['state'],
            'zipcode' => $request_data['zipcode'],
            'status' => $request_data['status'],
        ]);
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

    public function listAjaxData(Request $request){
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'users_group_id = 2';
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

        $total_users = User::where('users_group_id','=','2')->count();
        $data = [];
        foreach($users as $user){
            $city = (!empty($user->city))?$user->city:'';
            $state = (!empty($user->state))?$user->state:'';
            $action_html = "<span><a href=".route('dealer.edit',$user->id)." class='btn btn-primary'>Edit</a></span>";
            $child_user =  Helpers::getChildUserCount($user->id);
            $status = (!empty($user->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status" class="form-control change-status" data-id="'.$user->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $total_assigned_inquiries =  Helpers::getAssignedDealerInquiryCount($user->id);
            if(empty($child_user) && empty($total_assigned_inquiries)){
                $action_html .= "<span class='ml-3'><button type=submit data-user=$user->id class='btn btn-danger' onClick=deleteUser($user->id)>Delete</button></span>";
            }else{
                $action_html .= "<span class='ml-3'><span class='badge badge-info' title='Retailers'>$child_user</span> |  <span class='badge badge-info' title='Inquiries'>$total_assigned_inquiries</span></span>";
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
