<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers;
use App\Models\Tax;
use Darryldecode\Cart\Validators\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;

class TaxController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        View::share('title', 'Tax');
        View::share('elementActive', 'tax');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.tax.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tax.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        Validator::make($request->all(), [
            'name' => 'required',
        ],['name.required'=>"Name is required."])->validate();
        try{
            $request_data =  $request->except(['method','_token','id']);
            $request_data['status'] = (!empty($request_data['status']))?1:0;
            $tax = Tax::create($request_data);
        }catch(Exception $e){
            return redirect(route('tax.create'))->with('exception', $e->getMessage());
        }
        return redirect(route('tax.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function show(Tax $tax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function edit(Tax $tax)
    {
        return view('admin.tax.create',['tax'=>$tax]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tax $tax)
    {
        try{
            $request_data =  $request->except(['method','_token','id']);
            $status = (!empty($request_data['status']))?1:0;
            $request_data['status'] = $status;
            $is_tax = $tax->update($request_data);
        }catch(Exception $e){
            return redirect(route('tax.create'))->with('exception', $e->getMessage());
        }

        return redirect(route('tax.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tax $tax)
    {
        if((auth()->check()) && empty(auth()->user()->users_group_id)){
            $tax->delete();
            return response(json_encode($return_array = ['data'=>'Tax is successfully deleted']), 200)
        ->header('Content-Type', 'text/plain');
        }
        return response()->json([],401);
    }

     /**
     * listAjaxData
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function listAjaxData(Request $request){
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = '';
        $where .= (!empty($search)) ? " (name like '$search%')":'';
        $query = DB::table('taxes')
                ->selectRaw('id,name,status');
        if(!empty($where)){
            $query->whereRaw($where);
        }
        $query->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir']);
        $taxes = $query->get();

        $total_product = Tax::all()->count();
        $data = [];
        foreach($taxes as $tax){            
            $action_html = '';
            $tax_group_count = Helpers::getCountData('tax_group_items',['tax_id'=>$tax->id]);
            $status = (!empty($tax->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status", data-url = '.route('tax.update',$tax->id).' class="form-control change-status" data-id="'.$tax->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $action_html = "<span><a href=".route('tax.edit',$tax->id)." class='btn btn-primary'>Edit</a></span>";
            if(empty($tax_group_count)){
                $action_html .= "<span class='ml-3'><button type=submit data-user=$tax->id class='btn btn-danger' onClick=deleteUser($tax->id)>Delete</button></span>";
            }else{
                $action_html .= "<span class='ml-3'><span class='badge badge-info' title='Product'>$tax_group_count</span></span>";
            }
            $data[] = [$tax->id,$tax->name,$status_html,$action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_product;
        $return_array['recordsFiltered'] = $total_product;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }
}
