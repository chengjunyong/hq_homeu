@extends('layouts.app')
<title>Product Sub Category</title>
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
			<h2 align="center">Product Sub Category</h2>
			<div class="row">
				<div class="col-md-12">
					<div class="mb-3 text-right">
						<button type="button" class="btn btn-primary" onclick="window.location.assign('{{route('sub_category.create')}}')">Add Sub Category</button>
					</div>
					<table id="product_list" class="table">
						<thead style="background:#a1e619">
							<tr>
								<td>No</td>
								<td>Name</td>
                <td>Created By</td>
                <td>Last Updated By</td>
                <td>Last Update At</td>
								<td align="center">Action</td>
							</tr>
						</thead>
						<tbody>
							@foreach($subCategories as $subCategory)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $subCategory->name }}</td>
                  <td>{{ $subCategory->creator->name }}</td>
                  <td>{{ $subCategory->updator->name }}</td>
                  <td>{{ date("d-M-Y h:i:s a",strtotime($subCategory->updated_at)) }}</td>
                  <td align="center">
                    <button type="button" class="btn btn-primary" onclick="window.location.assign('{{ route('sub_category.edit',$subCategory->id)}}')">Edit / View</button>
                  </td>
                </tr>
              @endforeach
						</tbody>
					</table>
					<div style="float:right;margin-top: 5px">
						{{$subCategories->links()}}
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

@endsection