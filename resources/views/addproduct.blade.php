@extends('layouts.app')
<title>Add Product</title>
@section('content')
<style>
.row > .col-md-12,.col-md-6{
	margin-top: 10px;
}
</style>
<div class="container">
	<h2 align="center">Add Product</h2>
	<div class="card" style="border-radius: 1.25rem">
		<div class="card-title" style="padding: 10px">
			<h4>Insert New Product Information</h4>
		</div>
		<div class="card-body">
			<form method="post" action="{{route('postAddProduct')}}" id="form">
				@csrf
				<div class="row">
					<div class="col-md-12">
						<label>Department</label>
						<select name="department" id="department" class="form-control" required>
							@foreach($department as $result)
								<option value="{{$result->id}}">{{$result->department_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-12">
						<label>Category</label>
						<select name="category" id="category" class="form-control" required>
							@foreach($category as $result)
								<option value="{{$result->id}}">{{$result->category_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-12">
						<label>Barcode</label>
						<input type="text" name="barcode" class="form-control" required>
					</div>
					<div class="col-md-6">
						<label>Product Name</label>
						<input type="text" name="product_name" class="form-control" required>
					</div>
          <div class="col-md-6">
            <label>Measurement Type</label>
            <select class="form-control" name="measurement">
              <option value="unit" selected>Unit</option>
              <option value="kilogram">Kilogram</option>
              <option value="meter">Meter</option>
            </select>
          </div>
<!--           <div class="col-md-12">
            <label>UOM</label>
            <select name="uom" id="uom" class="form-control" required>
              <option name="Bag">Bag</option>
              <option name="Carton">Carton</option>
              <option name="Box">Box</option>
              <option name="Dozen">Dozen</option>
              <option name="Piece">Piece</option>
              <option name="Set">Set</option>
            </select>
          </div> -->
					<div class="col-md-6">
						<label>Cost<!--  <a href="{{route('getProductConfig')}}">(Price Auto Increase {{$default_price->default_price_margin}}%)</a> --></label>
						<input type="number" min="0" step="0.01" name="cost" id="cost" class="form-control" required>
					</div>
					<div class="col-md-6">
						<label>Price <span id="display_price" style="color:red;font-weight:bold">(1 Unit)</span></label>
						<input type="number" min="0" step="0.01" name="price" id="price" class="form-control" required>
					</div>
					<div class="col-md-6">
						<label>Reorder Level</label>
						<input type="number" name="reorder_level" class="form-control">
					</div>
					<div class="col-md-6">
						<label>Reorder Recommend Quantity</label>
						<input type="number" name="recommend_quantity" class="form-control">
					</div>
					<div class="col-md-12" style="text-align: center;margin-top: 20px">
						<input type="submit" class="btn btn-primary" value="Add">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="result" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">Create Product Status</h5>
      </div>
      <div class="modal-body" style="text-align: center">
        <h4 id="msg"></h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  let starget;
	let price_ptg = parseFloat({{$default_price->default_price_margin}});

  $("form").submit(()=>{
    $("input[type='submit']").prop('disabled',true);
  })

	$("#department").change(function(){
		$.get('{{route('ajaxGetCategory')}}',{'department_id' : $("#department").val()},
			function(data){
				$("#category").html("");
				Object.entries(data).forEach(([key, result]) => {
					$("#category").append(`<option value='${result['id']}'>${result['category_name']}</option>`)
				});
			},"json");
	});

	// $("#cost").on("input",function(){
	// 	let cost = parseFloat($(this).val());
	// 	let price = (cost * price_ptg / 100) + cost;
	// 	$("#price").val(price.toFixed(2));
	// });

	$("input[name=barcode]").on("keyup",function(){
    clearTimeout(starget);
    starget = setTimeout(()=>{
      let barcode = $("input[name=barcode]").val();
      let target = $("input[name=barcode]")[0];
      $.get('{{route('ajaxGetBarcode')}}',{'barcode': barcode},
        function(data){
          if(data != "true"){
            target.setCustomValidity('');
          }else{
            target.setCustomValidity('Barcode duplicate, please use other code');
          }
        },"html");
    },500);
  });

  $("select[name=measurement]").change(function(){
    if($(this).val() == 'unit'){
      $("#display_price").text('(1 Unit)');
    }else if($(this).val() == 'kilogram'){
      $("#display_price").text('(1 Kilogram)');
    }else{
      $("#display_price").text('(1 Meter)');
    }
  });
	
});

</script>
@if(session()->has('result'))
<script>
	$("#msg").text("Product Create Successful");
	$("#result").modal('toggle');
</script>
@endif

@endsection