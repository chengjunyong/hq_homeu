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
      <h4 style="margin:20px">Invoice History Record</h4>
    </div>
    <div class="card-body">
      <div style="float:right">
<!--         <input type="text" id="search" placeholder="" class="form-control" style="margin-bottom: 15px"/> -->
      </div>
      <div class="table table-responsive">
        <div class="filter">
          <h5 onclick="$('#filter-section').collapse('toggle')" style="background:#afe64c;cursor:pointer;padding: 10px;border-radius: 20px;">Filter Section<span style="float:right;font-size:25px;"><i class="fa fa-arrow-circle-down"></i></span></h5>
          <div class="collapse" id="filter-section">
            <form method="get" action="{{route('getInvoicePurchaseHistory')}}">
              <input type="text" name="filter" value=true hidden />
              <div class="row" style="margin:10px">
                <div class="col-md-3">
                  <div>
                    <label>Reference No</label>
                  </div>
                  <input type="text" name="ref_no" class="form-control" value="{{(isset($_GET['ref_no'])) ? $_GET['ref_no'] : '' }}" />
                </div>
                <div class="col-md-3">
                  <div>
                    <label>Invoice No</label>
                  </div>
                  <input type="text" name="inv_no" class="form-control" value="{{(isset($_GET['inv_no'])) ? $_GET['inv_no'] : '' }}" />
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <button type="button" class="btn btn-success" onclick="window.location.href='{{route('getInvoicePurchaseHistory')}}'" style="font-size: 18px;padding: 8px 5vw">Reset</button>
              </div>
            </form>
          </div>
        </div>
        <table id="history" style="width:100%">
          <thead style="background: #b8b8efd1">
            <tr>
              <td>No</td>
              <td>Reference No</td>
              <td>Invoice Number</td>
              <td>Supplier Name</td>
              <td>Total Item</td>
              <td>Total Value</td>
              <td>Date Completed</td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            @foreach($history as $key => $result)
              <tr>
                <td>{{$key + 1}}</td>
                <td>{{$result->reference_no}}</td>
                <td>{{$result->invoice_no}}</td>
                <td>{{$result->supplier_name}}</td>
                <td>{{$result->total_item}}</td>
                <td>Rm {{number_format($result->total_cost,2)}}</td>
                <td>{{$result->created_at}}</td>
                <td>
                  <button class="btn btn-primary" onclick="window.location.assign('{{route('getInvoicePurchaseHistoryDetail',$result->id)}}')">Details</button>
                  <button val="{{$result->id}}" class="btn btn-danger delete">Delete</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div style="float:right">{{ $history->links() }}</div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  $(".delete").click(function(){
    let a = $(this).attr('val');
    Swal.fire({
      title:'Delete Invoice',
      html:'Are you sure to delete this invoice. This action is irreversible',
      icon:'warning',
      showCancelButton:'Cancel',
      confirmButtonText:'Delete It !',
      reverseButtons: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $.post("{{route('ajaxDeleteInvoice')}}",
        {
          '_token': '{{csrf_token()}}',
          'id':a,
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