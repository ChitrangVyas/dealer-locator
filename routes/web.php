<?php

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

Route::get('/', function () {
    return redirect('admin/home');
});

Auth::routes(['register' => false]);
Route::group(['prefix'=>'admin','middleware'=>['auth']],function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/get-value', [App\Http\Controllers\CommonController::class, 'index'])->name('get_value');
    Route::get('staff/listajaxdata', [App\Http\Controllers\Admin\AdminUserController::class,'listAjaxData'])->name('stafflistajax');
    Route::get('dealer/listajaxdata', [App\Http\Controllers\Admin\DealerUserController::class,'listAjaxData'])->name('dealerlistajax');
    Route::get('retailer/listajaxdata', [App\Http\Controllers\Admin\RetailerUserController::class,'listAjaxData'])->name('retailerlistajax');
    Route::get('manufacturer/listajaxdata', [App\Http\Controllers\Admin\ManufacturerController::class,'listAjaxData'])->name('manufacturerlistajax');
    Route::get('brand/listajaxdata', [App\Http\Controllers\Admin\BrandController::class,'listAjaxData'])->name('brandlistajax');
    Route::get('category/listajaxdata', [App\Http\Controllers\Admin\CategoryController::class,'listAjaxData'])->name('categorylistajax');
    Route::get('product/listajaxdata', [App\Http\Controllers\Admin\ProductController::class,'listAjaxData'])->name('productlistajax');
    Route::get('inquiries_source/listajaxdata', [App\Http\Controllers\Admin\InquiriesSourceController::class,'listAjaxData'])->name('inquiries_sourcelistajax');
    Route::get('customer/listajaxdata', [App\Http\Controllers\Admin\CustomerUserController::class,'listAjaxData'])->name('customerlistajax');
    Route::get('customer/createAjax', [App\Http\Controllers\Admin\CustomerUserController::class,'createAjax'])->name('customerCreateAjax');
    Route::get('inquiry/listajaxdata', [App\Http\Controllers\Admin\InquiryController::class,'listAjaxData'])->name('inquirylistajax');
    Route::get('inquiry-log/reminder/add', [App\Http\Controllers\Admin\InquiryLogController::class,'createReminder'])->name('createReminder');
    Route::post('inquiry-log/reminder/add', [App\Http\Controllers\Admin\InquiryLogController::class,'storeReminder'])->name('storeReminder');

    Route::resource('staff', App\Http\Controllers\Admin\AdminUserController::class);
    Route::resource('dealer', App\Http\Controllers\Admin\DealerUserController::class);   
    Route::resource('retailer', App\Http\Controllers\Admin\RetailerUserController::class);   
    Route::resource('customer', App\Http\Controllers\Admin\CustomerUserController::class);   
    Route::resource('manufacturer', App\Http\Controllers\Admin\ManufacturerController::class);   
    Route::resource('brand', App\Http\Controllers\Admin\BrandController::class);   
    Route::resource('category', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('product', App\Http\Controllers\Admin\ProductController::class);   
    Route::resource('inquiries-source', App\Http\Controllers\Admin\InquiriesSourceController::class);
    Route::resource('inquiry', App\Http\Controllers\Admin\InquiryController::class);
    Route::resource('inquiry-log', App\Http\Controllers\Admin\InquiryLogController::class);
});


Route::get('/logout', function(){
    Auth::logout();
    // redirect to homepage
    return redirect('/');
})->name('Logout');

/* Route::get('/{lang}', function ($lang,$title='login') {
    Illuminate\Support\Facades\App::setlocale($lang);
    return view('home',['title' => '','elementActive'=>'']);
})->middleware('auth'); */
