<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends MainController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */

    public function __construct()
    {
        parent::__construct();
    }
    
    protected $redirectTo = RouteServiceProvider::HOME;

    public function resetPassword(Request $request){
        $request->validate($this->rules(), $this->validationErrorMessages());
        
    }
}
