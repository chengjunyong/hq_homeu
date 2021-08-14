@extends('layouts.app')
<title>Branch Restock History Detail</title>
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
			<div style="overflow-y: auto;height:425px;margin-top:25px">
				<table style="width:100%;">
					<thead style="background-color: #b8b8efd1">
						<tr>
							<td>No</td>
							<td style="width:5%">Barcode</td>
							<td>Product Name</td>
							<td>Quantity</td>
							<td align="center" style="width:10%">Restock Quantity</td>
							<td align="center" style="width:10%">Stock Lost Quantity</td>
							<td style="width:10%">Stock Lost Reason</td>
							<td>Remark</td>
						</tr>
						<tbody>
							@foreach($do_detail as $key => $result)
								<tr>
									<td>{{$key+1}}</td>
									<td>{{$result->barcode}}</td>
									<td>{{$result->product_name}}</td>
									<td class="quantity">{{$result->quantity}}</td>
									<td align="center"><input type="number" name="restock_quantity[]" value="{{$result->quantity - $result->stock_lost_quantity}}" class="restock_quantity" style="width:100%" disabled="" min=0></td>
									<td align="center"><input type="number" name="stock_lost_quantity[]" value="{{$result->stock_lost_quantity}}" class="stock_lost_quantity" style="width:50%" disabled min=0></td>
									<td>
										<select name="stock_lost_reason[]" disabled>
											<option value="damaged" {{($result->stock_lost_reason == 'damaged') ? 'selected' : ''}}>Damaged</option>
											<option value="lost" {{($result->stock_lost_reason == 'lost') ? 'selected' : ''}}>Lost</option>
											<option value="other" {{($result->stock_lost_reason == 'other') ? 'selected' : ''}}>Other</option>
											<option {{($result->stock_lost_quantity <= 0) ? 'selected' : ''}}>No Stock Lost</option>
										</select>
									</td>
									<td><input type="text" name="remark[]" style="width:100%" value="{{ $result->remark}}" disabled></td>
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