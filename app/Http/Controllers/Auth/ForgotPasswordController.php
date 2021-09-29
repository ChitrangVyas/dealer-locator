<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected function credentials(Request $request)
    {
        
        $user_group = (!empty($request->input('user_group')))?$request->input('user_group'):null;
        if(!empty($user_group)){
            return array_merge($request->only('email'), ['users_group_id' => (int)$user_group]);
        }
        return $request->only('email');

    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $user = $this->credentials($request);

    }
}
