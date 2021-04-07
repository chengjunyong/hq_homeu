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

<h2 align="center">Stock Balance Report</h2>
  <form action="{{route('postStockBalanceReport')}}" method="post">
    @csrf
    <div class="container">

      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 form-group">
              <label>Please Select Branch</label>
              <button type="button" class="btn btn-primary" id="export_report" style="float: right; margin-bottom: 10px;">Export Report</button>
              <select class="form-control" name="branch_id">
                @foreach($branch as $result)
                <option value="{{$result->id}}">{{$result->branch_name}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-12" style="text-align: center;margin:10px 0px 10px 0px">
              <input type="submit" class="btn btn-primary" value="Generate"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

<form method="POST" action="{{ route('exportStockBalance') }}" id="exportStockBalanceReport">
  @csrf
  <input type="hidden" id="branch_id" name="branch_id" />
</form>

<script>
  $("#export_report").click(function(){
    let branch_id = $("select[name='branch_id']").val();
    $("#branch_id").val(branch_id)

    swal.fire({
      title : 'Exporting Report',
      html  : 'It will take some time to process, please wait awhile.',
      didOpen: () => {
          swal.showLoading()
      },
      backdrop : true,
      allowOutsideClick : false,
    });

    $.post("{{route('exportStockBalance')}}",
      $("#exportStockBalanceReport").serialize(),
      function(data){
        swal.close();
        window.open(data);
      },'json');

  });
</script>
@endsection