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
    Route::get('vendor/', [App\Http\Controllers\Admin\CustomerUserController::class,'vendorIndex'])->name('vendor.index');
    Route::get('vendor/listajax', [App\Http\Controllers\Admin\VendorController::class,'vendorListAjaxData'])->name('vendorlistajax');
    //inquiry & inquiry-log
    Route::get('inquiry/listajaxdata', [App\Http\Controllers\Admin\InquiryController::class,'listAjaxData'])->name('inquirylistajax');
    Route::post('inquiry/assign/retailer', [App\Http\Controllers\Admin\InquiryController::class,'assignToRetailer'])->name('inquiry.assign.retailer');
    Route::post('inquiry/assign/retailer/update', [App\Http\Controllers\Admin\InquiryController::class,'updateInquiry'])->name('inquiry.update.ajax');
    Route::get('inquiry-log/reminder/add', [App\Http\Controllers\Admin\InquiryLogController::class,'createReminder'])->name('createReminder');
    Route::post('inquiry-log/reminder/add', [App\Http\Controllers\Admin\InquiryLogController::class,'storeReminder'])->name('storeReminder');
    Route::post('inquiry/status/', [App\Http\Controllers\Admin\InquiryController::class,'getInquiryStatus'])->name('inqiury.status');
    Route::post('inquiry/status/update', [App\Http\Controllers\Admin\InquiryController::class,'updateInquiryStatus'])->name('inqiury.status.update');
    //qoute
    Route::post('order/quote/create', [App\Http\Controllers\Admin\OrderController::class,'quoteCreate'])->name('order.quote.create');
    Route::post('quote/create', [App\Http\Controllers\Admin\OrderController::class,'createQuote'])->name('quote.create');
    Route::post('order/quote/', [App\Http\Controllers\Admin\OrderController::class,'quoteStore'])->name('order.quote.store');
    Route::get('quote/', [App\Http\Controllers\Admin\OrderController::class,'quoteIndex'])->name('quote.index');
    Route::get('quote/listQuoteAjaxData', [App\Http\Controllers\Admin\OrderController::class,'listQuoteAjaxData'])->name('list.quote');
    Route::post('quote/show', [App\Http\Controllers\Admin\OrderController::class,'showQuote'])->name('quote.show');
    //po
    Route::get('po/', [App\Http\Controllers\Admin\OrderController::class,'poIndex'])->name('po.index');
    Route::post('po/create', [App\Http\Controllers\Admin\OrderController::class,'poCreate'])->name('po.create');
    Route::get('po/listPoAjaxData', [App\Http\Controllers\Admin\OrderController::class,'listPoAjaxData'])->name('list.po');
    Route::post('po/show', [App\Http\Controllers\Admin\OrderController::class,'showPo'])->name('po.show');
    Route::post('order/po/', [App\Http\Controllers\Admin\OrderController::class,'storePo'])->name('order.po.store');
    Route::post('order/po/recieved', [App\Http\Controllers\Admin\OrderController::class,'poRecived'])->name('order.po.recieved');
    Route::post('order/po/upload/image', [App\Http\Controllers\Admin\OrderController::class,'uploadPoImage'])->name('order.po.upload.image');
    Route::post('order/po/image', [App\Http\Controllers\Admin\OrderController::class,'getPoImage'])->name('order.po.image');
    //sale
    Route::get('sale/', [App\Http\Controllers\Admin\OrderController::class,'saleIndex'])->name('sale.index');
    Route::get('sale/listSaleAjaxData', [App\Http\Controllers\Admin\OrderController::class,'listSaleAjaxData'])->name('list.sale');
    Route::post('sale/show', [App\Http\Controllers\Admin\OrderController::class,'showSale'])->name('sale.show');
    Route::post('cart/sale/create', [App\Http\Controllers\Admin\CartController::class,'createSale'])->name('cart.sale.create');
    Route::post('order/sale/', [App\Http\Controllers\Admin\OrderController::class,'storeSale'])->name('order.sale.store');
    Route::post('invoice/create', [App\Http\Controllers\Admin\OrderController::class,'invoiceCreate'])->name('invoice.create');
    Route::get('invoice', [App\Http\Controllers\Admin\OrderController::class,'invoiceIndex'])->name('invoice.index');
    Route::get('invoice/listInvoiceAjaxData', [App\Http\Controllers\Admin\OrderController::class,'listInvoiceAjaxData'])->name('list.invoice');
    Route::post('invoice/status/update', [App\Http\Controllers\Admin\OrderController::class,'updateOrderStatus'])->name('invoice.status.update');
    
    //cart
    Route::post('cart/inquiry-to-quote/create', [App\Http\Controllers\Admin\CartController::class,'createInquiryToQuoteCart'])->name('cart.inquiryToQuote.create');
    Route::post('cart/po/create', [App\Http\Controllers\Admin\CartController::class,'createPo'])->name('cart.po.create');
    Route::post('cart/invoice/create', [App\Http\Controllers\Admin\CartController::class,'createInvoice'])->name('cart.invoice.create');
    Route::post('cart/quote/create', [App\Http\Controllers\Admin\CartController::class,'createQuote'])->name('cart.quote.create');
    Route::post('cart/sale/create', [App\Http\Controllers\Admin\CartController::class,'createSale'])->name('cart.sale.create');
    Route::post('cart/product/add', [App\Http\Controllers\Admin\CartController::class,'addProductToCart'])->name('cart.product.add');
    Route::post('cart/product/new/', [App\Http\Controllers\Admin\CartController::class,'addNewProductToCart'])->name('cart.product.new');
    Route::put('cart/product/update', [App\Http\Controllers\Admin\CartController::class,'update'])->name('cart.update');
    Route::delete('cart/product/', [App\Http\Controllers\Admin\CartController::class,'removeCartProduct'])->name('cart.product.destroy');


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
    Route::resource('order', App\Http\Controllers\Admin\OrderController::class);
    Route::resource('vendor', App\Http\Controllers\Admin\VendorController::class);
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
