<?php

namespace App\Http\Controllers\Admin;

use App\Http\MyCart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Inquiry;
use App\Models\OrderImage;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;
class OrderController extends MainController
{

    private $myCart;
    public function __construct(MyCart $myCart)
    {
        $this->myCart = $myCart;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
    }

    public function quoteIndex(){
        View::share ( 'title', 'Quote' );
        View::share ( 'elementActive', 'quote' );
        // id|number|product|qty|amount|created by|customer|created_date
        return view('admin.quote.index');
    }

    public function showQuote(Request $request){
        $order = Order::findOrFail($request->get('data')['order_id']);
        $json['cart_title'] = "Quote #$order->order_number";
        $json['cart_content'] = view('admin.quote.view',['order'=>$order])->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function quoteCreate(Request $request)
    {
        $request_data = $request->only('data');
        $inquiry_id = (!empty($request_data['data']['inquiry_id']))? $request_data['data']['inquiry_id'] :'';
        $inquiry = Inquiry::findOrFail($inquiry_id);
        $quote_number = random_int(100000, 999999); 
        return view('admin.order.quote',['inquiry'=>$inquiry,'quote_number'=>$quote_number]);
    }

    public function createQuote(Request $request){
        $json['modal_title'] = "Create Quote";
        $json['modal_content'] = view('admin.quote.create')->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    //PO
    public function poIndex(){
        View::share ( 'title', 'PO' );
        View::share ( 'elementActive', 'po' );
        // id|number|product|qty|amount|created by|customer|created_date
        return view('admin.po.index');
    }
    public function poCreate(Request $request){
        $json['modal_title'] = "Create PO";
        $json['modal_content'] = view('admin.po.create')->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function showPo(Request $request){
        $order = Order::findOrFail($request->get('data')['order_id']);
        $json['cart_title'] = "PO #$order->order_number";
        $json['cart_content'] = view('admin.quote.view',['order'=>$order])->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function quoteStore(Request $request)
    {
        $request_data = $request->all();
        // $quote_data = $request->only(['type','order_number','status','from_user','to_user','inquiry_id']);
        $quote_data = $this->myCart->getAll($request_data['cart_id']);
        $cart = \Cart::Session($request_data['cart_id']);
        $quote_data['status'] =  'sent';
        $quote_data['total'] = $cart->getTotal();
        try{
            $quote = Order::create($quote_data);
            if(!empty($quote)){
                $quote_product_data = [];
                foreach($request_data['product'] as $key => $value){
                    $cart_product = $cart->get($key);
                    $quote_product_data = array('order_id' => $quote->id, 'product_id' => $cart_product->id,'product_name'=>$cart_product->name, 'qty' => $cart_product->quantity, 'amount' => $cart_product->price);
                    $order_product = '';
                    if(!empty($quote_product_data)){
                        $order_product = OrderProduct::create($quote_product_data);
                    }
                }
                if(!empty($quote_data['inquiry_id'])){
                    $inquiry = Inquiry::find($quote_data['inquiry_id']);
                    if(!empty($inquiry)){
                        $inquiry->update(['status'=>1]);
                    }
                }
                $cart->clear();
                $this->myCart->clear($request_data['cart_id']);
                if(!empty($order_product)){
                    return response()->json(['status' => 'successful','code'=>1,'message'=>'Quote Successfully created.']);
                }else{
                    return response()->json(['status' => 'error','code'=>0,'message'=>'Something goes wrong.']);
                }
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error','code' => 0,'message' => $e->getMessage()]);                
        }
    }
    public function storePo(Request $request){
        $request_data = $request->all();
        $cart = \Cart::Session($request_data['cart_id']);
        $cart_data = $this->myCart->getAll($request_data['cart_id']);
        $cart_data['status'] =  'sent';
        $cart_data['total'] = $cart->getTotal();
        $cart_data['order_reference'] = (!empty($cart_data['order_reference']))?$cart_data['order_reference']:NULL;
        try{
            $order = Order::create($cart_data);
            if(!empty($order)){
                $order_product_data = []; 
                foreach($request_data['product'] as $key => $value){
                    $cart_product = $cart->get($key);
                    $order_product_data = array('order_id' => $order->id, 'product_id' => ($request_data['is_new_product']==1)?NULL:$cart_product->id, 'product_name'=>$cart_product->name, 'qty' => $cart_product->quantity, 'amount' => $cart_product->price);
                    $order_product = '';
                    if(!empty($order_product_data)){
                        $order_product = OrderProduct::create($order_product_data);
                    }
                }
                $cart->clear();
                $this->myCart->clear($request_data['cart_id']);
                if(!empty($order_product)){
                    return response()->json(['status' => 'successful', 'data'=>['quote'=>$cart_data['order_reference']],'code'=>1,'message'=>'PO Successfully created.']);
                }else{
                    return response()->json(['status' => 'error','code'=>0,'message'=>'Something goes wrong.']);
                }
            }else{
                return response()->json(['status' => 'error','code'=>0,'message'=>'Unable to generate PO.']);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error','code' => 0,'message' => $e->getMessage()]);                
        }
    }

    public function poRecived(Request $request){
        $order = Order::findOrFail($request->post('order_id'));
        $updateOrder = $order->update(['is_po_recieved'=>(!empty($request->post('is_po_recieved')))?1:0, 'recieved_date'=>$request->post('recieved_date')]);
        $orderImage = [];
        if($request->file()){
            $filePath = $this->storeImage($request);
            $orderImage = OrderImage::create(['order_id'=>$order->id,'image'=>$filePath,'uploaded_by'=>$request->post('uploaded_by')]);
        }
        if($updateOrder){
           return response()->json(['status' => 'successful', 'data'=>[$updateOrder,$orderImage],'code'=>1,'message'=>'PO has been successfully Uploaded.']);
        }else{
            return response()->json(['status' => 'error','code'=>0,'message'=>'Unable to upload PO.']);
        }
    }
    public function uploadPoImage(Request $request){
        $order = Order::findOrFail($request->post('order_id'));
        $updateOrder = $order->update(['is_po_recieved'=>(!empty($request->post('is_po_recieved')))?1:0, 'recieved_date'=>$request->post('recieved_date')]);
        $orderImage = [];
        if($request->file()){
            $filePath = $this->storeImage($request);
            $orderImage = OrderImage::create(['order_id'=>$order->id,'image'=>$filePath,'uploaded_by'=>$request->post('uploaded_by')]);
        }
        if($filePath){
           return response()->json(['status' => 'successful', 'data'=>['filePath'=>url($filePath)],'code'=>1,'message'=>'PO image has been successfully uploaded.']);
        }else{
            return response()->json(['status' => 'error','code'=>0,'message'=>'Unable to upload image.']);
        }
    }

    public function storeImage($request){
        if($request->file()) {
            $fileName = time().'_'.$request->image->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('uploads/po', $fileName, 'public');
            return '/storage/' . $filePath;
        }
        return false;
    }

    public function getPoImage(Request $request){
        
        $order = Order::findOrFail($request->post('data')['order_id']); 
        $json['image_content'] = view('admin.quote.images',['order'=>$order])->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    //sale
    public function saleIndex(){
        View::share ( 'title', 'Sales' );
        View::share ( 'elementActive', 'sales' );
        // id|number|total|from|to|created_date
        return view('admin.sale.index');
    }

    public function showSale(Request $request){
        $order = Order::findOrFail($request->get('data')['order_id']);
        $json['cart_title'] = "Sale #$order->order_number";
        $json['cart_content'] = view('admin.quote.view',['order'=>$order])->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function storeSale(Request $request){
        $request_data = $request->all();
        $cart = \Cart::Session($request_data['cart_id']);
        $cart_data = $this->myCart->getAll($request_data['cart_id']);        
        $cart_data['status'] =  'sent';
        $cart_data['total'] = $cart->getTotal();
        try{
            $order = Order::create($cart_data);
            if(!empty($order)){
                $order_product_data = [];
                foreach($request_data['product'] as $key => $value){
                    $cart_product = $cart->get($value);
                    $order_product_data = array('order_id' => $order->id, 'product_id' => $cart_product->id, 'qty' => $cart_product->quantity, 'amount' => $cart_product->price);
                    $order_product = '';
                    if(!empty($order_product_data)){
                        $order_product = OrderProduct::create($order_product_data);
                    }
                }
                $cart->clear();
                $this->myCart->clear($request_data['cart_id']);
                if(!empty($order_product)){
                    return response()->json(['status' => 'successful', 'data'=>['quote'=>$cart_data['order_reference']],'code'=>1,'message'=>'Sales Successfully created.']);
                }else{
                    return response()->json(['status' => 'error','code'=>0,'message'=>'Something goes wrong.']);
                }
            }else{
                return response()->json(['status' => 'error','code'=>0,'message'=>'Unable to generate Sale.']);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error','code' => 0,'message' => $e->getMessage()]);                
        }
    }


    public function listQuoteAjaxData(Request $request){
        $user =  auth()->user();
        // $dealer_id = '';
        /* if((!empty($user->userGroup)) && $user->userGroup->id == '2'){
            $dealer_id = $user->id;
        } */

        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'orders.type = "quote" AND orders.deleted_at is NULL';
        if((!empty($user->userGroup)) && $user->userGroup->id == '2'){
            // $users = implode(',',User::where('parent_id',$user->id)->get()->pluck('id')->toArray());
            $where .= (!empty($user->id))?" AND (from_user=$user->id OR to_user=$user->id ":'';
            $where .= (!empty($users)) ? " OR from_user in ($users) )" :")";
        }
        if((!empty($user->userGroup)) && $user->userGroup->id == '3'){
            $where .= (!empty($user->id))?" AND from_user=$user->id":'';
        }else if(empty($user->userGroup) || $user->userGroup->id == '1'){
            $where .= (!empty($user->id))?" AND (for_manufacturer is NULL) AND (from_user=$user->id":'';
            $where .= (!empty($users)) ? " OR from_user in ($users) )" :")";
        }
        $where .= (!empty($search)) ? " AND (orders.order_number like '$search%' OR orders.total like '$search%' OR orders.created_at like '$search%' OR (c.name is NOT NULL AND c.name like '$search%') OR (d.name is NOT NULL AND d.name like '$search%'))":'';
        $orders = DB::table('orders')
        ->select(DB::raw('orders.id,order_number,c.name as customer,d.name as added_by, orders.total as amount, orders.created_at as created_date'))
        ->join('order_product AS op','orders.id','=','op.order_id','left')
        ->join('users AS c','orders.to_user','=','c.id','left')
        ->join('users AS d','orders.from_user','=','d.id','left')
        ->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->groupby('orders.id')
        ->get();
        
        $total_order = Order::where('type','quote')->where('deleted_at',NULL)->where('from_user',$user->id)->count();
        $data = [];
        foreach($orders as $order){
            $is_sale_generated = Order::where('type','sale')->where('order_reference',$order->id)->where('deleted_at',NULL)->count();
            $customer = (!empty($order->customer))?$order->customer:'N/A';
            $added_by  = (!empty($order->added_by))?$order->added_by:'N/A';
            $action_html = "<span><a data-url=".route('quote.show',$order->id)." data-order=".$order->id." class='btn btn-primary view-order' data-toggle='modal' data-target='#orderModal'>View</a></span>";
            // $action_html .= "<span class='ml-3 create-po-".$order->id."'><a data-url=".route('cart.po.create')." data-order=".$order->id." class='btn btn-success view-order'>Generate PO</a></span>";
            /* if(empty($is_sale_generated)){
                $action_html .= "<span class='ml-3 create-sale-".$order->id."'><a data-url=".route('cart.sale.create')." data-order=".$order->id." class='btn btn-default view-order' data-toggle='modal' data-target='#orderModal'>Generate Sale</a></span>";
            } */
            // $action_html = "<span><a href=".route('order.edit',$order->id)." class='btn btn-primary'>Edit</a></span>";
            // $action_html .= "<span class='ml-3'><button type=submit data-user=$order->id class='btn btn-danger' onClick=deleteOrder($order->id)>Delete</button></span>";
            $data[] = [$order->id,$order->order_number,$order->amount, ucwords($added_by), ucwords($customer),$order->created_date, $action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_order;
        $return_array['recordsFiltered'] = $total_order;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }

    public function listPoAjaxData(Request $request){
        $user =  auth()->user();
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'orders.type = "po" AND orders.deleted_at is NULL';
        if((!empty($user->userGroup)) && $user->userGroup->id == '2'){
            // $users = implode(',',User::where('parent_id',$user->id)->get()->pluck('id')->toArray());
            // $where .= (!empty($user->id))?" AND (from_user=$user->id OR to_user=$user->id ":'';
            $where .= (!empty($user->id))?" AND (from_user=$user->id":'';
            $where .= (!empty($users)) ? " OR from_user in ($users) )" :")";
        }
        if((!empty($user->userGroup)) && $user->userGroup->id == '3'){
            $where .= (!empty($user->id))?" AND from_user=$user->id":'';
        }else if(empty($user->userGroup) || $user->userGroup->id == '1'){
            $where .= (!empty($user->id))?" AND (for_manufacturer is NULL) AND (from_user=$user->id":'';
            $where .= (!empty($users)) ? " OR from_user in ($users) )" :")";
        }
        // $where .= (!empty($dealer_id))?" AND (dealer_id=$dealer_id OR inquiries.created_by=$dealer_id)":'';
        $where .= (!empty($search)) ? " AND (orders.order_number like '$search%' OR orders.total like '$search%' OR orders.created_at like '$search%' OR (c.name is NOT NULL AND c.name like '$search%') OR (d.name is NOT NULL AND d.name like '$search%'))":'';
        $orders = DB::table('orders')
        ->select(DB::raw('orders.id,order_number,orders.to_user,c.name as customer,d.name as added_by, orders.total as amount, orders.created_at as created_date'))
        ->join('order_product AS op','orders.id','=','op.order_id','left')
        ->join('users AS c','orders.to_user','=','c.id','left')
        ->join('users AS d','orders.from_user','=','d.id','left')
        ->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->groupby('orders.id')
        ->get();
        $total_order = Order::where('type','po')->where('from_user',$user->id)->where('deleted_at',NULL)->count();
        $data = [];
        foreach($orders as $order){
            $orderDetail = Order::where('id','=',$order->id)->first();
            $customer = (!empty($order->customer))?$order->customer:'N/A';
            $added_by  = (!empty($order->added_by))?$order->added_by:'N/A';
            $action_html = "<span><a data-url=".route('po.show',$order->id)." data-order=".$order->id." class='btn btn-primary view-order' data-toggle='modal' data-target='#orderModal'>View</a></span>";
            if((auth()->user()->id == $order->to_user)||((empty($user->userGroup) && $user->id == 1)||$user->userGroup == '1')){
                $action_html .= "<span class='ml-3 create-sale-".$order->id."'><a data-url=".route('cart.sale.create')." data-order=".$order->id." class='btn btn-default view-order' data-toggle='modal' data-target='#orderModal'>Generate Sale</a></span>";
            }
            // $action_html .= "<span class='ml-3 create-po-".$order->id."'><a data-url=".route('cart.po.create')." data-order=".$order->id." class='btn btn-success view-quote' data-toggle='modal' data-target='#orderModal'>Generate PO</a></span>";
            // $action_html = "<span><a href=".route('order.edit',$order->id)." class='btn btn-primary'>Edit</a></span>";
            // $action_html .= "<span class='ml-3'><button type=submit data-user=$order->id class='btn btn-danger' onClick=deleteOrder($order->id)>Delete</button></span>";
            $data[] = [$order->id,$order->order_number,$order->amount, ucwords($added_by), ucwords($orderDetail->toUser()),$order->created_date, $action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_order;
        $return_array['recordsFiltered'] = $total_order;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }


    public function listSaleAjaxData(Request $request){
        $user =  auth()->user();
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'orders.type = "sale" AND orders.deleted_at is NULL';
        if((!empty($user->userGroup)) && $user->userGroup->id == '2'){
            // $users = implode(',',User::where('parent_id',$user->id)->get()->pluck('id')->toArray());
            $where .= (!empty($user->id))?" AND (from_user=$user->id OR to_user=$user->id ":'';
            $where .= (!empty($users)) ? " OR from_user in ($users) )" :")";
        }
        if((!empty($user->userGroup)) && $user->userGroup->id == '3'){
            $where .= (!empty($user->id))?" AND from_user=$user->id":'';
        }else{
            $where .= (!empty($user->id))?" AND from_user=$user->id":'';
        }
        // $where .= (!empty($dealer_id))?" AND (dealer_id=$dealer_id OR inquiries.created_by=$dealer_id)":'';
        $where .= (!empty($search)) ? " AND (orders.order_number like '$search%' OR orders.total like '$search%' OR orders.created_at like '$search%' OR (c.name is NOT NULL AND c.name like '$search%') OR (d.name is NOT NULL AND d.name like '$search%'))":'';
        $orders = DB::table('orders')
        ->select(DB::raw('orders.id,order_number,c.name as customer,d.name as added_by, orders.total as amount, orders.created_at as created_date'))
        ->join('order_product AS op','orders.id','=','op.order_id','left')
        ->join('users AS c','orders.to_user','=','c.id','left')
        ->join('users AS d','orders.from_user','=','d.id','left')
        ->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->groupby('orders.id')
        ->get();
        
        $total_order = Order::where('type','po')->where('from_user',$user->id)->where('deleted_at',NULL)->count();
        $data = [];
        foreach($orders as $order){
            $customer = (!empty($order->customer))?$order->customer:'N/A';
            $added_by  = (!empty($order->added_by))?$order->added_by:'N/A';
            $action_html = "<span><a data-url=".route('sale.show',$order->id)." data-order=".$order->id." class='btn btn-primary view-order' data-toggle='modal' data-target='#orderModal'>View</a></span>";
            // $action_html .= "<span class='ml-3 create-po-".$order->id."'><a data-url=".route('cart.po.create')." data-order=".$order->id." class='btn btn-success view-quote' data-toggle='modal' data-target='#orderModal'>Generate PO</a></span>";
            // $action_html = "<span><a href=".route('order.edit',$order->id)." class='btn btn-primary'>Edit</a></span>";
            // $action_html .= "<span class='ml-3'><button type=submit data-user=$order->id class='btn btn-danger' onClick=deleteOrder($order->id)>Delete</button></span>";
            $data[] = [$order->id,$order->order_number,$order->amount, ucwords($added_by), ucwords($customer),$order->created_date, $action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_order;
        $return_array['recordsFiltered'] = $total_order;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }
        
}
