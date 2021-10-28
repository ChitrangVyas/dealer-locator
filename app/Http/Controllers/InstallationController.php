<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MainController;
use App\Http\MyCart;
use App\Models\Order;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class InstallationController extends MainController
{
    //
    private $myCart;
    public function __construct(MyCart $myCart)
    {
        parent::__construct();
        $this->myCart = $myCart;
    }
    
    public function installlationLogin(Request $request,$token){
        if(!empty($token)){
            try{
                Order::findOrFail(Crypt::decryptString($token));
            }catch (DecryptException $e) {
                return abort(401);
            }
        }
        return view('installation.login',['token'=>$token]);
    }

    public function Login(Request $request){
        $request_data = $request->except(['method','_token']);
        if(empty($request_data['token'])){
            return abort(401);
        }
        try{
            Order::findOrFail(Crypt::decryptString($request_data['token']));
        }catch (DecryptException $e) {
            return abort(401);
        }
        throw ValidationException::withMessages([
            'password' => 'Invalid Password',
        ]);
    }
}
