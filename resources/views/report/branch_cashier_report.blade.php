@extends('layouts.app')
<title>Branch Sales Report</title>
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

<form method="get" action="{{route('getBranchCashierReportDetail')}}" target="_blank">
  <h2 align="center">Branch Sales Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12 form-group">
            <label>Branch</label>
            <!-- <button type="button" class="btn btn-primary" id="export_report" style="float: right; margin-bottom: 10px;">Export Report</button> -->
            <select class="form-control" style="width: 100%;" name="branch">
              @foreach($branch as $value)
                <option value="{{ $value->token }}">{{ $value->branch_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-12">
            <label>Report date from</label>
            <input type="date" name="report_date" class="form-control" value="{{ $selected_date }}" required>
          </div>
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

<form method="POST" action="{{ route('exportBranchCashierReport') }}" id="exportBranchCashierReportForm">
  @csrf
  <input type="hidden" id="report_branch" name="branch" />
  <input type="hidden" id="report_date" name="report_date" value="{{ $selected_date }}" />
</form>

<script>

  $(document).ready(function(){

    $(".select2").select2();

    $("#export_report").click(function(){
      var report_date_from = $("input[name='report_date']").val();
      var branch_token = $("select[name='branch']").val();

      $("#report_branch").val(branch_token);
      $("#report_date").val(report_date_from);

      $("#exportBranchCashierReportForm").submit();
    });

  });

</script>



@endsection