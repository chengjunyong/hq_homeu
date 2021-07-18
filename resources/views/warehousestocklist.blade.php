@extends('layouts.app')
<title>Warehouse Stock Checklist</title>
@section('content')
<style>
	.container{
		max-width: 90%;
	}

	thead{
		background-color: #b8b8efd1;
	}

	thead > tr > td{
		padding-top: 10px;
		padding-bottom: 10px;
	}

	td{
		border:1px solid black;
	}
</style>

<div class="container">
	<h2 align="center">Warehouse Stock Checklist</h2>
	<div class="card" style="border-radius: 1.25rem;">
		<div class="card-title" style="padding: 10px">
			<h4>Warehouse Stock</h4>
		</div>

		<div>
			<form action="{{route('getWarehouseStockList')}}" method="get">
<!-- 	<button type="button" class="btn btn-primary" style="float:left;margin-left: 1%" onclick='window.location.assign("{{route('getAddWarehouseProduct')}}")'>Add Product</button> -->
        <div>
        <button type="button" onclick="window.location.assign('{{route('getWarehouseStockList')}}')" class="btn btn-primary" style="float:right;margin-right:2%;margin-left:10px;">Reset</button>
        <input type="text" id="search" name="search" class="form-control" placeholder="Barcode" style="width:25%;float:right">
        </div>
			</form>	
		</div>	

		<div class="card-body">
			<table id="warehouse_stock_list" style="width: 100%">
				<thead>
					<tr style="font-weight: bold;">
						<td>No</td>
						<td>Barcode</td>
						<td>Product Name</td>
						<td>Cost</td>
						<td>Price</td>
						<td>Stock Quantity</td>
						<td>Reorder Level</td>
						<td style="width:10%">Recommend QTY</td>
						<td>Last Updated</td>
					</tr>
				</thead>
				<tbody>
					@foreach($warehouse_stock as $key => $result)
						<tr>
							<td>{{$key+1}}</td>
							<td>{{$result->barcode}}</td>
							<td><a href="{{route('getEditWarehouseProduct',$result->id)}}">{{$result->product_name}}</a></td>
							<td>{{number_format($result->cost,2)}}</td>
							<td>{{number_format($result->price,2)}}</td>
							<td>{{$result->quantity}}</td>
							<td>{{$result->reorder_level}}</td>
							<td>{{$result->reorder_quantity}}</td>
							<td>{{ date('d-M-Y', strtotime($result->updated_at))}} {{ date('h:i A', strtotime($result->updated_at))}}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			@if($search == 1)
				<div style="float:right">
					{{$warehouse_stock->links()}}
				</div>
			@endif

		</div>
	</div>
</div>
<script>

$("#search").keypress(function(e){
	if(e.which == 13){
		$("form").submit();
	}
});

$("#warehouse_stock_list").dataTable({
	'ordering': false,
	'responsive': true,
	'searching': false,
	'paginate': false,
});

</script>


@endsection