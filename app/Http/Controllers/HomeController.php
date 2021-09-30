<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MainController;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ChangePasswordRequest;
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
        $total_order = Order::where('type', 'sale')->where('from_user', auth()->user()->id)->where('deleted_at', NULL)->count();
        $total_po = 0;
        $total_dealer = 0;
        $total_dealer = User::where('users_group_id', 2)->where('deleted_at', NULL)->count();
        $total_retailer = User::where('users_group_id', 3)->where('deleted_at', NULL)->count();
        if (empty($user->userGroup) || $user->userGroup->id == '1') {
            $total_po = Order::where('type', 'po')->where('from_user', $user->id, function ($query) {
                return $query->orWhere('for_manufacturer', 1);
            })->where('deleted_at', NULL)->count();
        } else {
            $total_po = Order::where('type', 'po')->where('from_user', $user->id)->where('deleted_at', NULL)->count();
        }
        $orders = Order::selectRaw('year(created_at) year, month(created_at) month, count(*) data')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->get()
            ->groupBy('year');
        $ordercount = [];
        $orderArr = [];
        foreach ($orders as $key => $value) {
            foreach ($value as $k => $val) {
                $ordercount[(int)$key][(int)$val['month']] = $val['data'];
            }
        }
        $count = 0;
        foreach ($ordercount as $key => $ordermcount) {
            for ($i = 1; $i <= 12; $i++) {
                if (!empty($ordermcount[$i])) {
                    $orderArr[$count][] = $ordermcount[$i];
                } else {
                    $orderArr[$count][] = 0;
                }
            }
            $count++;
        }
        // dd($orderArr);
        if (!isset($orderArr[0])) {
            $orderArr[0] = array();
        }
        return view('home', compact('elementActive', 'title', 'total_order', 'total_po', 'total_dealer', 'total_retailer', 'orderArr'));
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
