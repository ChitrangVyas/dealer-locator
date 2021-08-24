<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Order;
use App\Models\Admin\OrderProduct;
use App\Models\Inquiry;
use Exception;
use Illuminate\Http\Request;

class OrderController extends MainController
{
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

    public function quoteCreate(Request $request)
    {
        $request_data = $request->only('data');
        $inquiry_id = (!empty($request_data['data']['inquiry_id']))? $request_data['data']['inquiry_id'] :'';
        $inquiry = Inquiry::findOrFail($inquiry_id);
        $quote_number = random_int(100000, 999999); 
        return view('admin.order.quote',['inquiry'=>$inquiry,'quote_number'=>$quote_number]);
    }
    public function quoteStore(Request $request)
    {
        $request_data = $request->all();
        $quote_data = $request->only(['type','order_number','status','from_user','to_user','inquiry_id']);
        $quote_data['status'] =  'sent';
        try{
            $quote = Order::create($quote_data);
            if(!empty($quote)){
                $quote_product_data = [];
                foreach($request_data['product'] as $key => $value){
                    $quote_product_data[] = array('order_id' => $quote->id, 'product_id' => $value, 'qty' => $request_data['qty'][$value], 'amount' => $request_data['amount'][$value]);
                }
                // product_id | qty | price => amount
                $order_product = '';
                if(!empty($quote_product_data)){
                    $order_product = OrderProduct::insert($quote_product_data);
                }
                $inquiry = Inquiry::find($quote_data['inquiry_id']);
                if(!empty($inquiry)){
                    $inquiry->update(['status'=>1]);
                }
                
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
        
}
