@extends('layouts.app')
<title>Issue Purchase Order</title>
@section('content')
<script>
  Swal.fire({
    title: 'Fetching Product',
    html: 'Please wait, we are loading your product list.<br/><br/><b>Approximate In 1-2 Minutes</b>',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });
</script>
<style>
  .container{
    max-width: 90%;
  }

  #warehouse_stock_list_paginate{
    margin-top: 15px;
  }

  #branch_product_list_length,#branch_product_list_filter{

    margin-bottom:10px;
  }
</style>
<div class="container">
  <h2 align="center">Manual Issue Purchase Order</h2>
  <div class="card" style="border-radius: 1.25rem;bottom:15px;margin-top: 15px;">
    <div class="card-title" style="padding: 10px">
      <h4>Warehouse Stock</h4>
    </div>

    <div style="margin-left: 5px;">
      <div class="row">
        <div class="col">
          <h5>Supplier</h5>
          <select name="supplier_id" id="supplier_id" class="form-control">
            @foreach($supplier as $result)
              <option value="{{$result->id}}">{{$result->supplier_name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col" style="text-align:right;margin-right: 20px;">
          <button class="btn btn-primary" onclick="window.location.assign('{{route('getPurchaseOrderList')}}')">Purchase Order List</button>
        </div>
      </div>  
    </div>

    <div class="card-body">
      <table id="warehouse_stock_list" class="table-striped" style="width: 100%">
        <thead>
          <tr style="font-weight: bold;">
            <td>Barcode</td>
            <td>Product Name</td>
            <td align="right">Cost</td>
            <td align="right">Price</td>
            <td align="center">Stock Qty</td>
            <td align="center" style="width:7%;">Recommend Qty</td>
            <td align="center">Add To List</td>
          </tr>
        </thead>
        <tbody>
          @foreach($warehouse_stock as $key => $result)
            <tr>
              <td>{{$result->barcode}}</td>
              <td>{{$result->product_name}}</a></td>
              <td align="right">{{number_format($result->cost,2)}}</td>
              <td align="right">{{number_format($result->price,2)}}</td>
              <td align="center">{{$result->quantity}}</td>
              <td align="center" style="width:7%;">{{$result->reorder_quantity}}</td>
              <td align="center"><button class="btn btn-primary add-list" value="{{$result->id}}">Add</button></td>
            </tr>
          @endforeach
        </tbody>
      </table>

    </div>
  </div>
</div>
<script>

$(document).ready(function(){
  Swal.close();

  $('#warehouse_stock_list').DataTable({
    responsive: true,
    lengthMenu: [25,50,100],
  });

  $(".add-list").click(function(){
    quantityHandle($(this).val());
  });

  $("thead,#warehouse_stock_list_paginate").click(function(){
    $(".add-list").click(function(){
      quantityHandle($(this).val());
    });
  });

  $("input[type=search]").keyup(()=>{
    $(".add-list").click(function(){
      quantityHandle($(this).val());
    });
  });

});

function quantityHandle(target){
    Swal.fire({
    title : 'Quantity Order',
    input : 'number',
    inputAttributes : {
      step: 1,
      min: 1,
    },
    confirmButtonText : 'Add',
    showLoaderOnConfirm : true,
    preConfirm: (result) => {
      if(result != 0 && result != '' && result != null){
        $.get('{{route('ajaxAddManualStock')}}',
        {
          'supplier_id' : $("#supplier_id").val(),
          'warehouse_stock_id' : target,
          'order_quantity' : result,
        },function(data){
          if(data == true || data == 'true'){
            Swal.fire({
              title : 'Add To List Successful',
              icon : 'success',
            });
          }else{
            Swal.fire({
              title : 'Something Wrong, Please Contact IT Support',
              icon : 'error',
            });
          }

        },'json');
      }
    },
    inputValidator: (result) => {
      if(result == ''){
        return null;
      }else{
        if(!Number.isInteger(+result) || result <= 0){
          return 'Must be a real number & Quantity cannot smaller than 0';
        }else{
          return null;
        }
      }

    },

  });
}

</script>
@endsection