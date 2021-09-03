<?php

namespace App\Http\Controllers\Admin;

use App\Http\MyCart;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends MainController
{
    protected $myCart;
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
    public function update(Request $request)
    {
        $cart  = \Cart::session($request->input('cart_id'));
        if(!$cart->isEmpty()){
            if($request->input('is_new_product') == 1){
                foreach($request->input('product') as $key=>$val){
                    $cart->remove($key);
                    $cart->add(array(
                        'id' => $key,
                        'name' => $request->input('product')[$key],
                        'price' => $request->input('amount')[$key],
                        'quantity' => $request->input('qty')[$key],
                        'attributes' => array(),
                    ));
                }
            }else{
                foreach($request->input('product') as $product){
                    $product_data = Product::findOrFail($product);
                    $cart->remove($product);
                    $cart->add(array(
                        'id' => $product,
                        'name' => $product_data->name,
                        'price' => $request->input('amount')[$product],
                        'quantity' => $request->input('qty')[$product],
                        'attributes' => array(),
                    ));
                }
            }
        }
        return $this->getInquiryToQuoteCart($request->input('cart_id'),$cart);
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

    public function createInquiryToQuoteCart(Request $request){
        $request_data = $request->all();
        $is_new_product = 0;
        if(!empty($request_data['data']['inquiry_id'])){
        $id = "quote_".$request_data['data']['inquiry_id'];
        $inquiry = Inquiry::findOrFail($request_data['data']['inquiry_id']);
        $cart  = \Cart::session($id);
            if($cart->isEmpty()){
            $items = []; 
            foreach($inquiry->product as $product){
                $items[] = array(
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => '1',
                    'attributes' => array(),
                );
            }             
                $cart->add($items);
                $this->myCart->setOrderNumber($id,random_int(100000, 999999));
                $this->myCart->setInquiryId($id,$inquiry->id);
                $this->myCart->setType($id,'quote');
                $this->myCart->setFrom($id,auth()->user()->id);
                $this->myCart->setTo($id,$inquiry->customer_id);
            }
        }else{
            $id = "quote_".auth()->user()->id;
            $cart  = \Cart::session($id);
            $cart->add(array(
                'id' => 1,
                'name' => '',
                'price' => 1,
                'quantity' => '1',
                'attributes' => array(),
            ));
            $is_new_product = 1;
        }
        $json['cart_title'] = "Quote";
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart,$is_new_product)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);

    }

    public function getInquiryToQuoteCart($id,$cart,$is_nexw_product = 0){
        return view('admin.order.cart',['cart'=>$cart,'id'=>$id,'is_new_product' => '1','order_number'=>$this->myCart->getOrderNumber($id),'type'=>$this->myCart->getType($id),'inquiry_id'=>$this->myCart->getInquiryId($id)]);
    }

    public function addProductToCart(Request $request){
        $product = Product::findOrFail($request->input('product_id'));
        $cart  = \Cart::session($request->input('id'));
        if($cart->isEmpty()){
            $cart->add(
                array(
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => '1',
                    'attributes' => array(),
                )
            );
            $this->myCart->setTo($request->input('id'),(!empty(auth()->user()->parent_id))?auth()->user()->parent_id:$product->brand->manufacturer->id);
        }else{
            //
            $items = $cart->getContent();
            foreach($items as $row) {
                 $cart_product_id = $row->id; 
                 break;
            }
            $cart_product = Product::findOrFail($cart_product_id);
            if($product->brand->manufacturer->id == $cart_product->brand->manufacturer->id){
                $cart->add(
                    array(
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => '1',
                        'attributes' => array(),
                    )
                );
            }else{
                return response()->json(['status' => 'error','code' => 0, 'data'=>'', 'message'=>'Selected product is from diffrent manufacturer.']);
            }
        }
        $json['cart_content'] = $this->getInquiryToQuoteCart($request->input('id'),$cart)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function removeCartProduct(Request $request){
        $cart  = \Cart::session($request->input('id'));
        $cart->remove($request->input('product_id'));
        return $this->getInquiryToQuoteCart($request->input('id'),$cart);
    }

    //PO

    public function createPo(Request $request)
    {
        $request_data = $request->all();
        $is_new_product = 0;
        if(!empty($request_data['data']['order_id'])){
            $id = "po_".$request_data['data']['order_id'];
            $order = Order::findOrFail($request_data['data']['order_id']);
            $cart  = \Cart::session($id);
            if($cart->isEmpty()){
                $items = [];
                $manufacturer='';
                $is_dealer = ($order->addedBy->users_group_id == 2) ? 1 : 0;
                foreach($order->orderProduct as $orderProduct){
                    $items[] = array(
                        'id' => $orderProduct->product->id,
                        'name' => $orderProduct->product->name,
                        'price' => $orderProduct->amount,
                        'quantity' => $orderProduct->qty,
                        'attributes' => array(),
                    );
                    if($is_dealer){
                        $manufacturer = (empty($manufacturer))?$orderProduct->product->brand->manufacturer->id:$manufacturer;
                        if($manufacturer != $orderProduct->product->brand->manufacturer->id){
                            $json['cart_title'] = "PO".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
                            return response()->json(['status' => 'error','code'=>0, 'data'=>$json, 'message'=>'Please Select the Same Manufacturer Product']);    
                        }
                    }
                }
                $cart->add($items);
                $this->myCart->setOrderNumber($id,$order->order_number);
                $this->myCart->setOrderReference($id,$order->id);
                $this->myCart->setType($id,'po');
                // from login user
                $this->myCart->setFrom($id,auth()->user()->id);
                // to parent user
                $this->myCart->setTo($id,(!empty(auth()->user()->parent_id))?auth()->user()->parent_id:$manufacturer);
            }
        }else{
            $id = "po_".auth()->user()->id;
            $cart  = \Cart::session($id);
            if($cart->isEmpty()){
                $cart->add(array(
                    'id' => 1,
                    'name' => 'name',
                    'price' => 1,
                    'quantity' => '1',
                    'attributes' => array(),
                ));
            }
            $is_new_product = 1;
        }
        $json['cart_title'] = "PO".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart,$is_new_product)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function addNewProductToCart(Request $request){
        $id = "po_".auth()->user()->id;
        $cart  = \Cart::session($id);
        if($request->input('is_new_product')){
            $count = $cart->getContent()->count();
            if($cart->isEmpty()){
                $count =  $cart->getContent()->count() + 1;
            }else{
                $count = $cart->getContent()->last()->id + 1;
            }
            
                $cart->add(array(
                    'id' => $count,
                    'name' => 'name',
                    'price' => 1,
                    'quantity' => '1',
                    'attributes' => array(),
                ));
        }
        $is_new_product = 1;
        $json['cart_content'] = $this->getInquiryToQuoteCart($request->input('id'),$cart,$is_new_product)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }
     
    
    //SALE

    public function createSale(Request $request)
    {
        $request_data = $request->all();
        $id = "sale_".$request_data['data']['order_id'];
        $order = Order::findOrFail($request_data['data']['order_id']);
        $cart  = \Cart::session($id);
        if($cart->isEmpty()){
            $items = [];
            foreach($order->orderProduct as $orderProduct){
                $items[] = array(
                    'id' => $orderProduct->product->id,
                    'name' => $orderProduct->product->name,
                    'price' => $orderProduct->amount,
                    'quantity' => $orderProduct->qty,
                    'attributes' => array(),
                );
            }
            $cart->add($items);
            $this->myCart->setOrderNumber($id,$order->order_number);
            $this->myCart->setOrderReference($id,$order->id);
            $this->myCart->setType($id,'sale');
            // from login user
            $this->myCart->setFrom($id,$order->to_user);
            // to parent user
            $this->myCart->setTo($id,$order->from_user);
        }
        $json['cart_title'] = "Sale".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }
}
