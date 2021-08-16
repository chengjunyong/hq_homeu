@extends('layouts.app')
<title>Department & Category Sales Report</title>
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

  .form-control.select2 ~ span.select2 { height: 38px; }
  .form-control.select2 ~ span.select2 > .selection > .select2-selection { height: 38px; overflow: auto; }

</style>

<form method="POST" action="{{route('getDepartmentAndCategoryReportDetail')}}" target="_blank">
  @csrf
  <h2 align="center">Department & Category Sales Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12 form-group">
            <button type="button" class="btn btn-primary" id="export_report" style="float: right; margin-bottom: 10px;">Export Report</button>
          </div>
          <div class="col-md-6">
            <label>Department</label>
            <select name="department_id" class="form-control" required>
              <option value=0>Please select</option>
              @foreach($department_list as $department)
                <option value="{{ $department->id }}">{{ $department->department_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label>Category</label>
            <select name="category_id[]" class="form-control select2" style="width: 100%;" multiple="multiple" required>
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
          <!-- <div class="col-md-12" style="text-align: center;margin:10px 0px 10px 0px">
            <button type="submit" class="btn btn-primary">Generate</button>
          </div> -->
        </div>
      </div>
    </div>
  </div>
</form>

<form method="POST" action="{{ route('exportDepartmentAndCategoryReport') }}" id="exportDepartmentAndCategoryReportForm">
  @csrf
  <input type="hidden" id="report_date_from" name="export_report_date_from" value="{{ $selected_date_from }}" />
  <input type="hidden" id="report_date_to" name="export_report_date_to" value="{{ $selected_date_to }}" />

  <input type="hidden" id="department_id" name="export_department_id" value="" />
  <div id="category_id_box"></div>
  <input type="hidden" id="category_id" name="export_category_id[]" value="" />
</form>

<script>

  var department_list = @json($department_list);
  var category_list = @json($category_list);

  $(document).ready(function(){

    $(".select2").select2();

    $("select[name='department_id']").change(function(){
      let category_html = "";
      let department_id = $(this).val();
      for(var a = 0; a < category_list.length; a++)
      {
        if(category_list[a].department_id == department_id)
        {
          category_html += "<option value="+category_list[a].id+" selected>"+category_list[a].category_name+"</option>";
        }
      }

      $("select[name='category_id[]']").html(category_html);
      $(".select2").select2();
    });

    $("#export_report").click(function(){
      var report_date_from = $("input[name='report_date_from']").val();
      var report_date_to = $("input[name='report_date_to']").val();
      var department_id = $("select[name='department_id']").val();
      var category_id = $("select[name='category_id[]']").val();

      $("#report_date_from").val(report_date_from);
      $("#report_date_to").val(report_date_to);
      $("#department_id").val(department_id);

      $("#category_id_box").html("");
      for(var a = 0; a < category_id.length; a++)
      {
        $("#category_id_box").append("<input type='hidden' name='export_category_id[]' value='"+category_id[a]+"' />")
      }

      $("#exportDepartmentAndCategoryReportForm").submit();
    });

  });

</script>



@endsection