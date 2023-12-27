@extends('layouts.app')
<title>Item Based Sales Report</title>
@section('content')
<style>
  body{
    background: #f9fafb;
  }
  
  .select2-selection--multiple{
    padding: 0 0 5px 2px;
  }
</style>

<form method="get" action="{{route('printItemBasedSalesReport')}}" target="_blank">
  <h2 align="center">Item Based Sales Report</h2>
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
            <label>Department</label>
            <select name="department" class="form-control" id="department">
              <option value=""></option>
              @foreach($departments as $department)
                <option value="{{$department->id}}">{{$department->department_name}}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label>Category</label>
            <select name="category_ids[]" class="form-control select2" multiple>
              <option value=""></option>
            </select>
          </div>

          
          <div class="col-md-6">
            <label>Sub-Category</label>
            <select name="sub_category_ids[]" class="form-control select2" multiple>
              <option value=""></option>
              @foreach($subCategories as $subCategory)
              <option value="{{$subCategory->id}}">{{$subCategory->name}}</option>
            @endforeach
            </select>
          </div>

          
          <div class="col-md-6">
            <label>Brand</label>
            <select name="brand_ids[]" class="form-control select2" multiple>
              <option value=""></option>
              @foreach($brands as $brand)
                <option value="{{$brand->id}}">{{$brand->name}}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-12">
            <label>Branch</label>
            <select class="form-control select2" name="branches[]" id="branches" multiple="multiple" required>
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
      var category_list = @json($categories);

      $(".select2").select2();

      $("#department").change(function(){
        let category_html = "<option value=''></option>";
        let department_id = $(this).val();
        for(var a = 0; a < category_list.length; a++)
        {
          if(category_list[a].department_id == department_id)
          {
            category_html += "<option value="+category_list[a].id+" selected>"+category_list[a].category_name+"</option>";
          }
        }

        $("select[name='category_ids[]']").html(category_html);
        $(".select2").select2();
      });

  }); 

</script>

@endsection