@extends('layouts.app')
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

	.float{
    position:fixed;
    width:60px;
    height:60px;
    bottom:40px;
    right:40px;
    background-color:#0C9;
    color:#FFF;
    border-radius:50px;
    text-align:center;
    box-shadow: 2px 2px 3px #999;
    z-index: 99;
  }

</style>

<button class="float" onclick="window.location.assign('{{route('home')}}')" style="border:none"><i class="fa fa-arrow-left" style="font-size: 40px;"></i></button>

<h2 align="center">Product Check List</h2>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div style="float:left">
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_product">Add Product</button>
			</div>
			<div style="float:right">
				<form action="{{route('searchProduct')}}" method="get">
					<i class="fa fa-search"></i><input type="text" id="search" name="search" class="form-control" placeholder="Search">
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
						<td>Cost</td>
						<td>Price</td>
						<td>Stock Quantity</td>
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
							<td>{{$result->product_name}}</td>
							<td style="width:5%">{{number_format($result->cost,2)}}</td>
							<td style="width:5%">{{number_format($result->price,2)}}</td>
							<td align="center" style="width:2%">{{$result->quantity}}</td>
							<td align="center" style="width:2%">{{$result->reorder_level}}</td>
							<td align="center" style="width:2%">{{$result->recommend_quantity}}</td>
							<td align="center" style="width:9%">{{$result->created_at}}</td>
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

<!-- Modal -->
<div class="modal fade" id="add_product" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form id="add_product_form">
      		@csrf
      		<label>Barcode</label>
      		<input type="text" name="barcode" class='form-control'>
      		<label>Product Name</label>
      		<input type="text" name="product_name" class='form-control'>
      		<label>Price</label>
      		<input type="number" step="0.01" name="price" class='form-control'>
      		<label>Initial Quantity</label>
      		<input type="number" name="quantity" class='form-control'>
      	</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close">Close</button>
        <button type="button" class="btn btn-primary" id="add_product_btn">Add</button>
      </div>
    </div>
  </div>
</div>
<script>	
	$("#add_product_btn").click(function(){
		$.post("{{route('ajaxAddProduct')}}",
			$("#add_product_form").serialize(),
			function(data){
				if(data == 'true'){
					alert('Product Add Successful');
					$("#add_product_form").find('input').val('');
					$("#close").click();
				}else{
					alert('Product Add Fail, Please Contact IT Support')
				}
			},'html');
	});

</script>
@endsection