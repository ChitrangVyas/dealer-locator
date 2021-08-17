<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminUserCreateRequest;
use App\Http\Requests\Admin\AdminUserUpdateRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use View;
class AdminUserController extends MainController
{
    public function __construct() {
        View::share ( 'title', 'Staff User' );
        View::share ( 'elementActive', 'staff' );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('staff.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('staff.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserCreateRequest $request)
    {   
        //
        $request_data = $request->all();
        if($request_data['user_group'] == '1'){
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
            }catch(Exception $e){               
                return redirect(route('staff.create'))->with('exception', $e->getMessage());
            }
        }
        return redirect(route('staff.index'));
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
        return view('staff.create',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUserUpdateRequest $request, $id)
    {
        //
        $request_data = $request->all();
        $data = [
            'name' => $request_data['name'],
            'email' => $request_data['email'],
            'mobile' => $request_data['mobile'],
            'users_group_id' => $request_data['user_group'],
            'created_by' => $request_data['created_by'],
            'address' => $request_data['address'],
            'city_id' => $request_data['city'],
            'state_id' => $request_data['state'],
            'zipcode' => $request_data['zipcode'],
        ];

        if(!empty($request['password'])){
            $data['password'] = Hash::make($request_data['password']);
        }

        if($request_data['user_group'] == '1'){
            try{
                $user = User::where('id',$id)->update($data);
            } catch(Exception $e){
                return redirect(route('staff.edit'));
            }

        }
        return redirect(route('staff.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!empty($id)){
            $user = User::findOrFail($id);
            if($user->users_group_id == '1'){
                if($user->delete()){
                    $return_array = ['message'=>'User successfully deleted'];
                    return response(json_encode($return_array), 200)
                        ->header('Content-Type', 'application/json');
                }
            }
        }
    }

    public function listAjaxData(Request $request){
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'users_group_id = 1';
        $where .= (!empty($search)) ? " AND (users.name like '$search%' OR email like '$search%' OR mobile like '$search%' OR cities.name like '$search%' OR states.name like '$search%')":'';
        $users = DB::table('users')
        ->selectRaw('users.id,users.name,email,mobile,cities.name as city, states.name as state')
        ->join('cities','users.city_id','=','cities.id')
        ->join('states','users.state_id','=','states.id')
        ->whereRaw($where)
        ->where('deleted_at', NULL)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();

        $total_users = User::where('users_group_id','=','1')->count();
        $data = [];
        foreach($users as $user){
            $city = (!empty($user->city))?$user->city:'';
            $state = (!empty($user->state))?$user->state:'';
            $action_html = "<span><a href=".route('staff.edit',$user->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$user->id class='btn btn-danger' onClick=deleteUser($user->id)>Delete</button></span>";
            $data[] = [$user->id,$user->name,$user->email,$user->mobile,$city,$state,$action_html];
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
