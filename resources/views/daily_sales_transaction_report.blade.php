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

<form method="post" action="{{route('postDailySalesTransactionReport')}}" target="_blank">
  @csrf
  <h2 align="center">Daily Sales Transaction Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12 form-group">
            <button type="button" class="btn btn-primary" id="export_report" style="float: right; margin-bottom: 10px;">Export Report</button>
          </div>
          <div class="col-md-12" style="margin-bottom: 10px;">
            <label>Branch</label>
            <select class="form-control" name="branch_id[]" multiple="multiple">
              @foreach($branch as $result)
                <option value="{{$result->id}}">{{$result->branch_name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label>Report date from</label>
            <input type="date" name="report_date_from" class="form-control" value="{{date('Y-m-d',strtotime(now().'-1 days'))}}" required>
          </div>
          <div class="col-md-6">
            <label>Report date to</label>
            <input type="date" name="report_date_to" class="form-control" value="{{date('Y-m-d',strtotime(now().'-1 days'))}}" required>
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
<script>
  $(document).ready(function(){
    $("select[name='branch_id[]']").select2();

    $("#export_report").click(function(){
      let branch_id = $("select[name='branch_id[]']").val();
      let start = $("input[name=report_date_from]").val();
      let end = $("input[name=report_date_to]").val();

      $.get("{{route('ajaxExportSalesTransactionReport')}}",
      {
        'branch_id' : branch_id,
        'start' : start,
        'end' : end,
      },function(data){

        window.open(data);
        
      },'json');
    });
  });
</script>

@endsection