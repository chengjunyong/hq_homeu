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
        <div class="filter">
          <h5 onclick="$('#filter-section').collapse('toggle')" style="background:#afe64c;cursor:pointer;padding: 10px;border-radius: 20px;">Filter Section<span style="float:right;font-size:25px;"><i class="fa fa-arrow-circle-down"></i></span></h5>
          <div class="collapse" id="filter-section">
            <form method="get" action="{{route('getPurchaseOrderHistory')}}">
              <input type="text" name="filter" value=true hidden />
              <div class="row" style="margin:10px">
                <div class="col-md-4">
                  <div>
                    <label>Purchse Order No</label>
                  </div>
                  <input type="text" name="po_no" class="form-control" value="{{(isset($_GET['po_no'])) ? $_GET['po_no'] : '' }}" />
                </div>
                <div class="col-md-4">
                  <div>
                    <label>Supplier</label>
                  </div>
                  <select class="form-control" name="supplier">
                    <option value="null">No Selected</option>
                    @foreach($supplier as $result)
                      <option value="{{$result->id}}" {{isset($_GET['supplier']) && $_GET['supplier'] == $result->id ? 'selected' : ''}}>{{$result->supplier_name}}</option>
                    @endforeach 
                  </select>
                </div>
                <div class="col-md-4">
                  <div>
                    <label>Date Start</label>
                  </div>
                  <input type="date" name="date_start" class="form-control" value="{{(isset($_GET['date_start']) ? $_GET['date_start'] : '')}}"/>
                  <br/>
                  <div>
                    <label>Date End</label>
                  </div>
                  <input type="date" name="date_end" class="form-control" value="{{(isset($_GET['date_end']) ? $_GET['date_end'] : '')}}"/>
                </div>
              </div>
              <div class="col-md-12" style='text-align: center;'>
                <input type="submit" class='btn btn-primary' value="Filter" style="font-size: 18px;padding: 8px 5vw"/>
                <button type="button" class="btn btn-success" onclick="window.location.href='{{route('getPurchaseOrderHistory')}}'" style="font-size: 18px;padding: 8px 5vw">Reset</button>
              </div>
            </form>
          </div>
        </div>
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