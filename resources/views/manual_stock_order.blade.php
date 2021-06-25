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
            <option value="0" selected>HQ Warehouse</option>
            @foreach($branch as $result)
              <option value="{{$result->id}}">{{$result->branch_name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-1" style="font-size:25px;text-align: center">
          <i class="fa fa-random"></i>
        </div>
        <div class="col-md-3">
          <select name="to" id="to" class="form-control" id="to">
            @foreach($branch as $result)
              <option value="{{$result->id}}" {{ ($result->id == $_GET['branch_id']) ? 'selected' : '' }}>{{$result->branch_name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col" style="text-align:right;margin-right: 20px;">
          <button class="btn btn-primary" onclick="window.location.assign('{{route('getManualOrderList')}}')">Order List</button>
        </div>
      </div>  
    </div>

    <div class="card-body">
      <table id="branch_product_list" class="table-striped" style="width: 100%">
        <thead>
          <tr style="font-weight: bold;">
            <td>No</td>
            <td>Barcode</td>
            <td>Product Name</td>
            <td align="right">Cost</td>
            <td align="right">Price</td>
            <td align="center">Stock Quantity</td>
            <td align="center" style="width:7%;">Reorder Level</td>
            <td align="center" style="width:7%;">Reorder Recommend Quantity</td>
            <td align="center">Add To List</td>
          </tr>
        </thead>
        <tbody>
          @foreach($branch_product as $key => $result)
            <tr>
              <td>{{$key+1}}</td>
              <td>{{$result->barcode}}</td>
              <td>{{$result->product_name}}</a></td>
              <td align="right">{{number_format($result->cost,2)}}</td>
              <td align="right">{{number_format($result->price,2)}}</td>
              <td align="center">{{$result->quantity}}</td>
              <td align="center" style="width:7%;">{{$result->reorder_level}}</td>
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
  $('#branch_product_list').DataTable({
    responsive: true,
    lengthMenu: [25,50,100],
  });

  $("#to").change(function(){
    window.location.assign("{{route('getManualStockOrder')}}?branch_id="+$(this).val());
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