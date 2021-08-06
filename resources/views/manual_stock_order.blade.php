@extends('layouts.app')
<title>Manual Branch Stock Ordering</title>
@section('content')
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
      <h4>Branch Stock Selector</h4>
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
            <option value="null" {{ (isset($_GET['branch_id']) && $_GET['branch_id'] == 0) ? 'selected' : '' }}>No Branch Selected</option>
            @foreach($branch as $result)
              <option value="{{$result->id}}" {{ (isset($_GET['branch_id']) && $result->id == $_GET['branch_id']) ? 'selected' : '' }}>{{$result->branch_name}}</option>
            @endforeach
              <option value="hq" {{ (isset($_GET['branch_id']) && $_GET['branch_id'] == 'hq') ? 'selected' : '' }}>HQ Warehouse</option>
          </select>
        </div>
      </div> 
      <div class="row" style="margin-top: 20px;">
        <div class="col-md-12" style="margin-top: 10px;">
          <h4 align="center">Branch Stock Detail</h4>
        </div>
        <div class="col-md-6">
          <form method="get" action="{{route('getManualStockOrder')}}?branch_id=null&from=0" id="search_form">
            <input type="text" name="branch_id" value="{{$_GET['branch_id']}}" hidden/>
            <input type="text" name="from" value="{{$_GET['from']}}" hidden />
            <button type="button" class="btn btn-primary" onclick="window.location.assign('{{route('getManualStockOrder')}}?branch_id=0&from=0')" style="float:left;margin-right: 5px;margin-left: 5px;">Reset</button>
            <input type="text" name="search" class="form-control" placeholder="Barcode / Name" value="{{ (isset($_GET['search']) ? $_GET['search'] : '') }}" style="margin-left: 5px;width:50%;"/>
          </form>
        </div>
        <div class="col-md-6">
          <select class="form-control" id="branch_order_list" style="width:35%;float:right;margin-right:20px;">
            @foreach($branch as $result)
              <option value="{{$result->id}}">{{$result->branch_name}}</option>
            @endforeach
              <option value="0">HQ Warehouse</option>
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
            <td align="center">Add To List</td>
          </tr>
        </thead>
        <tbody>
          @if(isset($_GET['search']) && $_GET['search'] != "" && $target != null && ($page == 1 || $page == null))
            <tr>
                <td>{{$target->barcode}}</td>
                <td style="width:25%">{{$target->product_name}}</a></td>
                <td align="right">{{number_format($target->cost,2)}}</td>
                <td align="right">{{number_format($target->price,2)}}</td>
                <td align="center">{{$target->quantity}}</td>
                <td align="center" style="width:7%;">{{$target->reorder_quantity}}</td>
                <td align="center"><button class="btn btn-primary add-list" value="{{$target->id}}">Add</button></td>
            </tr>
          @endif
          @foreach($branch_product as $key => $result)
            <tr>
              <td>{{$result->barcode}}</td>
              <td style="width:25%">{{$result->product_name}}</a></td>
              <td align="right">{{number_format($result->cost,2)}}</td>
              <td align="right">{{number_format($result->price,2)}}</td>
              <td align="center">{{$result->quantity}}</td>
              <td align="center" style="width:7%;">{{$result->reorder_quantity}}</td>
              <td align="center"><button class="btn btn-primary add-list" value="{{$result->id}}">Add</button></td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="float:right;margin-top:15px;">
        @if(isset($_GET['search']) && $search != null)
          {{ $branch_product->appends(['branch_id'=>$branch_id,'from'=>$from,'search'=>$search])->links() }}
        @else
          {{ $branch_product->appends(['branch_id'=>$branch_id,'from'=>$from])->links() }}
        @endif
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function(){
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

  $("input[name=search]").keydown(function(e){
    if(e.keyCode == 13){
      $("#search_form").submit();
    }
  });

});

function quantityHandle(target){
  Swal.fire({
    title : 'Quantity Order',
    input : 'number',
    inputAttributes : {
      step: 0.01,
      min: 0.01,
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
        if(result <= 0){
          return 'Quantity cannot smaller than 0';
        }else{
          return null;
        }
      }
    },

  });
}
</script>


@endsection