@extends('layouts.app')
<title>Sales Report</title>
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

<form method="get" action="{{route('getSalesTransactionReport')}}" target="_blank">
  <h2 align="center">Sales Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12 form-group">
            <button type="button" class="btn btn-primary" id="export_report" style="float: right; margin-bottom: 10px;">Export Report</button>
          </div>
          <div class="col-md-6">
            <label>Report date from</label>
            <input type="date" name="report_date_from" class="form-control" value="{{ $selected_date_from }}" required>
          </div>

          <div class="col-md-6">
            <label>Report date to</label>
            <input type="date" name="report_date_to" class="form-control" value="{{ $selected_date_to }}" required>
          </div>

          <!-- <div class="col-md-12">
            <label>Invoice Number</label>
            <input type="text" name="invoice" class="form-control" />
          </div> -->
        </div>

        <div class="row">
          <div class="col-md-12" style="text-align: center;margin:10px 0px 10px 0px">
            <button type="submit" class="btn btn-primary">Generate</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<form method="POST" action="{{ route('exportSalesReport') }}" id="exportSalesReportForm">
  @csrf
  <input type="hidden" id="branch_token" name="branch_token" />
  <input type="hidden" id="report_date_from" name="report_date_from" value="{{ $selected_date_from }}" />
  <input type="hidden" id="report_date_to" name="report_date_to" value="{{ $selected_date_to }}" />
</form>

<script>

  $(document).ready(function(){
    $("#export_report").click(function(){
      var report_date_from = $("input[name='report_date_from']").val();
      var report_date_to = $("input[name='report_date_to']").val();
      var branch_token = $("select[name='branch_token']").val();

      $("#branch_token").val(branch_token);
      $("#report_date_from").val(report_date_from);
      $("#report_date_to").val(report_date_to);

      $("#exportSalesReportForm").submit();
    });
  });

</script>



@endsection