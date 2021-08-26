<?php

namespace App\Http\Controllers\Admin;

use App\Http\MyCart;
use App\Models\Inquiry;
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
        $id = "quote_".$request_data['data']['inquiry_id'];
        $inquiry = Inquiry::findOrFail($request_data['data']['inquiry_id']);
        $cart  = \Cart::session($id);
        if($cart->isEmpty()){
        $order_number = random_int(100000, 999999);
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
            $this->myCart->setOrderNumber($id,$order_number);
            $this->myCart->setInquiryId($id,$inquiry->id);
            $this->myCart->setTo($id,$inquiry->customer_id);
        }
        $json['cart_title'] = "Quote";
        $json['cart_content'] = $this->getInquiryToQuoteCart($id,$cart)->render();
        return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);

    }

    public function getInquiryToQuoteCart($id,$cart){
        return view('admin.order.cart',['cart'=>$cart,'id'=>$id,'to_user'=>$this->myCart->getTo($id),'order_number'=>$this->myCart->getOrderNumber($id),'inquiry_id'=>$this->myCart->getInquiryId($id)]);
    }

    public function addProductToCart(Request $request){
        $product = Product::findOrFail($request->input('product_id'));
        $cart  = \Cart::session($request->input('id'));
        $cart->add(
            array(
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => '1',
                'attributes' => array(),
            )
        );
        return $this->getInquiryToQuoteCart($request->input('id'),$cart);
    }

    public function removeCartProduct(Request $request){
        $cart  = \Cart::session($request->input('id'));
        $cart->remove($request->input('product_id'));
        return $this->getInquiryToQuoteCart($request->input('id'),$cart);
    }
        
}
