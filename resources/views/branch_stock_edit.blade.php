@extends('layouts.app')
<title>Branch Product Edit</title>
@section('content')
<div class="container">
	<h2 align="center">Branch Stock</h2>
	<div class="card" style="border-radius: 1.25rem">
		<div class="card-title" style="padding: 10px">
			<h4>Modify Branch Stock Information</h4>
		</div>
		<div class="card-body">
			<form method="post" id="form">
				@csrf
				<input type="text" value="{{$product->id}}" name="id" hidden /> 
				<div class="row"> 
					<div class="col-md-6">
						<label>Department</label>
						<input type="text" class="form-control" value="{{$product->department_name}}" disabled />
					</div>
					<div class="col-md-6">
						<label>Category</label>
						<input type="text" class="form-control" value="{{$product->category_name}}" disabled />
					</div>
					<div class="col-md-6">
						<label>Barcode</label>
						<input type="text" name="barcode" class="form-control" readonly value="{{$product->barcode}}" disabled>
					</div>
					<div class="col-md-6">
						<label>Product Name</label>
						<input type="text" name="product_name" class="form-control" value="{{$product->product_name}}" disabled>
					</div>
          <div class="col-md-6">
            <label>Measurement Type</label>
            <input type="text" name="uom" class="form-control" value="{{ucfirst($product->measurement)}}" disabled>
          </div>
					<div class="col-md-6">
						<label>Cost</label>
						<input type="number" min="0" step="0.0001" name="cost" id="cost" class="form-control" value="{{$product->cost}}" disabled>
					</div>
					<div class="col-md-6">
						<label>Price</label>
						<input type="number" min="0" step="0.0001" name="price" id="price" class="form-control" value="{{$product->price}}">
					</div>
					<div class="col-md-6">
						<label>Reorder Level</label>
						<input type="number" min="0" step="1" name="reorder_level" class="form-control" required value="{{$product->reorder_level}}">
					</div>
					<div class="col-md-6">
						<label>Reorder Recommend Quantity</label>
						<input type="number" min="0" step="1" name="recommend_quantity" class="form-control" required value="{{$product->recommend_quantity}}">
					</div>
					<div class="col-md-6">
						<label>Stock Quantity</label>
						<input type="number" min="0" step="1" name="stock_quantity" class="form-control" value="{{round($product->quantity,3)}}" readonly>
					</div>
					<div class="col-md-12" style="text-align: center;margin-top: 20px">
						<input type="submit" name="submit" hidden>
						<button type="button" id="submit" class="btn btn-primary">Update</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="result" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">Modify Product Status</h5>
      </div>
      <div class="modal-body" style="text-align: center">
        <h4 id="msg"></h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirm" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">Confirmation</h5>
      </div>
      <div class="modal-body" style="text-align: center">
        <h4>Are you sure to update the stock quantity on this product ?</h4>
      </div>
      <div class="modal-footer">
      	<button type="button" id="yes" class="btn btn-primary" data-dismiss="modal">Yes</button>
        <button type="button" id="no" class="btn btn-secondary" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
	$("#submit").click(function(e){
		$("#confirm").modal('toggle');
	});

	$("#yes").click(function(){
    let measurement = '{{$product->measurement}}';
    let qty = $("input[name=stock_quantity]").val();
    let decimals = (qty!=Math.floor(qty))?(qty.toString()).split('.')[1].length:0;
    if(measurement != 'unit' && decimals > 3){
      swal.fire('Error','Stock Quantity Cannot More Than 3 Decimal Place','error');
    }else if(measurement == 'unit' && decimals != 0){
      swal.fire('Error','Stock Quantity Cannot Be Decimal Number','error');
    }else{
  		$.post('{{route('postModifyBranchStock')}}',
      $("form").serialize(),
			function(data){
				if(data == 'success'){
					$("#msg").text('Update Successful');
				}else{
					$("#msg").text('Update Fail, please contact IT support');
				}
				$("#result").modal('toggle');
			},'html');
    }
	});

});
</script>


@endsection