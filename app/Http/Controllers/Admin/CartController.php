<?php

namespace App\Http\Controllers\Admin;

use App\Http\MyCart;
use App\Models\Inquiry;
use App\Models\Manufacturer;
use App\Models\Order;
use App\Models\Product;
use App\Models\TaxGroup;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends MainController
{
    protected $myCart;
    public function __construct(MyCart $myCart)
    {
        parent::__construct();
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
    public function validateForMaxMin($val){
        if($val<0){
            return 1;
        }
        return $val;
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
        $selected_tax_group = [];
        if(!$cart->isEmpty()){
            $cart->clear();
            if($request->input('is_new_product') == 1){
                foreach($request->input('product') as $key=>$val){      
                    $cart->remove($key);
                    $cart->add(array(
                        'id' => $key,
                        'name' => $request->input('product')[$key],
                        'price' => $this->validateForMaxMin($request->input('amount')[$key]),
                        'quantity' => $this->validateForMaxMin($request->input('qty')[$key]),
                        'attributes' => array(),
                    ));
                }
            }else{
                foreach($request->input('product') as $key=>$product){
                    $product_data = Product::findOrFail($key);
                    $cart->remove($key);
                    $condition = (isset($request->input('tax')[$product_data->id]))?$this->getTaxGroupCondition($request->input('cart_id'),$request->input('tax')[$product_data->id]):$this->getProductTaxCondition($product_data,$request->input('cart_id'));                    
                    if($request->input('discount')[$key] > 0){
                        $condition['Discount'] = new \Darryldecode\Cart\CartCondition(array(
                            'name' => 'Discount',
                            'type' => 'coupon',
                            'value' => '-'.($request->input('discount')[$key]>100 ? 100 : $request->input('discount')[$key]).'%',
                        ));
                    }
                    $cart->add(array(
                        'id' => $key,
                        'name' => $product_data->name,
                        'price' => $this->validateForMaxMin($request->input('amount')[$key]),
                        'quantity' => $this->validateForMaxMin($request->input('qty')[$key]),
                        'attributes' => array(
                            'hsn_sac' => (!empty($product_data->hsn_sac)) ? $product_data->hsn_sac : '',
                        ),
                        'conditions'=>$condition,
                    ));
                    $selected_tax_group[$product_data->id] = (isset($request->input('tax')[$product_data->id]))?$request->input('tax')[$product_data->id]:'';
                }
            }
        }
        $cart->getSubTotal();
        $this->myCart->setTaxGroup($request->input('cart_id'),$selected_tax_group);
        $this->myCart->setTaxTotal($request->input('cart_id'),$cart);
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
        if((!empty(auth()->user()->users_group_id)) || (auth()->user()->users_group_id > 1)){
            $this->myCart->setFrom($id,auth()->user()->id);
        }
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
        $taxGroup  = TaxGroup::where('is_default',1)->first();
        $product_tax_group = [];
        $selected_tax_group = [];
        $cart  = \Cart::session($id);
        if($cart->isEmpty()){
            $items = []; 
            $this->myCart->setTo($id,$inquiry->customer_id);
            if((!empty($inquiry->product)) && $inquiry->product->count() > 0){
                foreach($inquiry->product as $product){
                    $condition = $this->getProductTaxCondition($product,$id);
                    $product_tax_group = (!empty($product->taxGroup))?$product->taxGroup:$taxGroup;
                    $items[] = array(
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => '1',
                        'attributes' => array(
                            'hsn_sac' => (!empty($product->hsn_sac)) ? $product->hsn_sac : '',
                        ),
                        'conditions' => $condition,
                    );
                    $selected_tax_group[$product->id] = (!empty($product_tax_group))?$product_tax_group->id:'';
                }
                $cart->add($items);  
                $cart->getSubTotal();
                $this->myCart->setTaxTotal($id,$cart);
                $this->myCart->setTaxGroup($id,$selected_tax_group);    
            }    
                $this->myCart->setOrderNumber($id,random_int(100000, 999999));
                $this->myCart->setInquiryId($id,$inquiry->id);
                $this->myCart->setType($id,'quote');
                if((!empty(auth()->user()->users_group_id)) || (auth()->user()->users_group_id > 1)){
                    $this->myCart->setFrom($id,auth()->user()->id);
                }
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

    public function editQuote(Request $request){
        $request_data = $request->all();
        $id = "quote_edit_".$request_data['data']['order_id'];
        $order = Order::findOrFail($request_data['data']['order_id']);
        $taxGroup  = TaxGroup::where('is_default',1)->first();
        $product_tax_group = [];
        $selected_tax_group = [];
        $cart  = \Cart::session($id);
        if($cart->isEmpty()){
            $items = [];
            if((!empty($order))){
                if((!empty(auth()->user()->users_group_id)) || (auth()->user()->users_group_id > 1)){
                    $this->myCart->setFrom($id,auth()->user()->id);
                }
                
                $this->myCart->setTo($id,$order->to_user);
            }
            foreach($order->orderProduct as $orderProduct){
                $condition = $this->getProductTaxCondition($orderProduct->product,$id);
                $product_tax_group = (!empty($orderProduct->product->taxGroup))?$orderProduct->product->taxGroup:$taxGroup;
                $items[] = array(
                    'id' => $orderProduct->product->id,
                    'name' => $orderProduct->product->name,
                    'price' => $orderProduct->amount,
                    'quantity' => $orderProduct->qty,
                    'attributes' => array(
                        'hsn_sac' => (!empty($orderProduct->product->hsn_sac)) ? $orderProduct->product->hsn_sac : '',
                    ),
                    'conditions' => $condition,
                );
                $selected_tax_group[$orderProduct->product->id] = (!empty($product_tax_group))?$product_tax_group->id:'';
            }
            $cart->add($items);
            $cart->getSubTotal();
            $this->myCart->setTaxTotal($id,$cart);
            $this->myCart->setTaxGroup($id,$selected_tax_group);
            $this->myCart->setOrderNumber($id,$order->order_number);
            $this->myCart->setOrderReference($id,$order->id);
            $this->myCart->setType($id,'quote');
        }
        $json['cart_title'] = "Quote".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function getInquiryToQuoteCart($id,$cart,$is_new_product = 0){
        $to_user = [];
        $total_tax = [];
        $cart_total = 0.00;
        if(!empty($this->myCart->getTo($id))){
            $to_user = (!empty($this->myCart->getForManufacture($id)) && ($this->myCart->getType($id) == 'po')) ? NULL : User::findOrFail($this->myCart->getTo($id));
            
        }
        if(!empty($this->myCart->getTaxTotal($id))){
            $total_tax = $this->myCart->getTaxTotal($id);
        }
        $createtype=($this->myCart->getType($id) == 'po')? 'PO' : '';
        $tax_groups = TaxGroup::where('status',1)->get();
        $selected_tax_group = $this->myCart->getTaxGroup($id);
        $cart_total = $this->myCart->getCartTotal($id);
        return view('admin.order.cart',['cart'=>$cart,'id'=>$id,'is_new_product' => $is_new_product,'order_number'=>$this->myCart->getOrderNumber($id),'type'=>$this->myCart->getType($id),'to_user'=>$to_user,'inquiry_id'=>$this->myCart->getInquiryId($id),'total_tax'=>$total_tax,'tax_groups'=>$tax_groups,'selected_tax_group'=>$selected_tax_group,'cart_total'=>$cart_total,'myCart'=>$this->myCart,'createtype'=>$createtype]);
    }

    public function addProductToCart(Request $request){
        $product = Product::findOrFail($request->input('product_id'));
        $cart  = \Cart::session($request->input('id'));
        $taxGroup  = TaxGroup::where('is_default',1)->first();
        $selected_tax_group = [];
        $condition = [];
        $product_tax_group = (!empty($product->taxGroup))?$product->taxGroup:$taxGroup;
        if($cart->isEmpty()){
            $condition = $this->getProductTaxCondition($product,$request->input('id'));
            $cart->add(
                array(
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => '1',
                    'attributes' => array(
                        'hsn_sac' => (!empty($product->hsn_sac)) ? $product->hsn_sac : '',
                    ),
                    'conditions'=>$condition,
                )
            );
        }else{
            $cart_product = $product;
            $condition = $this->getProductTaxCondition($cart_product,$request->input('id'));
                $cart->add(
                    array(
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => '1',
                        'attributes' => array(
                            'hsn_sac' => (!empty($product->hsn_sac)) ? $product->hsn_sac : '',
                        ),
                        'conditions'=>$condition,
                    )
                );
        }
        $selected_tax_group[$product->id] = (!empty($product_tax_group))?$product_tax_group->id:'';
        $cart->getSubTotal();
        $this->myCart->setTaxTotal($request->input('id'),$cart);
        $this->myCart->setTaxGroup($request->input('id'),$selected_tax_group,true);
        if(empty($this->myCart->getTo($request->input('id'))) || ($this->myCart->getType($request->input('id'))) == 'po' ){
            // $this->myCart->setTo($request->input('id'),(!empty(auth()->user()->parent_id))?auth()->user()->parent_id : $product->brand->manufacturer->id);
            $this->myCart->setTo($request->input('id'),(!empty(auth()->user()->parent_id))?auth()->user()->parent_id : NULL);
        }
        if((empty(auth()->user()->parent_id) || empty($this->myCart->getTo($request->input('id')))) && ($this->myCart->getType($request->input('id'))) == 'po'){
            $this->myCart->setForManufacture($request->input('id'),1);
        }else{
            $this->myCart->setForManufacture($request->input('id'),0);
        }
        $json['cart_content'] = $this->getInquiryToQuoteCart($request->input('id'),$cart)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function removeCartProduct(Request $request){
        $cart  = \Cart::session($request->input('id'));        
        $this->myCart->removeItem($request->input('id'),['tax_group'=>[$request->input('product_id')]]);
        $cart->remove($request->input('product_id'));
        $cart->getSubTotal();
        $this->myCart->setTaxTotal($request->input('id'),$cart);
        return $this->getInquiryToQuoteCart($request->input('id'),$cart,$request->input('is_new_product',''));
    }

    //PO

    public function createPo(Request $request)
    {
        $request_data = $request->all();
        $is_new_product = 0;
        if(!empty($request_data['data']['order_id'])){
            $id = (!empty($request_data['data']['is_po_edit']))?"po_edit_".$request_data['data']['order_id']:"po_".$request_data['data']['order_id'];
            $order = Order::findOrFail($request_data['data']['order_id']);
            $taxGroup  = TaxGroup::where('is_default',1)->first();
            $product_tax_group = [];
            $selected_tax_group = [];
            $cart  = \Cart::session($id);
            if($cart->isEmpty()){
                $items = [];
                $manufacturer = NULL;
                $is_dealer = (!empty($order->addedBy) && $order->addedBy->users_group_id == 2) ? 1 : 0;
                if(!empty($request_data['data']['is_po_edit'])){
                    $this->myCart->setTo($id,$order->to_user);
                }else{
                    $this->myCart->setTo($id,(!empty(auth()->user()->parent_id))?auth()->user()->parent_id:$manufacturer);
                }
                $count = 1;
                $is_new_product = 0;
                foreach($order->orderProduct as $orderProduct){
                    $condition = $this->getProductTaxCondition($orderProduct,$id);
                    $product_tax_group = (!empty($orderProduct->product->taxGroup))?$orderProduct->product->taxGroup:$taxGroup;
                    $is_new_product = (!empty($orderProduct->product))?0:1;
                    $count++;
                    $items[] = array(
                        'id' => (!empty($orderProduct->product))?$orderProduct->product->id:$count,
                        'name' => (!empty($orderProduct->product))?$orderProduct->product->name:$orderProduct->product_name,
                        'price' => $orderProduct->amount,
                        'quantity' => $orderProduct->qty,
                        'attributes' => array(
                            'hsn_sac' => (!empty($orderProduct->product) && !empty($orderProduct->product->hsn_sac)) ? $orderProduct->product->hsn_sac : '',
                        ),
                        'conditions' => (!empty($orderProduct->product))?$condition:[],
                    );
                    if(!empty($orderProduct->product)){
                        $selected_tax_group[$orderProduct->product->id] = (!empty($product_tax_group))?$product_tax_group->id:'';
                    }
                }
                $cart->add($items);
                $cart->getSubTotal();
                if(!empty($request_data['data']['is_po_edit'])){
                    $this->myCart->setOrderId($id,$request_data['data']['order_id']);
                }
                $this->myCart->setTaxTotal($id,$cart);
                $this->myCart->setTaxGroup($id,$selected_tax_group);
                $this->myCart->setOrderNumber($id,$order->order_number);
                $this->myCart->setOrderReference($id,$order->id);
            }
            // $this->myCart->setTo($id,(!empty(auth()->user()->parent_id))?auth()->user()->parent_id:$manufacturer);
            
                if(empty(auth()->user()->parent_id) && empty($request_data['data']['is_po_edit'])){
                    $this->myCart->setForManufacture($id,1);
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
                $to_user = (!empty($request_data['data']['vendor']))?$request_data['data']['vendor']:NULL;
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
        if((!empty(auth()->user()->users_group_id)) || (auth()->user()->users_group_id > 1)){
            $this->myCart->setFrom($id,auth()->user()->id);
        }
        if((!empty($order->customer) && $order->customer->userGroup->id == 5) && ($order->type == 'po')){
            $is_new_product = 1;
        }
        $this->myCart->setTaxTotal($id,$cart);
        // $json['carttype']='PO';
        $json['cart_title'] = "PO".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart,$is_new_product)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function addNewProductToCart(Request $request){
        $user = explode('_',$request->input('id'));
        // $request_data = $request->all();
        if((!empty($user[1]=='edit'))){
            $id = "po_edit_".$user[2];
        }else{
            $id = (!empty($user[1]=='vendor')) ? "po_vendor_".$user[2] : "po_user_".auth()->user()->id;
        }
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
        $this->myCart->setCartTotal($id,$cart->getTotal());
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
        $taxGroup  = TaxGroup::where('is_default',1)->first();
        $product_tax_group = [];
        $selected_tax_group = [];
        $cart  = \Cart::session($id);
        if($cart->isEmpty()){
            $items = [];
            if((!empty($order))){
                if((!empty(auth()->user()->users_group_id)) || (auth()->user()->users_group_id > 1)){
                    $this->myCart->setFrom($id,auth()->user()->id);
                }
                if($order->type == 'po'){
                    $this->myCart->setTo($id,$order->from_user);
                }else{
                    $this->myCart->setTo($id,$order->to_user);
                }
                $this->myCart->setForManufacturePoTOSale($id,1);
            }
            foreach($order->orderProduct as $orderProduct){
                $condition = $this->getProductTaxCondition($orderProduct->product,$id);
                $product_tax_group = (!empty($orderProduct->product->taxGroup))?$orderProduct->product->taxGroup:$taxGroup;
                $items[] = array(
                    'id' => $orderProduct->product->id,
                    'name' => $orderProduct->product->name,
                    'price' => $orderProduct->amount,
                    'quantity' => $orderProduct->qty,
                    'attributes' => array(
                        'hsn_sac' => (!empty($orderProduct->product->hsn_sac)) ? $orderProduct->product->hsn_sac : '',
                    ),
                    'conditions' => $condition,
                );
                $selected_tax_group[$orderProduct->product->id] = (!empty($product_tax_group))?$product_tax_group->id:'';
            }
            $cart->add($items);
            $cart->getSubTotal();
            $this->myCart->setTaxTotal($id,$cart);
            $this->myCart->setTaxGroup($id,$selected_tax_group);
            $this->myCart->setOrderNumber($id,$order->order_number);
            $this->myCart->setOrderReference($id,$order->id);
            $this->myCart->setType($id,'sale');
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
        if((!empty(auth()->user()->users_group_id)) || (auth()->user()->users_group_id > 1)){
            $this->myCart->setFrom($id,auth()->user()->id);
        }
        $json['cart_title'] = "Sale".(!empty($this->myCart->getOrderNumber($id)) ? " #".$this->myCart->getOrderNumber($id) : '');
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart,$is_new_product)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
    }

    public function setTaxCondition($name,$type='tax',$value='',$attributes = array()){
        return new \Darryldecode\Cart\CartCondition(array(
            'name' => $name,
            'type' => $type,
            'value' => $value,
            'attributes' => $attributes,
        ));
    }

    public function getProductTaxCondition($product,$id){
        $condition = [];
        if(!empty($product->taxGroup)){
            $productTaxGroup = $product->taxGroup;
        }else{
            $productTaxGroup = TaxGroup::where('is_default',1)->first();
        }
        $condition = $this->getTaxCondition($id,$productTaxGroup);
        return $condition;
    }

    public function getTaxGroupCondition($id, $taxGroup){
        $condition = [];
        if(!empty($taxGroup)){
            $taxGroup  = TaxGroup::find($taxGroup);
            $condition = $this->getTaxCondition($id,$taxGroup);
        }
        return $condition;
    }

    public function getTaxCondition($id,$taxGroup){
        $count = 1;
        $condition = [];
        $cart_data = $this->myCart->getAll($id);
        $toUser  = User::find($cart_data['to_user']);
        $user_state = (!empty(auth()->user()->users_group_id))?auth()->user()->state->id:$this->settingJson['state'];
        $to_user_state = (!empty($toUser->state)) ? $toUser->state->id : $this->settingJson['state'];
        foreach($taxGroup->tax()->get() as $tax){
            if(($user_state == $to_user_state) && (!stristr($tax->slug,'igst'))){
                $condition["tax".$count] = $this->setTaxCondition($tax->name,"tax".$count,$tax->pivot->percentage."%",['tax_id'=>$tax->id]);
                $count++;
            }elseif(($user_state != $to_user_state) && (stristr($tax->slug,'igst'))){
                $condition["tax".$count] = $this->setTaxCondition($tax->name,"tax".$count,$tax->pivot->percentage."%",['tax_id'=>$tax->id]);
                $count++;
            }
        }
        return $condition;
    }
}
