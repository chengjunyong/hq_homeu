@extends('layouts.app')
<title>Manual Order List Detail</title>
@section('content')
<style>
  .container{
    min-width: 95%;
  }

  td{
    padding:5px;
  }
</style>
<div class="container" style="padding-bottom: 5vh;">
  <div class="card" style="margin-top: 10px">

    <div class="row">
      <div class="col-md-3">
        <div class="card-title">
          <h4>Manual Order List Detail</h4>
        </div>
      </div>
      <div class="col-md-9">
        <form action="{{route('importRestockList')}}" method="post" enctype="multipart/form-data">
          @csrf
          <div class="col-md-12" style="margin-top:10px;text-align:right">
            <input hidden class="form-control" type="text" name="to_branch_id" value="{{$to->id}}">
            <input hidden class="form-control" type="text" name="from_branch_id" value="{{$from->id}}">
            <input type="file" name="restock_list" class="form-control" style="width:30%;float:right;margin-left:5px;" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required/>
            <input type="submit" class="btn btn-success" value="Import Restock Item" style="margin-top: 3px;">
          </div>
        </form>
      </div>
    </div>

    <form id="form2">
      @csrf
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <label>From:</label>
            <input readonly class="form-control" type="text" name="from" value="{{$from->branch_name}}">
            <input hidden class="form-control" type="text" name="from_branch_id" value="{{$from->id}}">
          </div>
          <div class="col-md-6">
            <label>To:</label>
            <input readonly class="form-control" type="text" name="to" value="{{$to->branch_name}}">
            <input hidden class="form-control" type="text" name="to_branch_id" value="{{$to->id}}">
          </div>
          <div class="col-md-6">
            <label>Total Items:</label>
            <input readonly class="form-control" type="text" name="total_item" value="{{$total_item}}">
          </div>
          <div class="col-md-6">
            <label>Date Issue:</label>
            <input readonly class="form-control" type="text" name="created_at" value="{{$tmp[0]->created_at ?? null}}">
          </div>

        <div style="overflow-y: auto;height:425px;margin-top:25px;width:100%">
          <table class="table-responsive" style="width:100%;display: table !important;">
            <thead style="background-color: #b8b8efd1">
              <tr>
                <td>No</td>
                <td style="width:20%">Barcode</td>
                <td>Product Name</td>
                <td align="center">Measurement</td>
                <td align="right">Cost</td>
                <td align="right">Price Per Unit</td>
                <td align="center">Total Quantity Order</td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              @foreach($tmp as $key => $result)
                <tr id="{{$result->id}}">
                  <td>{{$key+1}}<input type="text" name="product_id[]" value="{{$result->branch_product_id}}" hidden /></td>
                  <td>{{$result->barcode}}<input type="text" name="barcode[]" value="{{$result->barcode}}" hidden /></td>
                  <td>{{$result->product_name}}<input type="text" name="product_name[]" value="{{$result->product_name}}" hidden /></td>
                  <td align="center">{{ucfirst($result->measurement)}}<input type="text" name="measurement[]" value="{{$result->measurement}}" hidden /></td>
                  <td align="right">{{number_format($result->cost,2)}}<input type="text" name="cost[]" value="{{$result->cost}}" hidden /></td>
                  <td align="right">{{number_format($result->price,2)}}<input type="text" name="price[]" value="{{$result->price}}" hidden /></td>
                  <td align="center">{{number_format($result->order_quantity,3)}}<input type="text" name="order_quantity[]" value="{{$result->order_quantity}}" hidden /></td>
                  <td><button type="button" class="btn btn-primary delete" value="{{$result->id}}">Delete</button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12" style="text-align: center">
          <input class="btn btn-primary" type="submit" value="Generate DO"/>
          <button type="button" id="reset" class="btn btn-danger">Reset</button>
        </div>
      </div>

    </div>
  </form>

</div>
<script>
$(document).ready(function(){
  $(".delete").click(function(){
    let id = $(this).val();
    Swal.fire({
      title: 'Remove Items',
      html: 'Please confirm if you want to remove this item from list',
      icon: 'warning',
      confirmButtonText: `Yes`,
      showCancelButton: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $("#"+id).remove();
        $.get('{{route('ajaxRemoveItem')}}',
        {
          'id' : id
        },function(data){
          if(data == true){
            Swal.fire({
              title: 'Success',
              html: 'Item remove successful',
              icon: 'success',
              confirmButtonText: `Yes`,
            }).then((result)=>{
              window.location.reload();
            });
          }else{
            Swal.fire('Fail','Item remove fail, please contact IT support','error');
          }
        },'json');
      }
    })
  });

  $("#form2").submit(function(e){
    e.preventDefault();
    Swal.fire({
      title: 'Generate DO',
      html: 'Please make sure all the items in the list are correct, this action is irreversible',
      icon: 'warning',
      confirmButtonText: `Yes`,
      showCancelButton: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $("input[type=submit]").prop('disabled',true);
        $.post('{{route('postManualOrderList')}}',$("form").serialize(),
        function(data){
          if(data == true){
            Swal.fire('Success','DO generate completed','success').then(()=>{window.location.assign('{{route('getDoHistory')}}')});
          }else{
            Swal.fire('Fail','Item remove fail, please contact IT support','error');
            $("input[type=submit]").prop('disabled',false);
          }
        },"json");
      }
    });
  });

  $("#reset").click(function(){
    let ans = confirm("Confirm reset the order list ?");

    if(ans){
      $.post("{{route('resetRestockList')}}",
      {
        '_token':"{{ csrf_token() }}",
        'from_id':$("input[name=from_branch_id]").val(),
        'to_id':$("input[name=to_branch_id]").val(),
      },function(data){
        if(data == 'success'){
          swal.fire('Success','Reset Successful','success').then(()=>{location.reload();});
        }
      },'json');
    }
  });

});
</script>

@if(session()->has('success'))
  <script>swal.fire('Success','Import Successful','success');</script>
@endif

@endsection