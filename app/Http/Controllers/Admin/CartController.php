<?php

namespace App\Http\Controllers\Admin;

use App\Http\MyCart;
use App\Models\Inquiry;
use App\Models\Manufacturer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
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
                foreach($request->input('product') as $key=>$product){
                    $product_data = Product::findOrFail($key);
                    $cart->remove($key);
                    $cart->add(array(
                        'id' => $key,
                        'name' => $product_data->name,
                        'price' => $request->input('amount')[$key],
                        'quantity' => $request->input('qty')[$key],
                        'attributes' => array(),
                    ));
                }
            }
        }
        return $this->getInquiryToQuoteCart($request->input('cart_id'),$cart,$request->input('is_new_product'));
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

    public function createQuote(Request $request){
        $request_data = $request->all();        
        $is_new_product = 0;
        if(!empty($request_data['data']['customer'])){
            $id = "quote_user_".$request_data['data']['customer'];
            $cart  = \Cart::session($id);
            if($cart->isEmpty()){
                $this->myCart->clear($id);
                $this->myCart->setOrderNumber($id,random_int(100000, 999999));
            }
            $this->myCart->setTo($id,$request_data['data']['customer']);
        }
        $this->myCart->setType($id,'quote');
        $this->myCart->setFrom($id,auth()->user()->id);
        $json['cart_title'] = "Quote".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart,$is_new_product)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
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
            if((!empty($inquiry->product)) && $inquiry->product->count() > 0){
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
            }    
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
                'name' => 'Product name',
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

    public function getInquiryToQuoteCart($id,$cart,$is_new_product = 0){
        $to_user = [];
        if(!empty($this->myCart->getTo($id))){
            $to_user = (!empty($this->myCart->getForManufacture($id)) && ($this->myCart->getType($id) == 'po')) ? Manufacturer::findOrFail($this->myCart->getTo($id)) : User::findOrFail($this->myCart->getTo($id));
        }
        
        return view('admin.order.cart',['cart'=>$cart,'id'=>$id,'is_new_product' => $is_new_product,'order_number'=>$this->myCart->getOrderNumber($id),'type'=>$this->myCart->getType($id),'to_user'=>$to_user,'inquiry_id'=>$this->myCart->getInquiryId($id)]);
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
        }else{
            //
            $items = $cart->getContent();

            foreach($items as $row) {
                 $cart_product_id = $row->id; 
                 break;
            }
            $cart_product = Product::findOrFail($cart_product_id);
            if($product->brand->manufacturer->id == $cart_product->brand->manufacturer->id || $this->myCart->getType($request->input('id'))== 'sale'){
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
        
        if(empty($this->myCart->getTo($request->input('id'))) || ($this->myCart->getType($request->input('id'))) == 'po' ){
            $this->myCart->setTo($request->input('id'),(!empty(auth()->user()->parent_id))?auth()->user()->parent_id : $product->brand->manufacturer->id);
        }
        if((empty(auth()->user()->parent_id) || empty($this->myCart->getTo($request->input('id')))) && ($this->myCart->getType($request->input('id'))) == 'po'){
            $this->myCart->setForManufacture($request->input('id'),$product->brand->manufacturer->id);
        }else{
            $this->myCart->setForManufacture($request->input('id'),0);
        }
        $json['cart_content'] = $this->getInquiryToQuoteCart($request->input('id'),$cart)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function removeCartProduct(Request $request){
        $cart  = \Cart::session($request->input('id'));
        $cart->remove($request->input('product_id'));
        return $this->getInquiryToQuoteCart($request->input('id'),$cart,$request->input('is_new_product'));
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
            }
            $this->myCart->setTo($id,(!empty(auth()->user()->parent_id))?auth()->user()->parent_id:$manufacturer);
                if(empty(auth()->user()->parent_id)){
                    $this->myCart->setForManufacture($id,$manufacturer);
                }
        }else{
            $id = (!empty($request_data['data']['vendor'])) ? "po_vendor_".$request_data['data']['vendor'] : "po_user_".auth()->user()->id;
            $cart  = \Cart::session($id);
            if(!empty($request_data['data']['vendor'])){
                $to_user = (!empty($request_data['data']['vendor']))?$request_data['data']['vendor']:'';
                if($cart->isEmpty()){
                    $cart->add(array(
                        'id' => 1,
                        'name' => 'Product name',
                        'price' => 1,
                        'quantity' => '1',
                        'attributes' => array(),
                    ));
                    $this->myCart->setOrderNumber($id,random_int(100000, 999999));
                }
                $this->myCart->setTo($id,$to_user);
                $is_new_product = 1;
            }else{$id = (!empty($request_data['data']['vendor'])) ? "po_vendor_".$request_data['data']['vendor'] : "po_user_".auth()->user()->id;
            $cart  = \Cart::session($id);
            if(!empty($request_data['data']['vendor'])){
                $to_user = (!empty($request_data['data']['vendor']))?$request_data['data']['vendor']:'';
                if($cart->isEmpty()){
                    $cart->add(array(
                        'id' => 1,
                        'name' => 'name',
                        'price' => 1,
                        'quantity' => '1',
                        'attributes' => array(),
                    ));
                    $this->myCart->setOrderNumber($id,random_int(100000, 999999));
                }
                $this->myCart->setTo($id,$to_user);
                $is_new_product = 1;
            }else{
                $is_new_product = 0;
                $to_user = (!empty($request_data['data']['dealer']))?$request_data['data']['dealer']:'';
            }
            if($cart->isEmpty()){
                $this->myCart->setOrderNumber($id,random_int(100000, 999999));
                $this->myCart->setTo($id,$to_user);
            }
                $is_new_product = 0;
                $to_user = (!empty($request_data['data']['dealer']))?$request_data['data']['dealer']:'';
            }
            if($cart->isEmpty()){
                $this->myCart->setOrderNumber($id,random_int(100000, 999999));
                $this->myCart->setTo($id,$to_user);
            }           
        }
        $this->myCart->setType($id,'po');
        $this->myCart->setFrom($id,auth()->user()->id);
        // dd($cart->getContent());
        $json['cart_title'] = "PO".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart,$is_new_product)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function addNewProductToCart(Request $request){
        $user = explode('_',$request->input('id'));
        $id = (!empty($user[1]=='vendor')) ? "po_vendor_".$user[2] : "po_user_".auth()->user()->id;
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
                    'name' => 'Product name',
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
            if((!empty($order)) && (!empty($order->for_manufacturer))){
                $this->myCart->setForManufacturePoTOSale($id,1);
                $this->myCart->setFrom($id,$order->to_user);
                $this->myCart->setTo($id,$order->from_user);
            }else{
                $this->myCart->setFrom($id,$order->from_user);
                $this->myCart->setTo($id,$order->to_user);
            }
        }
        $json['cart_title'] = "Sale".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function createInvoice(Request $request){
        $request_data = $request->all();        
        $is_new_product = 0;
        if(!empty($request_data['data']['customer'])){
            $id = "sale_user_".$request_data['data']['customer'];
            $cart  = \Cart::session($id);
            if($cart->isEmpty()){
                $this->myCart->clear($id);
                $this->myCart->setOrderNumber($id,random_int(100000, 999999));
            }
            $this->myCart->setTo($id,$request_data['data']['customer']);
        }
        $this->myCart->setType($id,'sale');
        $this->myCart->setFrom($id,auth()->user()->id);
        $json['cart_title'] = "Sale".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart,$is_new_product)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }
}
