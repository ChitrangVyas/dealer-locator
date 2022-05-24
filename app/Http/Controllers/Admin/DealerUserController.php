<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers;
use App\Http\Requests\Admin\DealerUserCreateRequest;
use App\Http\Requests\Admin\DealerUserUpdateRequest;
use App\Mail\Welcome;
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
        $request_data = $request->all();
        try{
            $filepath = '';
            if(!empty($request->file('logo'))){
                $fileName = time().'_'. Str::snake($request->logo->getClientOriginalName());
                $filepath = $this->storeImage($request,$fileName,'logo');
                //$request_data['logo'] = $filepath;
            }
            $user = User::create([
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
                'logo' => $filepath,
                'footer_text' => $request_data['footer_text'],
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
    public function showProduct(Request $request)
    {
        $users = User::findOrFail($request->get('data')['user_id']);
        $products = DB::table('products')
        ->selectRaw('products.id,products.name,products.price,product_discounts.discount_percentage')
        ->leftjoin("product_discounts",function($join) use ($request){
            $join->on("product_discounts.product_id","=","products.id")
                ->on("product_discounts.seller_id","=",DB::raw(!empty(auth()->user()->parent_id)?auth()->user()->id:0))
                ->on("product_discounts.buyer_id","=",DB::raw($request->get('data')['user_id']));

        },'left')
        ->where('product_discounts.deleted_at', NULL)
        ->get();   
        $json['cart_content'] = view('dealer.show',['users'=>$users,'products'=>$products])->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }
    public function addProductdiscount(Request $request)
    {
        $request_data = $request->all();
        $add_data = [];
        $i=0;
        $affectedRows = ProductDiscount::where('seller_id', $request_data['seller_id'])->where('buyer_id', $request_data['buyer_id'])->delete();       
        foreach($request_data['product_id'] as $k=>$v){
            if(!empty($request_data['discount_percentage'][$k])){
                $add_data['product_id']=$k;
                $add_data['discount_percentage'] = $request_data['discount_percentage'][$k];
                $add_data['seller_id'] = $request_data['seller_id'];
                $add_data['buyer_id'] = $request_data['buyer_id'];            
                $product_discount = ProductDiscount::create($add_data);
                $i++;
            }
        }       
        return response()->json(['status' => 'successful', 'code'=>1,'message'=>'Product Discount added Succesfully']);
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
            'gstin' => $request_data['gstin'],
            'footer_text' => $request_data['footer_text'],
        ];
        if(!empty($request->file())){
            $filename = str_replace('/storage/uploads/userlogo/', '', $user_data->logo);
            if(!empty($user_data->logo) && file_exists(storage_path('app/public/uploads/userlogo/'.$filename))){
                unlink(storage_path('app/public/uploads/userlogo/'.$filename));
            }
            $fileName = time().'_'. Str::snake($request->logo->getClientOriginalName());
            $filepath = $this->storeImage($request,$fileName,'logo');
            $upload_data['logo'] = $filepath;
        }
        
        $upload_data['status'] = (!empty($request_data['status']))?1:0;
        $user = User::where('id',$id)->update($upload_data);
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
            $indiVidual=  User::where('id','=',$user->id)->first();
            $action_html = "<span><a href=".route('dealer.edit',$user->id)." class='btn btn-primary'><i class='fas fa-pen' title='Edit'></i></a></span>";
            $child_user =  Helpers::getChildUserCount($user->id);
            $status = (!empty($user->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status" class="form-control change-status" data-id="'.$user->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $total_assigned_inquiries =  Helpers::getAssignedDealerInquiryCount($user->id);
            if(empty($child_user) && empty($total_assigned_inquiries)){
                $action_html .= "<span class='ml-3'><button type=submit data-user=$user->id class='btn btn-danger' onClick=deleteUser($user->id)><i class='fas fa-trash' title='Delete'></i></button></span>";
            }else{
                $action_html .= "<span class='ml-3'><span class='badge badge-info' title='Retailers'>$child_user</span> |  <span class='badge badge-info' title='Inquiries'>$total_assigned_inquiries</span></span>";
            }
            $action_html .="<span class='ml-3'><a data-url=".route('dealer.showProduct',$user->id)." data-product=".$user->id." class='btn btn-primary currency-btn view-product' data-toggle='modal' data-target='#productModal' title='View'><b>".$this->currency->symbol."</b></a></span>";
            $data[] = [$user->id,$user->name,$user->email,$indiVidual->getMobileWithCountryCode(),$city,$state,$status_html,$action_html];
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
