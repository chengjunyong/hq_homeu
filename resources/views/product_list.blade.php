@extends('layouts.app')
<title>Product Check List</title>
@section('content')
<style> 
  .table tbody td{
    padding: 0.35rem;
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
					<table id="product_list" class="table">
						<thead style="background:#a1e619">
							<tr>
								<td>No</td>
								<td>Bar Code</td>
								<td>Department</td>
								<td>Category</td>
								<td>Product Name</td>
                <td align="center">Measurement</td>
								<td>Cost</td>
								<td>Price</td>
								<td>Last Updated</td>
							</tr>
						</thead>
						<tbody>
							@foreach($product_list as $key => $result)
								<tr style="{{($key % 2 == 0) ? 'background:#ccc5c585' : ''}};">
									<td>{{$key+1}}</td>
									<td>{{$result->barcode}}</td>
									<td>{{$result->department_name}}</td>
									<td>{{$result->category_name}}</td>
									<td><a href="{{route('getModifyProduct',$result->id)}}">{{$result->product_name}}</a></td>
                  <td align="center">{{ucfirst($result->measurement)}}</td>
									<td style="width:5%">{{number_format($result->cost,2)}}</td>
									<td style="width:5%">{{number_format($result->price,2)}}</td>
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