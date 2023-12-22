@extends('layouts.app')
<title>Product Brand</title>
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
			<h2 align="center">Product Brand</h2>
			<div class="row">
				<div class="col-md-12">
					<div class="mb-3 text-right">
						<button type="button" class="btn btn-primary" onclick="window.location.assign('{{route('brand.create')}}')">Add Brand</button>
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
							@foreach($brands as $brand)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $brand->name }}</td>
                  <td>{{ $brand->creator->name }}</td>
                  <td>{{ $brand->updator->name }}</td>
                  <td>{{ date("d-M-Y h:i:s a",strtotime($brand->updated_at)) }}</td>
                  <td align="center">
                    <button type="button" class="btn btn-primary" onclick="window.location.assign('{{ route('brand.edit',$brand->id)}}')">Edit / View</button>
                  </td>
                </tr>
              @endforeach
						</tbody>
					</table>
					<div style="float:right;margin-top: 5px">
						{{$brands->links()}}
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

@endsection