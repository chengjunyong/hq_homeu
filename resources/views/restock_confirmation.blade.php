@extends('layouts.app')

@section('content')
<style>
	.container{
		min-width: 95%;
	}

	td{
		padding:5px;
	}
</style>
<div class="container">
	<div class="card" style="margin-top: 10px">
		<div class="card-title">
			<h4>Restock Confirmation</h4>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<label>From:</label>
					<input readonly class="form-control" type="text" name="from" value="{{$do_list->from}}">
				</div>
				<div class="col-md-6">
					<label>To:</label>
					<input readonly class="form-control" type="text" name="to" value="{{$do_list->to}}">
				</div>
				<div class="col-md-6">
					<label>DO Number:</label>
					<input readonly class="form-control" type="text" name="do_number" value="{{$do_list->do_number}}">
				</div>
				<div class="col-md-6">
					<label>Total Items:</label>
					<input readonly class="form-control" type="text" name="total_item" value="{{$do_list->total_item}}">
				</div>
				<div class="col-md-6">
					<label>Date Issue:</label>
					<input readonly class="form-control" type="text" name="created_at" value="{{$do_list->created_at}}">
				</div>
				<div class="col-md-6">
					<label>Description:</label>
					<input readonly class="form-control" type="text" name="description" value="{{$do_list->description}}">
				</div>
			</div>

			<form method="post" action="{{route('postRestockConfirmation')}}">
				@csrf
				<input type="text" name="do_number" value="{{$do_list->do_number}}" hidden/>
				<div style="overflow-y: auto;height:425px;margin-top:25px">
					<table style="width:100%;">
						<thead style="background-color: #b8b8efd1">
							<tr>
								<td>No</td>
								<td style="width:5%">Barcode</td>
								<td>Product Name</td>
								<td>Quantity</td>
								<td style="width:10%">Restock Quantity</td>
								<td style="width:10%">Stock Lost Quantity</td>
								<td style="width:10%">Stock Lost Reason</td>
								<td>Remark</td>
							</tr>
							<tbody>
								@foreach($do_detail as $key => $result)
									<input type="text" name="do_detail_id[]" hidden value="{{$result->id}}" />
									<input type="text" name="product_id[]" hidden value="{{$result->product_id}}" />
									<tr>
										<td>{{$key+1}}</td>
										<td>{{$result->barcode}}</td>
										<td>{{$result->product_name}}</td>
										<td class="quantity">{{$result->quantity}}</td>
										<td><input type="number" name="restock_quantity[]" value="{{$result->quantity}}" class="restock_quantity" style="width:50%" required min=0></td>
										<td><input type="number" name="stock_lost_quantity[]" value="0" class="stock_lost_quantity" style="width:50%" required min=0></td>
										<td>
											<select name="stock_lost_reason[]">
												<option value="damaged">Damaged</option>
												<option value="lost">Lost</option>
												<option value="other">Other</option>
											</select>
										</td>
										<td><input type="text" name="remark[]" style="width:100%"></td>
									</tr>
								@endforeach
							</tbody>
						</thead>
					</table>
				</div>
				<input type="submit" value="Confirm" class="btn btn-primary" style="float: right;margin-top: 15px">
			</form>

		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	$("input[type=submit]").click(function(){
		let total = 0;
		let length = $(".restock_quantity").length;
		for(let a=0;a<length;a++){
			total = parseInt($(".restock_quantity")[a].value) + parseInt($(".stock_lost_quantity")[a].value);
			if(total == parseInt($(".quantity")[a].innerHTML)){
				$(".restock_quantity")[a].setCustomValidity("");
			}else{
				$(".restock_quantity")[a].setCustomValidity("Quantity is not talley with the delivery quantity, Please make sure the restock quantity and stock lost quantity is correct");
			}
		}
	});
});
</script>
@endsection