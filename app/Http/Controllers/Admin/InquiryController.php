<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InquiryExport;
use App\Http\Helpers;
use App\Http\Requests\Admin\InquiriesCreateRequest;
use App\Http\Requests\Admin\InquiriesUpdateRequest;
use App\Mail\SendInquiryToCustomer;
use App\Models\InquiriesSource;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Settings;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
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
        $inquiries_source = InquiriesSource::withoutTrashed()->where('status',1)->get();
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
            $retailer_id = (!empty($request_data['retailer']))?$request_data['retailer']:'';
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
            'comments'=>$request_data['comments'],
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
            $inquiry = Inquiry::create($inquiry_data);
            if(!empty($dealer_id)){
                Helpers::storeNotification($dealer_id,"Inquiry #$inquiry->id has been assined to you.",0,1);
                $data = array();
                $data['name'] = $inquiry->dealer->name;
                $data['mobile_number'] = $inquiry->dealer->mobile;
                $data['inquiry_id'] = $inquiry->id;
                $respnose = $this->sentSMS($data);
                $sms_res = explode('|',$respnose);
                if($sms_res[0] == 'ok'){
                    Helpers::storeNotification($dealer_id,"Inquiry #$inquiry->id SMS sent to you.",0,1);
                }else{
                    $msg ="Failed to sent SMS".$sms_res[0];
                }
            }
            if(!empty($retailer_id)){
                Helpers::storeNotification($retailer_id,"Inquiry #$inquiry->id has been assined to you.",0,1);
                $data = array();
                $data['name'] = $inquiry->retailer->name;
                $data['mobile_number'] = $inquiry->retailer->mobile;
                $data['inquiry_id'] = $inquiry->id;
                $respnose = $this->sentSMS($data);
                $sms_res = explode('|',$respnose);
                if($sms_res[0] == 'ok'){
                    Helpers::storeNotification($retailer_id,"Inquiry #$inquiry->id SMS sent to you.",0,1);
                }else{
                   $msg ="Failed to sent SMS".$sms_res[0];
                }
            }
            if(!empty($request_data['send_mail_to_customer']) && (!empty($dealer_id))){
                try{
                    Mail::to($inquiry->customer->email)->send(new SendInquiryToCustomer(config('app.name').' - Inquiry',$inquiry));
                }catch(Exception $e){
                    return redirect(route('inquiry.index'))->with('exception', 'Unable to sent Mail.');
                }
            }
            if(!empty($request_data['product'])){
                $products = Product::find($request_data['product']);
                $inquiry->product()->sync($products);
            }
        }catch(Exception $e){
            return redirect(route('inquiry.create'))->with('exception', $e->getMessage());
        }
        return redirect(route('inquiry.index'));
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
            'comments'=>$request_data['comments'],
            // 'product_id'=>$request_data['product'],
        ];
        if(!empty($request_data['dealer'])){
            $update_data['dealer_id'] = $request_data['dealer'];
        }
        $update_data['retailer_id'] = (!empty($request_data['retailer']))?$request_data['retailer']:NULL;
        try{
            $is_inquiry_updated = $inquiry->where('id',$id)->update($update_data);
            if(!empty($update_data['dealer_id']) && $inquiry->dealer_id != $update_data['dealer_id'] ){
                Helpers::storeNotification($update_data['dealer_id'],"Inquiry #$inquiry->id has been assined to you.",0,1);
                $data = array();
                $data['name'] = $inquiry->dealer->name;
                $data['mobile_number'] = $inquiry->dealer->mobile;
                $data['inquiry_id'] = $inquiry->id;
                $respnose = $this->sentSMS($data);
                $sms_res = explode('|',$respnose);
                if($sms_res[0] == 'ok'){
                    Helpers::storeNotification($update_data['dealer_id'],"Inquiry #$inquiry->id SMS sent to you.",0,1);
                }else{
                    $msg ="Failed to sent SMS".$sms_res[0];
                }
            }
            if(!empty($update_data['retailer_id']) && $inquiry->retailer_id != $update_data['retailer_id'] ){
                Helpers::storeNotification($update_data['retailer_id'],"Inquiry #$inquiry->id has been assined to you.",0,1);
                $data = array();
                $data['name'] = $inquiry->retailer->name;
                $data['mobile_number'] = $inquiry->retailer->mobile;
                $data['inquiry_id'] = $inquiry->id;
                $respnose = $this->sentSMS($data);
                $sms_res = explode('|',$respnose);
                if($sms_res[0] == 'ok'){
                    Helpers::storeNotification($update_data['retailer_id'],"Inquiry #$inquiry->id SMS sent to you.",0,1);
                }else{
                    $msg ="Failed to sent SMS".$sms_res[0];
                }
            }
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
        $retailers = User::where('users_group_id',3)->where('parent_id',auth()->user()->id)->where('status' , '1')->get();
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
                if(!empty($update_data['retailer_id']) && $inquiry->retailer_id != $update_data['retailer_id'] ){
                    Helpers::storeNotification($update_data['retailer_id'],"Inquiry #$inquiry->id has been assined to you.",0,1);
                }
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
        $total_where = $where;
        $where .= (!empty($search)) ? " AND (c.name like '$search%' OR p.name like '$search%' OR source like '%$search%' OR (d.name is NOT NULL AND d.name like '$search%'))":'';
        $inquiries = DB::table('inquiries')
        ->select(DB::raw('inquiries.id, inquiries.status, inquiries.created_by,source,c.name as customer,c.email AS customer_email,c.mobile AS customer_mobile,d.name as dealer, r.name as retailer,group_concat(p.name SEPARATOR "<br>") as product'))
        ->join('inquiries_products','inquiries.id','=','inquiries_products.inquiry_id','left')
        ->join('inquiries_source','inquiries.inquiries_source_id','=','inquiries_source.id','left')
        ->join('users AS c','inquiries.customer_id','=','c.id','left')
        ->join('products AS p','inquiries_products.product_id','=','p.id','left')
        ->join('users AS d','inquiries.dealer_id','=','d.id','left')
        ->join('users AS r','inquiries.retailer_id','=','r.id','left')
        ->whereRaw($where);
        $total_inquiries = $inquiries->count();
        $inquiries = $inquiries->offset($start)
        ->limit($length)->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->groupby('inquiries.id')->get();
        
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
                $action_html .= "<span class='ml-2'><button type='button' id='createQuote' class='btn btn-success create-quote-".$inquiry->id."' data-inquiry=$inquiry->id data-create-quote-url='".route('cart.inquiryToQuote.create')."' data-toggle='modal' data-target='#quoteModal'><i class='fas fa-clipboard' title='Create Quote'></i></button></span>";
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

    public function inquiryReport(){
        View::share('title', 'Inquiry Report');
        View::share('elementActive', 'inquiry_report');
        return view('admin.report.inquiry');
    }

    public function reportListAjaxData(Request $request){
        $user =  auth()->user();
        $dealer_id = '';
        $retailer_id = '';
        if((!empty($user->userGroup)) && $user->userGroup->id == '2'){
            $dealer_id = $user->id;
        }else if((!empty($user->userGroup)) && $user->userGroup->id == '3'){
            $retailer_id = $user->id;
        }
        $start =  $request->input('start','');
        $length=  $request->input('length','');
        $search = $request->input('search.value','');
        $from_date = $request->input('from_date','');
        $to_date = $request->input('to_date','');
        $where = 'inquiries.deleted_at is NULL';
        $where .= (!empty($dealer_id))?" AND (dealer_id=$dealer_id OR inquiries.created_by=$dealer_id)":'';
        $where .= (!empty($retailer_id))?" AND (retailer_id=$retailer_id OR inquiries.created_by=$retailer_id)":'';
        $total_where = $where;
        $where .= (!empty($search)) ? " AND (c.name like '$search%' OR c.email like '$search%' OR c.mobile like '$search%' OR p.name like '$search%' OR source like '%$search%' OR (d.name like '$search%' OR  r.name like '$search%'))":'';
        if(!empty($from_date) && !empty($to_date)){
            $where .= " AND (inquiries.created_at BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59')";
        }
        $inquiries = DB::table('inquiries')
        ->join('inquiries_products','inquiries.id','=','inquiries_products.inquiry_id','left')
        ->join('inquiries_source','inquiries.inquiries_source_id','=','inquiries_source.id','left')
        ->join('users AS c','inquiries.customer_id','=','c.id','left')
        ->join('products AS p','inquiries_products.product_id','=','p.id','left')
        ->join('users AS d','inquiries.dealer_id','=','d.id','left')
        ->join('users AS r','inquiries.retailer_id','=','r.id','left');
        
        $total_inquiries = $inquiries->select(DB::raw('inquiries.id, inquiries.status, inquiries.created_by,source,c.name as customer,c.email AS customer_email,c.mobile AS customer_mobile,d.name as dealer, r.name as retailer,group_concat(p.name SEPARATOR "<br>") as product'))
        ->whereRaw($where)->groupBy('inquiries.id')->get()->count();

        if(!empty($request->input('export',''))){
            $inquiries = $inquiries->select(DB::raw('inquiries.id, inquiries.status, inquiries.created_by,source,c.name as customer,c.email AS customer_email,c.mobile AS customer_mobile,d.name as dealer, r.name as retailer,group_concat(p.name SEPARATOR ",") as product,inquiries.created_at'))
            ->whereRaw($where)->groupby('inquiries.id')->get();
            return Excel::download(new InquiryExport($inquiries),'Inquiries.xlsx');
        }else{
            $inquiries = $inquiries->select(DB::raw('inquiries.id, inquiries.status, inquiries.created_by,source,c.name as customer,c.email AS customer_email,c.mobile AS customer_mobile,d.name as dealer, r.name as retailer,group_concat(p.name SEPARATOR "<br>") as product'))
            ->whereRaw($where)->offset($start)
            ->limit($length)->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
            ->groupby('inquiries.id')->get();
            $data = [];
            foreach($inquiries as $inquiry){
                $dealer = (!empty($inquiry->dealer))?$inquiry->dealer:'N/A';
                $retailer = (!empty($inquiry->retailer))?$inquiry->retailer:'N/A';
                $action_html = "";
                $inquiryObj = Inquiry::find($inquiry->id);
                $inquiry_status = (!empty($inquiryObj->status) && $inquiryObj->status !=0 )?$inquiryObj->getInquirystatusName():'N/A';
                $status_html = $inquiry_status;
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
                $data[] = [$inquiry->id,$inquiry->product,ucwords($inquiry->customer),$inquiry->customer_email,$inquiry->customer_mobile,$inquiry->source,$dealer,$retailer,$status_html,$action_html];
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
    public function sentSMS($data){
        $settings = Settings::where('slug','sms-settings')->first();
        $settings['json_data'] = json_decode($settings['json_data'],true);
        $post_response = '';
        if($settings['json_data']['is_enabled'] == '1'){
        $searchReplaceArray = array('{name}'=>$data['name'],'{inquiry_no}'=>$data['inquiry_id']);
        $txt = $this->getMsg('inquiry_assign');
        $txtMsg = str_replace(array_keys($searchReplaceArray),  array_values($searchReplaceArray),  $txt);
        $sms_data = array();
        $sms_data['APIkey'] = $settings['json_data']['api_key'];
        $sms_data['SenderID'] = $settings['json_data']['sender_id'];
        $sms_data['SMSType'] = $settings['json_data']['sms_type'];
        $sms_data['Mobile'] = $data['mobile_number'];
        $sms_data['MsgText'] = $txtMsg;
        $sms_data['EntityID'] = $settings['json_data']['entity_id'];
        $sms_data['TemplateID'] = $settings['json_data']['template_id'];
        $query_string= "";
        foreach($sms_data as $k=>$v){
            $query_string .=$k."=".urlencode($v)."&"; 
        }
        $url = "http://web.shreesms.net/API/SendSMS.aspx?".$query_string;
         $ch = curl_init($url);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$post_response = curl_exec($ch); 
		curl_close($ch);
        
    }
    return $post_response;

    }
}
