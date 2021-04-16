@extends('layouts.app')

@section('content')
<style>
  .container{
    min-width:95%;
    margin-top: 10px;
  }
</style>

<div class="container">
  <div class="card">
    <div class="title">
      <h4 style="margin:20px">Warehouse Restock History</h4>
    </div>
    <div class="card-body">
      <div style="float:right">
<!--         <input type="text" id="search" placeholder="" class="form-control" style="margin-bottom: 15px"/> -->
      </div>
      <div class="table table-responsive">
        <table id="history" style="width:100%">
          <thead style="background: #b8b8efd1">
            <tr>
              <td>No</td>
              <td>Invoice Number</td>
              <td>PO Number</td>
              <td>Supplier Code</td>
              <td>Supplier Name</td>
              <td>Date Completed</td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            @foreach($history as $key => $result)
              <tr>
                <td>{{$key + 1}}</td>
                <td>{{$result->invoice_number}}</td>
                <td>{{$result->po_number}}</td>
                <td>{{$result->supplier_code}}</td>
                <td>{{$result->supplier_name}}</td>
                <td>{{$result->created_at}}</td>
                <td><button class="btn btn-primary" onclick="window.location.assign('{{route('getWarehouseRestockHistoryDetail',[$result->id,$result->po_number])}}')">Details</button></td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div style="float:right">{{ $history->links() }}</div>
      </div>
    </div>
  </div>
</div>

@endsection