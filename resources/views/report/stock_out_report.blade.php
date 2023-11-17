@extends('layouts.app')
<title>Stock Out Report</title>
@section('content')
<script src="{{ asset('datatable/datatables.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('datatable/datatables.min.css')}}"/>
<script src="{{ asset('js/md5.min.js') }}"></script>
<script src="{{ asset('js/debounce.js') }}"></script>
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

  .select2-selection__arrow{
    padding:20px;
  }

  .select2-selection.select2-selection--single{
    height: 40px;
    padding: 5px;
  }

  .select2-container--default .select2-selection--multiple {
    padding:5px;
  }

  .select2-selection__choice {
    margin:2px;
  }
</style>

<form method="post" action="{{route('postStockOutReport')}}" target="_blank">
  @csrf
  <h2 align="center">Stock Out Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <label>Report date from</label>
            <input type="date" name="report_date_from" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label>Report date to</label>
            <input type="date" name="report_date_to" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label>From Branch</label>
            <select class='form-control' name='from_branch' required>
              <option value=0 selected>HQ</option>
              @foreach($branches as $branch)
                <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label>To Branch</label>
            <select class='form-control' name='to_branch' required>
              <option value='all'>All Branch</option>
              @foreach($branches as $branch)
                <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
              @endforeach
              <option value=0>HQ</option>
            </select>
          </div>

          <div class="col-md-6">
            <label>Product Name</label>
            <input type="text" name="product_name" class="form-control">
          </div>

          <div class="col-md-6">
            <label>Barcode</label>
            <input type="text" name="barcode" class="form-control" >
          </div>

          <div class="col-md-6">
            <label>Department</label>
            <select class='form-control' name="department_id" id="department_id">
              @foreach($departments as $department)
                <option value="{{ $department->id }}">{{ $department->department_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label>Category</label>
            <select class='form-control' name="category_id[]" multiple>
              @foreach($departments->first()->categories as $category)
                <option value="{{ $category->id }}" selected>{{ $category->category_name }}</option>
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
    const departments = {!! $departments->toJson() !!};
    
    $("select[name='category_id[]']").select2();

    $("#department_id").change(function(){
      let id = $(this).val();

      let department = departments.filter(function(target){
        return target.id == id;
      });

      $("select[name='category_id[]']").html('');
      department[0].categories.forEach(function(target){
        $("select[name='category_id[]']").append(`<option value="${target.id}" selected>${target.category_name}</option>`);
      });
    });


  });



</script>

@endsection