@extends('layouts.app')
<title>Supplier Product List</title>
@section('content')


<form method="post" action="{{route('postSupplierProduct')}}" target="_blank">
  @csrf
  <h2 align="center">Refund Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12 form-group">
            <button type="button" class="btn btn-primary" id="export_report" style="float: right; margin-bottom: 10px;">Export Report</button>
          </div>
          <div class="col-md-12" style="margin-bottom: 10px;">
            <label>Branch</label>
            <select class="form-control" name="branch_id">
              @foreach($branch as $result)
                <option value="{{$result->id}}">{{$result->branch_name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-12">
            <label>Report Date</label>
            <input type="date" name="report_date" class="form-control" value="{{date('Y-m-d',strtotime(now().'-1 days'))}}" required>
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


@endsection