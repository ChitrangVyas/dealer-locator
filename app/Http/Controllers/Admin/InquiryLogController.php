<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InquiryLogCreateRequest;
use App\Http\Requests\Admin\ReminderCreateRequest;
use App\Models\InquiryLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use View;
use App\Models\Inquiry;
class InquiryLogController extends MainController
{
    public function __construct() {
        parent::__construct();
        View::share ( 'title', 'Inquiry Log' );
        View::share ( 'elementActive', 'inquiry_log' );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.inquiry.log');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('layouts.partials.log-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InquiryLogCreateRequest $request)
    {
        if($request->ajax()){
            try{
                $requestDefault = $request->all();
                $request_data = $request->all();
                $request_data['is_reminder']=0;
                $request_data['for_created_at']= date("Y-m-d H:i:s", time());
                $inquiry = InquiryLog::create($request_data);
                if(!empty($requestDefault['is_reminder'])){
                    $requestDefault['is_reminder'] = 1;
                    $requestDefault['description']='';
                    $inquiry = InquiryLog::create($requestDefault);
                }
                
                if(!empty($inquiry))
                    return response()->json(['status' => 'successful','code'=>1,'message'=>'Inquiry Successfully saved.']);
            } catch(Exception $e) { 
                return response()->json(['status' => 'error','code' => 0,'message' => $e->getMessage()]);                
            }
        } else {
            return response('',422)->json(['status' => 'error','message'=>'Invalid request','code' => 0]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        
        $inquiry_logs = InquiryLog::where('inquiry_id','=',$id)->orderBy('for_created_at','desc')->get()->groupBy(function($date){ return Carbon::parse($date->for_created_at)->format('Y-m-d');});              
        $inquiry = Inquiry::find($id);
        return view('admin.inquiry.viewlog',['inquiry_logs'=>$inquiry_logs,'inquiry'=>$inquiry]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
     
    }
       /*
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function createReminder()
    {
        //
        return view('admin.inquiry.reminder');
    }

    public function storeReminder(ReminderCreateRequest $request)
    {
        if($request->ajax()){
            try{
                $requestDefault = $request->all();
                $requestDefault['is_reminder'] = 1;
                $requestDefault['description']=(!empty($requestDefault['description'])) ? $requestDefault['description'] : '';
                $inquiry = InquiryLog::create($requestDefault);
                if(!empty($inquiry))
                    return response()->json(['status' => 'successful','code'=>1,'message'=>'Reminder Successfully saved.']);
            } catch(Exception $e) { 
                return response()->json(['status' => 'error','code' => 0,'message' => $e->getMessage()]);                
            }
        } else {
            return response('',422)->json(['status' => 'error','message'=>'Invalid request','code' => 0]);
        }        
    }
}
