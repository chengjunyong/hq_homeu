@extends('layouts.app')
<title>Stock Checking List</title>
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

  td{
    padding:5px;
  }
</style>
<div class="container">
  <h2 align="center">Stock Checking List</h2>
  <div class="card" style="border-radius: 1.25rem;bottom:15px;margin-top: 15px;">
    <div class="card-title" style="padding: 10px">
      <h4>Branch Stock Check</h4>
    </div>
    <div style="margin-left: 5px;">
      <form action="{{route('stockCheckList')}}" method="get">
        <div class="row">
          <div class="col-md-3">
            <select name="branch" class="form-control">
              @foreach($branch as $result)
                <option value="{{$result->id}}" {{ (isset($_GET['from']) && $result->id == $_GET['from']) ? 'selected' : '' }}>{{$result->branch_name}}</option>
              @endforeach
              <option value="warehouse">Warehouse</option>
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
          </div>
        </div> 
      </form>
      <div class="" style="float:right;margin-right:17px;">
        <button type="button" class="btn btn-success" id="approve-all">Approve All</button>
      </div>
    </div>


    <div class="card-body">
      <table id="branch_product_list" class="table-striped" style="width: 100%">
        <thead>
          <tr style="font-weight: bold;">
            <td>No</td>
            <td>Branch</td>
            <td>Barcode</td>
            <td>Product Name</td>
            <td>Checked Qty</td>
            <td>Check By</td>
            <td>Check At</td>
            <td>Action</td>
          </tr>
        </thead>
        <tbody>
          @foreach($data as $result)
            <tr>
              <td>{{$loop->iteration}}</td>
              <td>
                @if($result->destination == 'warehouse')
                  Warehouse
                @else
                  {{$result->branch->branch_name}}
                @endif
              </td>
              <td>{{$result->barcode}}</td>
              <td>{{$result->product->product_name}}</td>
              <td>{{$result->stock_count}}</td>
              <td>{{$result->user->name}}</td>
              <td>{{date("d-M -Y h:i:s a",strtotime($result->updated_at))}}</td>
              <td>
                <button type="button" class="btn btn-primary" onclick="changeQty({{$result->id}})">Edit</button>
                <button type="button" class="btn btn-danger" onclick="remove({{$result->id}})">Remove</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>


<script>
$(document).ready(function(){
  $("#branch_product_list").DataTable();

  $("#approve-all").click(function(){
    let id = "{{$_GET['branch'] ?? ''}}";
    if(id != ''){
      Swal.fire({
        title: 'Are you sure to approve?',
        text: "You won't be able to revert",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Approve',
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire('Info','Please Wait Until Success Update','info');
          Swal.showLoading();
          $.get("{{route('approveStockCheck')}}",
          {
            '_token':"{{csrf_token()}}",
            'branch_id':id,
          },function(data){
            Swal.close();
            if(data){
              swal.fire('Success','Update Successful','success');
            }else{
              swal.fire('Failed','Update Unsuccessful, Please Contact IT Support','error');
            }
          },'json');
        }
      });
    }else{
      swal.fire('Alert','Please Filter Branch Before Approve','warning');
    }
  });
  
});

function changeQty(id)
{
  Swal.fire({
    title: 'New Quantity',
    input: 'number',
    inputAttributes: {
      autocapitalize: 'off'
    },
    showCancelButton: true,
    confirmButtonText: 'Change',
    preConfirm: (qty) => {
      $.post("{{route('changeStockCheckQuantity')}}",
      {
        '_token':"{{csrf_token()}}",
        'id':id,
        'qty':qty,
      },function(data){
        if(data){
          swal.fire('Success','Quantity Update Successful','success');
        }else{
          swal.fire('Failed','Quantity Update Unsuccessful, Please Contact IT Support','error');
        }
      },'json');
    },
  })
}

function remove(id)
{
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("{{route('deleteStockCheck')}}",
      {
        '_token':"{{csrf_token()}}",
        'id':id,
      },function(data){
        if(data){
          swal.fire('Success','Delete Successful','success');
        }else{
          swal.fire('Failed','Delete Unsuccessful, Please Contact IT Support','error');
        }
      },'json');
    }
  })
}

</script>


@endsection