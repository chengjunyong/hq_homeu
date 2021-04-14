@extends('layouts.app')

@section('content')
<style>
	.container{
		max-width:98%;
		margin-top: 10px;
	}

	.confirm{
		margin-bottom: 10px;
	}

	input[type=checkbox]{
		width:20px;
		height:20px;
	}

	::-webkit-scrollbar {
	  width: 20px;
	}

	::-webkit-scrollbar-track {
	  background-color: transparent;
	}

	::-webkit-scrollbar-thumb {
	  background-color: #d6dee1;
	}

	::-webkit-scrollbar-thumb {
	  background-color: #d6dee1;
	  border-radius: 20px;
	}

	::-webkit-scrollbar-thumb {
	  background-color: #d6dee1;
	  border-radius: 20px;
	  border: 6px solid transparent;
	  background-clip: content-box;
	}

	::-webkit-scrollbar-thumb:hover {
  	background-color: #a8bbbf;
	}

</style>

<div class="container">
	<div class="card">
		<div class="card-body">
			<h4>Purchase Order</h4>
			<div class="row">
				<div class="col-md-4">
					<label>Purcahse Order Date</label>
					<input type="date" name="po_date" class="form-control" id="po_date" required/>
				</div>
				<div class="col-md-4">
					<label>Supplier</label>
					<select class="form-control" name="supplier_id" required id="supplier_id">
						<option disabled selected="">Please Select Supplier</option>
						@foreach($supplier as $result)
							<option value="{{$result->id}}">{{$result->supplier_name}}</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>
	</div>

	<form id="po_form">
		@csrf
		<div class="row" style="margin-top: 5px">
			<div class="col-md-5">
				<div class="card">
					<div class="card-body custom-scrollbar" style="min-height:650px;height:650px;overflow-y: scroll;">
						<table class="table table-responsive" id="po_table" style="width:100%">
							<thead>
								<tr>
	<!-- 								<td></td>
									<td>No</td>
									<td>Department</td>
									<td>Category</td>
	 -->						<td>Barcode</td>
									<td style="width:50%">Name</td>
									<td>Current Stock</td>
									<td>Reorder Level</td>
									<td>Recommend Quantity</td>
								</tr>
							</thead>
							<tbody>
								@foreach($items as $key => $result)
									<tr>
	<!-- 									<td><input type="checkbox" name="id[]" value="{{$result->id}}" class="form-control"/></td>
										<td>{{$key+1}}</td>
										<td>{{$result->department_name}}</td>
										<td>{{$result->category_name}}</td> -->
										<td>{{$result->barcode}}</td>
										<td>{{$result->product_name}}</td>
										<td>{{$result->quantity}}</td>
										<td>{{$result->reorder_level}}</td>
										<td>{{$result->reorder_quantity}}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
				
			<div class="col-md-4">
				<div class="card">
					<div class="card-body custom-scrollbar" style="min-height:650px;height:650px;overflow-y: scroll;">
						<h5 style="margin-bottom: 10px;border-bottom:2px solid black">Order Details</h5>
						@foreach($items as $result)
							<div class="row" style="margin-bottom: 10px" id="row{{$result->id}}">
								<div class="col-md-9">
									<label>{{$result->product_name}}</label>
								</div>
								<div class="col-md-2" style="padding: 0px">
									<input type="number" step="1" name="product_quantity[]" required min=1 style="width:100%;padding:6px 2px;" class="form-control" value="{{($result->reorder_quantity == null) ? '1' : $result->reorder_quantity }}" />
									<input type="text" name="product_id[]" value="{{$result->id}}" hidden />
								</div>
								<div class="col-md-1" style="padding: 0px 3px">
									<i class="fa fa-times-circle" style="cursor:pointer" ref="row{{$result->id}}"></i>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-body custom-scrollbar" style="min-height:650px">
						<h5>Supplier Details</h5>
						<label>Purchase Order Issue Date</label><br/>
						<input type="date" name="issue_date" class="form-control confirm" readonly value="">
						<label>To Supplier</label><br/>
						<input type="text" name="supplier_name" class="form-control confirm" readonly value="">
						<input type="text" name="supplier_id" value="" hidden >
						<label>Supplier Code</label><br/>
						<input type="text" name="supplier_code" class="form-control confirm" readonly value="">
						<label>Contact Number</label><br/>
						<input type="text" name="contact_number" class="form-control confirm" readonly value="">
						<label>Email</label><br/>
						<input type="text" name="email" class="form-control confirm" readonly value="">
						<button type="button" style="margin-top: 15px" class="btn btn-primary" id="issue_po">Generate PO</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
$(document).ready(function(){
	let table = $("#po_table").dataTable({
			'responsive':true,
			'ordering': false,
			"lengthMenu": [ 7 ],
		});

	$(".fa-times-circle").click(function(){
		let id = $(this).attr('ref');
		$("#"+id).remove();
	});

	$("#po_date").change(function(){
		$("input[name=issue_date]").val($(this).val());
	});

	$("#supplier_id").change(function(){
		$.get("{{route('ajaxGetSupplier')}}",
		{
			'id':$(this).val(),
		},function(data){
			$("input[name=supplier_name]").val(data['supplier_name']);
			$("input[name=supplier_code]").val(data['supplier_code']);
			$("input[name=supplier_id]").val(data['id']);
			if(data['contact_number'] == "null")
				$("input[name=contact_number]").val("Not Available");
			else
				$("input[name=contact_number]").val(data['contact_number']);

			if(data['email'] == "null")
				$("input[name=email]").val("Not Available");
			else
				$("input[name=email]").val(data['email']);
		},"json");
	});

	$("#issue_po").click(function(){
		if($("#supplier_id").val() == null || $("#po_date").val() == ""){
			swal.fire(
				'Warning',
				'Please select supplier and fill up the purchase order date',
				'error'
			);
		}else{
			$.post("{{route('ajaxPO')}}",
				$("form").serialize(),
				function(data){
					console.log(data);
					if(data['success'] == 1){
						Swal.fire({
							title:'Purchase Order Generate Successful',
							confirmButtonText: 'Redirect',
							icon: 'success',
						}).then((result)=>{
							if(result.isConfirmed){
								window.location.assign('{{$url}}');
								window.open(data['url']);
							}
						})
					}
				},"json");	
		}
	});

});
</script>

@endsection