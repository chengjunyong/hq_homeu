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
			<h4>DO Detail</h4>
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
					<label>Total Items:</label>
					<input readonly class="form-control" type="text" name="total_item" value="{{$do_list->total_item}}">
				</div>
				<div class="col-md-6">
					<label>Completed:</label>
					<input readonly class="form-control" type="text" name="completed" value="{{($do_list->completed == 0) ? 'No' : 'Yes'}}">
				</div>
				<div class="col-md-6">
					<label>Stock Lost Quantity:</label>
					<input readonly class="form-control" type="text" name="stock_lost_quantity" value="{{($do_list->stock_lost_quantity == null) ? 'Not Available' : $do_list->stock_lost_quantity}}">
				</div>
				<div class="col-md-6">
					<label>Stock Lost Reason:</label>
					<input readonly class="form-control" type="text" name="reason" value="{{($do_list->stock_lost_reason == null) ? 'Not Available' : $do_list->stock_lost_reason}}">
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

			<div style="overflow-y: auto;height:425px;margin-top:25px">
				<table style="width:100%;">
					<thead style="background-color: #b8b8efd1">
						<tr>
							<td>No</td>
							<td style="width:20%">Barcode</td>
							<td>Product Name</td>
							<td align="right">Price</td>
							<td align="center">Quantity</td>
						</tr>
						<tbody>
							@foreach($do_detail as $key => $result)
								<tr>
									<td>{{$key+1}}</td>
									<td>{{$result->barcode}}</td>
									<td>{{$result->product_name}}</td>
									<td align="right">{{number_format($result->price,2)}}</td>
									<td align="center">{{$result->quantity}}</td>
								</tr>
							@endforeach
						</tbody>
					</thead>
				</table>
			</div>

		</div>
	</div>
</div>


@endsection