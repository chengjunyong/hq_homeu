@extends('layouts.app')
<title>Branch Restock History</title>
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
			<h4 style="margin: 20px">Branch Restock History</h4>
		</div>
		<div class="card-body">
			<div style="float:right">
				<input type="text" id="search" placeholder="Search DO Number" class="form-control" style="margin-bottom: 15px"/>
			</div>
			<div class="table table-responsive">
				<table id="history" style="width:100%">
					<thead style="background: #b8b8efd1">
						<tr>
							<td>No</td>
							<td>DO Number</td>
							<td>From</td>
							<td>To</td>
							<td>Quantity Item</td>
              <td>Total Value</td>
							<td>Date Completed</td>
							<td>Stock Lost Status</td>
							<td></td>
						</tr>
					</thead>
					<tbody>
						@foreach($do_list as $key => $result)
							<tr>
								<td>{{$key + 1}}</td>
								<td><a href="{{route('getDoHistoryDetail',$result->do_number)}}">{{$result->do_number}}</a></td>
								<td>{{$result->from}}</td>
								<td>{{$result->to}}</td>
								<td>{{$result->total_item}}</td>
                <td>Rm {{ number_format($result->total_value,2) }}</td>
								<td>{{$result->created_at}}</td>
								<td>{{($result->stock_lost == 0) ? 'No' : 'Yes'}}</td>
								<td><buttton class="btn btn-primary" onclick="window.location.assign('{{route('getRestockHistoryDetail',$result->id)}}')">Details</buttton></td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div style="float:right">{{ $do_list->links() }}</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){

	$("#search").keypress(function(e){
		let header = "{{route('getRestockHistory')}}";
		if(e.keyCode == 13){
			let target = $("#search").val();
			header = `${header}?search=${target}`;
			window.location.assign(header);
		}
	});

});
</script>
@endsection