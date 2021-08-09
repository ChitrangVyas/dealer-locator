<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    //
    public function index(Request $request){
        $request_data = $request->all();
        if(!empty($request_data)){
            $this->getValue($request_data);
        }
    }

    public function getValue($request_data){
        if(!empty($request_data)){
        $table = $request_data['table'];
        $fields = (!empty($request_data['fields']))?$request_data['fields']:'id , name as text';
        $where = (!empty($request_data['q']))?" name like '$request_data[q]%'":'';
        $where .= (!empty($request_data['where']))?" and $request_data[where]":'';
        $query = "select $fields from $table";
        if(!empty($where)){
            $query .= " where $where";
        }
        $data = DB::table($table)
                     ->selectRaw($fields)
                     ->whereRaw($where)
                     ->take(10)
                     ->get();
        echo json_encode($data);
        }
    }
}
