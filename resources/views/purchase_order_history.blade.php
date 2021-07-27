@extends('layouts.app')
<title>Purchase Order History</title>
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
      <h4 style="margin: 20px">Purchase Order History</h4>
    </div>
    <div class="card-body">
      <div style="float:right">
<!--         <input type="text" id="search" placeholder="Search DO Number" class="form-control" style="margin-bottom: 15px"/> -->
      </div>
      <div class="table">
        <table id="history" style="width:100%">
          <thead style="background: #b8b8efd1">
            <tr>
              <td>No</td>
              <td>PO Number</td>
              <td>To Supplier</td>
              <td>Quantity Item</td>
              <td>Completed</td>
              <td>Date Issue</td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            @foreach($po as $key => $result)
              <tr>
                <td>{{$key + 1}}</td>
                <td><a href="{{ route('getPoHistoryDetail',$result->po_number) }}">{{$result->po_number}}</a></td>
                <td>{{$result->supplier_name}}</td>
                <td>{{$result->total_quantity_items}}</td>
                <td>{{($result->completed == 0)? 'No' : 'Yes'}}</td>
                <td>{{$result->created_at}}</td>
                <td>
                  <buttton class="btn btn-primary" onclick="window.open('{{route('getGeneratePurchaseOrder',$result->id)}}')">Print</buttton>
                  <buttton class="btn btn-danger delete" ref_id="{{$result->id}}">Delete</buttton>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div style="float:right">{{ $po->links() }}</div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){

  $("#search").keypress(function(e){
    let header = "{{route('getDoHistory')}}";
    if(e.keyCode == 13){
      let target = $("#search").val();
      header = `${header}?search=${target}`;
      window.location.assign(header);
    }
  });

  $(".delete").click(function(){
    let id = $(this).attr('ref_id');
    swal.fire({
      title:'Delele PO',
      html:'Are you sure to delete this purchase order',
      icon:'warning',
      confirmButtonText:'Delete It',
      showCancelButton: true,
    }).then((result)=>{
      if(result.isConfirmed){       
        $.get('{{route('getDeletePurchaseOrder')}}',
        {
          'id': id,
        },function(data){
          if(data){
            swal.fire('Successful','Delete Successful. You will be redirect in few second','success');
            setTimeout(()=>{window.location.reload()},'1000');
          }else{
            swal.fire('Error','Delete Unsuccessful, Please Contact IT Support','error');
          }
        },'json');
      }
    });
  });

});
</script>
@endsection