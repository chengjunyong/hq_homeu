@extends('layouts.app')
<title>Warehouse Restock Confirmation</title>
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
      <h4>Warehouse Restock Confirmation</h4>
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

      <form method="post" action="{{route('postWarehouseRestock')}}">
        @csrf
        <input type="text" name="po_number" value="{{$po->po_number}}" hidden/>
        <input type="text" name="po_id" value="{{$po->id}}" hidden/>
        <input type="text" name="supplier_name" value="{{$po->supplier_name}}" hidden>
        <input type="text" name="supplier_id" value="{{$po->supplier_id}}" hidden>
        <input type="text" name="supplier_code" value="{{$po->supplier_code}}" hidden>

        <label>Invoice Number</label>
        <input type="text" name="invoice_number" required class="form-control" style="width:35%"/>

        <div style="overflow-y: auto;height:425px;margin-top:25px">
          <table style="width:100%;">
            <thead style="background-color: #b8b8efd1">
              <tr>
                <td>No</td>
                <td style="width:5%">Barcode</td>
                <td>Product Name</td>
                <td align="center">Measurement</td>
                <td align="center">Order Quantity</td>
                <td align="center" style="width:10%">Quantity Received</td>
                <td align="center" style="width:10%">Cost</td>
                <td>Remark</td>
              </tr>
              <tbody>
                @foreach($po_detail as $key => $result)
                  <input type="text" name="product_id[]" hidden value="{{$result->product_id}}" />
                  <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$result->barcode}}</td>
                    <td>{{$result->product_name}}</td>.
                    <td align="center">{{ucfirst($result->measurement)}}</td>
                    <td align="center">{{$result->quantity}}</td>
                    <td align="center"><input type="number" name="received_quantity[]" value="{{$result->quantity}}" style="width:100%" required min=0 {{($result->measurement == 'unit') ? 'step=1' : 'step=0.001'}}></td>
                    <td align="center"><input type="number" name="cost[]" value="{{$result->cost}}" step="0.001" style="width:50%" required min=0 {{($result->measurement == 'unit') ? 'step=1' : 'step=0.001'}}></td>
                    <input type="text" hidden value="{{$result->barcode}}" name="barcode[]"/>
                    <input type="text" hidden value="{{$result->product_name}}" name="product_name[]"/>
                    <td><input type="text" name="remark[]" style="width:100%"></td>
                  </tr>
                @endforeach
              </tbody>
            </thead>
          </table>
        </div>
        <input type="submit" value="Confirm" class="btn btn-primary" style="float: right;margin-top: 15px">
      </form>

    </div>
  </div>
</div>
@if(session()->has('success'))
  <script>
    Swal.fire({
      title: 'Success',
      html: 'Invoice has been successfully record',
      icon: 'success',
      confirmButtonText: `Yes`,
    }).then((result)=>{
      window.location.assign('{{route('getPoList')}}');
    });
  </script>
@endif

@endsection