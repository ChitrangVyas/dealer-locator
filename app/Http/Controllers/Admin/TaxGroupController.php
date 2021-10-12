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
            $request_data['is_default'] = $request_data['is_default'];
            $is_tax_group = $tax_group->update($request_data);
            if(!empty($is_tax_group) && !empty($request->input('tax_id'))){
                $tax_group_data = $request->only(['tax_id','percentage']);
                if(!empty($tax_group->tax()->count())){
                    $tax_group->tax()->detach();
                }
                foreach ($tax_group_data['tax_id'] as $tax_id){
                    if(!empty($tax_group_data['percentage'][$tax_id])){
                        $update_data = [$tax_group->id=>$tax_id];
                        $is_updated = $tax_group->tax()->attach($update_data,["percentage"=>$tax_group_data['percentage'][$tax_id]]);
                    }
                }
            }
        }catch(Exception $e){
            return redirect(route('tax-group.create'))->with('exception', $e->getMessage());
        }
        return redirect(route('tax-group.index'));
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
        ],[
            'name.required'=>"Name is required.",
        ])->validate();
        try{
            $request_data = $request->only(['name','status','is_default']);
            $status = (!empty($request_data['status']))?1:0;
            $is_default = (!empty($request_data['is_default']))?1:0;
            $request_data['status'] = $status;
            $request_data['is_default'] = $is_default;
            $count=0;
            if(empty($is_default)){
               $count = TaxGroup::where('is_default',1)->where('id',$taxGroup->id)->count(); 
            }
            if(!empty($count)){
                return redirect()->back()->with('exception', 'Please make at least one group as default.');
            }
            $is_tax_group = $taxGroup->update($request_data);
            $this->updateDefault($taxGroup->id);
            $tax_group_data = $request->only(['tax_id','percentage']);
            if(!empty($is_tax_group) && !empty($tax_group_data['tax_id'])){
                if(!empty($taxGroup->tax()->count())){
                    $taxGroup->tax()->detach();
                }
                foreach ($tax_group_data['tax_id'] as $tax_id){
                    if(!empty($tax_group_data['percentage'][$tax_id])){
                        $update_data = [$taxGroup->id=>$tax_id];
                        $is_updated = $taxGroup->tax()->attach($update_data,["percentage"=>$tax_group_data['percentage'][$tax_id]]);
                    }
                }
            }
        }catch(Exception $e){
            return redirect(route('tax-group.create'))->with('exception', $e->getMessage());
        }
        return redirect(route('tax-group.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaxGroup  $taxGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxGroup $taxGroup)
    {        
        if((auth()->check()) && empty(auth()->user()->users_group_id)){
            try{
                $taxGroup->delete();
            }catch(Exception $e){
                return response()->json(['error'=>$e->getMessage()]);
            }
            return response(json_encode($return_array = ['data'=>'Tax group is successfully deleted']), 200)
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

        $total_tax_group = TaxGroup::all()->count();
        $data = [];
        foreach($taxes as $tax){            
            $action_html = '';
            $status = (!empty($tax->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status", data-url = '.route('tax-group.update',$tax->id).' class="form-control change-status" data-id="'.$tax->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $action_html = "<span><a href=".route('tax-group.edit',$tax->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$tax->id class='btn btn-danger' onClick=deleteItem($tax->id)>Delete</button></span>";
            $data[] = [$tax->id,$tax->name,$status_html,$action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_tax_group;
        $return_array['recordsFiltered'] = $total_tax_group;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }

    public function updateDefault($id){
        return TaxGroup::where('id','!=',$id)->update(['is_default'=>false]);
    }
}
