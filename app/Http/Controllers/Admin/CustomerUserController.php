<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerCreateRequest;
use App\Http\Requests\Admin\CustomerUpdateRequest;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use View;
class CustomerUserController extends Controller
{
    public function __construct() {
        View::share ( 'title', 'Contact' );
        View::share ( 'elementActive', 'customer' );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.customer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.customer.create');
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
        if($request_data['users_group_id'] == 4 || $request_data['users_group_id'] == 5){
        try{
            $user = User::create($request_data);
        }catch(Exception $e){
            return redirect(route('customer.create'))->with('exception', $e->getMessage());
        }
    }
        return view('admin.customer.index');
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
        return view('admin.customer.create',compact('user'));
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
        if($request_data['users_group_id'] == 4 || $request_data['users_group_id'] == 5){
            $user = User::where('id',$id)->update($request_data);
        }
        return redirect(route('customer.index'));
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
        // DB::enableQueryLog(); 
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'users_group_id IN (4,5)';
        $where .= (!empty($search)) ? " AND (users.name like '$search%' OR email like '$search%' OR mobile like '$search%' OR cities.name like '$search%' OR states.name like '$search%' OR users_group.name like '$search%')":'';
        $users = DB::table('users')
        ->selectRaw('users_group.name as contact_type,users.id,users.name,email,mobile,cities.name as city, states.name as state')
        ->join('users_group','users.users_group_id','=','users_group.id','left')
        ->join('cities','users.city_id','=','cities.id','left')
        ->join('states','users.state_id','=','states.id','left')
        ->whereRaw($where)
        ->where('users.deleted_at', NULL)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get(); 
        $total_users = User::whereIN('users_group_id',['4','5'])->count();
        // dd(DB::getQueryLog());
        $data = [];
        foreach($users as $user){
            $city = (!empty($user->city))?$user->city:'N/A';
            $state = (!empty($user->state))?$user->state:'N/A';
            $action_html = "<span><a href=".route('customer.edit',$user->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$user->id class='btn btn-danger' onClick=deleteUser($user->id)>Delete</button></span>";
            $data[] = [$user->id,$user->contact_type,$user->name,$user->email,$user->mobile,$city,$state,$action_html];
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
