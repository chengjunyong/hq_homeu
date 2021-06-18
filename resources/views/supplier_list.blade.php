@extends('layouts.app')
<title>Supplier List</title>
@section('content')
<style>
	.container{
		min-width:95%;
		margin-top: 15px;
		margin-bottom: 15px !important;
	}

	.table td, .table th {
		text-align: left;
		padding: 5px !important;

	}

</style>

<div class="container">
	<div class="card">
		<div class="title">
			<h4 style="margin: 20px">Supplier List</h4>
		</div>
		<div style="margin-left: 20px">
			<button class="btn btn-primary" onclick="window.location.assign('{{route('getCreateSupplier')}}')">Create Supplier</button>
		</div>
		<div class="card-body">

      <form action="{{route('getSupplier')}}" method="get">
  			<div style="float:right">
  				<input type="text" id="search" name="search" placeholder="Search Supplier Name" class="form-control" style="margin-bottom: 15px"/>
  			</div>
      </form>

			<div class="table table-responsive">
				<table id="history" style="width:100%">
					<thead style="background: #b8b8efd1">
						<tr>
							<td style="width:2%">No</td>
							<td style="width:5%">Supplier Code</td>
							<td style="width:30%">Supplier Name</td>
							<td style="width:30%">Contact Number</td>
							<td style="width:30%">Email</td>
							<td style="width:3%"></td>
						</tr>
					</thead>
					<tbody>
						@foreach($supplier as $key => $result)
							<tr>
								<td>{{$key + 1}}</td>
								<td>{{$result->supplier_code}}</td>
								<td>{{$result->supplier_name}}</td>
								<td>{{ ($result->contact_number == 'null') ? 'N/A' : $result->contact_number}}</td>
								<td>{{ ($result->email == 'null') ? 'N/A' : $result->email}}</td>
								<td><buttton class="btn btn-primary" onclick="window.location.assign('{{route('getEditSupplier',$result->supplier_code)}}')">Edit</buttton></td>
							</tr>
						@endforeach
					</tbody>
				</table>

				@if($search == 1)
          <div style="float:right">
            {{$supplier->links()}}
          </div>
        @endif

			</div>
		</div>
	</div>
	<br/>
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