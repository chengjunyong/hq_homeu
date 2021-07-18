@extends('layouts.app')
<title>Manual Branch Stock Ordering</title>
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

  #branch_product_list_paginate{
    margin-top: 15px;
  }

  #branch_product_list_length,#branch_product_list_filter{

    margin-bottom:10px;
  }
</style>
<div class="container">
  <h2 align="center">Manual Branch Stock Ordering</h2>
  <div class="card" style="border-radius: 1.25rem;bottom:15px;margin-top: 15px;">
    <div class="card-title" style="padding: 10px">
      <h4>Branch Stock</h4>
    </div>

    <div style="margin-left: 5px;">
      <div class="row">
        <div class="col-md-3">
          <select name="from" id="from" class="form-control">
            <option value="0" {{ (isset($_GET['from']) && $_GET['from'] == 0) ? 'selected' : '' }}>HQ Warehouse</option>
            @foreach($branch as $result)
              <option value="{{$result->id}}" {{ (isset($_GET['from']) && $result->id == $_GET['from']) ? 'selected' : '' }}>{{$result->branch_name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-1" style="font-size:25px;text-align: center">
          <i class="fa fa-random"></i>
        </div>
        <div class="col-md-3">
          <select name="to" id="to" class="form-control" id="to">
            <option {{ (isset($_GET['branch_id']) && $_GET['branch_id'] == 0) ? 'selected' : '' }}>No Branch Selected</option>
            @foreach($branch as $result)
              <option value="{{$result->id}}" {{ (isset($_GET['branch_id']) && $result->id == $_GET['branch_id']) ? 'selected' : '' }}>{{$result->branch_name}}</option>
            @endforeach
              <option value="hq" {{ (isset($_GET['branch_id']) && $_GET['branch_id'] == 'hq') ? 'selected' : '' }}>HQ Warehouse</option>
          </select>
        </div>
      </div> 
      <div class="row" style="margin-top: 20px;">
        <div class="col-md-12">
          <select class="form-control" id="branch_order_list" style="width:17%;float:right;margin-right:20px;">
            @foreach($branch as $result)
              <option value="{{$result->id}}">{{$result->branch_name}}</option>
            @endforeach
          </select>
          <button class="btn btn-primary" id="order_list" style="float:right;margin-right:5px">Order List</button>
        </div>
      </div> 
    </div>

    <div class="card-body">
      <table id="branch_product_list" class="table-striped" style="width: 100%">
        <thead>
          <tr style="font-weight: bold;">
            <td>Barcode</td>
            <td style="width:25%">Product Name</td>
            <td align="right">Cost</td>
            <td align="right">Price</td>
            <td align="center">Stock Qty</td>
            <td align="center" style="width:7%;">Recommend Qty</td>
<!-- 
 -->
            <td align="center">Add To List</td>
          </tr>
        </thead>
        <tbody>
          @foreach($branch_product as $key => $result)
            <tr>
              <td>{{$result->barcode}}</td>
              <td style="width:25%">{{$result->product_name}}</a></td>
              <td align="right">{{number_format($result->cost,2)}}</td>
              <td align="right">{{number_format($result->price,2)}}</td>
              <td align="center">{{$result->quantity}}</td>
              <td align="center" style="width:7%;">{{$result->reorder_quantity}}</td>
<!--               <td align="center">{{date("d-M-Y h:i:s A",strtotime($result->updated_at))}}</td> -->
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
  var table = $('#branch_product_list').DataTable({
    responsive: true,
    lengthMenu: [25,50,100],
    order: [[ 6, "asc" ]],
  });

  $("#to").change(function(){
    window.location.assign("{{route('getManualStockOrder')}}?branch_id="+$(this).val()+"&from="+$("#from").val());
  });

  $(".add-list").click(function(){
    quantityHandle($(this).val());
  });

  $("thead,#branch_product_list_paginate").click(function(){
    $(".add-list").click(function(){
      quantityHandle($(this).val());
    });
  });

  $("input[type=search]").keyup(()=>{
    $(".add-list").click(function(){
      quantityHandle($(this).val());
    });
  });

  $("#order_list").click(function(){
    let a = $("#branch_order_list").val();
    window.location.assign(`{{route('getManualOrderList')}}?id=${a}`);
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
        $.get('{{route('ajaxAddManualStockOrder')}}',
        {
          'branch_product_id' : target,
          'from' : $("#from").val(),
          'to' : $("#to").val(),
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