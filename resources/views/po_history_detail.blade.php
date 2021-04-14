@extends('layouts.app')

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
    <div class="card-title">
      <h4>Purchase Order Detail</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <label>Supplier Code:</label>
          <input readonly class="form-control" type="text" name="to" value="{{$po_detail->supplier_code}}">
        </div>
        <div class="col-md-6">
          <label>Supplier Name:</label>
          <input readonly class="form-control" type="text" name="to" value="{{$po_detail->supplier_name}}">
        </div>
        <div class="col-md-6">
          <label>Total Items:</label>
          <input readonly class="form-control" type="text" name="total_item" value="{{$po_detail->total_quantity_items}}">
        </div>
        <div class="col-md-6">
          <label>Completed:</label>
          <input readonly class="form-control" type="text" name="completed" value="{{($po_detail->completed == 0) ? 'No' : 'Yes'}}">
        </div>
        <div class="col-md-6">
          <label>Date Issue:</label>
          <input readonly class="form-control" type="text" name="created_at" value="{{$po_detail->created_at}}">
        </div>
      </div>

      <div style="overflow-y: auto;height:425px;margin-top:25px">
        <table style="width:100%;">
          <thead style="background-color: #b8b8efd1">
            <tr>
              <td>No</td>
              <td style="width:20%">Barcode</td>
              <td>Product Name</td>
              <td align="right">Cost Per Unit</td>
              <td align="center">Quantity Transfer</td>
              <td align="right">Total</td>
            </tr>
            <tbody>
              @foreach($po_list as $key => $result)
                <tr>
                  <td>{{$key+1}}</td>
                  <td>{{ $result->barcode }}</td>
                  <td>{{ $result->product_name }}</td>
                  <td align="right">{{ number_format($result->cost,2) }}</td>
                  <td align="center">{{ $result->quantity }}</td>
                  <td align="right">{{ number_format(floatval($result->cost) * floatval($result->quantity),2)}}</td>
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