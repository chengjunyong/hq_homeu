<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// branch sync
Route::post('/branchSync', 'api@branchSync')->name('branchSync');
Route::post('/branchSyncCompleted', 'api@branchSyncCompleted')->name('branchSyncCompleted');
Route::post('/branchSyncProductList', 'api@syncBranchProductList')->name('syncBranchProductList');
Route::post('/branchSyncProductListCompleted', 'api@branchSyncProductListCompleted')->name('branchSyncProductListCompleted');

// New function
Route::post('/newBranchSync', 'api@newBranchSync')->name('newBranchSync');


//CronJob
Route::get('/PriceSync','api@CronPriceSync')->name('CronPriceSync');
Route::get('/StockLog','api@dailyRecordStockBalance')->name('dailyRecordStockBalance');

// Calculate every time
Route::get('/recalculate-branch-stock','api@recalculateBranchStock')->name('recalculateBranchStock');


