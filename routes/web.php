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