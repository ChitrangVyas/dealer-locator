<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MainController;
use App\Http\Helpers;
use App\Models\City;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CommonController extends MainController
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
            $where .= (!empty($where) && ((!empty($request_data['q'])) || !empty($request_data['whereOr']) || !empty($request_data['whereAnd'])))? " AND " :'';
            $where .= (!empty($request_data['q']))? (empty($request_data['whereOr'])) ? $table.".name like '%$request_data[q]%'" : "(".$table.".name like '%$request_data[q]%'" :'';
            $where .= (!empty($request_data['whereOr']))?((!empty($request_data['whereOr']) && !empty($request_data['where']))?" $request_data[whereOr] )":')'):'';
            $where .= (!empty($request_data['whereAnd']))?" $request_data[whereAnd] ":'';
            $query = "select $fields from $table";
            if(!empty($where)){
                $query .= " where $where  order by name asc";
            }
            DB::enableQueryLog(); 
            if(!empty($request_data['option_group'])){
                $options_group=array();
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
                    if(!empty($optionGroup['groupBy'])){
                        $obj->groupBy($optionGroup['groupBy']);                
                    }
                    $resultset = $obj->get();
                    if(count($resultset)){
                    $options_group[] = 
                        [
                            'id'=> '',
                            'text'=> ucwords($optionGroup['name']),
                            'children' => $resultset
                        ];
                    }
                }
                echo json_encode($options_group);
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
                if(!empty($request_data['groupBy'])){
                    $obj->groupBy($request_data['groupBy']);                
                }
                $data = $obj->get();
                echo json_encode($data);
            }  
        }
    }

    public function getUserNotification(Request $request){
        $is_admin = $request->input('is_admin');
        $data = Helpers::getUserNotification($request->input('user_id'),$is_admin);
        echo json_encode($data);
    }
    public function getApplication(Request $request){
        $application = [
            'Domestic'=>['Private Bunglows','Appartments','Common Society','RO System','Others'],
            'Industrial'=>['Cooling Tower','Boiler','Common Utility','RO System','Others']
        ];
        $option[] = '<option value="">Please Select</option>';
       
        if(array_key_exists($request['q'],$application)){            
            foreach($application[$request['q']] as $k=>$v){
                if($request['app_type'] == $v){
                    $selected = "selected";
                }else{
                    $selected="";
                }
                $option[] = '<option value="'.$v.'"'.$selected.'>'.$v.'</option>';
            }
        }
       return $option;
    }

    public function updateItemStatus(Request $request){
        if(auth()->check()){
            $request_data = $request->except('_token','method');
            if(!empty($request_data['table']) && !empty($request_data['id'])){
                $status = (!empty($request_data['status']))?1:0;
                $update_status = DB::table($request_data['table'])->where('id',$request_data['id'])->update(['status'=>$status]);
                return response()->json(['status' => 'successful','code'=>1, 'data'=>'Status successfully updated', 'message'=>'']);
            }
        }
        return response()->json(['status' => 'error','code'=>0, 'data'=>'', 'message'=>'Something goes wrong']);
    }

    public function editProfile(Request $request){
        if($request->ajax()){
            $user = User::findOrFail($request->input('id'));
            $json_data = array('status' => 'success','code'=>1);
            $json_data['title'] = 'Edit Profile';
            $json_data['content'] =  view('layouts.partials.profile',['user'=>$user])->render();

            return response()->json(['status' => 'successful','code'=>1, 'data'=>$json_data, 'message'=>'']);
        } else {
            return abort(404);
        }

    }

    public function updateProfile(Request $request){
        if($request->ajax()){
            Validator::make($request->all(), [
                'name' => 'required',
                'mobile' => 'required',
                'address' => 'required',
            ],[
                'name.required'=>"Name is required.",
                'mobile.required' => "Mobile is required.",
                'address.required' => "Address is required.",
            ])->validate();            
            //dd($request);
                $request_data = $request->except('_method','_token','id','logo');
                //print_r($request_data);exit;
                $user = User::findOrFail($request->input('id'));
                if(!empty($request->file())){
                    $filename = str_replace('/storage/uploads/userlogo/', '', $user->logo);
                    if(!empty($user->logo) && file_exists(storage_path('app/public/uploads/userlogo/'.$filename))){
                        unlink(storage_path('app/public/uploads/userlogo/'.$filename));
                    }
                    $fileName = time().'_'. Str::snake($request->logo->getClientOriginalName());
                    $filePath = $request->file('logo')->storeAs('uploads/userlogo', $fileName, 'public');
                    $file_path = '/storage/' . $filePath;
                    $request_data['logo'] = $file_path;
                }
                try{
                     if($user->update($request_data)){
                        return response()->json(['status' => 'successful','code'=>1,'data'=>'Profile has been successfully updated.']);
                     }

                }catch(Exception $e){
                    return response()->json(['status'=>'error','code'=>0,'data'=>$e->getMessage()]);
                }
                return response()->json(['status'=>'error','code'=>0,'data'=>'Profile is not Updated']);
        }else{
            return abort(404,'Not Found');
        }
    }
    public function storeImage($request,$fileName,$input){
        if((!empty($fileName)) && (!empty($input))) {
            $filePath = $request->file($input)->storeAs('uploads/userlogo', $fileName, 'public');
            return '/storage/' . $filePath;
        }
        return '';
    }
}
