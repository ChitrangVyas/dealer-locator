<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CategoryCreateRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;
use View;

class CategoryController extends MainController
{
    public function __construct() {
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
        $parent_category =  Category::where('parent_category','=','0')->get();  
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
            $fileName = time().'_'.$request->image->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
        }
        try{
            $category = Category::create([
                'name' => $request_data['name'],
                'parent_category' => $request_data['parent_category'],                
                'image' => $file_path,
                'description'=> $request_data['description']
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
        $parent_category =  Category::where('parent_category','=','0')->get();       
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
        
        if($request->file()) {
            $fileName = time().'_'.$request->image->getClientOriginalName();
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
        $where .= (!empty($search)) ? " AND (name like '$search%' OR parent_category like '$search%')":'';
        $categories = DB::table('categories')
        ->selectRaw('categories.id,categories.name,categories.parent_category,categories.image')
        ->whereRaw($where)
        ->offset($start)
        ->limit($length)
        ->orderBy($request['columns'][$request['order'][0]['column']]['name'],$request['order'][0]['dir'])
        ->get();
        $total_users = Category::where('deleted_at','=',Null)->count();
        $data = [];
        foreach($categories as $category){
            $parent_category = Category::find($category->parent_category);
            $parent_category = (!empty($parent_category))?$parent_category->name:'N/A';
            $category_logo_html = (!empty($category->image))?"<img class='img image' src=".url($category->image)." width=50 />":'Image not found';
            $action_html = "<span><a href=".route('category.edit',$category->id)." class='btn btn-primary'>Edit</a></span>";
            $action_html .= "<span class='ml-3'><button type=submit data-user=$category->id class='btn btn-danger' onClick=deleteUser($category->id)>Delete</button></span>";
            $data[] = [$category->id,$category->name,$parent_category,$category_logo_html,$action_html];
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
