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

  .form-control.select2 ~ span.select2 { height: 100px; }
  .form-control.select2 ~ span.select2 > .selection > .select2-selection { height: 100px; overflow: auto; }

  .select2-selection__rendered{
    padding-bottom:5px !important;
  }

</style>

<form method="POST" id="stock_balance" action="{{route('postStockBalanceBranchReport')}}">
  @csrf
  <h2 align="center">Stock Kawalan Report</h2>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12 form-group">
            <button type="button" class="btn btn-secondary" id="export_report" style="float: right; margin-bottom: 10px;">Export As Excel</button>
          </div>
          <div class="col-md-12">
            <label>Branches</label>
            <select class="form-control" name="branch_id[]" multiple="multiple" required>
              @foreach($branches as $branch)
                <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
              @endforeach
              <option value=99>Warehouse</option>
            </select>
          </div>
          <div class="col-md-12" style="text-align: center;margin-top:10px;">
            <input type="submit" value="Generate" class="btn btn-primary"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
  $(document).ready(function(){
    $("select[name='branch_id[]']").select2();
  });

  $("#export_report").click(function(){
    if($("select[name='branch_id[]']").val().length != 0){
      swal.fire({
        title : 'Exporting Report',
        html  : 'It will take some time to process, please wait awhile.',
        didOpen: () => {
            swal.showLoading()
        },
        backdrop : true,
        allowOutsideClick : false,
      });

      $.post("{{route('ajaxStockBalanceBranchReport')}}",
      $('#stock_balance').serialize()
      ,function(data){
        swal.close();
        window.open(data);
      },'json');

    }else{
      swal.fire('Error','Please Select Branch Before Exporting Report','error');
    }
  });

</script>

@endsection