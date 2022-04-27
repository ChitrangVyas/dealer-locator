<?php

namespace App\Providers;

use App\Http\Helpers;
use App\Models\Country;
use App\Models\Settings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('admin', function () {
            $user = auth()->user();
            return (($user && (empty($user->users_group_id) || $user->userGroup->id == 1)));
        });

        Blade::if('notadmin', function () {
            $user = auth()->user();
            return (($user && ((!empty($user->users_group_id)) || $user->users_group_id > 1)));
        });

        Blade::if('dealer', function () {
            $user = auth()->user();
            return ($user &&(empty($user->users_group_id) || ($user->userGroup->id == 1 || $user->userGroup->id == 2)));
        });

        Blade::if('retailer', function () {
            $user = auth()->user();
            return ($user && ($user->userGroup->id == 3));
        });

        Blade::if('customer', function () {
            $user = auth()->user();
            return ($user && ($user->userGroup->id == 4 || $user->userGroup->id == 5));
        });

        Blade::if('notCustomer', function () {
            $user = auth()->user();
            return ($user && empty($user->users_group_id)) || ($user && ($user->userGroup->id < 4) );
        });

        Str::macro('currency', function ($price){
            return number_format($price, 2, '.', ',');
        });

        Str::macro('getMobileWithCountryCode', function ($mobile,$country){
            $country_data = Country::findorFail($country);
            if(!empty($mobile)){
                return  $country_data->phonecode." ".$mobile;
            }
            else{
                return $mobile;
            }
        });
        Str::macro('number_format', function ($amt){
           return Helpers::number_format($amt);
        });

    }
}
