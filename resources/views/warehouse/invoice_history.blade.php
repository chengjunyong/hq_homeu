@extends('layouts.app')
<title>Invoice History</title>
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
      <h4 style="margin:20px">Invoice History Record</h4>
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
              <td>Reference No</td>
              <td>Invoice Number</td>
              <td>Supplier Name</td>
              <td>Total Item</td>
              <td>Total Value</td>
              <td>Date Completed</td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            @foreach($history as $key => $result)
              <tr>
                <td>{{$key + 1}}</td>
                <td>{{$result->reference_no}}</td>
                <td>{{$result->invoice_no}}</td>
                <td>{{$result->supplier_name}}</td>
                <td>{{$result->total_item}}</td>
                <td>Rm {{number_format($result->total_cost,2)}}</td>
                <td>{{$result->created_at}}</td>
                <td><button class="btn btn-primary" onclick="window.location.assign('{{route('getInvoicePurchaseHistoryDetail',$result->id)}}')">Details</button></td>
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