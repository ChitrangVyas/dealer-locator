<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers;
use App\Http\Requests\Admin\CategoryCreateRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;
use Illuminate\Support\Str;
use View;

class CategoryController extends MainController
{
    public function __construct() {
        parent::__construct();
        View::share ( 'title', 'Category' );
        View::share ( 'elementActive', 'category' );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $parent_category =  Category::where('status',1)->where('parent_category','=','0')->get();  
        return view('admin.category.create',array('parent_category'=>$parent_category));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryCreateRequest $request)
    {
        //
        $request_data = $request->all();
        $file_path = '';
        if($request->file()) {
            $fileName = time().'_'. Str::snake($request->image->getClientOriginalName());
            $filePath = $request->file('image')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
        }
        try{
            $category = Category::create([
                'name' => $request_data['name'],
                'parent_category' => $request_data['parent_category'],                
                'image' => $file_path,
                'description'=> $request_data['description'],
                'status' => (!empty($request_data['status']))?1:0,
            ]);
        }catch(Exception $e){
            return redirect(route('category.create'))->with('exception', $e->getMessage());
        }
        return redirect(route('category.index'));
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
        $category = Category::findOrFail($id);
        $parent_category =  Category::where('status',1)->where('parent_category','=','0')->get();       
        return view('admin.category.create',array('category'=>$category,'parent_category'=>$parent_category));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateRequest $request, $id)
    {
        //
        $request_data = $request->all();
        $file_path = '';
        $upload_data = [
            'name' => $request_data['name'],
            'parent_category' => $request_data['parent_category'],
            'description'=> $request_data['description'],                
        ];
        $upload_data['status'] = (!empty($request_data['status']))?1:0;
        
        if($request->file()) {
            $fileName = time().'_'. Str::snake($request->image->getClientOriginalName());
            $filePath = $request->file('image')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
            $upload_data['image']  = $file_path;
        }

        $category = Category::where('id',$id)->update($upload_data);
    
        return redirect(route('category.index'));
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
            $category = Category::findOrFail($id);
            $category->delete();
            return response(json_encode($return_array = ['data'=>'category is successfully deleted']), 200)
        ->header('Content-Type', 'text/plain');
        }
    }
    public function listAjaxData(Request $request){
        $start =  $request['start'];
        $length=  $request['length'];
        $search = $request['search']['value'];
        $where = 'categories.deleted_at is NULL';
        $where .= (!empty($search)) ? " AND (categories.name like '%$search%' OR parent_category.name like '%$search%')":'';
        $categories = DB::table('categories')
        ->selectRaw('categories.id as cid,categories.name as cname,categories.parent_category as cp,categories.image as cimage,categories.status as cstatus')
        ->leftJoin('categories as parent_category','parent_category.id','=','categories.parent_category')
        ->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();
        $total_users = Category::where('deleted_at','=',Null)->count();
        $data = [];
        foreach($categories as $category){
            $child_category_count = Helpers::getCountData('categories',['parent_category'=>$category->cid]);
            $product_count = Helpers::getCountData('product_category',['category_id'=>$category->cid]);
            $status = (!empty($category->cstatus)) ? 'checked': '';
            $status_html = '<input type="checkbox" name="status" id="status" class="form-control change-status" data-id="'.$category->cid.'" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" '.$status.'>';
            $parent_category = Category::find($category->cp);
            $parent_category = (!empty($parent_category))?$parent_category->name:'N/A';
            $category_logo_html = (!empty($category->cimage))?"<img class='img image' src=".url($category->cimage)." width=50 />":'Image not found';
            $action_html = "<span><a href=".route('category.edit',$category->cid)." class='btn btn-primary'><i class='fas fa-pen' title='Edit'></i></a></span>";
            if(empty($child_category_count) && empty($product_count)){
                $action_html .= "<span class='ml-3'><button type=submit data-user=$category->cid class='btn btn-danger' onClick=deleteUser($category->cid)><i class='fas fa-trash' title='Delete'></i></button></span>";
            }else{
                $action_html .= "<span class='ml-3'><span class='badge badge-info' title='Category'> $child_category_count</span> | <span class='badge badge-info' title='Product'> $product_count</span></span>";
            }
            $data[] = [$category->cid,$category->cname,$parent_category,$category_logo_html,$status_html,$action_html];
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
