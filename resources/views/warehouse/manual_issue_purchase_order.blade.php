@extends('layouts.app')
<title>Issue Purchase Order</title>
@section('content')
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
            @if(isset($_GET['supplier']) && $_GET['supplier'] != "")
              @foreach($supplier as $result)
                  <option value="{{$result->id}}" {{($_GET['supplier'] == $result->id) ? 'selected' : '' }}>{{$result->supplier_name}}</option>
              @endforeach
            @else
              @foreach($supplier as $result)
                  <option value="{{$result->id}}">{{$result->supplier_name}}</option>
              @endforeach
            @endif
          </select>
        </div>
        <div class="col" style="text-align:right;margin-right: 20px;">
          <button class="btn btn-primary" onclick="window.location.assign('{{route('getPurchaseOrderList')}}')">Purchase Order List</button>
        </div>
      </div>
      <div class="col-md-12">
        <form method="get" action="{{route('getManualIssuePurchaseOrder')}}" id="search_form">
          <input type="text" name="search" class="form-control" placeholder="Barcode" value="{{ (isset($_GET['search'])) ? $_GET['search'] : '' }}" style="width:20%;float:right"/>
          <input type="number" name="supplier" id="search_supplier" hidden value="{{ isset($_GET['supplier']) ? $_GET['supplier'] : $supplier->first()->id}}" />
          <button type="button" class="btn btn-primary" onclick="window.location.assign('{{route('getManualIssuePurchaseOrder')}}')" style="float:right;margin-right:5px;">Reset</button>
        </form>
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
          @if(isset($_GET['search']) && $_GET['search'] != "" && $target != null && ($page == 1 || $page == null))
            <tr>
              <td>{{$target->barcode}}</td>
              <td>{{$target->product_name}}</a></td>
              <td align="right">{{number_format($target->cost,2)}}</td>
              <td align="right">{{number_format($target->price,2)}}</td>
              <td align="center">{{$target->quantity}}</td>
              <td align="center" style="width:7%;">{{$target->reorder_quantity}}</td>
              <td align="center"><button class="btn btn-primary add-list" value="{{$target->id}}">Add</button></td>
            </tr>
          @endif
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
      <div style="float:right;margin-top:15px;">
        @if(isset($_GET['search']) && isset($_GET['supplier']))
          {{ $warehouse_stock->appends(['search'=>$_GET['search'],'supplier'=>$_GET['supplier']])->links() }}
        @elseif(isset($_GET['search']))
          {{ $warehouse_stock->appends(['search'=>$_GET['search']])->links() }}
        @elseif(isset($_GET['supplier']))
          {{ $warehouse_stock->appends(['search'=>$_GET['supplier']])->links() }}
        @else
          {{ $warehouse_stock->links() }}
        @endif
      </div>
    </div>
  </div>
</div>
<script>

$(document).ready(function(){
  $(".add-list").click(function(){
    quantityHandle($(this).val());
  });

  $("input[name=search]").keydown(function(e){
    if(e.keyCode == 13){
      $("#search_form").submit();
    }
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

  $("#supplier_id").change(function(){
    $("#search_supplier").val($(this).val());
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