<?php

namespace App\Http\Controllers\Admin;

use App\Exports\POExport;
use App\Http\Helpers;
use App\Http\MyCart;
use App\Mail\OrderEmailTemplate;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Inquiry;
use App\Models\Inventory;
use App\Models\OrderImage;
use App\Models\OrderInquiries;
use App\Models\OrderProductTax;
use App\Models\OrderStage;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Settings;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use View;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class OrderController extends MainController
{

    private $myCart;
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

    public function getPo(Request $request)
    {
        $user = auth()->user();
        DB::enableQueryLog();
        $orders = Order::where('type', 'po')->where(function ($query) use ($user) {
            if ((!empty($user->userGroup)) && $user->userGroup->id == '2') {
                return $query->where('from_user', $user->id);
            }
            if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
                return $query->where('from_user', $user->id);
            } else if (empty($user->userGroup) || $user->userGroup->id == '1') {
                return $query->whereOr('for_manufacturer', 1)->whereOr('from_user', NULL)->whereOr('to_user', NULL);
            }
        })->orderBy('orders.updated_at', 'desc')->take(10)->get();
        $json['modal_title'] = "Customers";
        $json['modal_content'] = view('layouts.partials.home.order', ['orders' => $orders])->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    public function getOrders(Request $request)
    {
        $user = auth()->user();
        $orders = Order::where('type', 'sale')->where(function ($query) use ($user) {
            if ((!empty($user->userGroup)) && $user->userGroup->id == '2') {
                return $query->where('from_user', $user->id);
            }
            if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
                return $query->where('from_user', $user->id);
            } else if (empty($user->userGroup) || $user->userGroup->id == '1') {
                return $query->whereOr('for_manufacturer', 1)->whereOr('from_user', NULL)->whereOr('to_user', NULL);
            }
        })->orderBy('updated_at', 'desc')->take(10)->get();
        $json['modal_title'] = "Customers";
        $json['modal_content'] = view('layouts.partials.home.order', ['orders' => $orders])->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    public function getInstallation(Request $request)
    {
        $user = auth()->user();
        DB::enableQueryLog();
        $orders = Order::select('orders.*')->where('type', 'sale')->where(function ($query) use ($user) {
            if ((!empty($user->userGroup)) && $user->userGroup->id == '2') {
                return $query->where('from_user', $user->id);
            }
            if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
                return $query->where('from_user', $user->id);
            } else if (empty($user->userGroup) || $user->userGroup->id == '1') {
                return $query->whereOr('from_user', NULL)->whereOr('to_user', NULL);
            }
        })->where('stage', '3')->join('order_stages', 'orders.id', '=', 'order_stages.order_id')->orderBy('orders.updated_at', 'desc')->take(10)->get();
        $json['modal_title'] = "Customers";
        $json['modal_content'] = view('layouts.partials.home.order', ['orders' => $orders])->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    public function quoteIndex()
    {
        View::share('title', 'Quote');
        View::share('elementActive', 'quote');
        // id|number|product|qty|amount|created by|customer|created_date
        return view('admin.quote.index');
    }

    public function showQuote(Request $request)
    {
        $order = Order::findOrFail($request->get('data')['order_id']);
        $json['cart_title'] = "Quote #$order->order_number";
        $json['cart_content'] = view('admin.quote.view', ['order' => $order])->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    public function quoteCreate(Request $request)
    {
        $request_data = $request->only('data');
        $inquiry_id = (!empty($request_data['data']['inquiry_id'])) ? $request_data['data']['inquiry_id'] : '';
        $inquiry = Inquiry::findOrFail($inquiry_id);
        $quote_number = random_int(100000, 999999);
        return view('admin.order.quote', ['inquiry' => $inquiry, 'quote_number' => $quote_number]);
    }

    public function createQuote(Request $request)
    {
        $json['modal_title'] = "Create Quote";
        $json['modal_content'] = view('admin.quote.create')->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    //PO
    public function poIndex()
    {
        View::share('title', 'PO');
        View::share('elementActive', 'po');
        // id|number|product|qty|amount|created by|customer|created_date
        return view('admin.po.index');
    }
    public function poCreate(Request $request)
    {
        $json['modal_title'] = "Create PO";
        $json['modal_content'] = view('admin.po.create')->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    public function showPo(Request $request)
    {
        $order = Order::findOrFail($request->get('data')['order_id']);
        $json['cart_title'] = "PO #$order->order_number";
        $json['cart_content'] = view('admin.quote.view', ['order' => $order])->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    public function quoteStore(Request $request)
    {
        $request_data = $request->all();
        // $quote_data = $request->only(['type','order_number','status','from_user','to_user','inquiry_id']);
        $quote_data = $this->myCart->getAll($request_data['cart_id']);
        $time= date("H:i:s");
        $quote_data['created_at']=  date('Y-m-d H:i:s',strtotime($this->myCart->getCreatedat($request_data['cart_id']).$time));
        // order_number, inquiry_id, type, to_user,
        $tax_data['total_tax'] = $quote_data['total_tax'];
        $tax_data['product_tax'] = $quote_data['product_tax'];
        $tax_data['tax_group'] = (!empty($quote_data['tax_group'])) ? $quote_data['tax_group'] : [];
        $quote_data['total'] = $quote_data['cart_total'];
        unset($quote_data['total_tax']);
        unset($quote_data['product_tax']);
        unset($quote_data['cart_total']);
        unset($quote_data['tax_group']);
        if (isset($quote_data['inquiries'])) {
            unset($quote_data['inquiries']);
        }
        $cart = \Cart::Session($request_data['cart_id']);
        $quote_data['status'] =  'sent';
        try {
            $order_data = explode('_', $request_data['cart_id']);
            if ($order_data[1] == 'edit') {
                $quote = Order::findOrFail($order_data[2]);
                $quote->update($quote_data);
                if (!empty($request_data['product'])) {
                    $current_order_product = OrderProduct::where('order_id', $quote->id);
                    if (!empty($current_order_product)) {
                        $order_product = $current_order_product->delete();
                    }
                    foreach ($request_data['product'] as $key => $value) {
                        $cart_product = $cart->get($key);
                        $tax_group_id = $this->myCart->getTaxGroup($request_data['cart_id']);
                        $order_product_data = array('order_id' => $quote->id, 'product_id' => ($request_data['is_new_product'] == 1) ? NULL : $cart_product->id, 'product_name' => $cart_product->name, 'qty' => $cart_product->quantity, 'amount' => $cart_product->price, 'discount_percentage' => $this->myCart->getDiscountPerProductPercentage($cart_product->id, $request_data['cart_id'], $cart), 'discount' => $this->myCart->getDiscountPerProduct($cart_product->id, $request_data['cart_id'], $cart), 'tax_group_id' => $tax_group_id[$cart_product->id]);
                        $order_product = '';
                        if (!empty($order_product_data)) {
                            $order_product = OrderProduct::create($order_product_data);
                            if ((!empty($tax_data['product_tax']))) {
                                if (in_array($cart_product->id, array_keys($tax_data['product_tax']))) {
                                    foreach ($tax_data['product_tax'][$cart_product->id] as $tax) {
                                        $order_tax_data = OrderProductTax::create(["order_product_id" => $order_product->id, "tax_id" => $tax['tax_id'], "price" => $tax['tax']]);
                                    }
                                }
                            }
                        }
                    }
                    $cart->clear();
                    $this->myCart->clear($request_data['cart_id']);
                    if ((!empty($request_data['btnSave'])) && $request_data['btnSave'] == 'savensend') {
                        $from = (!empty($quote->addedBy)) ? $quote->addedBy->email : $this->settingJson['mail_from_email'];
                        $from_name = (!empty($quote->addedBy)) ? $quote->addedBy->name : $this->settingJson['mail_from_name'];
                        $customer = (!empty($quote->customer)) ? $quote->customer->email : $this->settingJson['mail_from_email'];
                        try {
                            Mail::to($customer)->send(new OrderEmailTemplate($quote, config('app.name') . ' - Quote', $from, $from_name));
                        } catch (Exception $e) {
                            return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to sent Mail.', 'body' => $e->getMessage()]);
                        }
                    }
                }
                return response()->json(['status' => 'successful', 'data' => ['quote' => $quote_data['order_reference']], 'code' => 1, 'message' => 'Quote Successfully updated.']);
            } else {
                $quote = Order::create($quote_data);
                if (!empty($quote)) {
                    $quote_product_data = [];
                    foreach ($request_data['product'] as $key => $value) {
                        $cart_product = $cart->get($key);
                        $tax_group_id = $this->myCart->getTaxGroup($request_data['cart_id']);
                        $quote_product_data = array('order_id' => $quote->id, 'product_id' => $cart_product->id, 'product_name' => $cart_product->name, 'qty' => $cart_product->quantity, 'amount' => $cart_product->price, 'discount_percentage' => $this->myCart->getDiscountPerProductPercentage($cart_product->id, $request_data['cart_id'], $cart), 'discount' => $this->myCart->getDiscountPerProduct($cart_product->id, $request_data['cart_id'], $cart), 'tax_group_id' => $tax_group_id[$cart_product->id]);
                        $order_product = '';
                        if (!empty($quote_product_data)) {
                            $order_product = OrderProduct::create($quote_product_data);
                            if (in_array($cart_product->id, array_keys($tax_data['product_tax']))) {
                                foreach ($tax_data['product_tax'][$cart_product->id] as $tax) {
                                    $order_tax_data = OrderProductTax::create(["order_product_id" => $order_product->id, "tax_id" => $tax['tax_id'], "price" => $tax['tax']]);
                                }
                            }
                        }
                    }
                    if (!empty($quote_data['inquiry_id'])) {
                        $inquiry = Inquiry::find($quote_data['inquiry_id']);
                        if (!empty($inquiry)) {
                            $inquiry->update(['status' => 1]);
                        }
                    }
                    $cart->clear();
                    $this->myCart->clear($request_data['cart_id']);
                    if (!empty($order_product)) {
                        if ((!empty($request_data['btnSave'])) && $request_data['btnSave'] == 'savensend') {
                            /* $email_data = [
                            'from'=>$quote->addedBy->email,
                            'from_name' => $quote->addedBy->name,
                            'to'=>$quote->customer->email,
                            'title' => "PO FOR #$quote->order_number",
                            'view' => 'emails.send',
                            'content' => "<p>This is Test for the Qoute</p>",

                        ]; */
                            $from = (!empty($quote->addedBy)) ? $quote->addedBy->email : $this->settingJson['mail_from_email'];
                            $from_name = (!empty($quote->addedBy)) ? $quote->addedBy->name : $this->settingJson['mail_from_name'];
                            $customer = (!empty($quote->customer)) ? $quote->customer->email : $this->settingJson['mail_from_email'];
                            try {
                                Mail::to($customer)->send(new OrderEmailTemplate($quote, config('app.name') . ' - Quote', $from, $from_name));
                            } catch (Exception $e) {
                                return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to sent Mail.', 'body' => $e->getMessage()]);
                            }
                            // $this->send($email_data);
                        }
                        return response()->json(['status' => 'successful', 'code' => 1, 'message' => 'Quote Successfully created.']);
                    } else {
                        return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Something goes wrong.']);
                    }
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'code' => 0, 'message' => $e->getMessage()]);
        }
    }
    public function storePo(Request $request)
    {
        \Validator::make($request->all(), [
            "product" => "required|array|min:1",
            'product.*' => 'required|string|min:1',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|numeric|min:1',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric|min:1'
        ])->validate();
        $request_data = $request->all();
        $cart = \Cart::Session($request_data['cart_id']);
        $cart_data = $this->myCart->getAll($request_data['cart_id']);
        $tax_data['tax_group'] = (!empty($cart_data['tax_group'])) ? $cart_data['tax_group'] : [];
        $inquiries = (!empty($cart_data['inquiries'])) ? $cart_data['inquiries'] : [];
        $tax_data['total_tax'] = (!empty($cart_data['total_tax'])) ? $cart_data['total_tax'] : '';
        $tax_data['product_tax'] = (!empty($cart_data['product_tax'])) ? $cart_data['product_tax'] : '';
        $cart_data['total'] = $cart_data['cart_total'];
        if (isset($cart_data['inquiries'])) {
            unset($cart_data['inquiries']);
        }
        if (isset($cart_data['tax_group'])) {
            unset($cart_data['tax_group']);
        }
        if (isset($cart_data['product_tax'])) {
            unset($cart_data['product_tax']);
        }
        if (isset($cart_data['total_tax'])) {
            unset($cart_data['total_tax']);
        }
        if (isset($cart_data['product_tax'])) {
            unset($cart_data['product_tax']);
        }
        if (isset($cart_data['cart_total'])) {
            unset($cart_data['cart_total']);
        }
        $cart_data['status'] =  'sent';
        $cart_data['order_reference'] = (!empty($cart_data['order_reference'])) ? $cart_data['order_reference'] : NULL;
        $time = date('H:i:s');
        $cart_data['created_at']=  date('Y-m-d H:i:s',strtotime($this->myCart->getCreatedat($request_data['cart_id']).$time));
        try {
            if (!empty($cart_data['order_id'])) {
                $order = Order::find($cart_data['order_id']);
                unset($cart_data['order_id']);
                $order->update($cart_data);
                $this->saveOrderInquiries($order->id, $inquiries);
                if (!empty($request_data['product'])) {
                    $current_order_product = OrderProduct::where('order_id', $order->id);
                    if (!empty($current_order_product)) {
                        $order_product = $current_order_product->delete();
                    }
                    foreach ($request_data['product'] as $key => $value) {
                        $cart_product = $cart->get($key);
                        $order_product_data = array('order_id' => $order->id, 'product_id' => ($request_data['is_new_product'] == 1) ? NULL : $cart_product->id, 'product_name' => $cart_product->name, 'qty' => $cart_product->quantity, 'amount' => $cart_product->price);
                        $order_product = '';
                        if (!empty($order_product_data)) {
                            $order_product = OrderProduct::create($order_product_data);
                            if ((!empty($tax_data['product_tax']))) {
                                if (in_array($cart_product->id, array_keys($tax_data['product_tax']))) {
                                    foreach ($tax_data['product_tax'][$cart_product->id] as $tax) {
                                        $order_tax_data = OrderProductTax::create(["order_product_id" => $order_product->id, "tax_id" => $tax['tax_id'], "price" => $tax['tax']]);
                                    }
                                }
                            }
                        }
                    }
                    $cart->clear();
                    $this->myCart->clear($request_data['cart_id']);

                    if ((!empty($request_data['btnSave'])) && $request_data['btnSave'] == 'savensend') {
                        $from = (!empty($order->addedBy)) ? $order->addedBy->email : $this->settingJson['mail_from_email'];
                        $from_name = (!empty($order->addedBy)) ? $order->addedBy->name : $this->settingJson['mail_from_name'];
                        $customer = (!empty($order->customer)) ? $order->customer->email : $this->settingJson['mail_from_email'];
                        try {
                            Mail::to($customer)->send(new OrderEmailTemplate($order, config('app.name') . ' - PO', $from, $from_name));
                        } catch (Exception $e) {
                            return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to sent Mail.']);
                        }
                    }
                }
                return response()->json(['status' => 'successful', 'data' => ['quote' => $cart_data['order_reference']], 'code' => 1, 'message' => 'PO Successfully updated.']);
            } else {
                $order = Order::create($cart_data);
                if (!empty($order)) {
                    $order_product_data = [];
                    $this->saveOrderInquiries($order->id, $inquiries);
                    foreach ($request_data['product'] as $key => $value) {
                        $cart_product = $cart->get($key);
                        $order_product_data = array('order_id' => $order->id, 'product_id' => ($request_data['is_new_product'] == 1) ? NULL : $cart_product->id, 'product_name' => $cart_product->name, 'qty' => $cart_product->quantity, 'amount' => $cart_product->price);
                        $order_product = '';
                        if (!empty($order_product_data)) {
                            $order_product = OrderProduct::create($order_product_data);
                            if (empty($request_data['is_new_product']) && (!empty($tax_data['product_tax']))) {
                                if (in_array($cart_product->id, array_keys($tax_data['product_tax']))) {
                                    foreach ($tax_data['product_tax'][$cart_product->id] as $tax) {
                                        $order_tax_data = OrderProductTax::create(["order_product_id" => $order_product->id, "tax_id" => $tax['tax_id'], "price" => $tax['tax']]);
                                    }
                                }
                            }
                        }
                    }
                    $cart->clear();
                    $this->myCart->clear($request_data['cart_id']);
                    if (!empty($order_product)) {
                        if ((!empty($request_data['btnSave'])) && $request_data['btnSave'] == 'savensend') {
                            /* $email_data = [
                                'from'=>$order->addedBy->email,
                                'to'=>$order->customer->email,
                                'title' => "PO FOR #$order->order_number",
                                'view' => 'emails.send',
                                'content' => "<p>This is Test for the PO</p>",

                            ]; */
                            $from = (!empty($order->addedBy)) ? $order->addedBy->email : $this->settingJson['mail_from_email'];
                            $from_name = (!empty($order->addedBy)) ? $order->addedBy->name : $this->settingJson['mail_from_name'];
                            $customer = (!empty($order->customer)) ? $order->customer->email : $this->settingJson['mail_from_email'];
                            try {
                                Mail::to($customer)->send(new OrderEmailTemplate($order, config('app.name') . ' - PO', $from, $from_name));
                            } catch (Exception $e) {
                                return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to sent Mail.']);
                            }
                            // Mail::to($order->customer->email)->send(new OrderShipped($order));
                            // $this->send($email_data);
                        }
                        return response()->json(['status' => 'successful', 'data' => ['quote' => $cart_data['order_reference']], 'code' => 1, 'message' => 'PO Successfully created.']);
                    } else {
                        return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Something goes wrong.']);
                    }
                } else {
                    return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to generate PO.']);
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'code' => 0, 'message' => $e->getMessage() . "<br>" . $e->getFile() . ": " . $e->getLine()]);
        }
    }

    public function poRecived(Request $request)
    {
        $order = Order::findOrFail($request->post('order_id'));
        $updateOrder = $order->update(['is_po_recieved' => (!empty($request->post('is_po_recieved'))) ? 1 : 0, 'recieved_date' => $request->post('recieved_date')]);
        $orderImage = [];
        if ($request->file()) {
            $filePath = $this->storeImage($request);
            $orderImage = OrderImage::create(['order_id' => $order->id, 'image' => $filePath, 'uploaded_by' => $request->post('uploaded_by')]);
        }
        if ($updateOrder) {
            return response()->json(['status' => 'successful', 'data' => [$updateOrder, $orderImage], 'code' => 1, 'message' => 'PO has been successfully Upadated.']);
        } else {
            return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to upload PO.']);
        }
    }
    public function uploadPoImage(Request $request)
    {
        $order = Order::findOrFail($request->post('order_id'));
        $updateOrder = $order->update(['is_po_recieved' => (!empty($request->post('is_po_recieved'))) ? 1 : 0, 'recieved_date' => $request->post('recieved_date')]);
        $orderImage = [];
        if ($request->file()) {
            $filePath = $this->storeImage($request);
            $orderImage = OrderImage::create(['order_id' => $order->id, 'image' => $filePath, 'uploaded_by' => $request->post('uploaded_by')]);
        }
        if ($filePath) {
            return response()->json(['status' => 'successful', 'data' => ['filePath' => url($filePath)], 'code' => 1, 'message' => 'PO image has been successfully uploaded.']);
        } else {
            return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to upload image.']);
        }
    }
    public function deletePoImage(Request $request){
        $request_data = $request->all();
        $orderImage = OrderImage::findOrFail($request_data['id']);
        if($orderImage){
            $file = explode("storage/uploads/po/", $orderImage->image);
            $filepath = storage_path() . "/app/public/uploads/po/" . $file[1];
            if (file_exists($filepath) && unlink($filepath)) {
                DB:: delete('delete from order_image where id = ?',[$request_data['id']]);
                return response()->json(['data'=>'Image is successfully deleted'], 200);
            }else{
                return response()->json(['data'=>'Image is not deleted'],500);
            }
        }else{
            return response()->json(['data'=>'Image is not deleted'],500);
        }
    }

    public function storeImage($request)
    {
        if ($request->file()) {
            $fileName = time() . '_' . Str::snake(Str::replace('#', '_', $request->image->getClientOriginalName()));
            $filePath = $request->file('image')->storeAs('uploads/po', $fileName, 'public');
            return '/storage/' . $filePath;
        }
        return false;
    }

    public function getPoImage(Request $request)
    {

        $order = Order::findOrFail($request->post('data')['order_id']);
        $json['image_content'] = view('admin.quote.images', ['order' => $order])->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    //sale
    public function saleIndex()
    {
        View::share('title', 'Orders');
        View::share('elementActive', 'sales');
        // id|number|total|from|to|created_date
        return view('admin.sale.index');
    }

    public function invoiceIndex()
    {
        View::share('title', 'Bill');
        View::share('elementActive', 'bill');
        // id|number|total|from|to|created_date
        return view('admin.invoice.index');
    }

    public function invoiceCreate(Request $request)
    {
        $json['modal_title'] = "Create Order";
        $json['modal_content'] = view('admin.invoice.create')->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    public function showSale(Request $request)
    {
        $order = Order::findOrFail($request->get('data')['order_id']);
        $json['cart_title'] = "Order #$order->order_number";
        $json['cart_content'] = view('admin.quote.view', ['order' => $order])->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }
    public function showInvoice(Request $request)
    {
        $order = Order::findOrFail($request->get('data')['order_id']);
        $json['cart_title'] = "Invoice #$order->order_number";
        $json['cart_content'] = view('admin.quote.view', ['order' => $order])->render();
        return response()->json(['status' => 'successful', 'code' => 1, 'data' => $json, 'message' => '']);
    }

    public function printInvoice(Request $request, $id = '')
    {
        $order = Order::findOrFail($id);

        $settings = Settings::find(1);
        if (!empty($settings)) {
            $settings = json_decode($settings['json_data'], true);
            if ($order->type == 'sale') {
                $termscondition = $settings['terms_condition_sale'];
            } else if ($order->type == 'po') {
                $termscondition = $settings['terms_condition_po'];
            } else if ($order->type == 'quote') {
                $termscondition = $settings['terms_condition_quote'];
            } else {
                $termscondition = '';
            }
        }

        // print_r($termscondition);
        // die;
        return view('admin.order.print', ['order' => $order, 'termscondition' => $termscondition]);
    }

    public function storeSale(Request $request)
    {

        $request_data = $request->all();

        $time = date('H:i:s');
        $cart = \Cart::Session($request_data['cart_id']);
        $cart_data = $this->myCart->getAll($request_data['cart_id']);

        $tax_data['tax_group'] = (!empty($cart_data['tax_group'])) ? $cart_data['tax_group'] : [];
        $tax_data['total_tax'] = $cart_data['total_tax'];
        $tax_data['product_tax'] = $cart_data['product_tax'];
        unset($cart_data['total_tax']);
        unset($cart_data['product_tax']);
        unset($cart_data['for_manufacture_po_to_sale']);
        $cart_data['status'] =  'sent';
        $cart_data['total'] = $cart_data['cart_total'];
        unset($cart_data['cart_total']);
        unset($cart_data['tax_group']);
        unset($cart_data['inquiries']);
        $cart_data['order_reference'] = (!empty($cart_data['order_reference'])) ? $cart_data['order_reference'] : NULL;
        $cart_data['created_at']=  date('Y-m-d H:i:s',strtotime($this->myCart->getCreatedat($request_data['cart_id']).$time));
        try {
            $order_data = explode('_', $request_data['cart_id']);
            if ($order_data[1] == 'edit') {
                $sale = Order::findOrFail($order_data[2]);
                $sale->update($cart_data);
                if (!empty($request_data['product'])) {
                    $current_order_product = OrderProduct::where('order_id', $sale->id);
                    if (!empty($current_order_product)) {
                        $order_product = $current_order_product->delete();
                    }
                    foreach ($request_data['product'] as $key => $value) {
                        $cart_product = $cart->get($key);
                        $tax_group_id = $this->myCart->getTaxGroup($request_data['cart_id']);
                        $order_product_data = array('order_id' => $sale->id, 'product_id' => ($request_data['is_new_product'] == 1) ? NULL : $cart_product->id, 'product_name' => $cart_product->name, 'qty' => $cart_product->quantity, 'amount' => $cart_product->price, 'discount_percentage' => $this->myCart->getDiscountPerProductPercentage($cart_product->id, $request_data['cart_id'], $cart), 'discount' => $this->myCart->getDiscountPerProduct($cart_product->id, $request_data['cart_id'], $cart), 'tax_group_id' => $tax_group_id[$cart_product->id]);
                        $order_product = '';
                        if (!empty($order_product_data)) {
                            $order_product = OrderProduct::create($order_product_data);
                            if ((!empty($tax_data['product_tax']))) {
                                if (in_array($cart_product->id, array_keys($tax_data['product_tax']))) {
                                    foreach ($tax_data['product_tax'][$cart_product->id] as $tax) {
                                        $order_tax_data = OrderProductTax::create(["order_product_id" => $order_product->id, "tax_id" => $tax['tax_id'], "price" => $tax['tax']]);
                                    }
                                }
                            }
                        }
                    }
                    $cart->clear();
                    $this->myCart->clear($request_data['cart_id']);
                    if ((!empty($request_data['btnSave'])) && $request_data['btnSave'] == 'savensend') {
                        $from = (!empty($sale->addedBy)) ? $sale->addedBy->email : $this->settingJson['mail_from_email'];
                        $from_name = (!empty($sale->addedBy)) ? $sale->addedBy->name : $this->settingJson['mail_from_name'];
                        $customer = (!empty($sale->customer)) ? $sale->customer->email : $this->settingJson['mail_from_email'];
                        try {
                            Mail::to($customer)->send(new OrderEmailTemplate($sale, config('app.name') . ' - Order', $from, $from_name));
                        } catch (Exception $e) {
                            return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to sent Mail.', 'body' => $e->getMessage()]);
                        }
                    }
                }
                return response()->json(['status' => 'successful', 'data' => ['quote' => $cart_data['order_reference']], 'code' => 1, 'message' => 'Order Successfully updated.']);
            } else {
                $order = Order::create($cart_data);
                if (!empty($order)) {
                    if (!empty($order->customer) && ($order->customer->users_group_id == 2 || ($order->customer->users_group_id == 3))) {
                        Helpers::storeNotification($order->customer->id, "Order #$order->order_number has been created for you.", 0, 3);
                    }
                    $order_product_data = [];
                    foreach ($request_data['product'] as $key => $value) {

                        $cart_product = $cart->get($key);
                        $order_product_data = array('order_id' => $order->id, 'product_id' => $cart_product->id, 'product_name' => $cart_product->name, 'qty' => $cart_product->quantity, 'amount' => $cart_product->price, 'discount_percentage' => $this->myCart->getDiscountPerProductPercentage($cart_product->id, $request_data['cart_id'], $cart), 'discount' => $this->myCart->getDiscountPerProduct($cart_product->id, $request_data['cart_id'], $cart));
                        $order_product = '';
                        $user_id = '';
                        if (!empty($order_product_data)) {
                            $order_product = OrderProduct::create($order_product_data);
                            if (empty(auth()->user()->users_group_id)) {
                                $user_id = NULL;
                            } else {
                                $user_id = $cart_data['from_user'];
                            }
                            $invenory = Inventory::where('user_id', $user_id)->where('product_id', 1);
                            $invenory_qty = $invenory->first('qty');
                            if (!empty($invenory_qty)) {
                                $invenory->update(['qty' => ($invenory_qty->qty - $cart_product->quantity)]);
                            }
                            if (in_array($cart_product->id, array_keys($tax_data['product_tax']))) {
                                foreach ($tax_data['product_tax'][$cart_product->id] as $tax) {
                                    $order_tax_data = OrderProductTax::create(["order_product_id" => $order_product->id, "tax_id" => $tax['tax_id'], "price" => $tax['tax']]);
                                }
                            }
                        }
                    }
                    OrderStage::create(array('order_id' => $order->id, 'stage' => 0));
                    $order_status_data[] = array('order_id' => $order->id, 'status_id' => 1, 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now());
                    if (empty($order->addedBy->users_group_id)) {
                        $order_status_data[0]['user_id'] = NULL;
                        $order_status_data[0]['is_master'] = 1;
                        //admin
                    } else {
                        //dealer and retailer
                        $order_status_data[0]['user_id'] = auth()->user()->id;
                        $order_status_data[0]['is_master'] = 0;
                    }
                    $order_status_data[] = array('order_id' => $order->id, 'status_id' => 1, 'user_id' => $order->to_user, 'is_master' => 0, 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now());
                    if (!empty($order_status_data)) {
                        $order_status = OrderStatus::insert($order_status_data);
                    }
                    $cart->clear();
                    $this->myCart->clear($request_data['cart_id']);
                    if (!empty($order_product)) {
                        if ((!empty($request_data['btnSave'])) && $request_data['btnSave'] == 'savensend') {
                            $from = (!empty($order->addedBy)) ? $order->addedBy->email : $this->settingJson['mail_from_email'];
                            $from_name = (!empty($order->addedBy)) ? $order->addedBy->name : $this->settingJson['mail_from_name'];
                            $customer = (!empty($order->customer)) ? $order->customer->email : $this->settingJson['mail_from_email'];
                            try {
                                Mail::to($customer)->send(new OrderEmailTemplate($order, config('app.name') . ' - Orders', $from, $from_name));
                            } catch (Exception $e) {
                                return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to sent Mail.', 'body' => $e->getMessage()]);
                            }
                        }
                        return response()->json(['status' => 'successful', 'data' => ['quote' => $cart_data['order_reference']], 'code' => 1, 'message' => 'Orders Successfully created.']);
                    } else {
                        return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Something goes wrong.']);
                    }
                } else {
                    return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to generate Order.']);
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'code' => 0, 'message' => $e->getMessage()]);
        }
    }


    public function listQuoteAjaxData(Request $request)
    {
        $user =  auth()->user();
        $start =  $request['start'];
        $length =  $request['length'];
        $search = $request['search']['value'];

        $radio_user = $request->input('radio_user','');
        $dealer =  $request->input('dealer','');
        $retailer =  $request->input('retailer','');

        $where = 'orders.type = "quote" AND orders.deleted_at is NULL';

        if(!empty($radio_user)){
            if($radio_user == "Own"){
                if((!empty($user->userGroup) && $user->userGroup->id == '1') || $user->users_group_id == null){
                    $where .= " AND (from_user=$user->id OR from_user IS NULL)"; //Admin
                }else{
                    $where .= " AND from_user=$user->id"; //Dealers
                }
            }elseif($radio_user == "Dealers"){
                if(!empty($dealer)){
                    $where .= " AND d.users_group_id = 2 AND from_user=$dealer"; //Admin
                }else{
                    $where .= " AND d.users_group_id = 2 AND from_user=d.id"; //Admin
                }
            }elseif($radio_user == "Retailers"){
                if(!empty($retailer)){
                    $where .= " AND d.users_group_id = 3 AND from_user=$retailer"; //Admin
                }else{
                    $where .= " AND d.users_group_id = 3 AND from_user=d.id"; //Admin
                }
            }elseif($radio_user == "DRetailers"){
                if(!empty($retailer)){
                    $where .= " AND d.parent_id = $user->id AND d.users_group_id = 3 AND from_user=$retailer"; //Dealers
                }else{
                    $where .= " AND d.parent_id = $user->id AND d.users_group_id = 3 AND from_user=d.id"; //Dealers
                }
            }
        }
        if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
            $where .= (!empty($user->id)) ? " AND from_user=$user->id" : '';
        }
        // if ((!empty($user->userGroup)) && $user->userGroup->id == '2') {
        //     $users = implode(',',User::where('parent_id',$user->id)->get()->pluck('id')->toArray());
        //     $where .= (!empty($user->id)) ? " AND (from_user=$user->id OR (to_user=$user->id AND from_user <> NULL) " : '';
        //     $where .= (!empty($users)) ? " OR from_user in ($users) )" : ")";
        // }
        // else if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
        //     $where .= (!empty($user->id)) ? " AND from_user=$user->id" : '';
        // }
        // else if ((!empty($user->userGroup)) && $user->userGroup->id == '1'){
        //     $where .= (!empty($user->id)) ? " AND (from_user=$user->id OR from_user IS NULL" : '';
        //     $where .= (!empty($users)) ? " OR from_user in ($users) )" : ")";
        // }
        $where .= (!empty($search)) ? " AND (orders.order_number like '%$search%' OR orders.total like '%$search%' OR orders.created_at like '%$search%' OR (c.name is NOT NULL AND c.name like '%$search%') OR (d.name is NOT NULL AND d.name like '%$search%'))" : '';

        $orders = DB::table('orders')
            ->select(DB::raw('orders.id,order_number, c.id AS to_user, d.id AS from_user, c.name as customer,d.name as added_by, orders.total as amount, orders.created_at as created_date'))
            ->join('order_product AS op', 'orders.id', '=', 'op.order_id', 'left')
            ->join('users AS c', 'orders.to_user', '=', 'c.id', 'left')
            ->join('users AS d', 'orders.from_user', '=', 'd.id', 'left')
            ->whereRaw($where)
            ->orderBy($request['columns'][$request['order'][0]['column']]['name'], $request['order'][0]['dir']);

            // echo $orders->whereRaw($where)->groupby('orders.id')->toSql();
            // exit;
        $total_order = $orders->whereRaw($where)->groupby('orders.id')->get()->count();


        $orders = $orders->offset($start)
            ->limit($length)
            ->orderBy($request['columns'][$request['order'][0]['column']]['name'], $request['order'][0]['dir'])
            ->groupby('orders.id')
            ->get();

        $data = [];

        foreach ($orders as $order) {
            $is_sale_generated = Order::where('type', 'sale')->where('order_reference', $order->id)->where('deleted_at', NULL)->count();
            $customer = (!empty($order->customer)) ? $order->customer : $this->settingJson['name'];
            $added_by  = (!empty($order->added_by)) ? $order->added_by : $this->settingJson['name'];

            $action_html = "<span><a data-url=" . route('quote.show', $order->id) . " data-order=" . $order->id . " class='btn btn-primary view-order' data-toggle='modal' data-target='#orderModal' title='View'><i class='far fa-eye' title='View'></i></a></span>";

            if( ( ($user->users_group_id == '1' ||  is_null($user->users_group_id) ) && $order->from_user == null) || $user->id == $order->from_user){
                // $action_html .= "<span class='ml-3 create-po-".$order->id."'><a data-url=".route('cart.po.create')." data-order=".$order->id." class='btn btn-success view-order'>Generate PO</a></span>";
                if (empty($is_sale_generated) && (auth()->user()->id == $order->from_user || (empty($user->users_group_id) && empty($order->from_user)))) {
                    $action_html .= "<span class='ml-2 create-sale-" . $order->id . "'><a data-url=" . route('cart.sale.create') . " data-order=" . $order->id . " class='btn btn-default view-order' data-toggle='modal' data-target='#orderModal' title='Generate Order'><i class='fas fa-shopping-cart'></i></a></span>";
                }
                $action_html .= "<span class='ml-2 edit-quote-" . $order->id . "'><a data-url=" . route('cart.quote.edit', $order->id) . " data-order=" . $order->id . " class='btn btn-primary view-order' title='Edit'><i class='fas fa-pen'></i></a></span>";
                $action_html .= "<span class='ml-2'><button type=button data-order=$order->id class='btn btn-danger' onClick=deleteOrder($order->id) title='Delete'><i class='fas fa-trash'></i></button></span>";
            }
            $amount = "<span class='symbol-icon'>".$this->currency->symbol."</span>".Helpers::number_format($order->amount);
            $data[] = [$order->id, $order->order_number,$amount, ucwords($added_by), ucwords($customer),  Helpers::dateTime_format($order->created_date), $action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_order;
        $return_array['recordsFiltered'] = $total_order;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
            ->header('Content-Type', 'text/plain');
    }

    public function listPoAjaxData(Request $request)
    {
        $user =  auth()->user();
        $start =  $request['start'];
        $length =  $request['length'];
        $search = $request['search']['value'];

        $radio_user = $request->input('radio_user','');
        $dealer =  $request->input('dealer','');
        $retailer =  $request->input('retailer','');

        $where = 'orders.type = "po" AND orders.deleted_at is NULL';

        if(!empty($radio_user)){
            if($radio_user == "Own"){
                if((!empty($user->userGroup) && $user->userGroup->id == '1') || $user->users_group_id == null){
                    $where .= " AND (from_user=$user->id OR from_user IS NULL)"; //Admin
                }else{
                    $where .= " AND from_user=$user->id"; //Dealers
                }
            }elseif($radio_user == "Dealers"){
                if(!empty($dealer)){
                    $where .= " AND d.users_group_id = 2 AND from_user=$dealer AND for_manufacturer > 0"; //Admin
                }else{
                    $where .= " AND d.users_group_id = 2 AND from_user=d.id AND for_manufacturer > 0"; //Admin
                }
            }elseif($radio_user == "Retailers"){
                if(!empty($retailer)){
                    $where .= " AND d.users_group_id = 3 AND from_user=$retailer AND for_manufacturer > 0"; //Admin
                }else{
                    $where .= " AND d.users_group_id = 3 AND from_user=d.id AND for_manufacturer > 0"; //Admin
                }
            }elseif($radio_user == "DRetailers"){
                if(!empty($retailer)){
                    $where .= " AND d.parent_id = $user->id AND d.users_group_id = 3 AND from_user=$retailer AND for_manufacturer > 0"; //Dealers
                }else{
                    $where .= " AND d.parent_id = $user->id AND d.users_group_id = 3 AND from_user=d.id AND for_manufacturer > 0"; //Dealers
                }
            }
        }
        if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
            $where .= (!empty($user->id)) ? " AND from_user=$user->id" : '';
        }
        // $andWhere= "";
        // if ((!empty($user->userGroup)) && $user->userGroup->id == '2') {
        //     $users = implode(',',User::where('parent_id',$user->id)->get()->pluck('id')->toArray());
        //     $where .= (!empty($user->id)) ? " AND (from_user=$user->id OR to_user=$user->id" : '';
        //     $where .= (!empty($users)) ? " OR from_user in ($users) )" : ")";
        //     $andWhere= " AND (c.users_group_id!=5 OR orders.from_user=".$user->id.")";
        // }
        // else if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
        //     $where .= (!empty($user->id)) ? " AND from_user=$user->id" : '';
        // }
        // else if (empty($user->userGroup) || $user->userGroup->id == '1') {
        //     $where .= (!empty($user->id)) ? " AND (for_manufacturer = '1' OR for_manufacturer = '0' OR for_manufacturer = null OR from_user=$user->id OR from_user IS NULL OR c.users_group_id!=5 OR orders.from_user IS NULL" : '';
        //     $where .= (!empty($users)) ? " OR from_user in ($users) )" : ")";
        // }

        $where .= (!empty($search)) ? " AND (orders.order_number like '%$search%' OR orders.total like '%$search%' OR orders.created_at like '%$search%' OR (c.name is NOT NULL AND c.name like '%$search%') OR (d.name is NOT NULL AND d.name like '%$search%'))" : '';

        //$where .= $andWhere;
        $orders = DB::table('orders')
            ->select(DB::raw('orders.id, orders.for_manufacturer, orders.from_user, order_number,orders.to_user,c.name as customer,d.name as added_by, orders.total as amount, orders.created_at as created_date'))
            ->join('order_product AS op', 'orders.id', '=', 'op.order_id', 'left')
            ->join('users AS c', 'orders.to_user', '=', 'c.id', 'left')
            ->join('users AS d', 'orders.from_user', '=', 'd.id', 'left')
            ->whereRaw($where);

        $total_order = $orders->groupby('orders.id')->get()->count();
        $orders = $orders->offset($start)
            ->limit($length)
            ->orderBy($request['columns'][$request['order'][0]['column']]['name'], $request['order'][0]['dir'])
            ->groupby('orders.id')
            ->get();

        $data = [];
        foreach ($orders as $order) {
            // $orderDetail = Order::where('id','=',$order->id)->first();
            $is_sale_generated = Order::where('type', 'sale')->where('order_reference', $order->id)->where('deleted_at', NULL)->count();
            $customer = (!empty($order->customer)) ? $order->customer : $this->settingJson['name'];
            $added_by  = (!empty($order->added_by)) ? $order->added_by : $this->settingJson['name'];
            $action_html = "<span><a data-url=" . route('po.show', $order->id) . " data-order=" . $order->id . " class='btn btn-primary view-order' data-toggle='modal' data-target='#orderModal' title='View'><i class='far fa-eye' title='View'></i></a></span>";
            if (((auth()->user()->id == $order->to_user) && empty($is_sale_generated)) || ((empty($user->users_group_id) || ($user->users_group_id == '1')) && (!empty($order->for_manufacturer) && empty($is_sale_generated)))) {
                $action_html .= "<span class='ml-3 create-sale-" . $order->id . "'><a data-url=" . route('cart.sale.create') . " data-order=" . $order->id . " class='btn btn-default view-order' data-toggle='modal' data-target='#orderModal' title='Generate Order'><i class='fas fa-shopping-cart' title='Generate Order'></i></a></span>";
            }
            if( ( ($user->users_group_id == '1' ||  is_null($user->users_group_id) ) && $order->from_user == null) || $user->id == $order->from_user){
                $action_html .= "<span class='ml-3 edit-po edit-po-" . $order->id . "' data-po=" . $order->id . " ><a data-url=" . route('po.show', $order->id) . " class='btn btn-primary' title='Edit'><i class='fas fa-pen'></i></a></span>";
                $action_html .= "<span class='ml-2'><button type=button data-order=$order->id class='btn btn-danger' onClick=deleteOrder($order->id) title='Delete'><i class='fas fa-trash'></i></button></span>";
            }
            $amount = "<span class='symbol-icon'>".$this->currency->symbol."</span>".Helpers::number_format($order->amount,2);
            $data[] = [$order->id, $order->order_number, $amount, ucwords($added_by), ucwords($customer),  Helpers::dateTime_format($order->created_date), $action_html];
        }

        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_order;
        $return_array['recordsFiltered'] = $total_order;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
            ->header('Content-Type', 'text/plain');
    }


    public function listSaleAjaxData(Request $request)
    {
        $user =  auth()->user();
        $start =  @$request['start'];
        $length =  @$request['length'];
        $search = @$request['search']['value'];

        $radio_user = $request->input('radio_user','');
        $dealer =  $request->input('dealer','');
        $retailer =  $request->input('retailer','');

        $where = 'orders.type = "sale" AND orders.deleted_at is NULL';

        if(!empty($radio_user)){
            if($radio_user == "Own"){
                if((!empty($user->userGroup) && $user->userGroup->id == '1') || $user->users_group_id == null){
                    $where .= " AND (from_user=$user->id OR from_user IS NULL)"; //Admin
                }else{
                    $where .= " AND from_user=$user->id"; //Dealers
                }
            }elseif($radio_user == "Dealers"){
                if(!empty($dealer)){
                    $where .= " AND d.users_group_id = 2 AND from_user=$dealer"; //Admin
                }else{
                    $where .= " AND d.users_group_id = 2 AND from_user=d.id"; //Admin
                }
            }elseif($radio_user == "Retailers"){
                if(!empty($retailer)){
                    $where .= " AND d.users_group_id = 3 AND from_user=$retailer"; //Admin
                }else{
                    $where .= " AND d.users_group_id = 3 AND from_user=d.id"; //Admin
                }
            }elseif($radio_user == "DRetailers"){
                if(!empty($retailer)){
                    $where .= " AND d.parent_id = $user->id AND d.users_group_id = 3 AND from_user=$retailer"; //Dealers
                }else{
                    $where .= " AND d.parent_id = $user->id AND d.users_group_id = 3 AND from_user=d.id"; //Dealers
                }
            }
        }
        if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
            $where .= (!empty($user->id)) ? " AND from_user=$user->id" : '';
        }

        // if ((!empty($user->userGroup)) && $user->userGroup->id == '2') { //Dealer
        //     $users = implode(',',User::where('parent_id',$user->id)->get()->pluck('id')->toArray());
        //     $where .= (!empty($user->id)) ? " AND (from_user=$user->id OR (to_user=$user->id AND from_user <> NULL) " : '';
        //     $where .= (!empty($users)) ? " OR from_user in ($users) )" : ")";
        // }else if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
        //     $where .= (!empty($user->id)) ? " AND from_user=$user->id" : '';
        // }else if ((!empty($user->userGroup)) && $user->userGroup->id == '1') {
        //     $where .= (!empty($user->id)) ? " AND (from_user=$user->id OR from_user is NULL)" : '';
        // }

        $where .= (!empty($search)) ? " AND (orders.order_number like '%$search%' OR orders.total like '%$search%' OR orders.created_at like '%$search%' OR (c.name is NOT NULL AND c.name like '%$search%'))" : '';

        $orders = DB::table('orders')
            ->select(DB::raw('orders.id,order_number,c.name as customer,d.name as added_by, orders.total as amount, orders.created_at as created_date'))
            ->join('order_product AS op', 'orders.id', '=', 'op.order_id', 'left')
            ->join('users AS c', 'orders.to_user', '=', 'c.id', 'left')
            ->join('users AS d', 'orders.from_user', '=', 'd.id', 'left')
            ->whereRaw($where)
            ->orderBy($request['columns'][$request['order'][0]['column']]['name'], $request['order'][0]['dir']);

        $total_order = $orders->whereRaw($where)->groupby('orders.id')->get()->count();

        $orders = $orders->offset($start)
            ->limit($length)
            ->orderBy($request['columns'][$request['order'][0]['column']]['name'], $request['order'][0]['dir'])
            ->groupby('orders.id')
            ->get();

        $data = [];
        foreach ($orders as $order) {
            $order_data = Order::find($order->id);
            $order_status = 'N/A';
            if (!empty($order_data->getUserOrderStatus())) {
                $order_status = $order_data->getInvoiceStatus(($order_data->getUserOrderStatus())->status_id);
            }
            $customer = (!empty($order->customer)) ? $order->customer : $this->settingJson['name'];
            $added_by  = (!empty($order->added_by))?$order->added_by:$this->settingJson['name'];
            $action_html = "<span><a data-url=" . route('sale.show', $order->id) . " data-order=" . $order->id . " class='btn btn-primary view-order' data-toggle='modal' data-target='#orderModal' title='View'><i class='far fa-eye' title='View'></i></a></span>";
            // $action_html .= "<span class='ml-3 create-po-".$order->id."'><a data-url=".route('cart.po.create')." data-order=".$order->id." class='btn btn-success view-quote' data-toggle='modal' data-target='#orderModal'>Generate PO</a></span>";
            if( ( ($user->users_group_id == '1' ||  is_null($user->users_group_id) ) && $order_data->from_user == null) || $user->id == $order_data->from_user)
            {
                $action_html .= "<span class='ml-3'><a data-url=" . route('cart.sale.edit', $order->id) . " data-order=" . $order->id . " class='btn btn-primary view-order' data-toggle='modal' data-target='#orderModal' title='Edit'><i class='fas fa-pen'></i></a></span>";
            }
            // $action_html .= "<span class='ml-3'><button type=submit data-user=$order->id class='btn btn-danger' onClick=deleteOrder($order->id)>Delete</button></span>";
            $amount ="<span class='symbol-icon'>".$this->currency->symbol."</span>".Helpers::number_format($order->amount);
            $data[] = [$order->id, $order->order_number, $amount, ucwords($added_by), ucwords($customer), $order_status, Helpers::dateTime_format($order->created_date), $action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_order;
        $return_array['recordsFiltered'] = $total_order;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
            ->header('Content-Type', 'text/plain');
    }

    public function listInvoiceAjaxData(Request $request)
    {
        $user =  auth()->user();
        $start =  $request['start'];
        $length =  $request['length'];
        $search = $request['search']['value'];
        $where = 'orders.type = "sale" AND orders.deleted_at is NULL';
        if ((!empty($user->userGroup)) && $user->userGroup->id == '2') {
            // $users = implode(',',User::where('parent_id',$user->id)->get()->pluck('id')->toArray());
            $where .= (!empty($user->id)) ? " AND (to_user=$user->id " : '';
            $where .= (!empty($users)) ? " OR from_user in ($users) )" : ")";
        }
        if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
            $where .= (!empty($user->id)) ? " AND to_user=$user->id" : '';
        } else {
            $where .= (!empty($user->id)) ? " AND to_user=$user->id" : '';
        }
        // $where .= (!empty($dealer_id))?" AND (dealer_id=$dealer_id OR inquiries.created_by=$dealer_id)":'';
        $where .= (!empty($search)) ? " AND (orders.order_number like '%$search%' OR orders.total like '%$search%' OR orders.created_at like '%$search%' OR (c.name is NOT NULL AND c.name like '%$search%') OR (d.name is NOT NULL AND d.name like '%$search%'))" : '';
        $orders = DB::table('orders')
            ->select(DB::raw('orders.id,order_number,c.name as customer,d.name as added_by, orders.total as amount, orders.created_at as created_date'))
            ->join('order_product AS op', 'orders.id', '=', 'op.order_id', 'left')
            ->join('users AS c', 'orders.to_user', '=', 'c.id', 'left')
            ->join('users AS d', 'orders.from_user', '=', 'd.id', 'left')
            ->whereRaw($where)
            ->offset($start)
            ->limit($length)
            ->orderBy($request['columns'][$request['order'][0]['column']]['name'], $request['order'][0]['dir'])
            ->groupby('orders.id')
            ->get();

        $total_order = Order::where('type', 'sale')->where('to_user', $user->id)->where('deleted_at', NULL)->count();
        $data = [];
        foreach ($orders as $order) {
            $order_data = Order::find($order->id);
            $order_status = 'N/A';
            if (!empty($order_data->getUserOrderStatus())) {
                $order_status = $order_data->getInvoiceStatus($order_data->getUserOrderStatus()->status_id);
            }
            $customer = (!empty($order->customer)) ? $order->customer : $this->settingJson['name'];
            $added_by  = (!empty($order->added_by)) ? $order->added_by : $this->settingJson['name'];
            $action_html = "<span><a data-url=" . route('invoice.show', $order->id) . " data-order=" . $order->id . " class='btn btn-primary view-order' data-toggle='modal' data-target='#orderModal' title='View'><i class='far fa-eye' title='View'></i></a></span>";
            // $action_html .= "<span class='ml-3 create-po-".$order->id."'><a data-url=".route('cart.po.create')." data-order=".$order->id." class='btn btn-success view-quote' data-toggle='modal' data-target='#orderModal'>Generate PO</a></span>";
            // $action_html = "<span><a href=".route('order.edit',$order->id)." class='btn btn-primary'>Edit</a></span>";
            // $action_html .= "<span class='ml-3'><button type=submit data-user=$order->id class='btn btn-danger' onClick=deleteOrder($order->id)>Delete</button></span>";
            $data[] = [$order->id, $order->order_number, $order->amount, ucwords($added_by), ucwords($customer), $order_status, $order->created_date, $action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_order;
        $return_array['recordsFiltered'] = $total_order;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
            ->header('Content-Type', 'text/plain');
    }

    public function send($email_data)
    {
        if (!empty($email_data)) {
            try {
                Mail::send($email_data['view'], ['details' => ['title' => $email_data['title'], 'content' => $email_data['content']]], function ($message) use ($email_data) {

                    $message->from((!empty($email_data['from'])) ? $email_data['from'] : 'admin@admin.com', (!empty($email_data['from_name'])) ? $email_data['from_name'] : 'Laravel');

                    $message->to($email_data['to']);

                    //Attach file
                    if (!empty($email_data['attach'])) {
                        $message->attach($email_data['attach']);
                    }

                    //Add a subject
                    $message->subject("Mail from Laravel");
                });
            } catch (\Exception $e) {
            }
        }
    }

    public function updateOrderStatus(Request $request)
    {
        $request_data = $request->except('_token', 'method');
        $orderStatus = OrderStatus::where('order_id', $request_data['order_id'])->where('user_id', (!empty(auth()->user()->users_group_id)) ? auth()->user()->id : NULL);
        if ((!empty($orderStatus->get())) && count($orderStatus->get())) {
            $order = Order::findOrFail($request_data['order_id']);
            if(isset($request_data['courier_details'])){
                $order->update(['courier_detail'=>$request_data['courier_details']]);
            }
            try {
                $is_updated = $orderStatus->update(['status_id' => $request_data['status_id'], 'updated_at' => \Carbon\Carbon::now()]);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'code' => 0, 'message' => $e->getMessage()]);
            }
            if (!empty($is_updated)) {
                return response()->json(['status' => 'successful', 'code' => 1, 'message' => 'Order Status Successfully Updated.']);
            } else {
                return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to Update Order Status.']);
            }
        } else {
            $order = Order::findOrFail($request_data['order_id']);
            $order->update(['courier_detail'=>$request_data['courier_details']]);
            $order_status_data[] = array('order_id' => $order->id, 'status_id' => $request_data['status_id'], 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now());
            if (empty($order->from_user->users_group_id)) {
                $order_status_data[0]['user_id'] = NULL;
                $order_status_data[0]['is_master'] = 1;
            } else {
                $order_status_data[0]['user_id'] = auth()->user()->id;
                $order_status_data[0]['is_master'] = 0;
            }
            $order_status_data[] = array('order_id' => $order->id, 'status_id' => $request_data['status_id'], 'user_id' => $order->to_user, 'is_master' => 0, 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now());
            if (!empty($order_status_data)) {
                $order_status = OrderStatus::insert($order_status_data);
            }
            if (!empty($order_status)) {
                return response()->json(['status' => 'successful', 'code' => 1, 'message' => 'Order Status Successfully Updated.']);
            } else {
                return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to Update Order Status.']);
            }
        }
    }
    public function updateOrderStage(Request $request)
    {
        $request_data = $request->except('_token', 'method');
        $orderStatus = OrderStage::where('order_id', $request_data['order_id'])->where('user_id', (!empty(auth()->user()->users_group_id)) ? auth()->user()->id : NULL);
        if ((!empty($orderStatus->get())) && count($orderStatus->get())) {
            try {
                $is_updated = $orderStatus->update(['stage' => $request_data['stage'], 'updated_at' => \Carbon\Carbon::now()]);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'code' => 0, 'message' => $e->getMessage()]);
            }
            if (!empty($is_updated)) {
                return response()->json(['status' => 'successful', 'code' => 1, 'message' => 'Order Stage Successfully Updated.']);
            } else {
                return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to Update Order Stage.']);
            }
        } else {
            $order = Order::findOrFail($request_data['order_id']);
            $order_stage_data = array('order_id' => $order->id, 'stage' => $request_data['stage']);
            if (empty($order->from_user->users_group_id)) {
                $order_stage_data['user_id'] = NULL;
            } else {
                $order_stage_data['user_id'] = auth()->user()->id;
            }
            if (!empty($order_stage_data)) {
                $order_status = OrderStage::create($order_stage_data);
            }
            if (!empty($order_status)) {
                return response()->json(['status' => 'successful', 'code' => 1, 'message' => 'Order Stage Successfully Updated.']);
            } else {
                return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to Update Order Stage.']);
            }
        }
    }

    public function updateOrderToken(Request $request)
    {
        $request_data = $request->except('_token', 'method');
        $is_updated = 0;
        try {
            $order = Order::findOrFail($request_data['order_id']);
            if (!empty($request->input('password', ''))) {
                $is_updated = $order->update(['token' => $request_data['password'], 'updated_at' => \Carbon\Carbon::now()]);
                if ($is_updated) {
                    return response()->json(['status' => 'successful', 'code' => 1, 'message' => 'Order Successfully Updated.']);
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'code' => 0, 'message' => $e->getMessage()]);
        }
        return response()->json(['status' => 'error', 'code' => 0, 'message' => 'Unable to Update Order.']);
    }

    //Reports

    public function poReport()
    {
        View::share('title', 'PO Report');
        View::share('elementActive', 'po_report');
        return view('admin.report.po');
    }
    public function salesReport()
    {
        View::share('title', 'Orders Report');
        View::share('elementActive', 'sales_report');
        $products = Product::withTrashed()->get();
        return view('admin.report.sales',compact('products'));
    }

    public function poReportListAjaxData(Request $request)
    {
        $user =  auth()->user();
        $start =  $request->input('start', '');
        $length =  $request->input('length', '');
        $search = $request->input('search.value', '');
        $from_date = $request->input('from_date', '');
        $to_date = $request->input('to_date', '');
        $sent_received = $request->input('sent_recieved', '');
        $where = 'orders.type = "po" AND orders.deleted_at is NULL';
        if ((!empty($user->userGroup)) && $user->userGroup->id == '2') {
            if (!empty($sent_received) && $sent_received == 'sent') {
                $where .= (!empty($user->id)) ? " AND (from_user=$user->id)" : '';
            } elseif (!empty($sent_received) && $sent_received == 'received') {
                $where .= (!empty($user->id)) ? " AND (to_user=$user->id)" : ')';
            } else {
                $where .= (!empty($user->id)) ? " AND (from_user=$user->id OR to_user=$user->id" : '';
                $where .= (!empty($users)) ? " OR from_user in ($users) )" : ")";
            }
        }
        if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
            $where .= (!empty($user->id)) ? " AND from_user=$user->id" : '';
        } else if (empty($user->userGroup) || $user->userGroup->id == '1') {
            if (!empty($sent_received) && $sent_received == 'sent') {
                $where .= (!empty($user->id)) ? " AND (from_user IS NULL)" : '';
            } elseif (!empty($sent_received) && $sent_received == 'received') {
                $where .= (!empty($user->id)) ? " AND (to_user IS NULL)" : ')';
            } else {
                $where .= (!empty($user->id)) ? " AND (for_manufacturer = '1' OR from_user=$user->id OR from_user IS NULL" : '';
                $where .= (!empty($users)) ? " OR from_user in ($users) )" : ")";
            }
        }
        $datetime_like_format = Session::get('datetime_like_format');
        if(!empty($datetime_like_format)){
            $datetime_like_format = $datetime_like_format;
        }else{
            $datetime_like_format = '%Y-%m-%d %H:%i:%s';
        }
        $where .= (!empty($search)) ? " AND (orders.order_number like '%$search%' OR orders.total like '%$search%' OR DATE_FORMAT(orders.created_at, '$datetime_like_format') like '%$search%' OR (c.name is NOT NULL AND c.name like '%$search%') OR (d.name is NOT NULL AND d.name like '%$search%'))" : '';
        if (!empty($from_date) && !empty($to_date)) {
            $where .= " AND (orders.created_at BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59')";
        }else if (!empty($from_date) && empty($to_date)) {
            $where .= " AND (orders.created_at >= '$from_date 00:00:00')";
        }else if (empty($from_date) && !empty($to_date)) {
            $where .= " AND (orders.created_at <= '$to_date 23:59:59')";
        }
        $orders = DB::table('orders')
            ->select(DB::raw('orders.id, orders.for_manufacturer, order_number,orders.to_user,c.name as customer,d.name as added_by, orders.total as amount, orders.created_at as created_date'))
            ->join('order_product AS op', 'orders.id', '=', 'op.order_id', 'left')
            ->join('users AS c', 'orders.to_user', '=', 'c.id', 'left')
            ->join('users AS d', 'orders.from_user', '=', 'd.id', 'left')
            ->whereRaw($where);

        $total_order = $orders->groupby('orders.id')->get()->count();
        if (!empty($request->input('export', ''))) {
            $orders = $orders
                ->groupby('orders.id')
                ->get();
            return Excel::download(new POExport($orders), 'PO.xlsx');
        } else {
            $orders = $orders->offset($start)
                ->limit($length)
                ->orderBy($request['columns'][$request['order'][0]['column']]['name'], $request['order'][0]['dir'])
                ->groupby('orders.id')
                ->get();
            $data = [];
            foreach ($orders as $order) {
                $customer = (!empty($order->customer)) ? $order->customer : $this->settingJson['name'];
                $amount  = "<span class='symbol-icon'>".$this->currency->symbol."</span>".Helpers::number_format($order->amount,2);
                $data[] = [$order->id, $order->order_number, $amount, ucwords($customer), Helpers::dateTime_format($order->created_date)];
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

    public function salesReportListAjaxData(Request $request)
    {
        $user =  auth()->user();
        $start =  $request->input('start', '');
        $length =  $request->input('length', '');
        $search = $request->input('search.value', '');
        $from_date = $request->input('from_date', '');
        $to_date = $request->input('to_date', '');
        $order_stage = $request->input('order_stage', '');
        $payment_status = $request->input('order_status', '');
        $order_product = $request->input('product', '');
        $where = 'orders.type = "sale" AND orders.deleted_at is NULL';
        if ((!empty($user->userGroup)) && $user->userGroup->id == '2') {
            // $users = implode(',',User::where('parent_id',$user->id)->get()->pluck('id')->toArray());
            $where .= (!empty($user->id)) ? " AND (from_user=$user->id OR (to_user=$user->id AND from_user <> NULL) " : '';
            $where .= (!empty($users)) ? " OR from_user in ($users) )" : ")";
        }
        if ((!empty($user->userGroup)) && $user->userGroup->id == '3') {
            $where .= (!empty($user->id)) ? " AND from_user=$user->id" : '';
        } else {
            $where .= (!empty($user->id)) ? " AND (from_user=$user->id OR from_user is NULL)" : '';
        }

        $datetime_like_format = Session::get('datetime_like_format');
        if(!empty($datetime_like_format)){
            $datetime_like_format = $datetime_like_format;
        }else{
            $datetime_like_format = '%Y-%m-%d %H:%i:%s';
        }

        $where .= (!empty($search)) ? " AND (orders.order_number like '%$search%' OR orders.total like '%$search%' OR DATE_FORMAT(orders.created_at, '$datetime_like_format') like '%$search%' OR (c.name is NOT NULL AND c.name like '%$search%') OR (d.name is NOT NULL AND d.name like '%$search%'))" : '';
        if (!empty($from_date) && !empty($to_date)) {
            $where .= " AND (orders.created_at BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59')";
        }else if (!empty($from_date) && empty($to_date)) {
            $where .= " AND (orders.created_at >= '$from_date 00:00:00')";
        }else if (empty($from_date) && !empty($to_date)) {
            $where .= " AND (orders.created_at <= '$to_date 23:59:59')";
        }
        if ($order_stage != "") {
            $where .= " AND (os.stage='$order_stage')";
        }
        if (!empty($payment_status)) {
            $where .= " AND (sa.status_id='$payment_status')";
        }
        if(!empty($order_product)){
            $where .= " AND (op.product_id='$order_product')";
        }

        $orders = DB::table('orders')
            ->select(DB::raw('orders.id,os.stage,sa.status_id, orders.for_manufacturer, order_number,orders.to_user,c.name as customer,d.name as added_by, orders.total as amount, orders.created_at as created_date'))
            ->join('order_product AS op', 'orders.id', '=', 'op.order_id', 'left')
            ->join('users AS c', 'orders.to_user', '=', 'c.id', 'left')
            ->join('users AS d', 'orders.from_user', '=', 'd.id', 'left')
            ->join('order_stages AS os', 'orders.id', '=', 'os.order_id', 'left')
            ->join('order_status AS sa', 'orders.id', '=', 'sa.order_id', 'left')
            ->whereRaw($where);
        //echo $orders->toSql();exit;
        $total_order = $orders->groupBy('orders.id')->get()->count();
        if (!empty($request->input('export', ''))) {
            $orders = $orders
                ->groupby('orders.id')
                ->get();
            return Excel::download(new POExport($orders), 'Orders.xlsx');
        } else {
            $orders = $orders->offset($start)
                ->limit($length)
                ->orderBy($request['columns'][$request['order'][0]['column']]['name'], $request['order'][0]['dir'])
                ->groupby('orders.id')
                ->get();
            $data = [];
            foreach ($orders as $order) {
                $order_data = Order::find($order->id);
                $order_status = 'N/A';
                if (!empty($order->status_id)) {
                    $order_status = $order_data->getInvoiceStatus($order->status_id);
                }
                $customer = (!empty($order->customer)) ? $order->customer : $this->settingJson['name'];
                $amount  = "<span class='symbol-icon'>".$this->currency->symbol."</span>".Helpers::number_format($order->amount,2);
                $data[] = [$order->id, $order->order_number, $amount, ucwords($customer), $order_status, $order_data->getSalesStage(!empty($order->stage) ? $order->stage : 0),  Helpers::dateTime_format($order->created_date)];
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

    public function deleteOrder(Request $request)
    {
        $user = auth()->user();
        $id = $request->input('order_id', '');
        if (!empty($id)) {
            try {
                $order = Order::findOrFail($id);
                if (empty($user->users_group_id) || ($order->from_user == $user->id)) {
                    $order->delete();
                    $return_array = ['code' => 1, 'status' => 'success', 'data' => 'Order is successfully deleted'];
                } else {
                    $return_array = ['code' => 1, 'status' => 'success', 'data' => 'You are not able delete this order.'];
                }
            } catch (Exception $e) {
                $return_array = ['code' => 0, 'status' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            $return_array = ['code' => 0, 'status' => 'error', 'message' => 'Something goes wrong.'];
        }
        return response()->json($return_array);
    }
    public function saveOrderInquiries($order, $array)
    {
        OrderInquiries::where('order_id', $order)->delete();
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                OrderInquiries::create(array('order_id' => $order, 'inquiry_id' => $key));
            }
        }
    }
}
