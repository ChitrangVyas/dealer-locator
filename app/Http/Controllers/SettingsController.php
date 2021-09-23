<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MainController;
use App\Models\City;
use App\Models\Settings;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use View;

class SettingsController extends MainController
{
    function __construct()
    {
        parent::__construct();
        View::share ( 'title', 'Settings' );
        View::share ( 'elementActive', 'settings' );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Settings::find(1);
        if(!empty($settings)){
            $settings['json_data'] = json_decode($settings['json_data'],true);
            $settings = $settings;
            // $settings['json_data']['state_name'] = '';
            // $settings['json_data']['city_name'] ='';
            // if(!empty($settings['json_data']['state'])){
            //     $settings['json_data']['state_name'] = $this->getState($settings['json_data']['state'])->name;
            // }

            // if(!empty($settings['json_data']['city'])){
            //     $settings['json_data']['city_name'] = $this->getCity($settings['json_data']['city'])->name;
            // }
        }        
        return view('settings',['timezone_list'=>$this->timezone_list(),'settings'=>$settings]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $settings = json_decode(Settings::all());
    
        return view('settings',['timezone_list'=>$this->timezone_list(),'settings'=>$settings]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request_data = $request->except(['_token','id']);
        $filepath = '';
        if($request->file()){
            $filepath = $this->storeImage($request);
            $request_data['logo'] = $filepath;
        }
        if(empty($request->input('id'))){
            $settings = Settings::create(['json_data'=>json_encode($request_data)]);
        }else{
            $settings = Settings::where('id',$request->input('id'))->update(['json_data'=>json_encode($request_data)]);
        }
        if(!empty($settings)){
            config(['app.name' => $request_data['site_name']]);
            config(['app.timezone' => $request_data['timezone']]);
            date_default_timezone_set(config('app.timezone'));
            //'timezone' => 'Europe/Brussels' OR UTC
            Artisan::call('cache:clear');
            Artisan::call('config:clear');            
        }
        return redirect(route('settings.index'));
    }
    public function storeImage($request){
        if($request->file()) {
            $this->validate($request,[
                'logo' => 'mimes:jpg,gif,svg,jpeg,png,bmp,tiff |max:4096',
            ]);
            $fileName = time().'_'.$request->logo->getClientOriginalName();
            $filePath = $request->file('logo')->storeAs('uploads/logo', $fileName, 'public');
            return '/storage/' . $filePath;
        }
        return '';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function edit(Settings $settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Settings $settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Settings $settings)
    {
        //
    }
    public function getState($state_id){
        return State::find($state_id);
    }
    public function getCity($city_id){
        return City::find($city_id);
    }
}
