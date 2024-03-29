@extends('layouts.app')
<title>Add Warehouse Product</title>
@section('content')
<style>
.row > .col-md-12,.col-md-6{
	margin-top: 10px;
}
</style>
<div class="container">
	<h2 align="center">Add Warehouse Product</h2>
	<div class="card" style="border-radius: 1.25rem">
		<div class="card-title" style="padding: 10px">
			<h4>Insertw Information</h4>
		</div>
		<div class="card-body">
			<form method="post" action="{{route('postAddWarehouseProduct')}}" id="form">
				@csrf
				<div class="row">
					<div class="col-md-12">
						<label>Department</label>
						<select name="department" id="department" class="form-control" required>
							@foreach($department as $result)
								<option value="{{$result->id}}">{{$result->department_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-12">
						<label>Category</label>
						<select name="category" id="category" class="form-control" required>
							@foreach($category as $result)
								<option value="{{$result->id}}">{{$result->category_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-12">
						<label>Barcode</label>
						<input type="text" name="barcode" class="form-control" required>
					</div>
					<div class="col-md-12">
						<label>Product Name</label>
						<input type="text" name="product_name" class="form-control" required>
					</div>
					<div class="col-md-6">
						<label>Cost</label>
						<input type="number" min="0" step="0.01" name="cost" id="cost" class="form-control" required>
					</div>
					<div class="col-md-6">
						<label>Reorder Level</label>
						<input type="number" min="0" step="1" name="reorder_level" class="form-control" required>
					</div>
					<div class="col-md-6">
						<label>Reorder Recommend Quantity</label>
						<input type="number" min="0" step="1" name="recommend_quantity" class="form-control" required>
					</div>
					<div class="col-md-12" style="text-align: center;margin-top: 20px">
						<input type="submit" class="btn btn-primary" value="Add">
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
        <h5 class="modal-title" id="title">Create Product Status</h5>
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

<script>
$(document).ready(function(){

	$("#department").change(function(){
		$.get('{{route('ajaxGetCategory')}}',{'department_id' : $("#department").val()},
			function(data){
				$("#category").html("");
				Object.entries(data).forEach(([key, result]) => {
					$("#category").append(`<option value='${result['id']}'>${result['category_name']}</option>`)
				});
			},"json");
	});

	$("input[name=barcode]").on("input",function(){
		let barcode = $(this).val();
		let target = $(this)[0];
		$.get('{{route('ajaxGetBarcode')}}',{'barcode': barcode},
			function(data){
				if(data != "true"){
					target.setCustomValidity('');
				}else{
					target.setCustomValidity('Barcode duplicate, please use other code');
				}
			},"html");
	});
	
});

</script>
@if(session()->has('result'))
<script>
	$("#msg").text("Product Create Successful");
	$("#result").modal('toggle');
</script>
@endif

@endsection