<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MainController;
use App\Http\Requests\Admin\SettingsRequest;
use App\Models\City;
use App\Models\Settings;
use App\Models\State;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
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
        $settings = json_decode(Settings::where('slug','settings')->get());
    
        return view('settings',['timezone_list'=>$this->timezone_list(),'settings'=>$settings]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingsRequest $request)
    {
        $request_data = $request->except(['_token','id']);
        $filepath = '';
        if($request->file()){
            $this->validate($request,[
                'logo' => 'mimes:jpg,gif,svg,jpeg,png,bmp,tiff |max:4096',
                'icon_logo' => 'mimes:jpg,gif,svg,jpeg,png,bmp,tiff |max:4096',
            ]);
            if(!empty($request->file('logo'))){
                $fileName = time().'_'. Str::snake($request->logo->getClientOriginalName());
                $filepath = $this->storeImage($request,$fileName,'logo');
                $request_data['logo'] = $filepath;
            }
            if(!empty($request->file('icon_logo'))){
                $fileName = time().'_'. Str::snake($request->icon_logo->getClientOriginalName());
                $filepath = $this->storeImage($request,$fileName,'icon_logo');
                $request_data['icon_logo'] = $filepath;
            }
        }
        
        if(empty($request->input('id'))){
            $settings = Settings::create(['json_data'=>json_encode($request_data)]);
        }else{
            $setting = Settings::where('id',$request->input('id'));
            $settings_data = (!empty($setting->first()->json_data))?json_decode($setting->first()->json_data,true):'';
            if(!empty($settings_data) && (!empty($settings_data['logo']) && empty($request_data['logo']))){
                $request_data['logo'] = $settings_data['logo'];
            }
            if(!empty($settings_data) && (!empty($settings_data['icon_logo']) &&  empty($request_data['icon_logo']))){
                $request_data['icon_logo'] = $settings_data['icon_logo'];
            }
            $settings = $setting->update(['json_data'=>json_encode($request_data)]);
        }
        if(!empty($settings)){
            if(!empty($request_data['site_name'])){
                config(['app.name' => $request_data['site_name']]);
            }
            if(!empty($request_data['timezone'])){
                config(['app.timezone' => $request_data['timezone']]);
                date_default_timezone_set(config('app.timezone'));
            }
            //'timezone' => 'Europe/Brussels' OR UTC
            Artisan::call('cache:clear');
            Artisan::call('config:clear');            
        }
        return redirect(route('settings.index'));
    }
    public function storeImage($request,$fileName,$input){
        if((!empty($fileName)) && (!empty($input))) {
            $filePath = $request->file($input)->storeAs('uploads/logo', $fileName, 'public');
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

    public function smsSettings(){
        View::share ( 'title', 'SMS Settings' );
        View::share ( 'elementActive', 'sms_settings' );
        $settings = Settings::where('slug','sms-settings')->first();
        $settings['json_data'] = json_decode($settings['json_data'],true);
        return view('sms-settings',['settings'=>$settings]);
    }

    public function StoreSmsSettings(Request $request){
        $request_data = $request->except(['id','_token']);
        if(!isset($request_data['is_enabled'])) $request_data['is_enabled'] = 0;
        if(!empty($request->input('id',''))){
            $setting = Settings::where('id',$request->input('id'));
            $settings_data = (!empty($setting->first()->json_data))?json_decode($setting->first()->json_data,true):'';
            $settings = $setting->update(['json_data'=>json_encode($request_data)]);
        }
        return redirect(route('sms.settings'));
    }

    public function getState($state_id){
        return State::find($state_id);
    }
    public function getCity($city_id){
        return City::find($city_id);
    }
    public function getCountry($country_id){
        return Country::find($country_id);
    }
}
