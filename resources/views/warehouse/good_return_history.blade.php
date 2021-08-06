@extends('layouts.app')
<title>Invoice History</title>
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
      <h4 style="margin:20px">Good Return History Record</h4>
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
              <td>GR No</td>
              <td>Reference Number</td>
              <td>Supplier Name</td>
              <td>Total Item</td>
              <td>Total Value</td>
              <td>Date Completed</td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            @foreach($gr_list as $key => $result)
              <tr>
                <td>{{$key + 1}}</td>
                <td>{{$result->gr_no}}</td>
                <td>{{$result->ref_no}}</td>
                <td>{{$result->supplier_name}}</td>
                <td>{{$result->total_quantity}}</td>
                <td>Rm {{number_format($result->total_cost,2)}}</td>
                <td>{{$result->created_at}}</td>
                <td>
                  <button class="btn btn-primary" onclick="window.location.assign('{{route('getGoodReturnHistoryDetail',$result->id)}}')">Details</button>
                  <button val="{{$result->gr_no}}" class="btn btn-danger delete">Delete</button>
                  <button class="btn btn-dark" onclick="window.open('{{route('getPrintGr',$result->id)}}')">Print</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div style="float:right">{{ $gr_list->links() }}</div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  $(".delete").click(function(){
    let a = $(this).attr('val');
    Swal.fire({
      title:'Delete GR Record',
      html:'Are you sure to delete this record. This action is irreversible',
      icon:'warning',
      showCancelButton:'Cancel',
      confirmButtonText:'Delete It !',
      reverseButtons: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $.post("{{route('ajaxDeleteGr')}}",
        {
          '_token': '{{csrf_token()}}',
          'gr_id':a,
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