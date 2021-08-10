<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\InquiriesCreateRequest;
use App\Http\Requests\Admin\InquiriesUpdateRequest;
use App\Models\InquiriesSource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;
use View;

class InquiriesSourceController extends MainController
{
    public function __construct() {
        View::share ( 'title', 'Inquiry Source' );
        View::share ( 'elementActive', 'inquiriesSource' );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.inquiries-source.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.inquiries-source.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InquiriesCreateRequest $request)
    {
        //
        $request_data = $request->all();
        try{
            $inquiry = InquiriesSource::create([
                'source' => $request_data['source'],
            ]);
        }catch(Exception $e){
            print_r([
                'source' => $request_data['source'],
            ]);

            echo $e; exit;
            return redirect(route('inquiries-source.create'));
        }
        return redirect(route('inquiries-source.index'));
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
        $inquiries_source = InquiriesSource::findOrFail($id);  
        return view('admin.inquiries-source.create',array('inquiries_source'=>$inquiries_source));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InquiriesUpdateRequest $request, $id)
    {
        //
        $request_data = $request->all();
        $file_path = '';
        $upload_data = [
            'source' => $request_data['source']                   
        ];
        $inquiry = InquiriesSource::where('id',$id)->update($upload_data);
    
        return redirect(route('inquiries-source.index'));
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
            $inquiry = InquiriesSource::findOrFail($id);
            $inquiry->delete();
            return response(json_encode($return_array = ['data'=>'inquiry is successfully deleted']), 200)
        ->header('Content-Type', 'text/plain');
        }
    }
    public function listAjaxData(Request $request){
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = '';
        $where .= (!empty($search)) ? " AND ( source like '$search%')":'';
        $inquiries = DB::table('inquiries_source')
        ->selectRaw('id,source')
        ->where('deleted_at', NULL)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();
        $total_users = InquiriesSource::where('deleted_at','=',Null)->count();
        $data = [];
        foreach($inquiries as $inquiry){            
            $action_html = "<span><a href=".route('inquiries-source.edit',$inquiry->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$inquiry->id class='btn btn-danger' onClick=deleteUser($inquiry->id)>Delete</button></span>";
            $data[] = [$inquiry->id,$inquiry->source,$action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_users;
        $return_array['recordsFiltered'] = $total_users;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }
}
