<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BrandCreateRequest;
use App\Http\Requests\Admin\BrandUpdateRequest;
use App\Models\Brand;
use App\Models\Manufacturer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;
use View;

class BrandController extends MainController
{
    public $manufacturer = [];
    public function __construct() {
        View::share ( 'title', 'Brand' );
        View::share ( 'elementActive', 'brand' );
    }
    public function getManufacturer(){
        $this->manufacturer = Manufacturer::all();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.brand.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $this->getManufacturer();
        return view('admin.brand.create',array('manufacturers'=>$this->manufacturer));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandCreateRequest $request)
    {   
        //
        $request_data = $request->all();
        $file_path = '';
        if($request->file()) {
            $fileName = time().'_'.$request->logo->getClientOriginalName();
            $filePath = $request->file('logo')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
        }
        try{
            $brand = Brand::create([
                'name' => $request_data['name'],
                'description' => $request_data['description'],
                'short_description' => $request_data['short_description'],
                'manufacturer_id' => $request_data['manufacturer_id'],
                'logo' => $file_path
            ]);
        }catch(Exception $e){
            print_r([
                'name' => $request_data['name'],
                'description' => $request_data['description'],
                'short_description' => $request_data['short_description'],
                'manufacturer_id' => $request_data['manufacturer_id'],
                'logo' => $file_path
            ]);

            echo $e; exit;
            return redirect(route('brand.create'));
        }

        return redirect(route('brand.index'));
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
        $this->getManufacturer();
        $brand = Brand::findOrFail($id);        
        return view('admin.brand.create',array('brand'=>$brand,'manufacturers'=>$this->manufacturer));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BrandUpdateRequest $request, $id)
    {
        //
        $request_data = $request->all();
        $file_path = '';
        $upload_data = [
            'name' => $request_data['name'],
            'description' => $request_data['description'],
            'short_description' => $request_data['short_description'],
            'manufacturer_id' => $request_data['manufacturer_id'],            
        ];
        
        if($request->file()) {
            $fileName = time().'_'.$request->logo->getClientOriginalName();
            $filePath = $request->file('logo')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
            $upload_data['logo']  = $file_path;
        }

        $brand = Brand::where('id',$id)->update($upload_data);
    
        return redirect(route('brand.index'));
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
            $brand = Brand::findOrFail($id);
            $brand->delete();
            return response(json_encode($return_array = ['data'=>'brand is successfully deleted']), 200)
        ->header('Content-Type', 'text/plain');
        }
    }

    public function listAjaxData(Request $request){
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = '';
        $where .= (!empty($search)) ? " AND (name like '$search%' OR email like '$search%')":'';
        $brands = DB::table('brands')
        ->leftJoin('manufacturers','brands.manufacturer_id','=','manufacturers.id')
        ->selectRaw('brands.id,brands.name,manufacturers.name as mname,brands.logo')
        ->where('brands.deleted_at', NULL)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();
        $total_users = Brand::where('deleted_at','=',Null)->count();
        $data = [];
        foreach($brands as $brand){            
            $brand_logo_html = (!empty($brand->logo))?"<img class='img logo' src=".url($brand->logo)." width=50 />":'Image not found';
            $action_html = "<span><a href=".route('brand.edit',$brand->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$brand->id class='btn btn-danger' onClick=deleteUser($brand->id)>Delete</button></span>";
            $data[] = [$brand->id,$brand->name,$brand->mname,$brand_logo_html,$action_html];
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
