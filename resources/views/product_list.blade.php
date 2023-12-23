@extends('layouts.app')
<title>Product Check List</title>
@section('content')
<style> 
  .table tbody td{
    padding: 0.35rem;
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
					<div style="float:right">
						<button type="button" class="btn btn-primary" onclick="window.location.assign('{{route('getAddProduct')}}')">Add Product</button>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<form action="{{route('getProductList')}}" method="get">
						<div class="row">
							<div class="col-md-4">
								<label>Product Name / Barcode</label>
								<input type="text" id="search" name="search" class="form-control" value="{{ request()->search }}">
							</div>
							<div class="col-md-2">
								<label>Department</label>
								<select name="department_id" class="form-control">
									<option value="" {{ request()->department_id == null ? 'selected' : '' }}>N/A</option>
									@foreach($departments as $department)
										<option value="{{$department->id}}" {{ request()->department_id == $department->id ? 'selected' : '' }}>{{$department->department_name}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-2">
								<label>Category</label>
								<select name="category_id" class="form-control">
									<option value="" {{ request()->category_id == null ? 'selected' : '' }}>N/A</option>
									@foreach($categories as $category)
										<option value="{{$category->id}}" {{ request()->category_id == $category->id ? 'selected' : '' }}>{{$category->category_name}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-2">
								<label>Sub Category</label>
								<select name="sub_category_id" class="form-control">
									<option value="" {{ request()->sub_category_id == null ? 'selected' : '' }}>N/A</option>
									@foreach($subCategories as $subCategory)
										<option value="{{$subCategory->id}}" {{ request()->sub_category_id == $subCategory->id ? 'selected' : '' }}>{{$subCategory->name}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-2">
								<label>Brand</label>
								<select name="brand_id" class="form-control">
									<option value="" {{ request()->brand_id == null ? 'selected' : '' }}>N/A</option>
									@foreach($brands as $brand)
										<option value="{{$brand->id}}" {{ request()->brand_id == $brand->id ? 'selected' : '' }}>{{$brand->name}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-12 mt-2 text-right">
								<button type="submit" class="btn btn-success">Filter</button>
								<button type="button" class="btn btn-secondary" onclick="window.location.assign('{{route('getProductList')}}')">Reset</button>
							</div>
						</div>
					</form>
					<table id="product_list" class="table">
						<thead style="background:#a1e619">
							<tr>
								<td>No</td>
								<td>Bar Code</td>
								<td>Department</td>
								<td>Category</td>
								<td>Sub Category</td>
								<td>Brand</td>
								<td>Product Name</td>
                <td align="center">Measurement</td>
								<td>Cost</td>
								<td>Price</td>
								<td align="center">Created Date</td>
								<td align="center">Last Updated</td>
							</tr>
						</thead>
						<tbody>
							@foreach($product_list as $key => $result)
								<tr style="{{($key % 2 == 0) ? 'background:#ccc5c585' : ''}};">
									<td>{{$key+1}}</td>
									<td>{{$result->barcode}}</td>
									<td>{{$result->department->department_name ?? 'N/A'}}</td>
									<td>{{$result->category->category_name ?? 'N/A'}}</td>
									<td>{{$result->subCategory->name ?? 'N/A'}}</td>
									<td>{{$result->brand->name ?? 'N/A'}}</td>
									<td>
                    @if($access && $permission)
                      <a href="{{route('getModifyProduct',$result->id)}}">{{$result->product_name}}</a>
                    @else
                      {{$result->product_name}}
                    @endif
                  </td>
                  <td align="center">{{ucfirst($result->measurement)}}</td>
									<td style="width:5%">{{number_format($result->cost,2)}}</td>
									<td style="width:5%">{{number_format($result->price,2)}}</td>
									<td align="center" style="width:9%">{{ date('d-M-Y', strtotime($result->created_at))}} <br/> {{ date('h:i A', strtotime($result->created_at))}}</td>
									<td align="center" style="width:9%">{{ date('d-M-Y', strtotime($result->updated_at))}} <br/> {{ date('h:i A', strtotime($result->updated_at))}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div style="float:right;margin-top: 5px">
						{{$product_list->appends(request()->query())->links()}}
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

@endsection