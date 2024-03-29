@extends('layouts.app')
<title>Home</title>
@section('content')
<style>
  #logout{
    border:none;
    background: none;
    font-size: 27px;
    font-weight: 700;
    margin: 15px 0px 10px 5px;
  }
  body{
    overflow-x: hidden;
  }

  .float{
    position:fixed;
    width:60px;
    height:60px;
    bottom:40px;
    right:40px;
    background-color:#0C9;
    color:#FFF;
    border-radius:50px;
    text-align:center;
    box-shadow: 2px 2px 3px #999;
    z-index: 99;
  }

  .float2{
    position:fixed;
    width:60px;
    height:60px;
    bottom:110px;
    right:40px;
    background-color:#92cb18;
    color:#FFF;
    border-radius:50px;
    text-align:center;
    box-shadow: 2px 2px 3px #999;
    z-index: 99;
    cursor: pointer;
  }

  .col-md-6{
    margin-top: 10px;
    margin-bottom: 10px;
  }

  .center{
    text-align: center;
  }

  .center>button{
    padding:10px 40px;
  }

  .first{
    float:left;
    font-size: 120px;
  }

  .icon{
    border-radius: 50px;
  }

  .icon>.card-body{
     margin: 0 auto;
  }

  .col-md-4{
    margin-top: 25px;
  }

  .branch-color{
    color:#ea0a0a;
  }

  .btn-branch{
    background-color:#ea0a0a; 
  }

  .product-color{
    color:#693aa7;
  }

  .btn-product{
    background-color:#693aa7;
  }

  .stock-color{
    color:#3fa57f;
  }

  .btn-stock{
    background-color: #3fa57f;
  }

  .sales-color{
    color:#7dbb02;
  }

  .btn-sales{
    background-color: #7dbb02;
  }

  .other-color{
    color:#239ee6;
  }

  .btn-other{
    background-color: #239ee6;
  }

</style>
<h4 align="center" style="font-size: 1.75rem;">POS Management System</h4><br/>
<div class="float">
  <form method="post" action="{{route('logout')}}">
    @csrf
    <button id="logout"><i class="fa fa-sign-out-alt"></i></button>
  </form>
</div>

<div class="float2" style="display:none">
  <i class="fa fa-arrow-left" style="font-size: 40px;margin-top: 10px"></i>
</div>

<div class="container" id="main_menu">
  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Branch</h4>
          <div class="col-md-5 branch-color">
            <i class="fa fa-store-alt first"></i>
          </div>
          <div class="col-md-7" style="float:right">
            <ul>
              <li>Branch Setup</li>
              <li>Check Stock List</li>
              <li>Branch Reorder Setting</li>
              <li>Branch Stock Alert</li>
            </ul>
            <div class="center">
              <button class="btn btn-primary btn-branch" id="branch_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Product</h4>
          <div class="col-md-5 product-color">
            <i class="fa fa-shopping-bag first"></i>
          </div>
          <div class="col-md-7" style="float:right">
            <ul>
              <li>Product List</li>
              <li>Add Product</li>
              <li>Modify Product</li>
              <li>Check Product Detail</li>
            </ul>
            <div class="center">
              <button class="btn btn-primary btn-product" id="product_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Warehouse</h4>
          <div class="col-md-5 stock-color">
            <i class="fa fa-warehouse first"></i>
          </div>
          <div class="col-md-7" style="float:right">
            <ul>
              <li>Warehouse Stock List</li>
              <li>Stock Reorder Setting</li>
              <li>Warehouse Restock</li>
              <li>Purchase Order</li>
            </ul>
            <div class="center">
              <button class="btn btn-primary btn-stock" id="stock_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Report</h4>
          <div class="col-md-5 sales-color">
            <i class="fa fa-file-invoice-dollar first"></i>
          </div>
          <div class="col-md-7" style="float:right">
            <ul>
              <li>Branch Sales</li>
              <li>Report</li>
              <li>Sales Sync Setting</li>
              <li>Export Report</li>
            </ul>
            <div class="center">
              <button class="btn btn-primary btn-sales" id="sales_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Audit Report</h4>
          <div class="col-md-5 audit-color">
            <i class="fa fa-user-secret first"></i>
          </div>
          <div class="col-md-7" style="float:right">
            <ul>
              <li>Item Audit</li>
              <li>Report</li>
              <li>Stock Movement</li>
              <li>Item Based</li>
            </ul>
            <div class="center">
              <button class="btn btn-primary btn-other" id="audit_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Other</h4>
          <div class="col-md-5 other-color">
            <i class="fa fa-wrench first"></i>
          </div>
          <div class="col-md-7" style="float:right">
            <ul>
              <li>User Access Control</li>
              <li>Supplier</li>
              <li>Email Settings</li>
              <li>Export & Import</li>
            </ul>
            <div class="center">
              <button class="btn btn-primary btn-other" id="other_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container" id="product_menu" style="display: none">
  <div class="row">

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Product Check List</h4>
            </div>
            <div class="col">
              <a href="{{route('getProductList')}}"><i class="fa fa-box first product-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Add Product</h4>
            </div>
            <div class="col">
              <a href="{{route('getAddProduct')}}"><i class="fa fa-plus-circle first product-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Product Price & Cost Setting</h4>
            </div>
            <div class="col">
              <a href="{{route('getProductConfig')}}"><i class="fa fa-dollar-sign first product-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Voucher Management</h4>
            </div>
            <div class="col">
              <a href="{{route('getVoucher')}}"><i class="fa fa-search-dollar first product-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Supplier's Product Checklist</h4>
            </div>
            <div class="col">
              <a href="{{route('getSupplierProduct')}}"><i class="fa fa-people-carry first product-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Hamper Management</h4>
            </div>
            <div class="col">
              <a href="{{route('getHamperList')}}"><i class="fa fa-gift first product-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>

<div class="container" id="branch_menu" style="display: none">
  <div class="row">

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Branch Setup</h4>
            </div>
            <div class="col">
              <a href="{{route('getBranch')}}"><i class="fa fa-sliders-h first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    @if($branch->id)
      <div class="col-md-4">
        <div class="card icon">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <h4 class="card-title">Branch Stock Checklist</h4>
              </div>
              <div class="col">
                <a href="{{route('getBranchStockList',$branch->id)}}"><i class="fa fa-clipboard-list first branch-color"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Branch Restock List</h4>
            </div>
            <div class="col">
              <a href="{{route('getBranchRestock')}}"><i class="fa fa-truck-loading first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Delivery Order History</h4>
            </div>
            <div class="col">
              <a href="{{route('getDoHistory')}}"><i class="fa fa-shipping-fast first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Restock Confirmation</h4>
            </div>
            <div class="col">
              <a href="{{route('getRestocklist')}}"><i class="fa fa-clipboard-check first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Branch Restock History</h4>
            </div>
            <div class="col">
              <a href="{{route('getRestockHistory')}}"><i class="fa fa-history first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Damaged Stock List</h4>
            </div>
            <div class="col">
              <a href="{{route('getDamagedStock')}}"><i class="fa fa-house-damage first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Lost List</h4>
            </div>
            <div class="col">
              <a href="{{route('getStockLost')}}"><i class="fa fa-window-close first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Branch Check Stock History</h4>
            </div>
            <div class="col">
              <a href="{{route('getBranchStockHistory')}}"><i class="fas fa-barcode first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Manual Stock Order</h4>
            </div>
            <div class="col">
              <a href="{{route('getManualStockOrder')}}?branch_id=0&from=0"><i class="fas fa-file-signature first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Adjustment</h4>
            </div>
            <div class="col">
              <a href="{{route('getStockAdjustment')}}"><i class="fas fa-folder-plus first branch-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="container" id="sales_menu" style="display: none">
  <div class="row">

  <!--     <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Total Sales Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getSalesReport')}}"><i class="fas fa-chart-bar first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <!-- <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Daily Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getDailyReport')}}"><i class="fas fa-calendar-day first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <!-- <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Branch Sales Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getBranchReport')}}"><i class="fas fa-th-large first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Branch Sales Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getBranchCashierReport')}}"><i class="fas fa-th-large first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Department & Category Sales Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getDepartmentAndCategoryReport')}}"><i class="fas fa-chart-pie first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Balance Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getStockBalance')}}"><i class="fas fa-cubes first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Product Sales Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getProductSalesReport')}}"><i class="fab fa-product-hunt first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Daily Sales Transaction Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getDailySalesTransactionReport')}}"><i class="fas fa-file-invoice first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Refund Detail Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getRefundReport')}}"><i class="fas fa-hand-holding-usd first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Monthly Refund Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getMonthlyRefundReport')}}"><i class="fas fa-calendar-times first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Date Range Sales Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getDateRangeSalesReport')}}"><i class="fas fa-money-bill-wave first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">PandaMart & GrabMart Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getDeliveryReport')}}"><i class="fas fa-biking first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Kawalan Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getStockBalanceBranchReport')}}"><i class="fas fa-layer-group first sales-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="container" id="other_menu" style="display: none">
  <div class="row">

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Supplier</h4>
            </div>
            <div class="col">
              <a href="{{route('getSupplier')}}"><i class="fas fa-user-friends first other-color" ></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($user->user_type == 1)
      <div class="col-md-4">
        <div class="card icon">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <h4 class="card-title">User Access Control</h4>
              </div>
              <div class="col">
                <a href="{{route('getUserAccessControl')}}"><i class="fas fa-users first other-color"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Checking</h4>
            </div>
            <div class="col">
              <a href="{{route('getCheckStockPage')}}"><i class="fas fa-barcode first other-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">User Profile</h4>
            </div>
            <div class="col">
              <a href="{{route('getUserProfile')}}"><i class="fas fa-user first other-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="container" id="stock_menu" style="display: none">
  <div class="row">

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Warehouse Stock</h4>
            </div>
            <div class="col">
              <a href="{{route('getWarehouseStockList')}}"><i class="fas fa-boxes first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Purchase Order</h4>
            </div>
            <div class="col">
              <a href="{{route('getPurchaseOrder')}}"><i class="fas fa-file-invoice-dollar first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Manual Issue Purchase Order</h4>
            </div>
            <div class="col">
              <a href="{{route('getManualIssuePurchaseOrder')}}"><i class="fas fa-shopping-cart first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Purchase Order History</h4>
            </div>
            <div class="col">
              <a href="{{route('getPurchaseOrderHistory')}}"><i class="fas fa-history first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Warehouse Restock</h4>
            </div>
            <div class="col">
              <a href="{{route('getPoList')}}"><i class="fas fa-folder-plus first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Warehouse Restock History</h4>
            </div>
            <div class="col">
              <a href="{{route('getWarehouseRestockHistory')}}"><i class="fas fa-paste first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Purchase (Invoice)</h4>
            </div>
            <div class="col">
              <a href="{{route('getStockPurchase')}}"><i class="fas fa-file-invoice first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Purchase History</h4>
            </div>
            <div class="col">
              <a href="{{route('getInvoicePurchaseHistory')}}"><i class="fas fa-clock first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Warehouse Check Stock History</h4>
            </div>
            <div class="col">
              <a href="{{route('getWarehouseStockHistory')}}"><i class="fas fa-barcode first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Good Return</h4>
            </div>
            <div class="col">
              <a href="{{route('getGoodReturn')}}"><i class="fas fa-house-damage first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Good Return History</h4>
            </div>
            <div class="col">
              <a href="{{route('getGoodReturnHistory')}}"><i class="fas fa-clipboard first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Write Off</h4>
            </div>
            <div class="col">
              <a href="{{route('getStockWriteOff')}}"><i class="fas fa-window-close first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Write Off History</h4>
            </div>
            <div class="col">
              <a href="{{route('getWriteOffHistory')}}"><i class="fas fa-clipboard-list first stock-color"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    

  </div>
</div>

<div class="container" id="audit_menu" style="display: none">
  <div class="row">
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Item Movement Report</h4>
            </div>
            <div class="col">
              <a href="{{route('stockMovementMenu')}}"><i class="fa fa-th first audit-color" ></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Stock Checking List</h4>
            </div>
            <div class="col">
              <a href="{{route('stockCheckList')}}"><i class="fa fa-sitemap first audit-color" ></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
$(document).ready(function(){
  $(".float2").click(function(){
    $(this).fadeOut("fast");
    $("#product_menu,#branch_menu,#stock_menu,#sales_menu,#other_menu,#audit_menu").fadeOut("fast",function(){
      $("#main_menu").fadeIn("fast");
    });
  });

  showMenu("product_btn","product_menu");
  showMenu("branch_btn","branch_menu");
  showMenu("sales_btn","sales_menu");
  showMenu("other_btn","other_menu");
  showMenu("stock_btn","stock_menu");
  showMenu("audit_btn","audit_menu");

  let a = "{{$target}}";
  if(a != "na"){
    changeMenu(a);
  }

  function showMenu(target_btn,target_menu){
    $("#"+target_btn).click(function(){
      $("#main_menu").fadeOut("fast",function(){
        $("#"+target_menu).fadeIn("fast");
        $(".float2").fadeIn("fast");
      });
    });
  }

  function changeMenu(target_menu){
    $("#main_menu").fadeOut("fast",function(){
      $("#"+target_menu).fadeIn("fast");
      $(".float2").fadeIn("fast");
    });
  }

});
</script>
@endsection
