@extends('layouts.app')
<title>Product Categeory</title>
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
			<h2 align="center">Product Categories</h2>
			<div class="row">
				<div class="col-md-12">
					<div class="text-right mb-2">
						<button type="button" class="btn btn-primary" onclick="window.location.assign('{{route('category.create')}}')">Add Category</button>
					</div>
					<table id="product_list" class="table">
						<thead style="background:#a1e619">
							<tr>
								<td>No</td>
								<td>Department</td>
                <td>Code</td>
                <td>Name</td>
                <td>Last Update At</td>
								<td align="center">Action</td>
							</tr>
						</thead>
						<tbody>
							@foreach($categories as $category)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $category->department_name }}</td>
                  <td>{{ $category->category_code }}</td>
                  <td>{{ $category->category_name }}</td>
                  <td>{{ date("d-M-Y h:i:s a",strtotime($category->updated_at)) }}</td>
                  <td>
                    <button type="button" class="btn btn-primary" onclick="window.location.assign('{{ route('category.edit',$category->id)}}')">Edit / View</button>
                  </td>
                </tr>
              @endforeach
						</tbody>
					</table>
					<div style="float:right;margin-top: 5px">
						{{$categories->links()}}
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

@endsection