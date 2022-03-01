<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MainController;
use App\Http\MyCart;
use App\Models\Order;
use App\Models\OrderImage;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use View;

class InstallationController extends MainController
{
    //
    private $myCart;
    public function __construct(MyCart $myCart)
    {
        parent::__construct();
        $this->myCart = $myCart;
    }
    
    public function installlationLogin(Request $request,$token=''){
        if(!empty($token)){
            try{
                Order::findOrFail(Crypt::decryptString($token));
                if(\Session::has('installation_user')){
                    return redirect(route('installation.order.dashboard'));
                }
            }catch (DecryptException $e) {
                return abort(401);
            }
        }
        return view('installation.login',['token'=>$token]);
    }

    public function Login(Request $request){
        $request_data = $request->except(['method','_token']);
        if(empty($request_data['token'])){
            throw ValidationException::withMessages([
                'password' => 'Token not found',
            ]);
        }
        try{
            $order = Order::findOrFail(Crypt::decryptString($request_data['token']));
            if(!empty($order->getSalesOrderStage()) && ($order->getSalesOrderStage()->stage == 2)){
                if(!empty($order->token) && $order->token == $request_data['password']){
                    \Session::put('installation_user',$order->id);
                    return redirect(route('installation.order.dashboard'));
                }else{
                    throw ValidationException::withMessages([
                        'password' => 'Invalid Password',
                    ]);
                }
            }else{
                return abort(401);
            }
        }catch (DecryptException $e) {
            return abort(401);
        }
    }
    
    public function dashboard(Request $request){
        \View::share('title','Dashboard');
        if(\Session::has('installation_user')){
            $order = Order::findOrFail(\Session::get('installation_user'));
            return view('installation.dashboard',['order'=>$order]);
        }else{
            return redirect(route('installation.login'));
        }
    }

    public function uploadInstallationImage(Request $request){
        $orderImage = [];
        \Validator::make($request->all(), [
            'image' => 'required|mimes:png,jpeg,jpg|max:10000'
        ],
        [
            'image.required'=> 'Image is required.',
        ])->validate();
        if($request->file()){
            try{
                $order = Order::findOrFail(\Session::get('installation_user'));
                $filePath = $this->storeImage($request);
                $orderImage = OrderImage::create(['order_id'=>$order->id,'product_id'=>$request->input('product_id',''),'image'=>$filePath,'uploaded_by'=>$request->input('uploaded_by',NULL)]);
                if(!empty($orderImage)){
                    return response()->json(['code'=>1,'status'=>'success' ,'message'=>'Image successfully uploaded.']);
                }else{
                    return response()->json(['code'=>0,'status'=>'error','message'=>'Unable to upload Image.']);
                }
            }catch(Exception $e){
                return response()->json(['code'=>0,'status'=>'error', 'exception'=> $e->getMessage()]);
            }
        }
        return response()->json(['code'=>0,'status'=>'error','message'=>'Something goes wrong.']);
    }

    public function removeInstallationImage(Request $request){
        try{
            $orderImage = OrderImage::findOrFail($request->input('order_image_id',''));
            if(!empty($orderImage)){
                $filename = str_replace('/storage/uploads/installation/', '', $orderImage->image);
                if(!empty($orderImage->image) && file_exists(storage_path('app/public/uploads/installation/'.$filename))){
                    unlink(storage_path('app/public/uploads/installation/'.$filename));
                }
                if($orderImage->delete()){
                    return response()->json(['code'=>1,'status'=>'success','message'=>'Image successfully Deleted.']);
                }
            }
        }catch(Exception $e){
            return response()->json(['code'=>0,'status'=>'error', 'exception'=> $e->getMessage()]);
        }
        return response()->json(['code'=>0,'status'=>'error','message'=>'Something goes wrong.']);
    }

    public function storeImage($request){
        if($request->file()) {
            $fileName = time().'_'.$request->image->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('uploads/installation', $fileName, 'public');
            return '/storage/' . $filePath;
        }
        return false;
    }
}
