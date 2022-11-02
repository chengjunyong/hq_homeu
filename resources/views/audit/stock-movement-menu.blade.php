@extends('layouts.app')
<title>Item Movement Report</title>
@section('content')
<script src="{{ asset('datatable/datatables.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('datatable/datatables.min.css')}}"/>
<script src="{{ asset('js/md5.min.js') }}"></script>
<script src="{{ asset('js/debounce.js') }}"></script>

<form method="get" id="form" action="{{route('getStockMovementMenu')}}">
  <h2 align="center">Item Movement Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12" style="margin-bottom:10px;">
            <button type="button" class="btn btn-primary" id="export_report" style="float: right; margin-bottom: 10px;">Export Report</button>
            <label>Product</label>
            <input list="product_list" id="product" name="product" class="form-control">
            <datalist id="product_list">
            </datalist>
          </div>
          <div class="col-md-12" style="margin-bottom:10px;">
            <label>Branch</label>
            <select class="form-control" name="branch">
              @foreach($branches as $branch)
                <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
              @endforeach
              {{-- <option value="warehouse">Warehouse</option> --}}
            </select>
          </div>
          <div class="col-md-6">
            <label>Report date from</label>
            <input type="date" name="report_date_from" class="form-control" value="" required>
          </div>
          <div class="col-md-6">
            <label>Report date to</label>
            <input type="date" name="report_date_to" class="form-control" value="" required>
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

    $("#product").keyup($.debounce(300,function(e){
      if($(this).val().length >= 3){
        $.get("{{route('ajaxGetProduct')}}",
        {
          'target':$(this).val(),
        },function(data){
          $("#product_list").html("");
          data.forEach(function(result,index){
            $("#product_list").append(`<option value='${result.product_name}'>`);
          });
        },'json');
      }
    }));

    $("#export_report").click(function(){
      if($("#product").val() != "" && $("input[name=report_date_from]").val() != "" && $("input[name=report_date_to]").val() != ""){
        let branch_id = $("#select_branch").val();
        $("#branch_id").val(branch_id);

        swal.fire({
          title : 'Exporting Report',
          html  : 'It will take some time to process, please wait awhile.',
          didOpen: () => {
              swal.showLoading()
          },
          backdrop : true,
          allowOutsideClick : false,
        });

        $.get("{{route('ajaxStockMovementMenu')}}",
          $("#form").serialize(),
          function(data){
            swal.close();
            if(data == "error"){
              swal.fire('Error','Product Not Found','error');
            }else{
              window.open(data);
            }
          },'json');
      }else{
        swal.fire('Error','Please Fill In All The Fields Before Exporting Report','error');
      }
    });

  });

</script>

@if(session('error'))
  <script>swal.fire('Error',"{{ session('error') }}",'error');</script>
        
@endif


@endsection