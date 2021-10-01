<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendResetPassword;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends MainController
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

    public function __construct()
    {
        parent::__construct();
    }

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
            $token = Str::random(60);
            DB::table('password_resets')->insert([
                'email' => $user->email,
                'user_id'=> $user->id,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            $user->sendPasswordResetNotification($token);
            $message = 'Reset password link has been successfull sent.';
            return $request->wantsJson()
                    ? new JsonResponse(['message' => trans($message)], 200)
                    : back()->with('status', trans($message));
            // Mail::to($user->email)->;
        } else {
            $message = 'Email is not registered.';
            return $request->wantsJson()
                    ? new JsonResponse(['message' => trans($message)], 404)
                    : back()->withErrors(['email' => trans($message)]);
        }


    }
}
