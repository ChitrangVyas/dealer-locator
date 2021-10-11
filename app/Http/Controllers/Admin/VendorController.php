<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerCreateRequest;
use App\Http\Requests\Admin\CustomerUpdateRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use View;

class VendorController extends MainController
{
    public function __construct() {
        parent::__construct();
        View::share ( 'title', 'Vendor' );
        View::share ( 'elementActive', 'vendor' );
        View::share ( 'select2Old', new User());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.vendor.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.vendor.create',['users_group_id'=>5]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerCreateRequest $request)
    {
        //
        $request_data = $request->all();
        $request_data['password'] = Hash::make('123456');
        $request_data['status'] = (!empty($request_data['status']))?1:0;
        if($request_data['users_group_id'] == 5){
            try{
                $user = User::create($request_data);
                $user['city'] = $user->city;
                //$user_data = $user->withAndWhereHas('city', function($query) use ($user){$query->where('id', $user->city_id);})->get()->toArray();
                
                if($request->ajax()){
                    return response()->json($user,200,[],1);
                }
            }catch(Exception $e){
                if($request->ajax()){
                    return response()->json(['exception'=> $e->getMessage()]);
                }
                return redirect(route('vendor.create'))->with('exception', $e->getMessage());
            }
        }else{
            return redirect(route('vendor.create'))->with('exception', 'Invalid User Group');
        }
        return view('admin.vendor.index');
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
        $user = User::findOrFail($id);
        $users_group_id = 5;
        return view('admin.vendor.create',compact('user','users_group_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerUpdateRequest $request, $id)
    {
        //
        $request_data = $request->except(['_token','_method']);
        $request_data['status'] = (!empty($request_data['status']))?1:0;
        if($request_data['users_group_id'] == 5){
            $user = User::where('id',$id)->update($request_data);
        }else{
            return redirect(route('vendor.create'))->with('exception', 'Invalid User Group');
        }
        return redirect(route('vendor.index'));
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
            if($user->users_group_id == 5){
                $user->delete();
            }
        }
    }

    public function vendorListAjaxData(Request $request){
        // DB::enableQueryLog(); 
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'users_group_id = 5 AND created_by='.auth()->user()->id;
        $where .= (!empty($search)) ? " AND (users.name like '$search%' OR email like '$search%' OR mobile like '$search%' OR cities.name like '$search%' OR states.name like '$search%' OR users_group.name like '$search%')":'';
        $users = DB::table('users')
        ->selectRaw('users_group.name as contact_type,users.id,users.name,email,mobile,cities.name as city, states.name as state,users.status')
        ->join('users_group','users.users_group_id','=','users_group.id','left')
        ->join('cities','users.city_id','=','cities.id','left')
        ->join('states','users.state_id','=','states.id','left')
        ->whereRaw($where)
        ->where('users.deleted_at', NULL)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get(); 
        $total_users = User::whereIN('users_group_id',['5'])->where('created_by',auth()->user()->id)->count();
        // dd(DB::getQueryLog());
        $data = [];
        foreach($users as $user){
            $city = (!empty($user->city))?$user->city:'N/A';
            $state = (!empty($user->state))?$user->state:'N/A';
            $status = (!empty($user->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status" class="form-control change-status" data-id="'.$user->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $action_html = "<span><a href=".route('vendor.edit',$user->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$user->id class='btn btn-danger' onClick=deleteUser($user->id)>Delete</button></span>";
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
