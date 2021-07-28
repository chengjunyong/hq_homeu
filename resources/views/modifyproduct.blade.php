@extends('layouts.app')
<title>Edit Product</title>
@section('content')
<style>
.row > .col-md-12,.col-md-6,.col-md-4{
	margin-top: 10px;
}
</style>
<div class="container">
	<h2 align="center">Edit Product</h2>
	<div class="card" style="border-radius: 1.25rem">
		<div class="card-title" style="padding: 10px">
      <div class="row">
        <div class="col">
  			 <h4 style="float:left">Modify Product Information</h4>
        </div>
        <div class="col">
          <button type="button" id="delete_product" class="btn btn-danger" style="float:right;">Delete Product</button>
        </div>
      </div>
		</div>
		<div class="card-body">
			<form method="post" action="{{route('postModifyProduct')}}" id="form">
				@csrf
				<div class="row">
					<div class="col-md-12">
						<label>Department</label>
						<select name="department" id="department" class="form-control" required>
							@foreach($department as $result)
								<option value="{{$result->id}}" {{($product->department_id == $result->id) ? 'selected' : ''}}>{{$result->department_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-12">
						<label>Category</label>
						<select name="category" id="category" class="form-control" required>
							@foreach($category as $result)
								<option value="{{$result->id}}" {{($product->category_id == $result->id) ? 'selected' : ''}}>{{$result->category_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-12">
						<label>Barcode</label>
						<input type="text" name="barcode" class="form-control" readonly value="{{$product->barcode}}">
					</div>
					<div class="col-md-12">
						<label>Product Name</label>
						<input type="text" name="product_name" class="form-control" required value="{{$product->product_name}}">
					</div>
          <div class="col-md-12">
            <label>Measurement Type</label>
            <select name="uom" id="uom" class="form-control" required>
              <option value="Pcs" {{ ($product->uom == "Pcs") ? 'selected' : ''}}>Pcs</option>
              <option value="Kg" {{ ($product->uom == "Kg") ? 'selected' : ''}}>Kilogram</option>
              <option value="Meter" {{ ($product->uom == "Meter") ? 'selected' : ''}}>Meter</option>
            </select>
          </div>
					<div class="col-md-6">
						<label>Cost</label>
						<input type="number" min="0" step="0.001" name="cost" id="cost" class="form-control" required value="{{number_format($product->cost,3)}}">
					</div>
					<div class="col-md-6">
						<label>Price <a href="{{route('getProductConfig')}}">(Auto Increase {{$default_price->default_price_margin}}%)</a></label>
						<input type="number" min="0" step="0.01" name="price" id="price" class="form-control" required value="{{$product->price}}">
					</div>
					<div class="col-md-6">
						<label>Reorder Level</label>
						<input type="number" min="0" step="1" name="reorder_level" class="form-control" required value="{{$product->reorder_level}}">
					</div>
					<div class="col-md-6">
						<label>Reorder Recommend Quantity</label>
						<input type="number" min="0" step="1" name="recommend_quantity" class="form-control" required value="{{$product->recommend_quantity}}">
					</div>
          <div class="col-md-6">
            <label>Schedule Date</label>
            <input type="date" name="schedule_date" class="form-control" value="{{$product->schedule_date}}">
          </div>
          <div class="col-md-6">
            <label>Schedule Price</label>
            <input type="number" min="0" step="0.01" name="schedule_price" class="form-control" value="{{$product->schedule_price}}">
          </div>
          <div class="col-md-12" style="text-align: center; margin: 5% 0px 0px 0px;"><label style='font-weight: bold;;font-size: 24px;'>Promotion Option</label></div>
          <div class="col-md-4">
            <label>Promotion Start Date</label>
            <input type="datetime-local" id="promo_start" name="promotion_start" class="form-control" value="{{ str_replace(" ","T",$product->promotion_start)}}">
          </div>
          <div class="col-md-4">
            <label>Promotion End Date</label>
            <input type="datetime-local" id="promo_end" name="promotion_end" class="form-control" value="{{ str_replace(" ","T",$product->promotion_end)}}">
          </div>
          <div class="col-md-4">
            <label>Promotion Price</label>
            <input type="number" min="0" step="0.01" id="promo_price" name="promotion_price" class="form-control" value="{{$product->promotion_price}}">
          </div>
          <div class="col-md-12" style="text-align: center; margin: 5% 0px 0px 0px;"><label style='font-weight: bold;;font-size: 24px;'>Wholesale Option</label></div>
          <div class="col-md-6">
            <label>Wholesales Start Date</label>
            <input type="datetime-local" id="wholesales_start" name="wholesales_start" class="form-control" value="{{ str_replace(" ","T",$product->wholesale_start_date)}}">
          </div>
          <div class="col-md-6">
            <label>Wholesales End Date</label>
            <input type="datetime-local" id="wholesales_end" name="wholesales_end" class="form-control" value="{{ str_replace(" ","T",$product->wholesale_end_date)}}">
          </div>
          <div class="col-md-6">
            <label>Wholesales Price (Each Product) - Level 1</label>
            <input type="number" min="0" step="0.001" id="wholesales_price" name="wholesales_price" class="form-control" value="{{($product->wholesale_price != '') ? number_format($product->wholesale_price,3) : ''}}">
          </div>
          <div class="col-md-6">
            <label>Wholesales Minimum Quantity - Level 1</label>
            <input type="number" min="2" id="wholesales_quantity" name="wholesales_quantity" class="form-control" value="{{$product->wholesale_quantity}}">
          </div>
          <div class="col-md-6">
            <label>Wholesales Price (Each Product) - Level 2</label>
            <input type="number" min="0" step="0.001" id="wholesales_price2" name="wholesales_price2" class="form-control" value="{{($product->wholesale_price2 != '') ? number_format($product->wholesale_price2,3) : ''}}">
          </div>
          <div class="col-md-6">
            <label>Wholesales Minimum Quantity - Level 2</label>
            <input type="number" min="2" id="wholesales_quantity2" name="wholesales_quantity2" class="form-control" value="{{$product->wholesale_quantity2}}">
          </div>
					<div class="col-md-12" style="text-align: center;margin-top: 20px">
						<input type="submit" class="btn btn-primary" value="Update">
            <input type="reset" class="btn btn-secondary" value="Reset">
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
        <h5 class="modal-title" id="title">Modify Product Status</h5>
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
	let price_ptg = parseFloat({{$default_price->default_price_margin}});

	$("#department").change(function(){
		$.get('{{route('ajaxGetCategory')}}',{'department_id' : $("#department").val()},
			function(data){
				$("#category").html("");
				Object.entries(data).forEach(([key, result]) => {
					$("#category").append(`<option value='${result['id']}'>${result['category_name']}</option>`)
				});
			},"json");
	});

	$("#cost").on("input",function(){
		let cost = parseFloat($(this).val());
		let price = (cost * price_ptg / 100) + cost;
		$("#price").val(price.toFixed(2));
	});

  $("input[type=reset]").click(()=>{
    $("#promo_start")[0].setCustomValidity('');
    $("#promo_end")[0].setCustomValidity('');
    $("#promo_price").prop("required",false);
  });

  $("#promo_start").change(()=>{
    $("#promo_price").prop("required",true);
    $("#promo_start")[0].setCustomValidity("");
    if(!$("#promo_end").val() == ""){
      let a = new Date($("#promo_start").val());
      let b = new Date($("#promo_end").val());
      if(a > b){
        $("#promo_start")[0].setCustomValidity("Promotion Start Date Cannot Late Than Promotion End Date");
      }else{
        $("#promo_start")[0].setCustomValidity("");
      }
    }else{
      $("#promo_end")[0].setCustomValidity("Promotion End Date Cannot Be Empty");
    }
  });

  $("#promo_end").change(()=>{
    $("#promo_price").prop("required",true);
    $("#promo_end")[0].setCustomValidity("");
    if(!$("#promo_start").val() == ""){
      let a = new Date($("#promo_start").val());
      let b = new Date($("#promo_end").val());
      if(a > b){
        $("#promo_end")[0].setCustomValidity("Promotion End Date Cannot Early Than Promotion Start Date");
      }else{
        $("#promo_end")[0].setCustomValidity("");
      }
    }else{
      $("#promo_start")[0].setCustomValidity("Promotion Start Date Cannot Be Empty");
    }
  });

  $("input[type=submit]").click(()=>{
    if(($("#promo_start").val() != "" || $("#promo_end").val() != "") && $("#promo_price").val() == "")
      $("#promo_price").prop("required",true);
  });

  $("#delete_product").click(function(){
    let product = $("input[name=product_name]").val()
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this product ("+product+")!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        let barcode = $("input[name=barcode]").val();
        $.post('{{route('postDeleteProduct')}}',
        {
          '_token':'{{csrf_token()}}',
          'barcode':barcode,
        },function(data){ 
          if(data){
            swal.fire({
              title:'Delete Successful',
              text:'Items has been deleted',
              icon:'success',
              confirmButtonText: 'OK',
              allowOutsideClick: false,
              allowEscapeKey: false,
            }).then(()=>{
              window.location.replace('{{route('getProductList')}}');
            });
          }else{
            swal.fire('Delete Fail','Delete Unsuccessful, Please Contact IT Support','error');
          }
        },'json');
      }
    })
  });

  $("#wholesales_start").change(()=>{
    $("#wholesales_price").prop("required",true);
    $("#wholesales_quantity").prop("required",true);
    $("#wholesales_start")[0].setCustomValidity("");
    if(!$("#wholesales_end").val() == ""){
      let a = new Date($("#wholesales_start").val());
      let b = new Date($("#wholesales_end").val());
      if(a > b){
        $("#wholesales_start")[0].setCustomValidity("Wholesales Start Date Cannot Late Than Wholesales End Date");
      }else{
        $("#wholesales_start")[0].setCustomValidity("");
      }
    }else{
      $("#wholesales_end")[0].setCustomValidity("Wholesales End Date Cannot Be Empty");
    }
  });

  $("#wholesales_end").change(()=>{
    $("#wholesales_price").prop("required",true);
    $("#wholesales_quantity").prop("required",true);
    $("#wholesales_end")[0].setCustomValidity("");
    if(!$("#wholesales_start").val() == ""){
      let a = new Date($("#wholesales_start").val());
      let b = new Date($("#wholesales_end").val());
      if(a > b){
        $("#wholesales_end")[0].setCustomValidity("Wholesales End Date Cannot Early Than Wholesales Start Date");
      }else{
        $("#wholesales_end")[0].setCustomValidity("");
      }
    }else{
      $("#wholesales_start")[0].setCustomValidity("Wholesales Start Date Cannot Be Empty");
    }
  });

	
});

</script>
@if(session()->has('result'))
<script>
	$("#msg").text("Product Update Successful");
	$("#result").modal('toggle');
</script>
@endif

@endsection