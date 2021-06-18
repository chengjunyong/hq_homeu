@extends('layouts.app')
<title>Branch Stock Checklist</title>
@section('content')
<style>
	.container{
		max-width: 90%;
	}

	thead{
		background-color: #aeef3ec9;
	}
</style>

<div class="container">
	<h2 align="center">Branch Stock Checklist</h2>
	<div class="card" style="border-radius: 1.25rem;">
		<div class="card-title" style="padding: 10px">
			<h4>Branch Stock</h4>
		</div>
		<div style="padding: 10px">
			<h5>Branch Selected</h5>	
		</div>

		<div>
			<select id="branch_id" class="form-control" style="width:40%;margin-left: 5px;float:left">
				@foreach($branch as $result)
					<option value="{{$result->url}}" {{ ($result->id == $branch_id) ? 'selected' : '' }}>{{$result->branch_name}}</option>
				@endforeach
			</select>
			<form action="{{route('searchBranchProduct')}}" method="get" style="float:right">
				<input type="text" id="search" name="search" class="form-control" placeholder="Search" style="width:98%">
				<input type="text" name="branch_id" value="{{$branch_id}}" hidden>
			</form>	
		</div>	


		<div class="card-body">
			<table id="branch_stock_list" style="width: 100%">
				<thead>
					<tr>
						<td>No</td>
						<td>Barcode</td>
						<td>Department</td>
						<td>Category</td>
						<td>Product Name</td>
						<td>Cost</td>
						<td>Price</td>
						<td>Stock Quantity</td>
						<td>Reorder Level</td>
						<td>Reorder Recommend Quantity</td>
						<td>Last Updated</td>
					</tr>
					<tbody>
							@foreach($branch_product as $key => $result)
							<tr style="background-color:{{ ($key % 2 == 1) ? '#23272430' : ''}}">
								<td>{{$key+1}}</td>
								<td>{{$result->barcode}}</td>
								<td>{{$result->department_name}}</td>
								<td>{{$result->category_name}}</td>
								<td><a href={{route('getModifyBranchStock',[$branch_id,$result->id])}}>{{$result->product_name}}</a></td>
								<td>{{$result->cost}}</td>
								<td>{{$result->price}}</td>
								<td>{{$result->quantity}}</td>
								<td>{{$result->reorder_level}}</td>
								<td>{{$result->recommend_quantity}}</td>
								<td>{{ $result->updated_at }}</td>
							</tr>
							@endforeach
					</tbody>
				</thead>
			</table>
			<div style="float:right">
			<br/>{{$branch_product->links()}}
			</div>
		</div>
	</div>
</div>
<script>

$("#search").keypress(function(e){
	if(e.which == 13){
		$("form").submit();
	}
});

$("#branch_id").change(function(){
	window.location.assign($(this).val());
})

$("branch_stock_list").dataTable({
		'paging': false,
		'searching': false,
		'ordering': false,
	});

</script>


@endsection