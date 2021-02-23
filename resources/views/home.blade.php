@extends('layouts.app')

@section('content')
<style>
  #logout{
    border:none;
    background: none;
    font-size: 27px;
    font-weight: 700;
    margin: 10px 0px 10px 0px;
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
          <div class="col-md-5">
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
              <button class="btn btn-primary" id="branch_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Product</h4>
          <div class="col-md-5">
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
              <button class="btn btn-primary" id="product_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Stock</h4>
          <div class="col-md-5">
            <i class="fa fa-warehouse first"></i>
          </div>
          <div class="col-md-7" style="float:right">
            <ul>
              <li>Warehouse Stock List</li>
              <li>Stock Reorder Setting</li>
              <li>Stock Refill</li>
              <li>Stock Check Module</li>
            </ul>
            <div class="center">
              <button class="btn btn-primary" id="stock_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Sales</h4>
          <div class="col-md-5">
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
              <button class="btn btn-primary" id="sales_btn">Access</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Other</h4>
          <div class="col-md-5">
            <i class="fa fa-wrench first"></i>
          </div>
          <div class="col-md-7" style="float:right">
            <ul>
              <li>Pending</li>
              <li>Pending</li>
              <li>Pending</li>
              <li>Pending</li>
            </ul>
            <div class="center">
              <button class="btn btn-primary" id="other_btn">Access</button>
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
              <a href="{{route('getProductList')}}"><i class="fa fa-box first"></i></a>
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
              <a href="{{route('getAddProduct')}}"><i class="fa fa-plus-circle first"></i></a>
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
              <a href="{{route('getProductConfig')}}"><i class="fa fa-dollar-sign first"></i></a>
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
              <a href="{{route('getBranch')}}"><i class="fa fa-sliders-h first"></i></a>
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
                <a href="{{route('getBranchStockList',$branch->id)}}"><i class="fa fa-clipboard-list first"></i></a>
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
              <a href="{{route('getBranchRestock')}}"><i class="fa fa-truck-loading first"></i></a>
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
              <a href="{{route('getDoHistory')}}"><i class="fa fa-shipping-fast first"></i></a>
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
              <a href="{{route('getRestocklist')}}"><i class="fa fa-clipboard-check first"></i></a>
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
              <a href="{{route('getRestockHistory')}}"><i class="fa fa-history first"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>

<div class="container" id="sales_menu" style="display: none">
  <div class="row">

    <div class="col-md-4">
      <div class="card icon">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h4 class="card-title">Sales Report</h4>
            </div>
            <div class="col">
              <a href="{{route('getSalesReport')}}"><i class="fas fa-chart-bar first"></i></a>
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
    $("#product_menu,#branch_menu,#stock_menu,#sales_menu,#other_menu").fadeOut("fast",function(){
      $("#main_menu").fadeIn("fast");
    });
  });

  showMenu("product_btn","product_menu");
  showMenu("branch_btn","branch_menu");
  showMenu("sales_btn","sales_menu");
  showMenu("other_btn","other_menu");
  showMenu("stock_btn","stock_menu");

  function showMenu(target_btn,target_menu){
    $("#"+target_btn).click(function(){
      $("#main_menu").fadeOut("fast",function(){
        $("#"+target_menu).fadeIn("fast");
        $(".float2").fadeIn("fast");
      });
    });
  }

});
</script>
@endsection
