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
        $where = (!empty($request_data['where']))?" $request_data[where]":'';
        $where .= (!empty($request_data['q']))? (empty($request_data['whereOr'])) ? " AND ".$table.".name like '%$request_data[q]%'" : "(".$table.".name like '%$request_data[q]%'" :'';
        $where .= (!empty($request_data['whereOr']))?" $request_data[whereOr] )":'';
        $where .= (!empty($request_data['whereAnd']))?" $request_data[whereAnd] ":'';
        $query = "select $fields from $table";
        if(!empty($where)){
            $query .= " where $where";
        }
        DB::enableQueryLog(); 
        if(!empty($request_data['option_group'])){
            foreach($request_data['option_group'] as $key=>$optionGroup){
                $option_group_fields = (!empty($optionGroup['fields']))?$optionGroup['fields']:'id , name as text';
                $option_group_where = (!empty($optionGroup['where']))?" $optionGroup[where]":'';
                $option_group_where .= (!empty($request_data['q']))? (empty($request_data['whereOr'])) ? " AND ".$table.".name like '%$request_data[q]%'" : $table.".name like '%$request_data[q]%'" :'';
                $option_group_where .= (!empty($optionGroup['whereOr']))?" $optionGroup[whereOr]":'';
                $obj = DB::table($optionGroup['table'])
                        ->selectRaw($option_group_fields)
                        ->whereRaw($option_group_where)
                        ->take(10);
                if(!empty($optionGroup['joins'])){
                    foreach($optionGroup['joins'] as $join){
                        $type = (!empty($join['type']))?$join['type']:'inner';
                        $obj->join($join['join_table'], $join['to_column'], '=', $join['from_column'],$type);
                    }
                }
                $options_group[] = 
                    [
                        'id'=> '',
                        'text'=> ucwords($optionGroup['name']),
                        'children' => $obj->get()
                    ];
            }
            $data = $options_group;
        }else{
            $obj = DB::table($table)
                        ->selectRaw($fields)
                        ->whereRaw($where)
                        ->take(10);
            if(!empty($request_data['joins'])){
                foreach($request_data['joins'] as $join){
                    $type = (!empty($join['type']))?$join['type']:'inner';
                    $obj->join($join['join_table'], $join['to_column'], '=', $join['from_column'],$type);
                }
            }
            $data = $obj->get();
        }

        // dd(DB::getQueryLog());
        echo json_encode($data);
        }
    }
}
