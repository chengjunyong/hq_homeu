@extends('layouts.app')
<title>Good Return History Details</title>
@section('content')
<style>
  .container{
    min-width: 95%;
  }

  td{
    padding:5px;
  }
</style>
<div class="container">
  <div class="card" style="margin-top: 10px">
    <div class="card-title">
      <h4>GR History Details</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <label>GR No:</label>
          <input readonly class="form-control" type="text" value="{{$gr->gr_no}}">
        </div>
        <div class="col-md-6">
          <label>GR Date:</label>
          <input readonly class="form-control" type="text" value="{{$gr->gr_date}}">
        </div>
        <div class="col-md-6">
          <label>Reference No:</label>
          <input readonly class="form-control" type="text" value="{{$gr->ref_no}}">
        </div>
        <div class="col-md-6">
          <label>Supplier Name:</label>
          <input readonly class="form-control" type="text" value="{{$gr->supplier_name}}">
        </div>
        <div class="col-md-6">
          <label>Total Items:</label>
          <input readonly class="form-control" type="text" value="{{$gr->total_different_item}}">
        </div>
        <div class="col-md-6">
          <label>Total Items Quantity:</label>
          <input readonly class="form-control" type="text" value="{{$gr->total_quantity}}">
        </div>
        <div class="col-md-6">
          <label>Total Value:</label>
          <input readonly class="form-control" type="text" value="Rm {{number_format($gr->total_cost,2)}}">
        </div>
        <div class="col-md-6">
          <label>Record Creator:</label>
          <input readonly class="form-control" type="text" value="{{$gr->creator_name}}">
        </div>
        <div class="col-md-6">
          <label>Created At:</label>
          <input readonly class="form-control" type="text" value="{{$gr->created_at}}">
        </div>
      </div>

      <form method="post" action="{{route('postGoodReturnHistoryDetail')}}">
        @csrf
        <input type="text" name="gr_no" value="{{$gr->gr_no}}" hidden/>
        <div style="overflow-y: auto;height:425px;margin-top:25px">
          <table style="width:100%;">
            <thead style="background-color: #b8b8efd1">
              <tr>
                <td>No</td>
                <td>Barcode</td>
                <td>Product Name</td>
                <td align="center">Quantity</td>
                <td align="center">Cost</td>
                <td align="right">Total Value</td>
              </tr>
              <tbody>
                @foreach($gr_detail as $key => $result)
                  <input type="text" name="gr_detail_id[]" value="{{$result->id}}" hidden/>
                  <input type="text" name="barcode[]" value="{{$result->barcode}}" hidden/>
                  <tr>
                    <td>{{$key +1}}</td>
                    <td>{{$result->barcode}}</td>
                    <td>{{$result->product_name}}</td>
                    <td align="center"><input type="number" class="quantity" name="quantity[]" min="1" step="1" value="{{$result->quantity}}" style="text-align: right;width:5vw" required /></td>
                    <td align="center">Rm <input type="number" class="cost" name="cost[]" min="0.00" step="0.001" value="{{number_format($result->cost,3,'.','')}}" style="text-align: right;width:7vw"/></td>                    
                    <td align="right">Rm <input type="number" class="total" name="total[]" min="0.00" step="0.001" value="{{number_format($result->total_cost,2,'.','')}}" style="text-align: right;width:10vw" required /></td>
                  </tr>
                @endforeach
              </tbody>
            </thead>
          </table>
        </div>
        <div class="row" style="margin-top:30px;">
          <div class="col-md-12" style="text-align: center">
            <input type="submit" class="btn btn-primary" value="Modify"/>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  $(".total").on("keyup change",function(){
    let quantity = parseInt($(this).parent().siblings().eq(3).children().val());
    let total = parseFloat($(this).val());
    let result = total / quantity;
    $(this).parent().siblings().eq(4).children().val(result.toFixed(3));
  });
  $(".cost").on("keyup change",function(){
    let quantity = parseInt($(this).parent().siblings().eq(3).children().val());
    let cost = parseFloat($(this).val());
    let result = cost * quantity;
    $(this).parent().siblings().eq(4).children().val(result.toFixed(3));
  });
  $(".quantity").on("keyup change",function(){
    let total = parseFloat($(this).parent().siblings().eq(4).children().val());
    let cost = parseInt($(this).parent().siblings().eq(3).children().val())
    let quantity = $(this).val();
    if(total){
      let result = total / quantity;
      $(this).parent().siblings().eq(3).children().val(result.toFixed(3));
    }  
  });
});
</script>
@if(session()->has('success'))
  <script>
    Swal.fire("Success","Update Successful",'success');
  </script>
@endif

@endsection