@extends('layouts.app')
<title>Stock Adjustment</title>
@section('content')
<style>
  .col-md-12{
    text-align: center;
    margin-top:10px;
  }

  .container{
    max-width: 90%;
    margin-top:15px;
  }
</style>
<div class="container">
  <div class="card">
    <div class="card-body">
      <h5>Export Branch Product List</h5>
      <div class="row">
        <div class="col-md-12">
          <h5>Branch</h5>
        </div>
        <div class="col-md-12">
          <select class="form-control" name="branch_id" id="branch_id">
            @foreach($branches as $branch)
              <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-12">
          <button type="button" id="export" class="btn btn-primary">Export</button>
        </div>
        <div class="col-md-12">
          <label style="color:red">You can export each branch stock list in excel format, after modified the stock can import to below section</label>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <div class="card-body">
      <h5>Import Branch Stock List</h5>
      <form method="post" action="{{route('postImportBranchStock')}}" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-12">
            <h5>Please import the branch stock list</h5>
          </div>
          <div class="col-md-12">
            <input type="file" class="form-control" name="branch_stock_list" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
          </div>
          <div class="col-md-12">
            <input type="submit" class="btn btn-primary" value="Import"/>
          </div>
          <div class="col-md-12">
            <label style="color:red">Please wait the browser finish the import process, the larger data you import the longer time needed.</label>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  $(document).ready(function(){

    $("#export").click(function(){
      let branch_id = $("#branch_id").val();
      let csrf = '{{csrf_token()}}';

      swal.fire({
        title : 'Exporting Branch Stock List',
        html  : 'It will take some time to process, please wait awhile.',
        didOpen: () => {
            swal.showLoading()
        },
        backdrop : true,
        allowOutsideClick : false,
      });

      $.post("{{route('postExportBranchStock')}}",
        {
          '_token' : csrf,
          'branch_id': branch_id,
        },
        function(data){
          swal.close();
          window.open(data);
        },'json');
    });
    
  });
</script>

@if(session()->has('result'))
  <script>
    Swal.fire('','Import Successful','success');
  </script>
@endif
@endsection