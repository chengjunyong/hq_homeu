@extends('layouts.app')
<title>Stock Write Off History</title>
@section('content')
<style>
  .container{
    min-width:95%;
    margin-top: 10px;
  }
</style>

<div class="container">
  <div class="card">
    <div class="title">
      <h4 style="margin:20px">Stock Write Off History Record</h4>
    </div>
    <div class="card-body">
      <div style="float:right">
<!--         <input type="text" id="search" placeholder="" class="form-control" style="margin-bottom: 15px"/> -->
      </div>
      <div class="table table-responsive">
        <table id="history" style="width:100%">
          <thead style="background: #b8b8efd1">
            <tr>
              <td>No</td>
              <td>Reference No</td>
              <td>Total Item</td>
              <td>Total Value</td>
              <td>Write Off By</td>
              <td>Write Off Date</td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            @foreach($wf as $key => $result)
              <tr>
                <td>{{$key + 1}}</td>
                <td>{{$result->seq_no}}</td>
                <td>{{$result->total_item}}</td>
                <td>{{$result->total_amount}}</td>
                <td>{{$result->created_by}}</td>
                <td>{{date('Y-m-d h:i:s A',strtotime($result->write_off_date))}}</td>
                <td>
                  <button class="btn btn-primary" onclick="window.location.assign('{{route('getWriteOffPrint',$result->id)}}')">Print</button>
                  <button val="{{$result->id}}" class="btn btn-danger delete">Delete</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div style="float:right">{{ $wf->links() }}</div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  $(".delete").click(function(){
    let id = $(this).attr('val');
    Swal.fire({
      title:'Delete Invoice',
      html:'Are you sure to delete this invoice. This action is irreversible',
      icon:'warning',
      showCancelButton:'Cancel',
      confirmButtonText:'Delete It !',
      reverseButtons: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $.get("{{route('ajaxDeleteWriteOffRecord')}}",
        {
          'id':id,
        },function(data){
          if(data){
            swal.fire({
              title:"Success",
              html:"Record Removed Successful",
              icon:'success'
            }).then(()=>{
              window.location.reload();
            });
          }
        },'json');
        
      }
    });
  });

});
</script>
@endsection