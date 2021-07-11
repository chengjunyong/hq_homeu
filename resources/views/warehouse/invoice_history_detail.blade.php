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
        <div class="col-md-6">
          <label>Invoice Date:</label>
          <input readonly class="form-control" type="text" name="created_at" value="{{$invoice->invoice_date}}">
        </div>
        <div class="col-md-6">
          <label>Invoice No:</label>
          <input readonly class="form-control" type="text" name="created_at" value="{{$invoice->invoice_no}}">
        </div>
        <div class="col-md-6">
          <label>Supplier Name:</label>
          <input readonly class="form-control" type="text" name="supplier_name" value="{{$invoice->supplier_name}}">
        </div>
        <div class="col-md-6">
          <label>Total Items:</label>
          <input readonly class="form-control" type="text" name="total_item" value="{{$invoice->total_different_item}}">
        </div>
        <div class="col-md-6">
          <label>Total Items Quantity:</label>
          <input readonly class="form-control" type="text" name="total_item" value="{{$invoice->total_item}}">
        </div>
        <div class="col-md-6">
          <label>Total Value:</label>
          <input readonly class="form-control" type="text" name="total_item" value="{{$invoice->total_cost}}">
        </div>
        <div class="col-md-6">
          <label>Record Creator:</label>
          <input readonly class="form-control" type="text" name="created_at" value="{{$invoice->creator_name}}">
        </div>
        <div class="col-md-6">
          <label>Created At:</label>
          <input readonly class="form-control" type="text" name="created_at" value="{{$invoice->created_at}}">
        </div>
      </div>

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
                <tr>
                  <td>{{$key +1}}</td>
                  <td>{{$result->barcode}}</td>
                  <td>{{$result->product_name}}</td>
                  <td align="center">Rm {{number_format($result->cost,2)}}</td>
                  <td align="center">{{$result->quantity}}</td>
                  <td align="right">Rm {{number_format($result->quantity * $result->cost,2)}}</td>
                </tr>
              @endforeach
            </tbody>
          </thead>
        </table>
      </div>


    </div>
  </div>
</div>


@endsection