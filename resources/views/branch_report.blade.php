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

<form method="get" action="{{route('getBranchReportDetail')}}" target="_blank">
  <h2 align="center">Branch Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <label>Branch</label>
            <select class="form-control select2" multiple="multiple" style="width: 100%;" name="branch[]">
              @foreach($branch as $value)
                <option value="{{ $value->token }}">{{ $value->branch_name }}</option>
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
    </div>
  </div>
</form>

<script>

  $(document).ready(function(){

    $(".select2").select2();

  });

</script>



@endsection