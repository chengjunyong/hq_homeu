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

Route::get('/branchsetup','BranchController@getBranch')->name('getBranch');
Route::post('/createbranch','BranchController@createBranch')->name('createBranch');

Route::get('/productlist','ProductController@getProductList')->name('getProductList');
Route::get('/searchproduct','ProductController@searchProduct')->name('searchProduct');
Route::post('/addproduct','ProductController@ajaxAddProduct')->name('ajaxAddProduct');