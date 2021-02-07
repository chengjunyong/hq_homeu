@extends('layouts.app')

@section('content')
<style>
	.container{
		max-width:98%;
	}

	.branch > .card-body{
		padding:0.5px;
	}

	.branch{
		margin: 10px 0px;
	}

	#branch{
		width:30%;
		margin: 10px 5px;
	}

	table{
		width:100%;
	}

	td{
		border:1px solid black;
	}

	#stock_list_paginate{
		margin-top: 13px;
	}
</style>

<h2 align="center">Branch Product Restock</h2>
<div class="container">
	<div class="card branch">
		<div class="card-body">
			<h4 style="margin-left:5px">Branch</h4>
			<select class="form-control" id="branch">
				<option value="{{route('getBranchRestock')}}">None</option>
				@foreach($branch as $result)
					<option value="{{route('getBranchRestock')}}?branch_id={{$result->id}}" {{ ($result->id == $branch_id) ? 'selected' : '' }}>{{$result->branch_name}}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="row">
		<div class="col-md-7">
			<div class="card">
				<div class="card-title">
					<h4>Stock List</h4>
				</div>
				<div class="card-body">
					<table id="stock_list" class="display">
						<thead>
							<tr style="background: #b2b43c;">
								<td>No</td>
								<td>Barcode</td>
								<td>Product Name</td>
								<td align="center" style="width:5%">Current Stock Quantity</td>
								<td align="center" style="width:10%">Reorder Level</td>
								<td align="right" style="width:10%">Recommend Quantity</td>
							</tr>
						</thead>
						<tbody>
							@foreach($branch_product as $key => $result)
								<tr>
									<td>{{$key + 1}}</td>
									<td style="width:20%">{{$result->barcode}}</td>
									<td>{{$result->product_name}}</td>
									<td align="center">{{$result->quantity}}</td>
									<td align="right">{{$result->reorder_level}}</td>
									<td align="right">{{$result->recommend_quantity}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-5" style="padding-left: 0px">
			<div class="card" style="overflow-y: auto;height: calc(100vh - 270px)">
				<div class="card-title">
					<h4>Stock Order</h4>
				</div>
				<div class="card-body">
					<form method="post" action="{{route('postBranchStock')}}">
						@csrf
						<h5>Transfer</h5>
						<div class="row" style="margin-bottom: 10px">
							<div class="col">
								<select class="form-control" name="branch_transfer">
									<option value="0">HQ Warehouse</option>
									@foreach($branch as $result)
										@if($result->id != $branch_id)
											<option value="{{$result->id}}">{{$result->branch_name}}</option>
										@endif
									@endforeach
								</select>
							</div>
							<div class="col-md-1" style="font-size: 26px">
								<i class="fa fa-random"></i>
							</div>
							<div class="col">
								@foreach($branch as $result)
									@if($result->id == $branch_id)
										<input type="text" disabled value="{{$result->branch_name}}" class="form-control">
									@endif
								@endforeach
							</div>
						</div>

						<input type="text" name="branch_id" value="{{$branch_id}}" hidden>
						@foreach($branch_product as $result)
						<div class="card" style="background-color: #d9971da1;margin-bottom: 15px">
							<div style="text-align: right;margin-right: 3px;font-size: 25px;float:right">
								<i class="fa fa-times delete" style="cursor:pointer"></i>
							</div>
							<label>Product Barcode : {{$result->barcode}}</label>
							<input type="text" hidden value="{{$result->barcode}}" name="barcode[]">
							<label>Product Name : {{$result->product_name}}</label>
							<input type="text" hidden value="{{$result->product_name}}" name="product_name[]">
							<label>Restock Quantity: 								
								<input type="number" name="reorder_quantity[]" step="1" required placeholder="Reorder Quantity" class="form-control" value="{{number_format($result->recommend_quantity,0)}}" style="width:30%;display:unset">
							</label>
						</div>
						@endforeach

						@if($branch_id != null)
							<input type="submit" value="Generate DO" class="btn btn-primary">
						@endif

					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$("#stock_list").dataTable({
		'paging': true,
		'searching': false,
		'ordering': false,
		'lengthMenu': [15,25,50,100],
		'responsive': true,
	});

	$(".delete").click(function(){
		$(this).parents().eq(1).remove();
	});

	$("#branch").change(function(){
		window.location.assign($(this).val());
	});

});
</script>

@endsection
