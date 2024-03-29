@extends('layouts.app')
<title>Branch Restock List</title>
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
			<h4 style="margin:20px">Delivery Order List</h4>
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
							<td>Date Issue</td>
							<td></td>
						</tr>
					</thead>
					<tbody>
						@foreach($do_list as $key => $result)
							<tr>
								<td>{{$key + 1}}</td>
								<td>{{$result->do_number}}</td>
								<td>{{$result->from}}</td>
								<td>{{$result->to}}</td>
								<td>{{$result->total_item}}</td>
								<td>{{$result->created_at}}</td>
								<td><button class="btn btn-primary" onclick="window.location.assign('{{route('getRestockConfirmation',$result->do_number)}}')">Confirm Delivered</button></td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div style="float:right">{{ $do_list->links() }}</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="result" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">Update</h5>
      </div>
      <div class="modal-body" style="text-align: center">
        <label style="margin-top: 5px" id="label">Update Successful</label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>

$(document).ready(function(){
	$("#search").keypress(function(e){
		let header = "{{route('getRestocklist')}}";
		if(e.keyCode == 13){
			let target = $("#search").val();
			header = `${header}?search=${target}`;
			window.location.assign(header);
		}
	});
});

</script>

@if(session()->get('success'))
	<script>$("#result").modal('toggle');</script>
@endif

@endsection