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
			<h4>Delivery Order History</h4>
		</div>
		<div class="card-body">
			<div style="float:right">
				<input type="text" id="search" placeholder="Search DO Number" class="form-control" style="margin-bottom: 15px"/>
			</div>
			<div class="table">
				<table id="history" style="width:100%">
					<thead style="background: #b8b8efd1">
						<tr>
							<td>No</td>
							<td>DO Number</td>
							<td>From</td>
							<td>To</td>
							<td>Quantity Item</td>
							<td>Completed</td>
							<td>Date Issue</td>
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
								<td>{{($result->completed == 0)? 'No' : 'Yes'}}</td>
								<td>{{$result->created_at}}</td>
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
		let header = "{{route('getDoHistory')}}";
		if(e.keyCode == 13){
			let target = $("#search").val();
			header = `${header}?search=${target}`;
			window.location.assign(header);
		}
	});

});
</script>
@endsection