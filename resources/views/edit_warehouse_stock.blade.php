@extends('layouts.app')

@section('content')
<style>
.row > .col-md-12,.col-md-6{
	margin-top: 10px;
}
</style>
<div class="container">
	<h2 align="center">Edit Warehouse Product</h2>
	<div class="card" style="border-radius: 1.25rem">
		<div class="card-title" style="padding: 10px">
			<h4>Modify Warehouse Product Information</h4>
		</div>
		<div class="card-body">
			<form method="post" action="{{route('postModifyWarehouseProduct')}}" id="form">
				@csrf
				<div class="row">
					<div class="col-md-12">
						<label>Department</label>
						<select name="department" id="department" class="form-control" required>
							@foreach($department as $result)
								<option value="{{$result->id}}" {{($warehouse_stock->department_id == $result->id) ? 'selected' : ''}}>{{$result->department_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-12">
						<label>Category</label>
						<select name="category" id="category" class="form-control" required>
							@foreach($category as $result)
								<option value="{{$result->id}}" {{($warehouse_stock->category_id == $result->id) ? 'selected' : ''}}>{{$result->category_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-12">
						<label>Barcode</label>
						<input type="text" name="barcode" class="form-control" readonly value="{{$warehouse_stock->barcode}}">
					</div>
					<div class="col-md-12">
						<label>Product Name</label>
						<input type="text" name="product_name" class="form-control" required value="{{$warehouse_stock->product_name}}">
					</div>
					<div class="col-md-6">
						<label>Cost</label>
						<input type="number" min="0" step="0.01" name="cost" id="cost" class="form-control" required value="{{$warehouse_stock->cost}}">
					</div>
          <div class="col-md-6">
            <label>Stock Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" required value="{{$warehouse_stock->quantity}}">
          </div>
					<div class="col-md-6">
						<label>Reorder Level</label>
						<input type="number" min="0" step="1" name="reorder_level" class="form-control" required value="{{$warehouse_stock->reorder_level}}">
					</div>
					<div class="col-md-6">
						<label>Reorder Recommend Quantity</label>
						<input type="number" min="0" step="1" name="recommend_quantity" class="form-control" required value="{{$warehouse_stock->reorder_quantity}}">
					</div>
					<div class="col-md-12" style="text-align: center;margin-top: 20px">
						<input type="submit" class="btn btn-primary" value="Update">
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

});

</script>
@if(session()->has('result'))
<script>
	$("#msg").text("Product Update Successful");
	$("#result").modal('toggle');
</script>
@endif

@endsection