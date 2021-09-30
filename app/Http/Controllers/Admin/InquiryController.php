<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\InquiriesCreateRequest;
use App\Http\Requests\Admin\InquiriesUpdateRequest;
use App\Models\InquiriesSource;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Null_;
use View;

class InquiryController extends MainController
{
    public function __construct() {
        parent::__construct();
        View::share ( 'title', 'Inquiry' );
        View::share ( 'elementActive', 'inquiry' );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.inquiry.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $inquiries_source = InquiriesSource::withoutTrashed()->get();
        return view('admin.inquiry.create',compact('inquiries_source'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InquiriesCreateRequest $request)
    {   
        $request_data = $request->all();
        $dealer_id = NULL;
        if(auth()->user()->users_group_id == '2'){
            $dealer_id = auth()->user()->id;
        }elseif (auth()->user()->users_group_id == '3') {
            $dealer_id = auth()->user()->parent_id;
            $retailer_id = (!empty($request_data['retailer']))?$request_data['retailer']:auth()->user()->id;
        }else{
            $dealer_id = $request_data['dealer'];
            $retailer_id = $request_data['retailer'];
        }
        $inquiry_data = [
            'inquiries_source_id'=>$request_data['inquiry_source'],
            'customer_id'=>$request_data['customer'],
            'created_by'=>$request_data['created_by'],
            'dealer_id' => $dealer_id,
            // 'product_id'=>$request_data['product'],
        ];
        if(empty($inquiry_data['status'])){
            $inquiry_data['status'] = 1;
        }
        if(!empty($retailer_id)){
            $inquiry_data['retailer_id'] = $retailer_id;
        }
        try{
            if(!empty($request_data['product'])){
                $products = Product::find($request_data['product']);
                $manufacturer = '';
                foreach($products as $product){
                    $manufacturer = (empty($manufacturer))?$product->brand->manufacturer->id:$manufacturer;
                    if($manufacturer != $product->brand->manufacturer->id){
                        return redirect(route('inquiry.create'))->with('exception', 'Please Select the Same Manufacturer Product');
                    }
                }
            }
            $inquiry = Inquiry::create($inquiry_data);
            if(!empty($request_data['product'])){
                $inquiry->product()->sync($products);
            }
        }catch(Exception $e){
            return redirect(route('inquiry.create'))->with('exception', $e->getMessage());
        }
        return view('admin.inquiry.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $inquiry = Inquiry::findOrFail($request->input('data')['id']);
        $inquiry_products =  (!empty($inquiry->product->count()))? implode(', ',$inquiry->product->pluck('name')->toArray()):'N/A';
        $json['modal_title'] = "Inquiry View";
        $json['modal_content'] = view('admin.inquiry.view',['inquiry'=>$inquiry,'inquiry_products'=>$inquiry_products])->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);   
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
        $inquiry = Inquiry::findOrFail($id);
        $inquiries_source = InquiriesSource::withoutTrashed()->get();
        return view('admin.inquiry.create',compact('inquiry','inquiries_source'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InquiriesUpdateRequest $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $request_data = $request->all();
        $is_inquiry_updated ='';
        $update_data = [
            'inquiries_source_id'=>$request_data['inquiry_source'],
            'customer_id'=>$request_data['customer'],
            // 'product_id'=>$request_data['product'],
        ];
        if(!empty($request_data['dealer'])){
            $update_data['dealer_id'] = $request_data['dealer'];
        }
        if(!empty($request_data['retailer'])){
            $update_data['retailer_id']=$request_data['retailer'];
        }
        try{
            $is_inquiry_updated = $inquiry->where('id',$id)->update($update_data);
        }catch(Exception $e){
            return redirect(route('inquiry.create'))->with('exception', $e->getMessage());
        }
        $products= [];
        if($is_inquiry_updated && (!empty($request_data['product']))){
            $products = Product::find($request_data['product']);
        }
        $inquiry->product()->sync($products);
        return redirect(route('inquiry.index'));
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
            $user = Inquiry::findOrFail($id);
            $user->delete();
        }
    }

    public function assignToRetailer(Request $request)
    {
        $retailers = User::where('users_group_id',3)->where('parent_id',auth()->user()->id)->get();
        return view('admin.inquiry.assignretailer',['retailers'=>$retailers]);
    }

    public function updateInquiry(Request $request){
        Validator::make($request->all(), [
            // 'retailer' => 'required',
            'id' => 'required',
        ])->validate();
        if($request->ajax()){
            $update_data['retailer_id'] = $request->input('retailer');
            $inquiry = Inquiry::findOrFail($request->input('id'));
            
            try{
                $is_inquiry_updated = $inquiry->where('id',$request->input('id'))->update($update_data);
            }catch(Exception $e){
                return response()->json(['status' => 'error','code'=>0,'message'=>$e->getMessage()]);
            }
            if(!empty($is_inquiry_updated)){
                return response()->json(['status' => 'successful', 'data'=>[],'code'=>1,'message'=>'Retailer has been successfully assigned.']);
            }
        }else{
            return response()->json(['status' => 'error','code'=>0,'message'=>'Bad Method Call.']);
        }
    }

    public function getInquiryStatus(Request $request){
        $inquiry = Inquiry::findOrFail($request->input('data')['id']);

        $json['modal_title'] = "Inquiry Status";
        $json['modal_content'] = view('admin.inquiry.status',['inquiry'=>$inquiry])->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function getInquiries(Request $request){
        $user = auth()->user();
        DB::enableQueryLog();
        $inquiries = [];
        if((empty($user->users_group_id)) || $user->users_group_id == '1'){
            $inquiries = Inquiry::where('dealer_id',NULL)->where('retailer_id',NULL)->latest()->take(5)->get();
        }else if((!empty($user->users_group_id)) && $user->users_group_id == '2'){
            $inquiries = Inquiry::where('dealer_id',$user->id)->latest()->take(5)->get();
        }else if((!empty($user->users_group_id)) && $user->users_group_id == '3'){
            $inquiries = Inquiry::where('retailer_id',$user->id)->latest()->take(5)->get();
        }
        $json['modal_title'] = "Inquiries";
        $json['modal_content'] = view('layouts.inquiry',['inquiries'=>$inquiries])->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function updateInquiryStatus(Request $request){
        Validator::make($request->all(), [
            'status' => 'required',
            'inquiry_id' => 'required',
        ])->validate();
        if($request->ajax()){
            $update_data['status'] = $request->input('status');
            $inquiry = Inquiry::findOrFail($request->input('inquiry_id'));
            
            try{
                $is_inquiry_updated = $inquiry->where('id',$request->input('inquiry_id'))->update($update_data);
            }catch(Exception $e){
                return response()->json(['status' => 'error','code'=>0,'message'=>$e->getMessage()]);
            }
            if(!empty($is_inquiry_updated)){
                return response()->json(['status' => 'successful', 'data'=>[],'code'=>1,'message'=>'Status has been successfully Updated.']);
            }
        }else{
            return response()->json(['status' => 'error','code'=>0,'message'=>'Bad Method Call.']);
        }
    }


    public function listAjaxData(Request $request){
        $user =  auth()->user();
        $dealer_id = '';
        $retailer_id = '';
        if((!empty($user->userGroup)) && $user->userGroup->id == '2'){
            $dealer_id = $user->id;
        }else if((!empty($user->userGroup)) && $user->userGroup->id == '3'){
            $retailer_id = $user->id;
        }

        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'inquiries.deleted_at is NULL';
        $where .= (!empty($dealer_id))?" AND (dealer_id=$dealer_id OR inquiries.created_by=$dealer_id)":'';
        $where .= (!empty($retailer_id))?" AND (retailer_id=$retailer_id OR inquiries.created_by=$retailer_id)":'';
        $where .= (!empty($search)) ? " AND (c.name like '$search%' OR p.name like '$search%' OR source like '%$search%' OR (d.name is NOT NULL AND d.name like '$search%'))":'';
        $inquiries = DB::table('inquiries')
        ->select(DB::raw('inquiries.id, inquiries.status, inquiries.created_by,source,c.name as customer,c.email AS customer_email,c.mobile AS customer_mobile,d.name as dealer, r.name as retailer,group_concat(p.name SEPARATOR "<br>") as product'))
        ->join('inquiries_products','inquiries.id','=','inquiries_products.inquiry_id','left')
        ->join('inquiries_source','inquiries.inquiries_source_id','=','inquiries_source.id','left')
        ->join('users AS c','inquiries.customer_id','=','c.id','left')
        ->join('products AS p','inquiries_products.product_id','=','p.id','left')
        ->join('users AS d','inquiries.dealer_id','=','d.id','left')
        ->join('users AS r','inquiries.retailer_id','=','r.id','left')
        ->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->groupby('inquiries.id')
        ->get();
        $total_inquiries = Inquiry::whereRaw($where)->count();
        $data = [];
        foreach($inquiries as $inquiry){
            $dealer = (!empty($inquiry->dealer))?$inquiry->dealer:'N/A';
            $retailer = (!empty($inquiry->retailer))?$inquiry->retailer:'N/A';
            $action_html = "";
            $inquiryObj = Inquiry::find($inquiry->id);
            $inquiry_status = (!empty($inquiryObj->status) && $inquiryObj->status !=0 )?$inquiryObj->getInquirystatusName():'N/A';
            $status_html = "<button type='button' class='btn btn-info change-status btn-sm' data-inquiry=$inquiry->id data-change-status-url='".route('inqiury.status')."'>".$inquiry_status."</button>";
            $action_html .= "<span><button type='button' id='addLog' class='btn btn-default' data-inquiry=$inquiry->id data-view-url='".route('inquiry-log.show',$inquiry->id)."' data-add-url='".route('inquiry-log.create')."' data-task-url='".route('createReminder')."' data-toggle='modal' data-target='#modal-lg' title='View Log'><i class='fas fa-clipboard-list' title='View Log'></i></button></span>";
            $action_html .= "<span class='ml-2'><button type='button' class='btn btn-primary view-inquiry' data-inquiry=$inquiry->id data-view-url='".route('inquiry.view',$inquiry->id)."' title='View Inquiry'><i class='far fa-eye' title='View Inquiry'></i></button></span>";
            if((!empty($user->userGroup)) && $user->userGroup->id == '2'){
                $action_html .= "<span class='ml-2'><button type='button' id='assignRetailer' class='btn btn-default' data-inquiry=$inquiry->id data-assign-to-retailer-url='".route('inquiry.assign.retailer')."' data-task-url='".route('createReminder')."' data-toggle='modal' data-target='#assignToRetailer' title='Assign to retailer'><i class='fas fa-user' title='Assign to retailer'></i></button></span>";
            }
            if(!empty($inquiry->status) && $inquiry->status < 5){
                // $action_html .= "<span class='ml-3'><button type='button' id='createQuote' class=' ml-3 btn btn-success create-quote-".$inquiry->id."' data-inquiry=$inquiry->id data-create-quote-url='".route('order.quote.create')."' data-toggle='modal' data-target='#quoteModal'>Quote</button></span>";
                $action_html .= "<span class='ml-2'><button type='button' id='createQuote' class='btn btn-success create-quote-".$inquiry->id."' data-inquiry=$inquiry->id data-create-quote-url='".route('cart.inquiryToQuote.create')."' data-toggle='modal' data-target='#quoteModal'><i class='fas fa-clipboard' title='Create Qute'></i></button></span>";
            }
            if(empty($user->userGroup) || ((($user->id == $inquiry->dealer || $user->id == $inquiry->retailer) && $user->id == $inquiry->created_by) || ($user->id == $inquiry->created_by))){
                $action_html .= "<span class='ml-2'><a href=".route('inquiry.edit',$inquiry->id)." class='btn btn-primary'><i class='fas fa-pen' title='Edit'></i></a></span>";
                $action_html .= "<span class='ml-2'><button type=submit data-user=$inquiry->id class='btn btn-danger' onClick=deleteUser($inquiry->id)><i class='fas fa-trash' title='Delete'></i></button></span>";
            }
            // $action_html .= "<span class='ml-3'><a href=# class='btn btn-primary'>View</a></span>";
            $data[] = [$inquiry->id,ucwords($inquiry->customer),$inquiry->customer_email,$inquiry->customer_mobile,$inquiry->source,$dealer,$retailer,$status_html,$action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_inquiries;
        $return_array['recordsFiltered'] = $total_inquiries;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }
}
