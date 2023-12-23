@extends('layouts.app')
<title>Branch Restock Report</title>
@section('content')
<style>
  body{
    background: #f9fafb;
  }
</style>

<form method="get" action="{{route('printBranchRestockReport')}}" target="_blank">
  <h2 align="center">Branch Restock Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">

          <div class="col-md-6">
            <label>Report date from</label>
            <input type="date" name="start" class="form-control" value="" required>
          </div>

          <div class="col-md-6">
            <label>Report date to</label>
            <input type="date" name="end" class="form-control" value="" required>
          </div>

          <div class="col-md-6">
            <label>Branch</label>
            <select class="form-control" name="from_branches[]" id="branches" multiple="multiple" required>
              @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
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

<script>
  $(document).ready(function(){
      $("#branches").select2();
  }); 

</script>

@endsection