<?php

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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

//Branch
Route::get('/branchsetup','BranchController@getBranch')->name('getBranch');
Route::post('/createbranch','BranchController@createBranch')->name('createBranch');
Route::get('/branchstocklist/{branch_id}','BranchController@getBranchStockList')->name('getBranchStockList');
Route::get('/searchbranchproduct','BranchController@searchBranchProduct')->name('searchBranchProduct');
Route::get('/modifybranchstock/{id}','BranchController@getModifyBranchStock')->name('getModifyBranchStock');
Route::post('/modifybranchstock','BranchController@postModifyBranchStock')->name('postModifyBranchStock');
Route::get('/restock','BranchController@getBranchRestock')->name('getBranchRestock');
Route::post('/generatedo','BranchController@postBranchStock')->name('postBranchStock');
Route::get('/printdo/{do_number}','BranchController@getPrintDo')->name('getPrintDo');
Route::get('/dohistory','BranchController@getDoHistory')->name('getDoHistory');
Route::get('/dohistorydetail/{do_number}','BranchController@getDoHistoryDetail')->name('getDoHistoryDetail');
Route::get('/restocklist','BranchController@getRestocklist')->name('getRestocklist');
Route::get('/restockconfirmation/{do_number}','BranchController@getRestockConfirmation')->name('getRestockConfirmation');
Route::post('restockconfirmation','BranchController@postRestockConfirmation')->name('postRestockConfirmation');
Route::get('/branchrestockhistory','BranchController@getRestockHistory')->name('getRestockHistory');
Route::get('/branchrestockhistorydetail/{id}','BranchController@getRestockHistoryDetail')->name('getRestockHistoryDetail');
Route::get('/damagedstock','BranchController@getDamagedStock')->name('getDamagedStock');
Route::post('/generatedamagedstock','BranchController@postDamagedStock')->name('postDamagedStock');
Route::get('/damagedstockhistory','BranchController@getDamagedStockHistory')->name('getDamagedStockHistory');
Route::get('/GR/{gr_number}','BranchController@getGenerateGR')->name('getGenerateGR');
Route::get('/stocklost','BranchController@getStockLost')->name('getStockLost');
Route::post('/generatestocklost','BranchController@postStockLost')->name('postStockLost');
Route::get('/SL/{sl_id}','BranchController@getGenerateSL')->name('getGenerateSL');
Route::get('/stocklosthistory','BranchController@getStockLostHistory')->name('getStockLostHistory');

//Product
Route::get('/productlist','ProductController@getProductList')->name('getProductList');
Route::get('/searchproduct','ProductController@searchProduct')->name('searchProduct');
Route::post('/addproduct','ProductController@ajaxAddProduct')->name('ajaxAddProduct');
Route::get('/productconfig','ProductController@getProductConfig')->name('getProductConfig');
Route::post('/setproductconfig','ProductController@postProductConfig')->name('postProductConfig');
Route::get('/addproduct','ProductController@getAddProduct')->name('getAddProduct');
Route::get('/getcategory','ProductController@ajaxGetCategory')->name('ajaxGetCategory');
Route::post('/createproduct','ProductController@postAddProduct')->name('postAddProduct');
Route::get('/getbarcode','ProductController@ajaxGetBarcode')->name('ajaxGetBarcode');
Route::get('/modifyproduct/{id}','ProductController@getModifyProduct')->name('getModifyProduct');
Route::post('/modifyproduct','ProductController@postModifyProduct')->name('postModifyProduct');

//Other
Route::get('/Supplier','OtherController@getSupplier')->name('getSupplier');
Route::get('/EditSupplier/{id}','OtherController@getEditSupplier')->name('getEditSupplier');
Route::post('/postEditSupplier','OtherController@postEditSupplier')->name('postEditSupplier');
Route::get('/createSupplier','OtherController@getCreateSupplier')->name('getCreateSupplier');
Route::post('/postcreateSupplier','OtherController@postCreateSupplier')->name('postCreateSupplier');

// Report
Route::get('/sales_report', 'SalesController@getSalesReport')->name('getSalesReport');
Route::get('/sales_report/{branch_id}/{id}', 'SalesController@getSalesReportDetail')->name('getSalesReportDetail');
Route::get('/sales_report_transaction', 'SalesController@getSalesTransactionReport')->name('getSalesTransactionReport');
Route::get('/daily_report', 'SalesController@getDailyReport')->name('getDailyReport');
Route::get('/daily_report_detail', 'SalesController@getdailyReportDetail')->name('getdailyReportDetail');
Route::get('/branch_report', 'SalesController@getBranchReport')->name('getBranchReport');
Route::get('/branch_report_detail', 'SalesController@getBranchReportDetail')->name('getBranchReportDetail');

// sync URL
Route::get('/branchSync','BranchController@branchSync')->name('branchSync');
Route::get('/branchSyncCompleted', 'BranchController@branchSyncCompleted')->name('branchSyncCompleted'); 

// User access control
Route::get('/user_access_control', 'UserController@getUserAccessControl')->name('getUserAccessControl'); 
Route::post('/createNewUser', 'UserController@createNewUser')->name('createNewUser');
Route::post('/editUser', 'UserController@editUser')->name('editUser');
Route::get('/no_access', 'UserController@getNoAccess')->name('no_access');

// barcode check stock
Route::get('/check_stock', 'BarcodeController@getCheckStockPage')->name('getCheckStockPage');
Route::post('/getProductByBarcode', 'BarcodeController@getProductByBarcode')->name('getProductByBarcode');
Route::post('/updateBranchStockByScanner', 'BarcodeController@updateBranchStockByScanner')->name('updateBranchStockByScanner');

// Testing page
Route::get('testingPage', 'UserController@testingPage')->name('testingPage');


