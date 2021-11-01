@extends('layouts.app')
<title>Supplier Product List</title>
@section('content')


<form method="GET" action="{{route('getSupplierProductReport')}}">
  <h2 align="center">Supplier Product List</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12 form-group">
            <button type="button" class="btn btn-primary" id="export_report" style="float: right; margin-bottom: 10px;">Export Report</button>
          </div>
          <div class="col-md-12" style="margin-bottom: 10px;">
            <label>Supplier</label>
            <select class="form-control" name="supplier_id" id="form_supplier_id">
              @foreach($supplier_list as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
              @endforeach
            </select>
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

<form method="POST" action="{{ route('exportSupplierProductReport') }}" id="exportSupplierProductReportForm">
  @csrf
  <input type="hidden" id="report_supplier_id" name="supplier_id" />
</form>

<script>
  $("#export_report").click(function(){
    $("#report_supplier_id").val($("#form_supplier_id").val());
    $("#exportSupplierProductReportForm").submit();
  });

</script>
@endsection