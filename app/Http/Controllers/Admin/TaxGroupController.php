<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tax;
use App\Models\TaxGroup;
use Darryldecode\Cart\Validators\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;

class TaxGroupController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        View::share('title', 'Tax Group');
        View::share('elementActive', 'tax-group');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.tax-group.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $taxes = Tax::all();
        return view('admin.tax-group.create',['taxes'=>$taxes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required',
        ],[
            'name.required'=>"Name is required.",
        ])->validate();
        try{
            $request_data = $request->only(['name']);
            $request_data['is_default'] = (!empty($request->input('is_default')))?1:0;
            $tax_group = TaxGroup::create($request_data);
            if(!empty($tax_group) && !empty($request->input('tax_id'))){
                $tax_goup_items = $request->only(['tax_id','percentage']);
                $tax_goup_items['tax_group_id'] = $tax_group->id;
                $tax_group->tax()->sync($tax_goup_items);
            }
        }catch(Exception $e){
            return redirect(route('product.create'))->with('exception', $e->getMessage());
        }
        return redirect(route('product.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaxGroup  $taxGroup
     * @return \Illuminate\Http\Response
     */
    public function show(TaxGroup $taxGroup)
    {
        //
    }

    /**x/1/edit
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TaxGroup  $taxGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(TaxGroup $taxGroup)
    {
        $taxes = Tax::all();
        return view('admin.tax-group.create',['taxGroup'=>$taxGroup,'taxes'=>$taxes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaxGroup  $taxGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaxGroup $taxGroup)
    {
        Validator::make($request->all(), [
            'name' => 'required',
            'tax_id' => 'required',
            'percentage'=>'required',
        ],[
            'name.required'=>"Name is required.",
            'tax_id.required'=>"Please select the Tax.",
            'percentage.required'=>"Percentage is required.",
        ])->validate();
        try{
            $request_data = $request->only(['name','status','is_deafult']);
            $status = (!empty($request_data['status']))?1:0;
            $is_deafult = (!empty($request_data['is_deafult']))?1:0;
            $request_data['status'] = $status;
            $request_data['is_deafult'] = $is_deafult;
            $is_tax_group = $taxGroup->update($request_data);
            if(!empty($is_tax_group) && !empty($request['tax_id'])){
                $tax_goup_items = $request->only(['tax_id','percentage']);
                $tax_goup_items['tax_group_id'] = $taxGroup->id;
                $taxGroup->tax()->sync($tax_goup_items);
            }
        }catch(Exception $e){
            return redirect(route('product.create'))->with('exception', $e->getMessage());
        }
        return redirect(route('product.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaxGroup  $taxGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxGroup $taxGroup)
    {
        //
        if((auth()->check()) && empty(auth()->user()->users_group_id)){
            $taxGroup->delete();
            return response(json_encode($return_array = ['data'=>'Product is successfully deleted']), 200)
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
        $query = DB::table('tax_groups')
                ->selectRaw('id,name,status');
        if(!empty($where)){
            $query->whereRaw($where);
        }
        $query->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir']);
        $taxes = $query->get();

        $total_product = TaxGroup::all()->count();
        $data = [];
        foreach($taxes as $tax){            
            $action_html = '';
            $status = (!empty($tax->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status", data-url = '.route('tax-group.update',$tax->id).' class="form-control change-status" data-id="'.$tax->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $action_html = "<span><a href=".route('tax-group.edit',$tax->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$tax->id class='btn btn-danger' onClick=deleteUser($tax->id)>Delete</button></span>";
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
