@extends('layouts.app')

@section('content')
<script src="{{ asset('js/md5.min.js') }}"></script>
<style>
	body{
		background: #f9fafb;
	}

	#branch_list{
		width:100%;
		border:1px solid black;
	}

	tr{
		border:1px solid black;
	}

	td{
		padding: 5px 0px 5px 0px;
	}

</style>
<div class="container">
	<h2 align="center">Branch Setup</h2>
	<button class="btn btn-secondary" style="float:right" onclick="window.location.assign('{{route('home')}}')">Back</button>
	<div class="row">
		<div class="col-md-12">
			<h4>Create New Branch</h4>
		</div>
	</div>
</div>

	<form method="post" action="{{route('createBranch')}}">
		@csrf
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<label>Branch Name</label>
					<input type="text" name="branch_name" class="form-control" required>
				</div>
				<div class="col-md-6">
					<label>Contact Number</label>
					<input type="text" name="contact_number" class="form-control" required>
				</div>

				<label>Token Branch (The token must be unique text)</label>
				<div class="col-md-6 input-group">
					<input type="text" name="token" class="form-control" required id="token">
					<button type="button" class="btn btn-outline-danger" id="generate">Generate Token</button>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12" style="margin-left: 11px">
					<label>Address</label>
					<textarea class="form-control" name="address" style="width:70%" required></textarea>
				</div>
			</div>

			<div class="row">
				<div class="col" style="text-align: center;margin:10px 0px 10px 0px">
					<input type="submit" class="btn btn-primary" value="Create"/> 
				</div>
			</div>
		</div>
	</form>

<div class="container">
	<div class="row">
		<h4>Branch List</h4>
		<table id="branch_list">
			<thead style="background-color: #403c3c80;text-align: center">
				<tr>
					<th>Branch ID</th>
					<th>Branch Name</th>
					<th>Branch Contact</th>
					<th>Token</th>
					<th>Created Date</th>
					<th>Last Update</th>
					<th></th>
				</tr>
			</thead>
			<tbody style="text-align: center">
				@foreach($branch as $result)
					<tr>
						<td>{{$result->id}}</td>
						<td>{{$result->branch_name}}</td>
						<td>{{$result->contact}}</td>
						<td>{{$result->token}}</td>
						<td>{{$result->created_at}}</td>
						<td>{{$result->updated_at}}</td>
						<td><a href="#"><button class="btn btn-danger">Modify</button></a></td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<script>
	$("#branch_list").dataTable({
		'ordering':false,
		'paging':false,
		'searching':false,
	});

	$("#generate").click(function(){
		let result = md5(Math.random());
		$("#token").val(result);
	})
</script>



@endsection