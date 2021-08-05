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
    return redirect('/home');
});

Route::group(['prefix'=>'admin','middleware'=>['auth']],function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/staff', [App\Http\Controllers\UserController::class, 'create'])->name('staff');
    Route::post('/staff/add', [App\Http\Controllers\UserController::class, 'store'])->name('add_staff');
});

Auth::routes(['register' => false]);

Route::get('/logout', function(){
    Auth::logout();
    // redirect to homepage
    return redirect('/');
})->name('Logout');

/* Route::get('/{lang}', function ($lang,$title='login') {
    Illuminate\Support\Facades\App::setlocale($lang);
    return view('home',['title' => '','elementActive'=>'']);
})->middleware('auth'); */
