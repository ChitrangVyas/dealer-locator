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

Route::get('/', function () {
    return redirect('admin/home');
});

Route::get('/home', function () {
    return redirect('admin/home');
});
Route::get('/admin', function () {
    return redirect('admin/home');
});

Route::get('installation/logout', function(){
    if(\Session::has('installation_user')){
        \Session::forget('installation_user');
    }
    return redirect(route('installation.login'));
    
})->name('installation.logout');

Route::group(['prefix'=>'installation'],function () {
    Route::get('/order/login/{token?}', [App\Http\Controllers\InstallationController::class, 'installlationLogin'])->name('installation.login');
    Route::post('login', [App\Http\Controllers\InstallationController::class, 'login'])->name('installation.order.login');
    Route::get('/dashboard', [App\Http\Controllers\InstallationController::class, 'dashboard'])->name('installation.order.dashboard');
    Route::post('image/upload', [App\Http\Controllers\InstallationController::class, 'uploadInstallationImage'])->name('installation.image.upload');
    Route::post('add/note', [App\Http\Controllers\InstallationController::class, 'addInstallationNote'])->name('installation.add.note');
    Route::post('image/delete', [App\Http\Controllers\InstallationController::class, 'removeInstallationImage'])->name('installation.image.delete');
    //
});
Auth::routes(['register' => false]);
Route::group(['prefix'=>'admin','middleware'=>['auth']],function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home/changepassword', [App\Http\Controllers\HomeController::class, 'changepassword'])->name('changepassword');
    Route::post('/home/updatepassword', [App\Http\Controllers\HomeController::class, 'updatepassword'])->name('updatepassword');
    Route::post('home/inquiry', [App\Http\Controllers\Admin\InquiryController::class,'getInquiries'])->name('home.inqiuries');
    Route::post('home/customer', [App\Http\Controllers\Admin\CustomerUserController::class,'getcustomers'])->name('home.customers');
    Route::post('home/po', [App\Http\Controllers\Admin\OrderController::class,'getPo'])->name('home.po');
    Route::post('home/orders', [App\Http\Controllers\Admin\OrderController::class,'getOrders'])->name('home.orders');
    Route::post('home/installation', [App\Http\Controllers\Admin\OrderController::class,'getInstallation'])->name('home.installation');
    Route::any('/get-value', [App\Http\Controllers\CommonController::class, 'index'])->name('get_value');
    Route::any('/get-application', [App\Http\Controllers\CommonController::class, 'getApplication'])->name('get-application');
    Route::any('/get-user-notification', [App\Http\Controllers\CommonController::class, 'getUserNotification'])->name('user.notification');
    Route::any('/status/update', [App\Http\Controllers\CommonController::class, 'updateItemStatus'])->name('status.update');
    Route::any('staff/listajaxdata', [App\Http\Controllers\Admin\AdminUserController::class,'listAjaxData'])->name('stafflistajax');
    Route::any('dealer/listajaxdata', [App\Http\Controllers\Admin\DealerUserController::class,'listAjaxData'])->name('dealerlistajax');
    Route::post('dealer/showProduct', [App\Http\Controllers\Admin\DealerUserController::class,'showProduct'])->name('dealer.showProduct');
    Route::post('dealer/addProductdiscount', [App\Http\Controllers\Admin\DealerUserController::class,'addProductdiscount'])->name('dealer.addProductdiscount');
    Route::post('retailer/showProduct', [App\Http\Controllers\Admin\RetailerUserController::class,'showProduct'])->name('retailer.showProduct');
    Route::post('retailer/addProductdiscount', [App\Http\Controllers\Admin\RetailerUserController::class,'addProductdiscount'])->name('retailer.addProductdiscount');
    Route::any('retailer/listajaxdata', [App\Http\Controllers\Admin\RetailerUserController::class,'listAjaxData'])->name('retailerlistajax');
    Route::any('manufacturer/listajaxdata', [App\Http\Controllers\Admin\ManufacturerController::class,'listAjaxData'])->name('manufacturerlistajax');
    Route::any('brand/listajaxdata', [App\Http\Controllers\Admin\BrandController::class,'listAjaxData'])->name('brandlistajax');
    Route::any('category/listajaxdata', [App\Http\Controllers\Admin\CategoryController::class,'listAjaxData'])->name('categorylistajax');
    Route::any('product/listajaxdata', [App\Http\Controllers\Admin\ProductController::class,'listAjaxData'])->name('productlistajax');
    Route::any('tax/listajaxdata', [App\Http\Controllers\Admin\TaxController::class,'listAjaxData'])->name('taxlistajax');
    Route::any('tax-group/listajaxdata', [App\Http\Controllers\Admin\TaxGroupController::class,'listAjaxData'])->name('taxgrouplistajax');
    Route::post('product/inventory', [App\Http\Controllers\Admin\ProductController::class,'getProductInventory'])->name('product.inventory');
    Route::post('product/inventory/update', [App\Http\Controllers\Admin\ProductController::class,'updateProductInventory'])->name('product.inventory.update');
    Route::any('inquiries_source/listajaxdata', [App\Http\Controllers\Admin\InquiriesSourceController::class,'listAjaxData'])->name('inquiries_sourcelistajax');
    Route::any('customer/listajaxdata', [App\Http\Controllers\Admin\CustomerUserController::class,'listAjaxData'])->name('customerlistajax');
    Route::any('notification/listajaxdata', [App\Http\Controllers\NotificationController::class,'listAjaxData'])->name('notificationlistajax');
    Route::post('notification/show/all', [App\Http\Controllers\NotificationController::class,'showAll'])->name('notification.all');
    Route::post('notification/markasread', [App\Http\Controllers\NotificationController::class,'markAsRead'])->name('notification.markasread');
    Route::post('notification/get', [App\Http\Controllers\NotificationController::class,'userNotification'])->name('notification.get');
    Route::get('customer/createAjax', [App\Http\Controllers\Admin\CustomerUserController::class,'createAjax'])->name('customerCreateAjax');
    Route::post('profile/edit', [App\Http\Controllers\CommonController::class,'editProfile'])->name('profile.edit');
    Route::put('profile/', [App\Http\Controllers\CommonController::class,'updateProfile'])->name('profile.update');
    Route::get('vendor/', [App\Http\Controllers\Admin\CustomerUserController::class,'vendorIndex'])->name('vendor.index');
    Route::any('vendor/listajax', [App\Http\Controllers\Admin\VendorController::class,'vendorListAjaxData'])->name('vendorlistajax');
    //report
    Route::any('report/product', [App\Http\Controllers\Admin\ProductController::class,'productReport'])->name('report.product');
    Route::any('report/lowStockReport', [App\Http\Controllers\Admin\ProductController::class,'lowStockReport'])->name('report.lowstock');
    Route::any('report/po', [App\Http\Controllers\Admin\OrderController::class,'poReport'])->name('report.po');
    Route::any('report/sales', [App\Http\Controllers\Admin\OrderController::class,'salesReport'])->name('report.sales');
    Route::any('report/inquiry', [App\Http\Controllers\Admin\InquiryController::class,'inquiryReport'])->name('report.inquiry');
    Route::any('product/reportListAjaxData', [App\Http\Controllers\Admin\ProductController::class,'reportListAjaxData'])->name('productreportlistajax');
    Route::any('product/lowStockReportListAjaxData', [App\Http\Controllers\Admin\ProductController::class,'lowStockReportListAjaxData'])->name('lowstockreportlistajax');
    Route::any('inquiry/reportListAjaxData', [App\Http\Controllers\Admin\InquiryController::class,'reportListAjaxData'])->name('inquiryreportlistajax');
    Route::any('product/poReportListAjaxData', [App\Http\Controllers\Admin\OrderController::class,'poReportListAjaxData'])->name('poreportlistajax');
    Route::any('product/salesReportListAjaxData', [App\Http\Controllers\Admin\OrderController::class,'salesReportListAjaxData'])->name('salesreportlistajax');
    //inquiry & inquiry-log
    Route::any('inquiry/listajaxdata', [App\Http\Controllers\Admin\InquiryController::class,'listAjaxData'])->name('inquirylistajax');
    Route::post('inquiry/view', [App\Http\Controllers\Admin\InquiryController::class,'show'])->name('inquiry.view');
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
    Route::any('quote/listQuoteAjaxData', [App\Http\Controllers\Admin\OrderController::class,'listQuoteAjaxData'])->name('list.quote');
    Route::post('quote/show', [App\Http\Controllers\Admin\OrderController::class,'showQuote'])->name('quote.show');
    //po
    Route::get('po/', [App\Http\Controllers\Admin\OrderController::class,'poIndex'])->name('po.index');
    Route::post('po/create', [App\Http\Controllers\Admin\OrderController::class,'poCreate'])->name('po.create');
    Route::any('po/listPoAjaxData', [App\Http\Controllers\Admin\OrderController::class,'listPoAjaxData'])->name('list.po');
    Route::post('po/show', [App\Http\Controllers\Admin\OrderController::class,'showPo'])->name('po.show');
    Route::post('order/po/', [App\Http\Controllers\Admin\OrderController::class,'storePo'])->name('order.po.store');
    Route::post('order/po/recieved', [App\Http\Controllers\Admin\OrderController::class,'poRecived'])->name('order.po.recieved');
    Route::post('order/po/upload/image', [App\Http\Controllers\Admin\OrderController::class,'uploadPoImage'])->name('order.po.upload.image');
    Route::delete('order/po/delete/image', [App\Http\Controllers\Admin\OrderController::class,'deletePoImage'])->name('order.po.delete.image');
    Route::post('order/po/image', [App\Http\Controllers\Admin\OrderController::class,'getPoImage'])->name('order.po.image');
    Route::post('order/delete', [App\Http\Controllers\Admin\OrderController::class,'deleteOrder'])->name('order.delete');

    //sale
    Route::get('sale/', [App\Http\Controllers\Admin\OrderController::class,'saleIndex'])->name('sale.index');
    Route::any('sale/listSaleAjaxData', [App\Http\Controllers\Admin\OrderController::class,'listSaleAjaxData'])->name('list.sale');
    Route::post('sale/show', [App\Http\Controllers\Admin\OrderController::class,'showSale'])->name('sale.show');
    Route::post('invoice/show', [App\Http\Controllers\Admin\OrderController::class,'showInvoice'])->name('invoice.show');
    Route::post('cart/sale/create', [App\Http\Controllers\Admin\CartController::class,'createSale'])->name('cart.sale.create');
    Route::post('order/sale/', [App\Http\Controllers\Admin\OrderController::class,'storeSale'])->name('order.sale.store');
    Route::post('invoice/create', [App\Http\Controllers\Admin\OrderController::class,'invoiceCreate'])->name('invoice.create');
    Route::get('invoice', [App\Http\Controllers\Admin\OrderController::class,'invoiceIndex'])->name('invoice.index');
    Route::any('invoice/listInvoiceAjaxData', [App\Http\Controllers\Admin\OrderController::class,'listInvoiceAjaxData'])->name('list.invoice');
    Route::post('invoice/status/update', [App\Http\Controllers\Admin\OrderController::class,'updateOrderStatus'])->name('invoice.status.update');
    Route::post('sale/stage/update', [App\Http\Controllers\Admin\OrderController::class,'updateOrderStage'])->name('sale.stage.update');
    Route::post('order/token/update',[App\Http\Controllers\Admin\OrderController::class,'updateOrderToken'])->name('order.token.update');
    Route::any('order/{id}/print',[App\Http\Controllers\Admin\OrderController::class,'printInvoice'])->name('order.print');
    
    //cart
    Route::post('cart/inquiry-to-quote/create', [App\Http\Controllers\Admin\CartController::class,'createInquiryToQuoteCart'])->name('cart.inquiryToQuote.create');
    Route::post('cart/po/create', [App\Http\Controllers\Admin\CartController::class,'createPo'])->name('cart.po.create');
    Route::post('cart/invoice/create', [App\Http\Controllers\Admin\CartController::class,'createInvoice'])->name('cart.invoice.create');
    Route::any('cart/update/create_at', [App\Http\Controllers\Admin\CartController::class,'updateCreatedAt'])->name('cart.created_at.update');
    Route::any('cart/get/create_at', [App\Http\Controllers\Admin\CartController::class,'getCreatedAt'])->name('cart.created_at.get');
    Route::post('cart/quote/create', [App\Http\Controllers\Admin\CartController::class,'createQuote'])->name('cart.quote.create');
    Route::post('cart/quote/edit', [App\Http\Controllers\Admin\CartController::class,'editQuote'])->name('cart.quote.edit');
    Route::post('cart/sale/create', [App\Http\Controllers\Admin\CartController::class,'createSale'])->name('cart.sale.create');
    Route::post('cart/sale/edit', [App\Http\Controllers\Admin\CartController::class,'editSale'])->name('cart.sale.edit');
    Route::post('cart/product/add', [App\Http\Controllers\Admin\CartController::class,'addProductToCart'])->name('cart.product.add');
    Route::post('cart/product/new/', [App\Http\Controllers\Admin\CartController::class,'addNewProductToCart'])->name('cart.product.new');
    Route::put('cart/product/update', [App\Http\Controllers\Admin\CartController::class,'update'])->name('cart.update');
    Route::delete('cart/product/', [App\Http\Controllers\Admin\CartController::class,'removeCartProduct'])->name('cart.product.destroy');

    // SMS Settings
    Route::get('settings/sms', [App\Http\Controllers\SettingsController::class,'smsSettings'])->name('sms.settings');
    Route::post('settings/sms', [App\Http\Controllers\SettingsController::class,'StoreSmsSettings'])->name('sms.settings.store');

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
    Route::resource('settings', App\Http\Controllers\SettingsController::class);
    Route::resource('notification', App\Http\Controllers\NotificationController::class);
    Route::resource('tax', App\Http\Controllers\Admin\TaxController::class);
    Route::resource('tax-group', App\Http\Controllers\Admin\TaxGroupController::class);
    
});


Route::get('/logout', function(){
    Auth::logout();
    // redirect to homepage
    return redirect('/admin');
})->name('Logout');

Route::get('/dealer/login',[App\Http\Controllers\Admin\DealerUserController::class,'login'])->name('dealer.login');
Route::get('/dealer/password/reset',[App\Http\Controllers\Admin\DealerUserController::class,'forgotPassword'])->name('dealer.forgot.password');
Route::get('/retailer/login',[App\Http\Controllers\Admin\RetailerUserController::class,'login'])->name('retailer.login');
Route::get('/retailer/password/reset',[App\Http\Controllers\Admin\RetailerUserController::class,'forgotPassword'])->name('retailer.forgot.password');

/* Route::get('/{lang}', function ($lang,$title='login') {
    Illuminate\Support\Facades\App::setlocale($lang);
    return view('home',['title' => '','elementActive'=>'']);
})->middleware('auth'); */
