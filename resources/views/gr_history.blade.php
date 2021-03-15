@extends('layouts.app')
@section('content')
<style>
	.container{
		min-width:95%;
		margin-top: 10px;
	}

	.table td{
		padding:5px !important;
	}
</style>

<div class="container">
	<div class="card">
		<div class="title">
			<h4 style="margin: 20px">Goods Return History</h4>
		</div>
		<div class="card-body">
			<div class="table">
				<table id="history" style="width:100%">
					<thead style="background: #b8b8efd1">
						<tr>
							<td>No</td>
							<td>GR Number</td>
							<td style="text-align: center">Total Quantity</td>
							<td style="text-align: right">Total Amount</td>
							<td style="text-align: center">Generate Date</td>
						</tr>
					</thead>
					<tbody>
						@foreach($gr_list as $key => $result)
							<tr>
								<td>{{$key + 1}}</td>
								<td><a href="{{route('getGenerateGR',$result->gr_number)}}">{{$result->gr_number}}</a></td>
								<td style="text-align: center">{{$result->lost_quantity}}</td>
								<td style="text-align: right">Rm {{number_format($result->total,2)}}</td>
								<td style="text-align: center">{{$result->created_at}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="paginate" style="float:right;margin-top: 15px">
					{{ $gr_list->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){

});

</script>
@endsection