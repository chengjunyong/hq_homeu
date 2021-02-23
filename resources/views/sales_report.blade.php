@extends('layouts.app')

@section('content')
<script src="{{ asset('datatable/datatables.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('datatable/datatables.min.css')}}"/>
<script src="{{ asset('js/md5.min.js') }}"></script>
<style>
  body{
    background: #f9fafb;
  }

  #sales_report{
    width:100%;
    border:1px solid black;
  }

  tr{
    border:1px solid black;
  }

  td{
    padding: 5px 0px 5px 0px;
  }

</style>

<form method="get" action="{{route('getSalesReport')}}">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <label>Branch</label>
        <select class="form-control" name="branch_token">
          <option value="0">Please select</option>
          @foreach($branch as $value)
            <option value="{{ $value->token }}" {{ $selected_branch->id == $value->id ? 'selected' : '' }}>{{ $value->branch_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label>Report date from</label>
        <input type="date" name="report_date_from" class="form-control" value="{{ $selected_date_from }}" required>
      </div>

      <div class="col-md-6">
        <label>Report date to</label>
        <input type="date" name="report_date_to" class="form-control" value="{{ $selected_date_to }}" required>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12" style="text-align: center;margin:10px 0px 10px 0px">
        <input type="submit" class="btn btn-primary"/>
      </div>
    </div>
  </div>
</form>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h4>Sales Report</h4>
      <table id="sales_report">
        <thead style="background-color: #403c3c80;text-align: center">
          <tr>
            <th>Invoice No</th>
            <th>Payment type</th>
            <th>Reference No</th>
            <th>Subtotal</th>
            <th>Discount</th>
            <th>Total</th>
            <th>Received payment</th>
            <th>Balance</th>
            <th>Transaction date</th>
            <th>Detail</th>
          </tr>
        </thead>
        <tbody style="text-align: center">
          @foreach($transaction as $result)
            <tr>
              <td>{{ $result->transaction_no }}</td>
              <td>{{ $result->payment_type_text }}</td>
              <td>{{ $result->invoice_no }}</td>
              <td>{{ number_format($result->subtotal, 2) }}</td>
              <td>{{ number_format($result->total_discount, 2) }}</td>
              <td>{{ number_format($result->total, 2) }}</td>
              <td>{{ number_format($result->payment, 2) }}</td>
              <td>{{ number_format($result->balance, 2) }}</td>
              <td data-order="{{ $result->transaction_date }}">{{ date('d M Y g:i:s A', strtotime($result->transaction_date)) }}</td>
              <td>
                <a href="{{ route('getSalesReportDetail', ['branch_id' => $result->branch_id, 'id' => $result->branch_transaction_id ]) }}" class="btn btn-primary">Detail</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="float:right;">
        {{$transaction->links()}}
      </div>
    </div>
  </div>
</div>


<script>


</script>



@endsection