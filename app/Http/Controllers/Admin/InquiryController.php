<?php

namespace App\Http\Controllers\Admin;

<<<<<<< HEAD
use App\Http\Requests\Admin\InquiriesCreateRequest;
use App\Http\Requests\Admin\InquiriesUpdateRequest;
=======
use App\Http\Requests\Admin\InquiriesUpdateRequest;
use App\Http\Requests\Admin\InquiryCreateRequest;
use App\Http\Requests\Admin\InquiryUpdateRequest;
>>>>>>> 636895ac7d6ae88510f9ff1c441210870cb91d44
use App\Models\InquiriesSource;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use View;

class InquiryController extends MainController
{
    public function __construct() {
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
        //
        $request_data = $request->all();
        $dealer_id = NULL;
        if(auth()->user()->users_group_id == '2'){
            $dealer_id = auth()->user()->id;
        }elseif (auth()->user()->users_group_id == '3') {
            $dealer_id = auth()->user()->parent_id;
            $retailer_id = (!empty($request_data['retailer']))?$request_data['retailer']:auth()->user()->id;
        }
        $inquiry_data = [
            'inquiries_source_id'=>$request_data['inquiry_source'],
            'customer_id'=>$request_data['customer'],
            'created_by'=>$request_data['created_by'],
            'dealer_id' => $dealer_id,
            // 'product_id'=>$request_data['product'],
        ];
        if(!empty($retailer_id)){
            $inquiry_data['retailer_id'] = $retailer_id;
        }
        try{
<<<<<<< HEAD
            $inquiry = Inquiry::create([
                'inquiries_source_id'=>$request_data['inquiry_source_id'],
                'customer_id'=>$request_data['customer'],
                'created_by'=>$request_data['created_by'],
                'dealer_id' => $dealer_id,
                // 'product_id'=>$request_data['product'],
            ]);
=======
            $inquiry = Inquiry::create($inquiry_data);
>>>>>>> 636895ac7d6ae88510f9ff1c441210870cb91d44
            if(!empty($request_data['product'])){
                $products = Product::find($request_data['product']);
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
            'retailer' => 'required',
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


    public function listAjaxData(Request $request){
        $user =  auth()->user();
        $dealer_id = '';
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
        ->select(DB::raw('inquiries.id, inquiries.status, inquiries.created_by,source,c.name as customer,d.name as dealer, r.name as retailer,group_concat(p.name SEPARATOR "<br>") as product'))
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
            $action_html .= "<span class='ml-3'><button type='button' id='addLog' class=' ml-3 btn btn-default' data-inquiry=$inquiry->id data-view-url='".route('inquiry-log.show',$inquiry->id)."' data-add-url='".route('inquiry-log.create')."' data-task-url='".route('createReminder')."' data-toggle='modal' data-target='#modal-lg'>View Log</button></span>";
            if((!empty($user->userGroup)) && $user->userGroup->id == '2'){
                $action_html .= "<span class='ml-3'><button type='button' id='assignRetailer' class=' ml-3 btn btn-default' data-inquiry=$inquiry->id data-assign-to-retailer-url='".route('inquiry.assign.retailer')."' data-task-url='".route('createReminder')."' data-toggle='modal' data-target='#assignToRetailer'>Assign to retailer</button></span>";
            }
            if(empty($inquiry->status)){
                // $action_html .= "<span class='ml-3'><button type='button' id='createQuote' class=' ml-3 btn btn-success create-quote-".$inquiry->id."' data-inquiry=$inquiry->id data-create-quote-url='".route('order.quote.create')."' data-toggle='modal' data-target='#quoteModal'>Quote</button></span>";
                $action_html .= "<span class='ml-3'><button type='button' id='createQuote' class=' ml-3 btn btn-success create-quote-".$inquiry->id."' data-inquiry=$inquiry->id data-create-quote-url='".route('cart.inquiryToQuote.create')."' data-toggle='modal' data-target='#quoteModal'><i class='fas fa-clipboard' title='Create Qute'></i></button></span>";
            }
            if(empty($user->userGroup) || ((($user->id == $inquiry->dealer || $user->id == $inquiry->retailer) && $user->id == $inquiry->created_by) || ($user->id == $inquiry->created_by))){
                $action_html .= "<span class='ml-3'><a href=".route('inquiry.edit',$inquiry->id)." class='btn btn-primary'><i class='fas fa-pen' title='Edit'></i></a></span>";
                $action_html .= "<span class='ml-3'><button type=submit data-user=$inquiry->id class='btn btn-danger' onClick=deleteUser($inquiry->id)><i class='fas fa-trash' title='Delete'></i></button></span>";
            }
            // $action_html .= "<span class='ml-3'><a href=# class='btn btn-primary'>View</a></span>";
            $data[] = [$inquiry->id,$inquiry->product,ucwords($inquiry->customer),$inquiry->source,$dealer,$retailer,$action_html];
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
