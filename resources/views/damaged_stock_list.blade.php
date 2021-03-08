@extends('layouts.app')
@section('content')
<style>
	.container{
		min-width:95%;
		margin-top: 10px;
	}
</style>

<div class="container">
	<div class="card">
		<div class="title">
			<h4 style="margin: 20px">Damaged Stock List</h4>
		</div>
		<div class="card-body">
			<div class="table">
				<table id="history" style="width:100%">
					<thead style="background: #b8b8efd1">
						<tr>
							<td>No</td>
							<td>From Do Number</td>
							<td>Product Name</td>
							<td>Damaged Quantity</td>
							<td>Price Per Unit</td>
							<td>Total Price</td>
							<td>Remark</td>
						</tr>
					</thead>
					<tbody>
						@foreach($do_detail as $key => $result)
							<tr>
								<td>{{$key + 1}}</td>
								<td><a href="{{route('getDoHistoryDetail',$result->do_number)}}">{{$result->do_number}}</a></td>
								<td>{{$result->product_name}}</td>
								<td>{{$result->stock_lost_quantity}}</td>
								<td>Rm {{number_format($result->price,2)}}</td>
								<td>Rm {{number_format($result->price * $result->stock_lost_quantity,2)}}</td>
								<td>{{($result->remark != null) ? $result->remark : 'No Remark'}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){

});
</script>
@endsection