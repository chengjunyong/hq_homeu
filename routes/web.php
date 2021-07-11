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
Route::get('/editBranch','BranchController@editBranch')->name('editBranch');
Route::get('/branchstocklist/{branch_id}','BranchController@getBranchStockList')->name('getBranchStockList');
Route::get('/searchbranchproduct','BranchController@searchBranchProduct')->name('searchBranchProduct');
Route::get('/modifybranchstock/{branch_id}/{id}','BranchController@getModifyBranchStock')->name('getModifyBranchStock');
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
Route::get('/branch_stock_history', 'BranchController@getBranchStockHistory')->name('getBranchStockHistory');
Route::get('/branch_stock_history_detail', 'BranchController@getBranchStockHistoryDetail')->name('getBranchStockHistoryDetail');
Route::get('/warehouse_stock_history', 'WarehouseController@getWarehouseStockHistory')->name('getWarehouseStockHistory');
Route::get('/warehouse_stock_history_detail', 'WarehouseController@getWarehouseStockHistoryDetail')->name('getWarehouseStockHistoryDetail');
Route::get('/ManualStockOrder','BranchController@getManualStockOrder')->name('getManualStockOrder');
Route::get('/AddManualStockOrder','BranchController@ajaxAddManualStockOrder')->name('ajaxAddManualStockOrder');
Route::get('/ManualOrderList','BranchController@getManualOrderList')->name('getManualOrderList');
Route::post('/ManualOrderList','BranchController@postManualOrderList')->name('postManualOrderList');
Route::get('/RemoveItem','BranchController@ajaxRemoveItem')->name('ajaxRemoveItem');

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
Route::get('/voucher','ProductController@getVoucher')->name('getVoucher');
Route::post('/voucher','ProductController@postVoucher')->name('postVoucher');
Route::post('/DeleteProduct','ProductController@postDeleteProduct')->name('postDeleteProduct');
Route::get('/Import','ProductController@getImport')->name('getImport');
Route::post('/ImportProcess','ProductController@postImport')->name('postImport');

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
Route::post('/exportSalesReport', 'SalesController@exportSalesReport')->name('exportSalesReport');
Route::post('/exportBranchReport', 'SalesController@exportBranchReport')->name('exportBranchReport');
Route::post('/exportBranchStockReport', 'SalesController@exportBranchStockReport')->name('exportBranchStockReport');
Route::post('/exportWarehouseStockReport', 'SalesController@exportWarehouseStockReport')->name('exportWarehouseStockReport');

Route::get('/stock_balance','SalesController@getStockBalance')->name('getStockBalance');
Route::post('/stock_balance_report','SalesController@postStockBalanceReport')->name('postStockBalanceReport');
Route::post('/export_stock_balance', 'SalesController@exportStockBalance')->name('exportStockBalance');

Route::get('/stock_reorder','SalesController@getStockReorder')->name('getStockReorder');
Route::post('/stock_report_report','SalesController@getStockReorderReport')->name('getStockReorderReport');

Route::get('/ProductSalesReport','SalesController@getProductSalesReport')->name('getProductSalesReport');
Route::post('/ProductSalesReport','SalesController@postProductSalesReport')->name('postProductSalesReport');
Route::post('/ExportProductSalesReport','SalesController@exportProductSalesReport')->name('exportProductSalesReport');

Route::get('/DailySalesTransactionReport','SalesController@getDailySalesTransactionReport')->name('getDailySalesTransactionReport');
Route::post('/DailySalesTransactionReport','SalesController@postDailySalesTransactionReport')->name('postDailySalesTransactionReport');

// User access control
Route::get('/user_access_control', 'UserController@getUserAccessControl')->name('getUserAccessControl'); 
Route::post('/createNewUser', 'UserController@createNewUser')->name('createNewUser');
Route::post('/editUser', 'UserController@editUser')->name('editUser');
Route::get('/no_access', 'UserController@getNoAccess')->name('no_access');
Route::get('/profile', 'UserController@getUserProfile')->name('getUserProfile');
Route::post('/update_profile', 'UserController@updateUserProfile')->name('updateUserProfile');

// barcode check stock
Route::get('/check_stock', 'BarcodeController@getCheckStockPage')->name('getCheckStockPage');
Route::post('/getProductByBarcode', 'BarcodeController@getProductByBarcode')->name('getProductByBarcode');
Route::post('/updateBranchStockByScanner', 'BarcodeController@updateBranchStockByScanner')->name('updateBranchStockByScanner');

// Testing page
Route::get('testingPage', 'UserController@testingPage')->name('testingPage');
Route::get('test_mail', 'HomeController@testMail')->name('testMail');

//Warehouse
Route::get('/WarehouseStockList','WarehouseController@getWarehouseStockList')->name('getWarehouseStockList');
Route::get('/AddWarehouseProduct','WarehouseController@getAddWarehouseProduct')->name('getAddWarehouseProduct');
Route::post('/AddWarehouseProduct','WarehouseController@postAddWarehouseProduct')->name('postAddWarehouseProduct');
Route::get('/EditWarehouseProduct/{id}','WarehouseController@getEditWarehouseProduct')->name('getEditWarehouseProduct');
Route::post('EditWarehouseProduct','WarehouseController@postModifyWarehouseProduct')->name('postModifyWarehouseProduct');
Route::get('/PurchaseOrder','WarehouseController@getPurchaseOrder')->name('getPurchaseOrder');
Route::get('/ajaxGetSupplier','WarehouseController@ajaxGetSupplier')->name('ajaxGetSupplier');
Route::post('/sendPurchaseOrder','WarehouseController@ajaxPO')->name('ajaxPO');
Route::get('/GeneratePurchaseOrder/{id}','WarehouseController@getGeneratePurchaseOrder')->name('getGeneratePurchaseOrder');
Route::get('/PurchaseOrderHistory','WarehouseController@getPurchaseOrderHistory')->name('getPurchaseOrderHistory');
Route::get('/PoHistoryDetail/{po_number}','WarehouseController@getPoHistoryDetail')->name('getPoHistoryDetail');
Route::get('/PoList','WarehouseController@getPoList')->name('getPoList');
Route::get('/WarehouseRestock/{po_number}','WarehouseController@getWarehouseRestock')->name('getWarehouseRestock');
Route::post('/WarehouseRestockProcess','WarehouseController@postWarehouseRestock')->name('postWarehouseRestock');
Route::get('/WarehouseRestockHistory','WarehouseController@getWarehouseRestockHistory')->name('getWarehouseRestockHistory');
Route::get('/WarehouseRestockHistoryDetail/{id}/{po_number}','WarehouseController@getWarehouseRestockHistoryDetail')->name('getWarehouseRestockHistoryDetail');
Route::get('/ManualIssuePurchaseOrder','WarehouseController@getManualIssuePurchaseOrder')->name('getManualIssuePurchaseOrder');
Route::get('/AddManualPurchaseOrder','WarehouseController@ajaxAddManualStock')->name('ajaxAddManualStock');
Route::get('/PurchaseOrderList','WarehouseController@getPurchaseOrderList')->name('getPurchaseOrderList');
Route::get('/RemovePurchaseOrderListItem','WarehouseController@ajaxRemovePurchaseOrderListItem')->name('ajaxRemovePurchaseOrderListItem');
Route::post('/GeneratePO','WarehouseController@postManualPurchaseOrderList')->name('postManualPurchaseOrderList');
Route::get('/StockPurchase','WarehouseController@getStockPurchase')->name('getStockPurchase');
Route::get('/SearchBarcode','WarehouseController@ajaxSearchBar')->name('ajaxSearchBar');
Route::get('/AddPurchaseListItem','WarehouseController@ajaxAddPurchaseListItem')->name('ajaxAddPurchaseListItem');
Route::post('/StockPurchase','WarehouseController@postStockPurchase')->name('postStockPurchase');
Route::get('/InvoicePurchaseHistory','WarehouseController@getInvoicePurchaseHistory')->name('getInvoicePurchaseHistory');
Route::get('/InvoicePurchaseHistoryDetail/{invoice_id}','WarehouseController@getInvoicePurchaseHistoryDetail')->name('getInvoicePurchaseHistoryDetail');

//Transaction Number Correction Function
Route::get('/TransactionCorrection/{target_date}/{branch_code}/{token}','SalesController@transactionCorrection');