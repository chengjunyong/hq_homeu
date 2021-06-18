@extends('layouts.app')
<title>Warehouse Restock History Details</title>
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
      <h4>Warehouse Restock History Details</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <label>Supplier Name:</label>
          <input readonly class="form-control" type="text" name="supplier_name" value="{{$po->supplier_name}}">
        </div>
        <div class="col-md-6">
          <label>Supplier Code:</label>
          <input readonly class="form-control" type="text" name="to" value="{{$po->supplier_code}}">
        </div>
        <div class="col-md-6">
          <label>PO Number:</label>
          <input readonly class="form-control" type="text" name="do_number" value="{{$po->po_number}}">
        </div>
        <div class="col-md-6">
          <label>Total Items:</label>
          <input readonly class="form-control" type="text" name="total_item" value="{{$po->total_quantity_items}}">
        </div>
        <div class="col-md-6">
          <label>Total Value:</label>
          <input readonly class="form-control" type="text" name="total_item" value="Rm {{number_format($po->total_amount,2)}}">
        </div>
        <div class="col-md-6">
          <label>Date Issue:</label>
          <input readonly class="form-control" type="text" name="created_at" value="{{$po->issue_date}}">
        </div>
      </div>

      <form>
        @csrf

        <label>Invoice Number</label>
        <input type="text" name="invoice_number" value="{{$invoice->invoice_number}}" required class="form-control" style="width:35%" disabled/>

        <div style="overflow-y: auto;height:425px;margin-top:25px">
          <table style="width:100%;">
            <thead style="background-color: #b8b8efd1">
              <tr>
                <td>No</td>
                <td style="width:5%">Barcode</td>
                <td>Product Name</td>
                <td align="center">Order Quantity</td>
                <td align="center" style="width:10%">Quantity Received</td>
                <td align="center" style="width:10%">Cost</td>
                <td>Remark</td>
              </tr>
              <tbody>
                @foreach($detail as $key => $result)
                  <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$result->barcode}}</td>
                    <td>{{$result->product_name}}</td>
                    <td align="center">{{$result->quantity}}</td>
                    <td align="center"><input type="number" value="{{$result->quantity}}" style="width:50%" disabled></td>
                    <td align="center"><input type="number" value="{{$result->cost}}"  style="width:50%" disabled></td>
                    <td><input type="text" style="width:100%" value="{{$result->remark}}" disabled></td>
                  </tr>
                @endforeach
              </tbody>
            </thead>
          </table>
        </div>
      </form>

    </div>
  </div>
</div>


@endsection