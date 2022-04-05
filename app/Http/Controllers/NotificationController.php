<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MainController;
use App\Http\Helpers;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\HelperSet;
use View;

class NotificationController extends MainController
{
    public function __construct() {
        parent::__construct();
        View::share ( 'title', 'Notification' );
        View::share ( 'elementActive', 'notification' );    
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = auth()->user();
        Notification::where(function($query) use($user){
             $query->where('user_id',$user->id)->where('is_read',0);
        })->update(['is_read'=>1]);
        return view('admin.notification.index');
    }

    public function markAsRead(Request $request){
        $id  = $request->input('id');
        if(!empty($id)){
            $notification = Notification::findOrFail($id);
            $update_data = $request->except('id','_token');
            $is_updated = $notification->where('id',$id)->update($update_data);
            if(!empty($is_updated)){
                return response()->json(['status' => 'successful','code'=>1,'message'=>'']);
            }
        }
        return response()->json(['status' => 'error','code'=>0,'message'=>'Something goes wrong.']);
    }


    /**
     * Display User's All Unread Notification.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function showAll(Request $request)
    {
        // $request_data = $request->all();
        $user = auth()->user();
        $is_admin = (empty($user->users_group_id))?1:0;
        $user_id = (empty($is_admin))?$user->id:'';
        $notifications = Helpers::getUnreadNotification($user_id,$is_admin);
        $total_notifications = $notifications->count();
        $notifications = $notifications->take(10)->get();
        $json['total_notifications'] = (!empty($total_notifications))?$total_notifications:'';
        $json['content'] = view('admin.notification.ajax-notification',['total_notification'=>$total_notifications,'notifications'=>$notifications])->render();
        return response()->json($json);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function userNotification(Request $request)
    {
        // $request_data = $request->all();
        $id = $request->input('id');
        if(!empty($id)){
            $user = auth()->user();
            $user_id = (empty($user->users_group_id))?NULL:$user->id;
            $notifications = Helpers::getNotification($user_id,$id)->first();
            $icon = $notifications->getIcon()['tag'];
            return response()->json(["code"=>1,"message"=>"","data"=>["title"=>"Notification","content"=>$notifications,'icon'=>$icon]]);
        }else{
            return response()->json(["code"=>0,"message"=>"Unable to find notification","data"=>""]);
        }
    }

    public function listAjaxData(Request $request)
    {
        $user =  auth()->user();
        $user_id = NULL;
        if ((!empty($user->users_group_id)) && $user->users_group_id == '2') {
            $user_id = $user->id;
        } else if ((!empty($user->users_group_id)) && $user->users_group_id == '3') {
            $user_id = $user->id;
        }
        $start =  $request['start'];
        $length =  $request['length'];
        $search = $request['search']['value'];
        if (!empty($user_id)) {
            $where = "user_id = $user_id";
        } else {
            $where = 'user_id IS NULL';
        }
        $where .= (!empty($search)) ? " AND (message like '$search%' OR created_at like '%$search%')" : '';
        $notifications = DB::table('notifications')
            ->selectRaw('id,message,created_at')
            ->whereRaw($where)
            ->offset($start)
            ->limit($length)
            ->orderBy($request['columns'][$request['order'][0]['column']]['name'], $request['order'][0]['dir'])
            ->get();

        $total_notifications = Notification::where(function ($query) use ($user_id) {
            if (!empty($user_id)) {
                return $query->where('user_id', $user_id);
            } else {
                return $query->whereNull('user_id');
            }
        })->count();
        $data = [];
        foreach ($notifications as $notification) {
            // $action_html = "<span><a href=".route('staff.edit',$user->id)." class='btn btn-primary'>Edit</a></span>";
            $data[] = [$notification->id, $notification->message, Helpers::dateTime_format($notification->created_at)];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_notifications;
        $return_array['recordsFiltered'] = $total_notifications;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
            ->header('Content-Type', 'text/plain');
    }
}
