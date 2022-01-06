@extends('layouts.app')
<title>Good Return History Details</title>
@section('content')
<style>
  .container{
    min-width: 95%;
  }

  td{
    padding:5px;
  }

  #supplier{
    cursor: pointer;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 33px;
  }

  .select2-container .select2-selection--single{
    height: 36px;
  }

  .select2-container--default .select2-selection--single{
    border: 1px solid #ced4da;
  }

  .add-btn{
    position: fixed;
    width: 60px;
    height: 60px;
    bottom: 110px;
    right: 8px;
    background-color: #7b3cd1;
    color: #FFF;
    border-radius: 50px;
    text-align: center;
    box-shadow: 2px 2px 3px #999;
    z-index: 99;
    font-size: 30px;
  }

  .add-section div{
    margin-top: 10px;
  }

</style>
<div class="container">
  <div class="card" style="margin-top: 10px">
    <div class="card-title">
      <h4>GR History Details</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <label>GR No:</label>
          <input readonly class="form-control" name="gr_no" type="text" value="{{$gr->gr_no}}">
        </div>
        <div class="col-md-6">
          <label>GR Date:</label>
          <input readonly class="form-control" type="text" value="{{$gr->gr_date}}">
        </div>
        <div class="col-md-6">
          <label>Reference No:</label>
          <input readonly class="form-control" type="text" value="{{$gr->ref_no}}">
        </div>
        <div class="col-md-6">
          <label>Supplier Name:</label>
          <input readonly class="form-control" type="text" id="supplier" value="{{$gr->supplier_name}}">
        </div>
        <div class="col-md-6">
          <label>Total Items:</label>
          <input readonly class="form-control" type="text" value="{{$gr->total_different_item}}">
        </div>
        <div class="col-md-6">
          <label>Total Items Quantity:</label>
          <input readonly class="form-control" type="text" value="{{$gr->total_quantity}}">
        </div>
        <div class="col-md-6">
          <label>Total Value:</label>
          <input readonly class="form-control" type="text" value="Rm {{number_format($gr->total_cost,2)}}">
        </div>
        <div class="col-md-6">
          <label>Record Creator:</label>
          <input readonly class="form-control" type="text" value="{{$gr->creator_name}}">
        </div>
        <div class="col-md-6">
          <label>Created At:</label>
          <input readonly class="form-control" type="text" value="{{$gr->created_at}}">
        </div>
      </div>

      <form method="post" action="{{route('postGoodReturnHistoryDetail')}}">
        @csrf
        <input type="text" name="gr_no" value="{{$gr->gr_no}}" hidden/>
        <div style="overflow-y: auto;height:425px;margin-top:25px">
          <table style="width:100%;">
            <thead style="background-color: #b8b8efd1">
              <tr>
                <td>No</td>
                <td>Barcode</td>
                <td>Product Name</td>
                <td align="center">Measurement</td>
                <td align="center">Quantity</td>
                <td align="center">Cost</td>
                <td align="right">Total Value</td>
                <td></td>
              </tr>
              <tbody>
                @foreach($gr_detail as $key => $result)
                  <input type="text" name="gr_detail_id[]" value="{{$result->id}}" hidden/>
                  <input type="text" name="barcode[]" value="{{$result->barcode}}" hidden/>
                  <tr>
                    <td>{{$key +1}}</td>
                    <td>{{$result->barcode}}</td>
                    <td>{{$result->product_name}}</td>
                    <td align="center">{{ucfirst($result->measurement)}}</td>
                    <td align="center"><input type="number" class="quantity" name="quantity[]" {{($result->measurement == 'unit') ? 'min=1' : 'min=0.001'}} {{($result->measurement == 'unit') ? 'step=1' : 'step=0.001'}} value="{{$result->quantity}}" style="text-align: right;width:8vw" required /></td>
                    <td align="center">Rm <input type="number" class="cost" name="cost[]" min="0.00" step="0.001" value="{{number_format($result->cost,3,'.','')}}" style="text-align: right;width:7vw"/></td>                    
                    <td align="right">Rm <input type="number" class="total" name="total[]" min="0.00" step="0.001" value="{{number_format($result->total_cost,2,'.','')}}" style="text-align: right;width:10vw" required /></td>
                    <td align="right"><button style="padding:3px 10px;" class="btn btn-danger delete-item" type="button" val="{{$result->id}}">Remove</button></td>
                  </tr>
                @endforeach
              </tbody>
            </thead>
          </table>
        </div>
        <div class="row" style="margin-top:30px;">
          <div class="col-md-12" style="text-align: center">
            <input type="submit" class="btn btn-primary" value="Modify"/>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>

<div class="modal fade" id="supplier-modal" aria-hidden="true" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="max-width: 50%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Change Supplier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4" style="margin-top: 15px;">
            <label>Change Supplier :</label>
          </div>
          <div class="col-md-8" style="margin-top: 15px;">
            <select name="supplier_id" id="supplier_id" style="width:100%;">
              @foreach($supplier as $result)
                <option value="{{$result->id}}" {{($gr->supplier_id == $result->id) ? 'selected' : ''}}>{{$result->supplier_name}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="change-supplier" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add-item-modal" aria-hidden="true" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="max-width: 50%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Items</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-3">
            <label>Barcode :</label>
          </div>
          <div class="col-md-9">
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Barcode" id="add-barcode">
              <div class="input-group-append">
                <button class="btn btn-primary" id="searchBarcode">Check</button>
              </div>
            </div>
          </div>
        </div>
        <div class="row add-section">
          <div class="col-md-3">
            <label>Product Name</label>
          </div>
          <div class="col-md-9">
            <input type="text" class="form-control" id="add-product-name" readonly>
          </div>

          <div class="col-md-3">
            <label>Cost</label>
          </div>
          <div class="col-md-9">
            <input type="number" step="0.0001" min="0" id="add-cost" class="form-control">
          </div>

          <div class="col-md-3">
            <label>Quantity</label>
          </div>
          <div class="col-md-9">
            <input type="number" min="0" id="add-quantity" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="add-item" class="btn btn-primary">Add</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<button type="button" id="add-btn" class="btn btn-primary add-btn"><i class="fa fa-plus"></i></button>

<script>
$(document).ready(function(){
  let checked_barcode = '';
  $('#supplier_id').select2();

  $(".total").on("keyup change",function(){
    let quantity = parseInt($(this).parent().siblings().eq(4).children().val());
    let total = parseFloat($(this).val());
    let result = total / quantity;
    $(this).parent().siblings().eq(5).children().val(result.toFixed(3));
  });

  $(".cost").on("keyup change",function(){
    let quantity = parseInt($(this).parent().siblings().eq(4).children().val());
    let cost = parseFloat($(this).val());
    let result = cost * quantity;
    $(this).parent().siblings().eq(5).children().val(result.toFixed(3));
  });

  $(".quantity").on("keyup change",function(){
    let total = parseFloat($(this).parent().siblings().eq(5).children().val());
    let cost = parseInt($(this).parent().siblings().eq(4).children().val())
    let quantity = $(this).val();
    if(total){
      let result = total / quantity;
      $(this).parent().siblings().eq(4).children().val(result.toFixed(3));
    }  
  });

  $("#supplier").click(function(){
    $("#supplier-modal").modal('show');
  });

  $("#change-supplier").click(function(){
    $.get("{{route('ajaxChangeSupplier')}}",
    {
      'id': $("#supplier_id").val(),
      'gr_no': $("input[name=gr_no]").val(),
    },function(data){
      if(data){
        window.location.reload();
      }else{
        swal.fire('Error','Change Supplier Unsuccessful, Please Try Again','error');
      }
    },'json');
  })

  $(".delete-item").click(function(){
    let id = $(this).attr('val');
    swal.fire({
      title:'Delete Item',
      html:'Are you sure to delete this item. This action is irreversible',
      icon:'warning',
      showCancelButton:'Cancel',
      confirmButtonText:'Delete !',
    }).then((result)=>{
      if(result.isConfirmed){
        $.get("{{route('ajaxDeleteGrItem')}}",
        {
          'id': id
        },function(data){
          console.log(data);
          if(data){
            window.location.reload();
          }else{
            swal.fire('Error','Delete Unsuccessful Please Try Again Later','error');
          }
        },'json');
      }
    });

  });

  $("#add-btn").click(function(){
    $("#add-item-modal").modal('show');
  });

  $("#searchBarcode").click(function(){
    $("#searchBarcode").prop('disabled',true);
    $.get("{{route('ajaxSearchBar')}}",
    {
      'barcode': $("#add-barcode").val()
    },function(data){ 
      if(data != false){
        checked_barcode = $("#add-barcode").val();
        $("#add-product-name").val(data.product_name);
        $("#add-cost").val(data.cost);
        $("#add-quantity").val(0);
      }else{
        swal.fire('Error','Barcode Not Found','error');
      }
      $("#searchBarcode").prop('disabled',false);
    },'json');

  });

  $("#add-item").click(function(){
    $(this).prop('disabled',true);
    if($("#add-barcode").val().trim() == '' || $("#add-cost").val().trim() == '' || $("#add-quantity").val().trim() == ''){
      swal.fire('Error','Please fill up all the fields','error');
      $(this).prop('disabled',false);
    }else{
      if($("#add-cost").val() <= 0 || $("#add-quantity").val() <= 0){
        swal.fire('Error','Cost & Quantity cannot be 0','error');
        $(this).prop('disabled',false);
      }else{
        $.get("{{route('ajaxAddGrItem')}}",
        {
          'gr_no' : $("input[name=gr_no]").val(),
          'barcode': checked_barcode,
          'cost': $("#add-cost").val(),
          'quantity': $("#add-quantity").val(),
        },function(data){
          if(data){
            window.location.reload();
          }else{
            swal.fire('Error','Add Unsuccessful Please Try Again Later','error');
            $("#add-item").prop('disabled',false);
          }

        },'json');
      }
    }
  });

});
</script>
@if(session()->has('success'))
  <script>
    Swal.fire("Success","Update Successful",'success');
  </script>
@endif

@endsection