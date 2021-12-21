<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCreateRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\TaxGroup;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use JeroenNoten\LaravelAdminLte\AdminLte;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\Types\Null_;
use View;

class ProductController extends MainController
{
    private $product;
    public function __construct(Product $product,AdminLte $adminLte)
    {
        parent::__construct();
        $this->product = $product;
        View::share('title', 'Product');
        View::share('elementActive', 'product');
        View::share('adminlte',$adminLte);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $brands =  Brand::withoutTrashed()->where('status',1)->get();
        $categories = Category::withoutTrashed()->where('status',1)->get();
        $tax_groups = TaxGroup::where('status',1)->get();
        return view('admin.product.create',compact('brands','categories','tax_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductCreateRequest $request)
    {
        //
        $request_data = $request->all();
        $file_path = '';
        if($request->file()) {
            $fileName = time().'_'. Str::snake($request->image->getClientOriginalName());
            $filePath = $request->file('image')->storeAs('uploads/products', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
        }
        try{
            $product = Product::create([
                'name' => $request_data['name'],
                'description' => $request_data['description'],
                'brand_id' => $request_data['brand_id'],
                // 'inventory' => $request['inventory'],
                'price' => $request['price'],
                // 'category' => $request_data['category'],
                'image' => $file_path,
                'status' => (!empty($request_data['status']))?1:0,
                'tax_group_id'=> $request_data['tax_group_id'],
                'hsn_sac'=> $request_data['hsn_sac'],
                'installation_place'=> $request_data['installation_place'],
                'max_pipe_size_and_water_flow'=> $request_data['max_pipe_size_and_water_flow'],
            ]);
            if(!empty($product) && !empty($request_data['inventory'])){
                $inventory = Inventory::create([
                    'user_id' => NULL,
                    'product_id' => $product->id,
                    'qty' => $request_data['inventory'],
                    'low_stock'=> $request_data['low_stock']
                ]);
            }
            if((!empty($product)) && (!empty($request_data['categories']))){
                $categories = Category::find($request_data['categories']);
                $product->categories()->sync($categories);
            }
        }catch(Exception $e){
            return redirect(route('product.create'))->with('exception', $e->getMessage());
        }

        return redirect(route('product.index'));
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
        $brands = Brand::withoutTrashed()->where('status',1)->get();        
        $product = Product::findOrFail($id);
        $product_category = $product->categories()->pluck('category_id')->toArray();
        $categories = Category::withoutTrashed()->where('status',1)->get();
        $tax_groups = TaxGroup::where('status',1)->get();
        return view('admin.product.create',array('brands'=>$brands,'product'=>$product,'product_category'=>$product_category,'categories'=>$categories,'tax_groups'=>$tax_groups));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        //
        $product = Product::findOrFail($id);
        $request_data = $request->all();
        $file_path = '';
        $upload_data = [
            'name' => $request_data['name'],
            'description' => $request_data['description'],
            'brand_id' => $request_data['brand_id'],
            // 'inventory' => $request['inventory'],           
            'price' => $request['price'],
            'tax_group_id'=> $request_data['tax_group_id'],
            'hsn_sac'=> $request_data['hsn_sac'],
            'installation_place'=> $request_data['installation_place'],
            'max_pipe_size_and_water_flow'=> $request_data['max_pipe_size_and_water_flow'],    
        ];
        // echo "<pre>";
        // print_r($upload_data);
        // die;
        $upload_data['status'] = (!empty($request_data['status']))?1:0;
        try{
            if($request->file()) {
                $filename = str_replace('/storage/uploads/products/', '', $product->image);
                if(!empty($product->image) && file_exists(storage_path('app/public/uploads/products/'.$filename))){
                    unlink(storage_path('app/public/uploads/products/'.$filename));
                }
                $fileName = time().'_'. Str::snake($request->image->getClientOriginalName());
                $filePath = $request->file('image')->storeAs('uploads/products', $fileName, 'public');
                $file_path = '/storage/' . $filePath;
                $upload_data['image']  = $file_path;
            }

            $is_updated = $product->update($upload_data);
            if($is_updated && !empty($request_data['inventory'])){
                $inventory = Inventory::where('user_id',NULL)->where('product_id',$id)->first();
                if(!empty($inventory)){
                $inventory->update(['qty'=>$request['inventory'],'low_stock'=>$request_data['low_stock']]);
                }else{
                        Inventory::create([
                            'user_id' => NULL,
                            'product_id' => $product->id,
                            'qty' => $request_data['inventory'],
                            'low_stock'=> $request_data['low_stock']
                        ]);
                }
            }
            if($is_updated && !empty($request_data['categories'])){
                $categories = Category::find($request_data['categories']);
                $product->categories()->sync($categories);
            }
        }catch(Exception $e){
            return redirect(route('product.create'))->with('exception', $e->getMessage());
        }
    
        return redirect(route('product.index'));
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
            $product = Product::findOrFail($id);
            $product->delete();
            return response(json_encode($return_array = ['data'=>'Product is successfully deleted']), 200)
        ->header('Content-Type', 'text/plain');
        }
    }
    /**
     * listAjaxData
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function listAjaxData(Request $request){
        $user  = auth()->user();
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'products.deleted_at is NULL';
        $where .= (!empty($search)) ? " AND (products.name like '$search%' OR brands.name like '$search%')":'';
        $query = DB::table('products');
        $query->leftJoin('brands','products.brand_id','=','brands.id')
        ->leftJoin('inventory',function($query) use($user){
            $query->on('products.id','=','inventory.product_id');
            if((empty($user->users_group_id)) || ($user->users_group_id == 1)){
                $query->where('user_id',NULL);
            }else{
                $query->where('user_id',$user->id);
            }
        })
        ->selectRaw('products.id,products.name,brands.name as brand_name,products.image,inventory.qty AS inventory,products.status');
        $query->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir']);
        $products = $query->get();

        $total_product = Product::where('deleted_at','=',Null)->count();
        $data = [];
        foreach($products as $product){            
            $product_image_html = (!empty($product->image))?"<img class='img logo' src=".url($product->image)." width=50 />":'Image not found';
            $action_html = '';
            $inventory_html = '';
            $inventory = (empty($product->inventory))?'0':$product->inventory;
            $status = (!empty($product->status)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status" class="form-control" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.' disabled>';
            if((empty(auth()->user()->users_group_id)) || (auth()->user()->users_group_id == 1)){
                $action_html = "<span><a href=".route('product.edit',$product->id)." class='btn btn-primary'>Edit</a></span>";
                $action_html .= "<span class='ml-3'><button type=submit data-user=$product->id class='btn btn-danger' onClick=deleteUser($product->id)>Delete</button></span>";
                $status_html = '<input type="checkbox" name="status" id="status" class="form-control change-status" data-id="'.$product->id.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
                $inventory_html = "<span>$inventory</span>";
            }else{
                $inventory_html = "<span class='ml-3'><button data-product=$product->id class='btn btn-link add-stock'>$inventory</button></span>";
                // $inventory_html = "<button class=btn btn-default add-stock data-product=".$product->id.">".(empty($product->inventory))?'II':$product->inventory."</button>";
            }
            $data[] = [$product->id,$product->name,$product->brand_name,$product_image_html,$inventory_html,$status_html,$action_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_product;
        $return_array['recordsFiltered'] = $total_product;
        $return_array['data'] = $data;
        return response(json_encode($return_array), 200)
        ->header('Content-Type', 'text/plain');
    }
    /**
     * listAjaxData
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function reportListAjaxData(Request $request){
        $user  = auth()->user();
        $start =  $request->input('start','');
        $length=  $request->input('length','');
        $search = $request->input('search.value','');
        $from_date = $request->input('from_date','');
        $to_date = $request->input('to_date','');
        $where = 'products.deleted_at is NULL';
        $where .= (!empty($search)) ? " AND (products.name like '$search%' OR brands.name like '$search%')":'';
        $query = DB::table('products');
        $query->leftJoin('brands','products.brand_id','=','brands.id')
        ->leftJoin('inventory',function($query) use($user){
            $query->on('products.id','=','inventory.product_id');
            if((empty($user->users_group_id)) || ($user->users_group_id == 1)){
                $query->where('user_id',NULL);
            }else{
                $query->where('user_id',$user->id);
            }
        })
        ->selectRaw('products.id,products.name,brands.name as brand_name,products.image,inventory.qty AS inventory,products.status');
        $total_product = count($query->whereRaw($where)->get());
        if(!empty($request->input('export',''))){
            $inventories = $query->whereRaw($where)->get();
            return Excel::download(new ProductExport($inventories),'Inventory.xlsx');
        }else{
            $query->whereRaw($where)
            ->offset($start)
            ->limit($length)
            ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir']);
            $products = $query->get();

            $data = [];
            foreach($products as $product){            
                $product_image_html = (!empty($product->image))?"<img class='img logo' src=".url($product->image)." width=50 />":'Image not found';
                $action_html = '';
                $inventory_html = '';
                $inventory = (empty($product->inventory))?'0':$product->inventory;
                $status = (!empty($product->status)) ? 'Enable': 'Disabled';
                $status_html = $status;
                $inventory_html = "<span>$inventory</span>";
                $data[] = [$product->id,$product->name,$product->brand_name,$product_image_html,$inventory_html,$status_html];
            }
            $return_array = array();
            $return_array['draw'] = $request['draw'];
            $return_array['recordsTotal'] = $total_product;
            $return_array['recordsFiltered'] = $total_product;
            $return_array['data'] = $data;
            return response()->json($return_array);
        }
    }

    /**
     * lowStockReportListAjaxData
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function lowStockReportListAjaxData(Request $request){
        //$this->exporter->download(new UsersExport, 'users.xlsx');
        // 
        $user  = auth()->user();
        $start =  $request->input('start','');
        $length=  $request->input('length','');
        $search = $request->input('search.value','');
        $where = 'products.deleted_at is NULL';
        $where .= (!empty($search)) ? " AND (products.name like '$search%' OR brands.name like '$search%')":'';
        $query = DB::table('products');
        $query->leftJoin('brands','products.brand_id','=','brands.id')
        ->leftJoin('inventory',function($query) use($user){
            $query->on('products.id','=','inventory.product_id');
            if((empty($user->users_group_id)) || ($user->users_group_id == 1)){
                $query->where('user_id',NULL);
            }else{
                $query->where('user_id',$user->id);
            }
        })
        ->selectRaw('products.id,products.name,brands.name as brand_name,products.image,inventory.qty AS inventory,products.status')
        ->whereColumn('inventory.qty','<','low_stock');
        $total_product = $query->whereRaw($where)->count();
        if(!empty($request->input('export',''))){
            $lowStockProducts = $query->whereRaw($where)->get();
            return Excel::download(new ProductExport($lowStockProducts),'LowStock.xlsx');
        }else{
        $query->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir']);
        $products = $query->get();

        $data = [];
        foreach($products as $product){            
            $product_image_html = (!empty($product->image))?"<img class='img logo' src=".url($product->image)." width=50 />":'Image not found';
            $action_html = '';
            $inventory_html = '';
            $inventory = (empty($product->inventory))?'0':$product->inventory;
            $status = (!empty($product->status)) ? 'Enable': 'Disabled';
            $status_html = $status;
            $inventory_html = "<span>$inventory</span>";
            $data[] = [$product->id,$product->name,$product->brand_name,$product_image_html,$inventory_html,$status_html];
        }
        $return_array = array();
        $return_array['draw'] = $request['draw'];
        $return_array['recordsTotal'] = $total_product;
        $return_array['recordsFiltered'] = $total_product;
        $return_array['data'] = $data;
        return response()->json($return_array);
    }
    }
    public function getProductInventory(Request $request){
        Validator::make($request->all()['data'], [
            'product_id' => 'required',
        ])->validate();
        if($request->ajax()){
            $product = Product::findOrFail($request->input('data')['product_id']);
            $json['modal_title'] = "Product";
            $json['modal_content'] = view('admin.product.stock',['product'=>$product])->render();
            return response()->json(['status' => 'successful','code'=>1, 'data'=>$json, 'message'=>'']);
        }else{
            return response()->json(['status' => 'error','code'=>0,'message'=>'Bad Method Call.']);
        }
    }

    public function updateProductInventory(Request $request){
        Validator::make($request->all(), [
            'product_id' => 'required',
            "inventory" => 'required|numeric',
            "low_stock" => 'required|numeric'
        ])->validate(); 

        if($request->ajax()){
            $update_data['qty'] = $request->input('inventory');
            $update_data['low_stock'] = $request->input('low_stock');
            try{
                $inventory = Inventory::where('product_id',$request->input('product_id'))->where('user_id',auth()->user()->id);
                if(!empty($inventory->get()->toArray())){
                    $is_inventory_updated = $inventory->update($update_data);
                }else{
                    $is_inventory_updated = Inventory::create(['product_id'=>$request->input('product_id'),'user_id'=>auth()->user()->id,'qty'=>$request->input('inventory')]);
                }
            }catch(Exception $e){
                return response()->json(['status' => 'error','code'=>0,'message'=>$e->getMessage()]);
            }
            if(!empty($is_inventory_updated)){
                return response()->json(['status' => 'successful', 'data'=>[],'code'=>1,'message'=>'Inventory has been successfully Updated.']);
            }
        }else{
            return response()->json(['status' => 'error','code'=>0,'message'=>'Bad Method Call.']);
        }
    }

    public function productReport(){
        View::share('title', 'Inventory Report');
        View::share('elementActive', 'inventory_report');
        return view('admin.report.inventory');
    }

    public function lowStockReport(){
        View::share('title', 'Low Stock Report');
        View::share('elementActive', 'low_stock_report');
        return view('admin.report.low-stock');
    }
}
