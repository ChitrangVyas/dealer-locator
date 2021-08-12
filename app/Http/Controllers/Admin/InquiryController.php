<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InquiriesUpdateRequest;
use App\Http\Requests\Admin\InquiryCreateRequest;
use App\Http\Requests\Admin\InquiryUpdateRequest;
use App\Models\InquiriesSource;
use App\Models\Inquiry;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;

class InquiryController extends Controller
{
    public function __construct() {
        View::share ( 'title', 'Inquiry' );
        View::share ( 'elementActive', 'inquiry' );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.inquiry.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $inquiries_source = InquiriesSource::withoutTrashed()->get();
        return view('admin.inquiry.create',compact('inquiries_source'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InquiryCreateRequest $request)
    {   
        //
        $request_data = $request->all();
        try{
            Inquiry::create([
                'inquiries_source_id'=>$request_data['inquiry_source'],
                'customer_id'=>$request_data['customer'],
                'product_id'=>$request_data['product'],
            ]);
        }catch(Exception $e){
            return redirect(route('inquiry.create'))->with('exception', $e->getMessage());;
        }
        return view('admin.inquiry.index');
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
        $inquiry = Inquiry::findOrFail($id);
        $inquiries_source = InquiriesSource::withoutTrashed()->get();
        return view('admin.inquiry.create',compact('inquiry','inquiries_source'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InquiryUpdateRequest $request, $id)
    {
        $request_data = $request->all();
        $update_data = [
            'inquiries_source_id'=>$request_data['inquiry_source'],
            'customer_id'=>$request_data['customer'],
            'product_id'=>$request_data['product'],
        ];
        if($request_data['dealer']){
            $update_data['dealer_id'] = $request_data['dealer'];
        }
        $user = Inquiry::where('id',$id)->update($update_data);
        return redirect(route('inquiry.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        if(!empty($id)){
            $user = Inquiry::findOrFail($id);
            $user->delete();
        }
    }

    public function listAjaxData(Request $request){
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'inquiries.deleted_at is NULL';
        $where .= (!empty($search)) ? " AND (c.name like '$search%' OR p.name like '$search%' OR source like '%$search%' OR (d.name is NOT NULL AND d.name like '$search%'))":'';
        $inquiries = DB::table('inquiries')
        ->selectRaw('inquiries.id,source,c.name as customer,d.name as dealer,p.name as product')
        ->join('inquiries_source','inquiries.inquiries_source_id','=','inquiries_source.id')
        ->join('users AS c','inquiries.customer_id','=','c.id')
        ->join('products AS p','inquiries.product_id','=','p.id')
        ->join('users AS d','inquiries.dealer_id','=','d.id','left')
        ->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();
        $total_inquiries = Inquiry::where('deleted_at',NULL)->count();
        $data = [];
        foreach($inquiries as $inquiry){
            $dealer = (!empty($inquiry->dealer))?$inquiry->dealer:'N/A';
            $action_html = "<span><a href=".route('inquiry.edit',$inquiry->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$inquiry->id class='btn btn-danger' onClick=deleteUser($inquiry->id)>Delete</button></span>";
            $data[] = [$inquiry->id,$inquiry->source,$inquiry->customer,$inquiry->product,$dealer,$action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_inquiries;
        $return_array['recordsFiltered'] = $total_inquiries;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }
}
