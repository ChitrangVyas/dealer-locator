<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCreateRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use View;

class ProductController extends Controller
{
    
    public function __construct()
    {
        View::share('title', 'Product');
        View::share('elementActive', 'product');
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
        $brands =  Brand::withoutTrashed()->get();
        $categories = Category::withoutTrashed()->get();
        return view('admin.product.create',compact('brands','categories'));
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
            $fileName = time().'_'.$request->image->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('uploads/products', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
        }
        try{
            $brand = Product::create([
                'name' => $request_data['name'],
                'description' => $request_data['description'],
                'brand_id' => $request_data['brand_id'],
                'inventory' => $request['inventory'],
                // 'category' => $request_data['category'],
                'image' => $file_path
            ]);
        }catch(Exception $e){
            print_r([
                'name' => $request_data['name'],
                'description' => $request_data['description'],
                'brand_id' => $request_data['brand_id'],
                'inventory' => $request['inventory'],
                // 'category' => $request_data['category'],
                'image' => $file_path
            ]);

            echo $e; exit;
            return redirect(route('product.create'));
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
        //
        $brands = Brand::withoutTrashed()->get();        
        $product = Product::findOrFail($id);
        return view('admin.product.create',array('brands'=>$brands,'product'=>$product));
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
            'inventory' => $request['inventory'],           
        ];
        
        if($request->file()) {
            $filename = str_replace('/storage/uploads/products/', '', $product->image);
            unlink(storage_path('app/public/uploads/products/'.$filename));
            $fileName = time().'_'.$request->image->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('uploads/products', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
            $upload_data['image']  = $file_path;
        }

        $brand = $product->update($upload_data);
    
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
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'products.deleted_at is NULL';
        $where .= (!empty($search)) ? " AND (products.name like '$search%' OR brands.name like '$search%')":'';
        $products = DB::table('products')
        ->leftJoin('brands','products.brand_id','=','brands.id')
        ->selectRaw('products.id,products.name,brands.name as brand_name,products.image,inventory')
        ->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();
        $total_product = Product::where('deleted_at','=',Null)->count();
        $data = [];
        foreach($products as $product){            
            $product_image_html = (!empty($product->image))?"<img class='img logo' src=".url($product->image)." width=50 />":'Image not found';
            $action_html = "<span><a href=".route('product.edit',$product->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$product->id class='btn btn-danger' onClick=deleteUser($product->id)>Delete</button></span>";
            $data[] = [$product->id,$product->name,$product->brand_name,$product_image_html,$product->inventory,$action_html];
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
