@extends('layouts.app')
<title>Manual Order List Detail</title>
@section('content')
<style>
  .container{
    min-width: 95%;
  }

  td{
    padding:5px;
  }
</style>
<div class="container" style="padding-bottom: 5vh;">
  <div class="card" style="margin-top: 10px">
    <div class="card-title">
      <h4>Manual Order List Detail</h4>
    </div>

    <form>
      @csrf
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <label>Supplier:</label>
            <input readonly class="form-control" type="text" name="supplier" value="{{$supplier->supplier_name}}">
            <input hidden class="form-control" type="text" name="supplier_id" value="{{$tmp[0]->supplier_id}}">
          </div>
          <div class="col-md-6">
            <label>Total Items:</label>
            <input readonly class="form-control" type="text" name="total_item" value="{{$total_item}}">
          </div>
          <div class="col-md-6">
            <label>Total Cost:</label>
            <input readonly class="form-control" type="text" name="total_cost" value="{{$total_cost}}">
          </div>
          <div class="col-md-6">
            <label>Date Issue:</label>
            <input readonly class="form-control" type="text" name="issue_date" value="{{date("Y-m-d")}}">
          </div>

        <div style="overflow-y: auto;height:425px;margin-top:25px;width:100%">
          <table class="table-responsive" style="width:100%;display: table !important;">
            <thead style="background-color: #b8b8efd1">
              <tr>
                <td>No</td>
                <td style="width:20%">Barcode</td>
                <td>Product Name</td>
                <td align="center">Measurement</td>
                <td align="right">Cost</td>
                <td align="right">Price</td>
                <td align="center">Total Quantity Order</td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              @foreach($tmp as $key => $result)
                <tr id="{{$result->id}}">
                  <td>{{$key+1}}<input type="text" name="product_id[]" value="{{$result->warehouse_stock_id}}" hidden /></td>
                  <td>{{$result->barcode}}<input type="text" name="barcode[]" value="{{$result->barcode}}" hidden /></td>
                  <td>{{$result->product_name}}<input type="text" name="product_name[]" value="{{$result->product_name}}" hidden /></td>
                  <td align="center">{{ucfirst($result->measurement)}}<input type="text" name="measurement[]" value="{{$result->measurement}}" hidden /></td>
                  <td align="right">{{number_format($result->cost,2)}}<input type="text" name="cost[]" value="{{$result->cost}}" hidden /></td>
                  <td align="right">{{number_format($result->price,2)}}<input type="text" name="price[]" value="{{$result->price}}" hidden /></td>
                  <td align="center">{{$result->order_quantity}}<input type="text" name="order_quantity[]" value="{{$result->order_quantity}}" hidden /></td>
                  <td><button type="button" class="btn btn-primary delete" value="{{$result->id}}">Delete</button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12" style="text-align: center">
          <input class="btn btn-primary" type="submit" value="Generate Purchase Order"/>
        </div>
      </div>

    </div>
  </form>

</div>
<script>
$(document).ready(function(){
  $(".delete").click(function(){
    let id = $(this).val();
    Swal.fire({
      title: 'Remove Items',
      html: 'Please confirm if you want to remove this item from list',
      icon: 'warning',
      confirmButtonText: `Yes`,
      showCancelButton: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $("#"+id).remove();
        $.get('{{route('ajaxRemovePurchaseOrderListItem')}}',
        {
          'id' : id
        },function(data){
          if(data == true){
            Swal.fire({
              title: 'Success',
              html: 'Item remove successful',
              icon: 'success',
              confirmButtonText: `Yes`,
            }).then((result)=>{
              window.location.reload();
            });
          }else{
            Swal.fire('Fail','Item remove fail, please contact IT support','error');
          }
        },'json');
      }
    })
  });

  $("form").submit(function(e){
    e.preventDefault();
    Swal.fire({
      title: 'Generate PO',
      html: 'Please make sure all the items in the list are correct, this action is irreversible',
      icon: 'warning',
      confirmButtonText: `Yes`,
      showCancelButton: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $.post('{{route('postManualPurchaseOrderList')}}',$("form").serialize(),
        function(data){
          if(data == true){
            Swal.fire('Success','Purchase order generate completed','success').then(()=>{window.location.assign('{{route('getManualIssuePurchaseOrder')}}')});
          }else{
            Swal.fire('Fail','Item remove fail, please contact IT support','error');
          }
        },"json");
      }
    });
      
  });

});
</script>

@endsection