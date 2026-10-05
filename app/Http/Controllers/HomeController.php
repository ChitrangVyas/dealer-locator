<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MainController;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Models\Category;
use Hash;


class HomeController extends MainController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $elementActive = "home";
        $title = 'Dashboard';
        $user = auth()->user();
        $category_count = Category::count();
        $dealer_count = User::where('users_group_id','=','2')->count();
        return view('home', compact('elementActive', 'title','category_count','dealer_count'));
    }

    public function changepassword(Request $request)
    {
        $elementActive = '';
        $title = '';
        $user = auth()->user();
        return view('changepassword', compact('elementActive', 'user', 'title'));
    }

    public function updatepassword(ChangePasswordRequest $request)
    {
        if (!Hash::check($request->input('old_password'), auth()->user()->password)) {
            $return_array['errors']['old_password'] = 'Old password mismatch';
            return response(json_encode($return_array), 422)
            ->header('Content-Type', 'application/json');
        }

        $user = User::find($request->input('id'));
        $user->update(['password' => Hash::make($request->input('new_password'))]);
        $return_array['code'] = 1;
        $return_array['message'] = 'Password updated sucessfully';
        \Session::flash('success', 'Password updated sucessfully');
        return response(json_encode($return_array), 200)
            ->header('Content-Type', 'application/json');
    }
}
