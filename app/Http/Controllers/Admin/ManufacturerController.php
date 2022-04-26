<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers;
use App\Http\Requests\Admin\ManufacturerCreateRequest;
use App\Http\Requests\Admin\ManufacturerUpdateRequest;
use App\Models\Manufacturer;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;
use Illuminate\Support\Str;
use View;

class ManufacturerController extends MainController
{
    public function __construct() {
        parent::__construct();
        View::share ( 'title', 'Manufacturer' );
        View::share ( 'elementActive', 'manufacturer' );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('manufacturer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('manufacturer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ManufacturerCreateRequest $request)
    {   
        //
        $request_data = $request->all();
        $request_data['status'] = (!empty($request_data['status']))?1:0;
        $file_path = '';
        if($request->file()) {
            $fileName = time().'_'. Str::snake($request->logo->getClientOriginalName());
            $filePath = $request->file('logo')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
            $request_data['logo'] = $file_path;
        }
        try{
            $manufacturer = Manufacturer::create($request_data);
        }catch(Exception $e){
            return redirect(route('manufacturer.create'))->with('exception', $e->getMessage());            
        }

        return redirect(route('manufacturer.index'));
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
        $manufacturer = Manufacturer::findOrFail($id);
        return view('manufacturer.create',compact('manufacturer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ManufacturerUpdateRequest $request, $id)
    {
        //
        $request_data = $request->except(['_method','_token','id']);
        $request_data['status'] = (!empty($request_data['status']))?1:0;
        $file_path = '';   
        
        if($request->file()) {
            $fileName = time().'_'. Str::snake($request->logo->getClientOriginalName());
            $filePath = $request->file('logo')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
            $request_data['logo']  = $file_path;
        }

        $manufacturer = Manufacturer::where('id',$id)->update($request_data);
    
        return redirect(route('manufacturer.index'));
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
            $manufacturer = Manufacturer::findOrFail($id);
            $manufacturer->delete();
            return response(json_encode($return_array = ['data'=>'manufacturer is successfully deleted']), 200)
        ->header('Content-Type', 'text/plain');
        }
    }

    public function listAjaxData(Request $request){
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'deleted_at IS NULL';
        $where .= (!empty($search)) ? " AND (name like '%$search%' OR email like '%$search%' OR phone like '%$search%')":'';
        $manufacturers = DB::table('manufacturers')
        ->selectRaw('id,name,email,mobile,logo,status')
        ->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();
        $total_users = Manufacturer::where('deleted_at','=',Null)->count();
        $data = [];
        foreach($manufacturers as $manufacturer){
            $indiVidual=  Manufacturer::where('id','=',$manufacturer->id)->first();
            $brand_count = Helpers::getCountData('brands',['manufacturer_id'=>$manufacturer->id]);
            $manufacturer_logo_html = (!empty($manufacturer->logo))?"<img class='img logo' src=".url($manufacturer->logo)." width=50 />":'Image not found';
            $status = (!empty($manufacturer->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status" class="form-control change-status" data-id="'.$manufacturer->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $action_html = "<span><a href=".route('manufacturer.edit',$manufacturer->id)." class='btn btn-primary'><i class='fas fa-pen' title='Edit'></i></a></span>";
            if(empty($brand_count)){
                $action_html .= "<span class='ml-3'><button type=submit data-user=$manufacturer->id class='btn btn-danger' onClick=deleteUser($manufacturer->id)><i class='fas fa-trash' title='Delete'></i></button></span>";
            }else{
                $action_html .= "<span class='ml-3'><span class='badge badge-info' title='Brand'>$brand_count</span></span>";
            }
            $data[] = [$manufacturer->id,$manufacturer->name,$manufacturer->email,$indiVidual->getMobileWithCountryCode(),$manufacturer_logo_html,$status_html,$action_html];
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
