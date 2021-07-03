@extends('layouts.app')
<title>Product Check List</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
@section('content')
<style>
	table{
		width:100%;
	}

	td{
		border:1px solid black;
	}

	thead{
		font-size:18px;
		font-weight: 700;
	}

	#search{
		width: 80%;
		display:inline-block;
		margin: 10px 0px 10px 0px;
	}

	.fa-search{
		font-size:25px;
		margin-right: 3px;
	}

	.container{
		max-width: 98%;
	}

</style>
<div class="container">
	<div class="card" style="margin-top: 15px">
		<div class="card-body">
			<h2 align="center">Product Check List</h2>
			<div class="row">
				<div class="col-md-12">
					<div style="float:left">
						<button type="button" class="btn btn-primary" onclick="window.location.assign('{{route('getAddProduct')}}')">Add Product</button>
					</div>
					<div style="float:right">
						<form action="{{route('searchProduct')}}" method="get">
							<i class="fa fa-search"></i><input type="text" id="search" name="search" class="form-control" placeholder="Search" value="{{$search}}">
						</form>
					</div>
					<table id="product_list">
						<thead style="background:#a1e619">
							<tr>
								<td>No</td>
								<td>Bar Code</td>
								<td>Department</td>
								<td>Category</td>
								<td>Product Name</td>
                <td>UOM</td>
								<td>Cost</td>
								<td>Price</td>
								<td>Reorder Level</td>
								<td>Reorder Recommend Quantity</td>
								<td>Last Updated</td>
							</tr>
						</thead>
						<tbody>
							@foreach($product_list as $key => $result)
								<tr style="{{($key % 2 == 0) ? 'background:#ccc5c5' : ''}};">
									<td>{{$key+1}}</td>
									<td>{{$result->barcode}}</td>
									<td>{{$result->department_name}}</td>
									<td>{{$result->category_name}}</td>
									<td><a href="{{route('getModifyProduct',$result->id)}}">{{$result->product_name}}</a></td>
                  <td>{{$result->uom}}</td>
									<td style="width:5%">{{number_format($result->cost,2)}}</td>
									<td style="width:5%">{{number_format($result->price,2)}}</td>
									<td align="center" style="width:2%">{{$result->reorder_level}}</td>
									<td align="center" style="width:2%">{{$result->recommend_quantity}}</td>
									<td align="center" style="width:9%">{{ date('d-M-Y', strtotime($result->updated_at))}} <br/> {{ date('h:i A', strtotime($result->updated_at))}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div style="float:right;margin-top: 5px">
						{{$product_list->links()}}
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

@endsection