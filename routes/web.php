<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\App;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\FrontendController::class, 'index'],function () {
});
Route::any('/getValue', [App\Http\Controllers\FrontendController::class, 'getValue'])->name('getValue');
Route::any('/getState', [App\Http\Controllers\FrontendController::class, 'getState'])->name('getState');
Route::any('/getDealers', [App\Http\Controllers\FrontendController::class, 'getDealers'])->name('getDealers');
Route::get('/home', function () {
    return redirect('admin/home');
});
Route::get('/admin', function () {
    return redirect('admin/home');
});

Route::group(['prefix' => 'admin'],function () {
    Auth::routes(['register' => false]);
});
Route::group(['prefix'=>'admin','middleware'=>['auth']],function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home/changepassword', [App\Http\Controllers\HomeController::class, 'changepassword'])->name('changepassword');
    Route::post('/home/updatepassword', [App\Http\Controllers\HomeController::class, 'updatepassword'])->name('updatepassword');
    Route::any('/get-value', [App\Http\Controllers\CommonController::class, 'index'])->name('get_value');
    Route::any('/get-application', [App\Http\Controllers\CommonController::class, 'getApplication'])->name('get-application');
    Route::any('/status/update', [App\Http\Controllers\CommonController::class, 'updateItemStatus'])->name('status.update');
    Route::any('staff/listajaxdata', [App\Http\Controllers\Admin\AdminUserController::class,'listAjaxData'])->name('stafflistajax');
    Route::any('dealer/listajaxdata', [App\Http\Controllers\Admin\DealerUserController::class,'listAjaxData'])->name('dealerlistajax');
    Route::any('category/listajaxdata', [App\Http\Controllers\Admin\CategoryController::class,'listAjaxData'])->name('categorylistajax');
    Route::post('profile/edit', [App\Http\Controllers\CommonController::class,'editProfile'])->name('profile.edit');
    Route::put('profile/', [App\Http\Controllers\CommonController::class,'updateProfile'])->name('profile.update');

    Route::resource('staff', App\Http\Controllers\Admin\AdminUserController::class);
    Route::resource('dealer', App\Http\Controllers\Admin\DealerUserController::class); 
    Route::resource('category', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('product', App\Http\Controllers\Admin\ProductController::class);  
    Route::resource('settings', App\Http\Controllers\SettingsController::class);
    
});


Route::get('admin/logout', function(){
    Auth::logout();
    // redirect to homepage
    return redirect('/admin');
})->name('Logout');
/* Route::get('/{lang}', function ($lang,$title='login') {
    Illuminate\Support\Facades\App::setlocale($lang);
    return view('home',['title' => '','elementActive'=>'']);
})->middleware('auth'); */
