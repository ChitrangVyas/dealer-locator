<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
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

        $data = $this->credentials($request);

        $user = User::where($data)->first();
        $message = '';
        if(!empty($user)){
            /* return $request->wantsJson()
                    ? new JsonResponse(['message' => trans($message)], 200)
                    : back()->with('status', trans($message)); */
        }else{
            $message = 'Email is not registered.';
            return $request->wantsJson()
                    ? new JsonResponse(['message' => trans($message)], 200)
                    : back()->with('status', trans($message));
        }


    }
}
