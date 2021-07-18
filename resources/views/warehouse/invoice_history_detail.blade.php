@extends('layouts.app')
<title>Invoice History Details</title>
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
      <h4>Invoice History Details</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <label>Reference No:</label>
          <input readonly class="form-control" type="text" value="{{$invoice->reference_no}}">
        </div>
        <div class="col-md-6">
          <label>Invoice Date:</label>
          <input readonly class="form-control" type="text" value="{{$invoice->invoice_date}}">
        </div>
        <div class="col-md-6">
          <label>Invoice No:</label>
          <input readonly class="form-control" type="text" value="{{$invoice->invoice_no}}">
        </div>
        <div class="col-md-6">
          <label>Supplier Name:</label>
          <input readonly class="form-control" type="text" value="{{$invoice->supplier_name}}">
        </div>
        <div class="col-md-6">
          <label>Total Items:</label>
          <input readonly class="form-control" type="text" value="{{$invoice->total_different_item}}">
        </div>
        <div class="col-md-6">
          <label>Total Items Quantity:</label>
          <input readonly class="form-control" type="text" value="{{$invoice->total_item}}">
        </div>
        <div class="col-md-6">
          <label>Total Value:</label>
          <input readonly class="form-control" type="text" value="Rm {{number_format($invoice->total_cost,2)}}">
        </div>
        <div class="col-md-6">
          <label>Record Creator:</label>
          <input readonly class="form-control" type="text" value="{{$invoice->creator_name}}">
        </div>
        <div class="col-md-6">
          <label>Created At:</label>
          <input readonly class="form-control" type="text" value="{{$invoice->created_at}}">
        </div>
      </div>

      <form method="post" action="{{route('postInvoicePurchaseHistoryDetail')}}">
        @csrf
        <input type="text" name="ref_no" value="{{$invoice->reference_no}}" hidden/>
        <div style="overflow-y: auto;height:425px;margin-top:25px">
          <table style="width:100%;">
            <thead style="background-color: #b8b8efd1">
              <tr>
                <td>No</td>
                <td>Barcode</td>
                <td>Product Name</td>
                <td align="center">Cost</td>
                <td align="center">Quantity</td>
                <td align="right">Total Value</td>
              </tr>
              <tbody>
                @foreach($invoice_detail as $key => $result)
                  <input type="text" name="invoice_purchase_detail_id[]" value="{{$result->id}}" hidden/>
                  <input type="text" name="barcode[]" value="{{$result->barcode}}" hidden/>
                  <tr>
                    <td>{{$key +1}}</td>
                    <td>{{$result->barcode}}</td>
                    <td>{{$result->product_name}}</td>
                    <td align="center">Rm <input type="number" class="cost" name="cost[]" min="0.01" step="0.01" value="{{$result->cost}}" style="text-align: right;width:7vw"/></td>
                    <td align="center"><input type="number" class="quantity" name="quantity[]" min="1" step="1" value="{{$result->quantity}}" style="text-align: right;width:5vw"/></td>
                    <td align="right">Rm <input type="number" class="total" name="total[]" min="0.01" step="0.01" value="{{$result->total_cost}}" style="text-align: right;width:10vw"/></td>
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
    let quantity = parseInt($(this).parent().siblings().eq(4).children().val());
    let total = parseFloat($(this).val());
    let result = total / quantity;
    $(this).parent().siblings().eq(3).children().val(result.toFixed(2));
  });

  $(".cost").on("keyup change",function(){
    let quantity = parseInt($(this).parent().siblings().eq(3).children().val());
    console.log(quantity);
    let cost = parseFloat($(this).val());
    let result = cost * quantity;
    $(this).parent().siblings().eq(4).children().val(result.toFixed(2));
  });

  $(".quantity").on("keyup change",function(){
    let total = parseFloat($(this).parent().siblings().eq(4).children().val());
    let cost = parseInt($(this).parent().siblings().eq(3).children().val())
    let quantity = $(this).val();
    if(total){
      let result = total / quantity;
      $(this).parent().siblings().eq(3).children().val(result.toFixed(2));
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