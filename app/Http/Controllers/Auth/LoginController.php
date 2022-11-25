<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LoginController extends MainController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function credentials(Request $request)
    {
        $user_group = (!empty($request->input('user_group')))?$request->input('user_group'):null;
        if(!empty($user_group)){
            return array_merge($request->only($this->username(), 'password'), ['users_group_id' => $user_group,'status'=>'1']);
        }
        return $request->only($this->username(), 'password');

    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => [
                'required',
                'string',
                Rule::exists('users','email')
                    ->where(function ($query) use ($request) {
                        $query->where(function($q){
                            $q->where('users_group_id',"<>",4)->orWhereNull('users_group_id');
                        });
                    })
                ],
            'password' => 'required|string',
        ]);
    }
}
