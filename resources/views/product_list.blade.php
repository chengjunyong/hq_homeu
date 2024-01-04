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
								<td align="center">
									All<br/>
									<input type="checkbox" id="all" style="height:25px;width:25px" />
								</td>
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
									<td align="center">
										<input type="checkbox" style="height:25px;width:25px" name="product_ids[]" value="{{$result->id}}" />
									</td>
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
					<div style="float:left;margin-top: 5px">
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#bulk-edit">Bulk Change</button>
					</div>
					<div style="float:right;margin-top: 5px">
						{{$product_list->appends(request()->query())->links()}}
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="bulk-edit" tabindex="-1"	>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bulk Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">

					<div class="col-md-12">
						<div class="form-group">
							<label>Department</label>
							<select name="department_id" class="form-control" id="department">
								@foreach($departments as $department)
									<option value="{{$department->id}}">{{$department->department_name}}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label>Category</label>
							<select name="category_id" class="form-control" id="category">
								<option value="1">Not Available</option>
							</select>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label>Sub Category</label>
							<select name="subcategory_id" class="form-control" id="subcategory">
								<option value="">N/A</option>
								@foreach($subCategories as $subCategory)
									<option value="{{$subCategory->id}}">{{$subCategory->name}}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label>Brand</label>
							<select name="brand_id" class="form-control" id="brand">
								<option value="">N/A</option>
								@foreach($brands as $brand)
									<option value="{{$brand->id}}">{{$brand->name}}</option>
								@endforeach
							</select>
						</div>
					</div>

				</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="bulk-update">Update</button>
      </div>
    </div>
  </div>
</div>

<script>
	$(document).ready(function(){

		$("#all").change(function(){
			if($(this).prop('checked')){
				$("input[name='product_ids[]']").prop('checked',true);
			}else{
				$("input[name='product_ids[]']").prop('checked',false);
			}
		});

		$("#department").change(function(){
			$.get('{{route('ajaxGetCategory')}}',{'department_id' : $("#department").val()},
				function(data){
					$("#category").html("");
					Object.entries(data).forEach(([key, result]) => {
						$("#category").append(`<option value='${result['id']}'>${result['category_name']}</option>`)
					});
				},"json");
		});

		$("#bulk-update").click(function(){
			let el = $("input[name='product_ids[]']:checked");
			let ids = [];
			let departmentId = $("#department").val();
			let categoryId = $("#category").val();
			let subCategoryId = $("#subcategory").val();
			let brandId = $("#brand").val();

			el.each(function(index,target){
				ids[index] = target.value;
			});

			$.get("{{route('bulkChanges')}}",
			{
				'ids' : ids,
				'department_id' : departmentId,
				'category_id' : categoryId,
				'subCategory_id' : subCategoryId,
				'brand_id' : brandId,

			},function(data){
				if(data){
					$("#bulk-edit").modal('hide');
					swal.fire('Bulk Edit','Update successful','success').then(()=>{
						window.location.reload();
					});
				}else{
					swal.fire('Bulk Edit','Update failed, please try again later','error');
				}
			},'json');
		});

	});
</script>
@endsection