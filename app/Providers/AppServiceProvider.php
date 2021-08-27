<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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

    }
}
