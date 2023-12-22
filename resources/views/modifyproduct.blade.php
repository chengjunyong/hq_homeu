@extends('layouts.app')
<title>Edit Product</title>
@section('content')
<style>
.row > .col-md-12,.col-md-6,.col-md-4{
	margin-top: 10px;
}

.fa-arrow-alt-circle-down{
  color: #6dd410;
  font-size: 27px;
  margin-left:8px;
}

.supplier_table thead th{
  border: none;
}

#history_length{
  margin-bottom:15px;
}

td{
  padding:10px 0px;
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
          <br/><br/>
          <button type="button" id="product_sync" class="btn btn-primary" style="float:right">Trigger Product Sync</button>
          <br/><br/>
          <button type="button" id="supplier_list" class="btn btn-secondary" style="float:right">Supplier</button>
        </div>
      </div>
		</div>
		<div class="card-body">
			<form method="post" action="{{route('postModifyProduct')}}" id="form">
				@csrf
				<div class="row">
					<div class="col-md-6">
						<label>Department</label>
						<select name="department" id="department" class="form-control" required>
							@foreach($department as $result)
								<option value="{{$result->id}}" {{($product->department_id == $result->id) ? 'selected' : ''}}>{{$result->department_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-6">
						<label>Category</label>
						<select name="category" id="category" class="form-control" required>
							@foreach($category as $result)
								<option value="{{$result->id}}" {{($product->category_id == $result->id) ? 'selected' : ''}}>{{$result->category_name}}</option>
							@endforeach
						</select>
					</div>
          <div class="col-md-6">
						<label>Brand</label>
						<select name="brand" id="brand" class="form-control" required>
							@foreach($brands as $result)
								<option value="{{$result->id}}" {{($product->brand_id == $result->id) ? 'selected' : ''}}>{{$result->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-6">
						<label>Barcode</label>
						<input type="text" name="barcode" class="form-control" readonly value="{{$product->barcode}}">
					</div>
					<div class="col-md-6">
						<label>Product Name</label>
						<input type="text" name="product_name" class="form-control" required value="{{$product->product_name}}">
					</div>
          <div class="col-md-6">
            <label>Measurement Type</label>
            <select class="form-control" name="measurement">
              <option value="unit" {{($product->measurement == 'unit') ? 'selected' : ''}}>Unit</option>
              <option value="kilogram" {{($product->measurement == 'kilogram') ? 'selected' : ''}}>Kilogram</option>
              <option value="meter" {{($product->measurement == 'meter') ? 'selected' : ''}}>Meter</option>
            </select>
          </div>
          <div class="col-md-6"></div>
					<div class="col-md-6">
						<label>Cost <!-- <a href="{{route('getProductConfig')}}">(Auto Increase {{$default_price->default_price_margin}}%)</a> --></label>
						<input type="number" min="0" step="0.001" name="cost" id="cost" class="form-control" required value="{{number_format($product->cost,3)}}">
					</div>
					<div class="col-md-6">
						<label>Price 
              <span id="display_price" style="color:red;font-weight:bold">
                @if($product->measurement == 'unit')
                  (1 Unit)
                @elseif($product->measurement == 'kilogram')
                  (1 Kilogram)
                @else
                  (1 Meter)
                @endif
              </span>
            </label>
						<input type="number" min="0" step="0.0001" name="price" id="price" class="form-control" required value="{{$product->price}}">
					</div>
					<div class="col-md-6">
						<label>Reorder Level</label>
						<input type="number" min="0" step="1" name="reorder_level" class="form-control" required value="{{$product->reorder_level}}" disabled>
					</div>
					<div class="col-md-6">
						<label>Reorder Recommend Quantity</label>
						<input type="number" min="0" step="1" name="recommend_quantity" class="form-control" required value="{{$product->recommend_quantity}}" disabled>
					</div>
          <div class="col-md-6">
            <label>Schedule Date</label>
            <input type="date" name="schedule_date" class="form-control" value="{{$product->schedule_date}}">
          </div>
          <div class="col-md-6">
            <label>Schedule Price</label>
            <input type="number" min="0" step="0.01" name="schedule_price" class="form-control" value="{{$product->schedule_price}}">
          </div>
          <div class="col-md-12">
            <label>Remark</label>
            <textarea name="remark" class="form-control" rows="5">{!! $product->remark !!}</textarea>
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

          <!-- Normal Wholesales Option -->
          <div class="col-md-12" style="text-align: center; margin: 5% 0px 0px 0px;">
            <a href="#w1" data-toggle="collapse">
              <label style='font-weight: bold;font-size: 24px;cursor: pointer;'>
                Wholesales Option <i class="fa fa-chevron-down"></i>
              </label>
            </a>
          </div>
          <div class="collapse" id="w1">
            <div class="row" style="margin: 0px;">
              <div class="col-md-6">
                <label>Wholesales Price (Each Product) - Level 1</label>
                <input type="number" min="0" step="0.00001" id="normal_wholesales_price" name="normal_wholesales_price" class="form-control" value="{{($product->normal_wholesale_price != '') ? $product->normal_wholesale_price : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Minimum Quantity - Level 1</label>
                <input type="number" min="2" id="normal_wholesales_quantity" name="normal_wholesales_quantity" class="form-control" value="{{$product->normal_wholesale_quantity}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Price (Each Product) - Level 2</label>
                <input type="number" min="0" step="0.00001" id="normal_wholesales_price2" name="normal_wholesales_price2" class="form-control" value="{{($product->normal_wholesale_price2 != '') ? $product->normal_wholesale_price2 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Minimum Quantity - Level 2</label>
                <input type="number" min="2" id="normal_wholesales_quantity2" name="normal_wholesales_quantity2" class="form-control" value="{{$product->normal_wholesale_quantity2}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Price (Each Product) - Level 3</label>
                <input type="number" min="0" step="0.00001" id="normal_wholesales_price3" name="normal_wholesales_price3" class="form-control" value="{{($product->normal_wholesale_price3 != '') ? $product->normal_wholesale_price3 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Minimum Quantity - Level 3</label>
                <input type="number" min="2" id="normal_wholesales_quantity3" name="normal_wholesales_quantity3" class="form-control" value="{{$product->normal_wholesale_quantity3}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Price (Each Product) - Level 4</label>
                <input type="number" min="0" step="0.00001" id="normal_wholesales_price4" name="normal_wholesales_price4" class="form-control" value="{{($product->normal_wholesale_price4 != '') ? $product->normal_wholesale_price4 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Minimum Quantity - Level 4</label>
                <input type="number" min="2" id="normal_wholesales_quantity4" name="normal_wholesales_quantity4" class="form-control" value="{{$product->normal_wholesale_quantity4}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Price (Each Product) - Level 5</label>
                <input type="number" min="0" step="0.00001" id="normal_wholesales_price5" name="normal_wholesales_price5" class="form-control" value="{{($product->normal_wholesale_price5 != '') ? $product->normal_wholesale_price5 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Minimum Quantity - Level 5</label>
                <input type="number" min="2" id="normal_wholesales_quantity5" name="normal_wholesales_quantity5" class="form-control" value="{{$product->normal_wholesale_quantity5}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Price (Each Product) - Level 6</label>
                <input type="number" min="0" step="0.00001" id="normal_wholesales_price6" name="normal_wholesales_price6" class="form-control" value="{{($product->normal_wholesale_price6 != '') ? $product->normal_wholesale_price6 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Minimum Quantity - Level 6</label>
                <input type="number" min="2" id="normal_wholesales_quantity6" name="normal_wholesales_quantity6" class="form-control" value="{{$product->normal_wholesale_quantity6}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Price (Each Product) - Level 7</label>
                <input type="number" min="0" step="0.0001" id="normal_wholesales_price7" name="normal_wholesales_price7" class="form-control" value="{{($product->normal_wholesale_price7 != '') ? $product->normal_wholesale_price7 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Minimum Quantity - Level 7</label>
                <input type="number" min="2" id="normal_wholesales_quantity7" name="normal_wholesales_quantity7" class="form-control" value="{{$product->normal_wholesale_quantity7}}">
              </div>
            </div>
          </div>
          <!-- Normal Wholesales Option -->

          <!-- Promotion Wholesales Option -->
          <div class="col-md-12" style="text-align: center; margin: 5% 0px 0px 0px;">
            <a href="#w2" data-toggle="collapse">
              <label style='font-weight: bold;font-size: 24px;cursor: pointer;'>
                Wholesales Promotion Option <i class="fa fa-chevron-down"></i>
              </label>
            </a>
          </div>
          <div class="collapse" id="w2">
            <div class="row" style="margin:0px">
              <div class="col-md-6">
                <label>Wholesales Promotion Start Date</label>
                <input type="datetime-local" id="wholesales_start" name="wholesales_start" class="form-control" value="{{ str_replace(" ","T",$product->wholesale_start_date)}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion End Date</label>
                <input type="datetime-local" id="wholesales_end" name="wholesales_end" class="form-control" value="{{ str_replace(" ","T",$product->wholesale_end_date)}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Price (Each Product) - Level 1</label>
                <input type="number" min="0" step="0.00001" id="wholesales_price" name="wholesales_price" class="form-control" value="{{($product->wholesale_price != '') ? $product->wholesale_price : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Minimum Quantity - Level 1</label>
                <input type="number" min="2" id="wholesales_quantity" name="wholesales_quantity" class="form-control" value="{{$product->wholesale_quantity}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Price (Each Product) - Level 2</label>
                <input type="number" min="0" step="0.00001" id="wholesales_price2" name="wholesales_price2" class="form-control" value="{{($product->wholesale_price2 != '') ? $product->wholesale_price2 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Minimum Quantity - Level 2</label>
                <input type="number" min="2" id="wholesales_quantity2" name="wholesales_quantity2" class="form-control" value="{{$product->wholesale_quantity2}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Price (Each Product) - Level 3</label>
                <input type="number" min="0" step="0.00001" id="wholesales_price3" name="wholesales_price3" class="form-control" value="{{($product->wholesale_price3 != '') ? $product->wholesale_price3 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Minimum Quantity - Level 3</label>
                <input type="number" min="2" id="wholesales_quantity3" name="wholesales_quantity3" class="form-control" value="{{$product->wholesale_quantity3}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Price (Each Product) - Level 4</label>
                <input type="number" min="0" step="0.00001" id="wholesales_price4" name="wholesales_price4" class="form-control" value="{{($product->wholesale_price4 != '') ? $product->wholesale_price4 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Minimum Quantity - Level 4</label>
                <input type="number" min="2" id="wholesales_quantity4" name="wholesales_quantity4" class="form-control" value="{{$product->wholesale_quantity4}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Price (Each Product) - Level 5</label>
                <input type="number" min="0" step="0.00001" id="wholesales_price5" name="wholesales_price5" class="form-control" value="{{($product->wholesale_price5 != '') ? $product->wholesale_price5 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Minimum Quantity - Level 5</label>
                <input type="number" min="2" id="wholesales_quantity5" name="wholesales_quantity5" class="form-control" value="{{$product->wholesale_quantity5}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Price (Each Product) - Level 6</label>
                <input type="number" min="0" step="0.00001" id="wholesales_price6" name="wholesales_price6" class="form-control" value="{{($product->wholesale_price6 != '') ? $product->wholesale_price6 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Minimum Quantity - Level 6</label>
                <input type="number" min="2" id="wholesales_quantity6" name="wholesales_quantity6" class="form-control" value="{{$product->wholesale_quantity6}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Price (Each Product) - Level 7</label>
                <input type="number" min="0" step="0.00001" id="wholesales_price7" name="wholesales_price7" class="form-control" value="{{($product->wholesale_price7 != '') ? $product->wholesale_price7 : ''}}">
              </div>
              <div class="col-md-6">
                <label>Wholesales Promotion Minimum Quantity - Level 7</label>
                <input type="number" min="2" id="wholesales_quantity7" name="wholesales_quantity7" class="form-control" value="{{$product->wholesale_quantity7}}">
              </div>
            </div>
          </div>
          <!-- Promotion Wholesales Option -->

          {{-- History --}}
          <div class="col-md-12" style="margin:25px 0px;">
            <div style="text-align: center">
              <label style='font-weight: bold;font-size: 24px;'>History</label>
            </div>
            <table id="history" style="width:100%">
              <thead>
                <th>No</th>
                <th>Previous Data</th>
                <th>Current Data</th>
                <th style="text-align: center">Modified By</th>
                <th style="text-align: center">Modified At</th>
              </thead>
              <tbody>
                @foreach($history as $index => $result)
                  <tr>
                    <td>{{$index + 1}}</td>
                    <td>{!! $result->previous_value !!}</td>
                    <td>{!! $result->current_value !!}</td>
                    <td style="text-align: center">{{$result->creator_name}}</td>
                    <td style="text-align: center">{{date("d-M-Y H:i:s A",strtotime($result->created_at))}}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          {{-- History --}}
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

<div class="modal fade" id="supplier_table" role="dialog">
  <div class="modal-dialog" role="document" style="max-width: 80%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">Supplier List</h5>
      </div>
      <div class="modal-body" style="text-align: center">
        <table class="table supplier_table" style="width:100%;">
          <thead style="border:none">
            <th>No</th>
            <th>Supplier Code</th>
            <th>Supplier Name</th>
            <th>Contact</th>
            <th></th>
          </thead>
          <tbody id="supplier_details">
            @foreach($supplier_list as $index => $result)
              <tr>
                <td>{{$index+1}}</td>
                <td>{{$result->supplier_code}}</td>
                <td>{{$result->supplier_name}}</td>
                <td>{{$result->contact_number}}</td>
                <td><button class="btn btn-danger delete_supplier" val="{{$result->id}}">Delete</button></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="add_new_supplier">Add new supplier</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="supplier_selector" role="dialog" style="background-color:#000000d4">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">Add Supplier</h5>
      </div>
      <div class="modal-body" style="text-align: center">
        <select id="supplier_id" class="form-control">
          @foreach($supplier as $result)
            <option value="{{$result->id}}">{{$result->supplier_name}}</option>
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="submit_supplier">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
	let price_ptg = parseFloat({{$default_price->default_price_margin}});

  $("select[name=measurement]").change(function(){
    if($(this).val() == 'unit'){
      $("#display_price").text('(1 Unit)');
    }else if($(this).val() == 'kilogram'){
      $("#display_price").text('(1 Kilogram)');
    }else{
      $("#display_price").text('(1 Meter)');
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

	// $("#cost").on("input",function(){
	// 	let cost = parseFloat($(this).val());
	// 	let price = (cost * price_ptg / 100) + cost;
	// 	$("#price").val(price.toFixed(2));
	// });

  $("input[type=reset]").click(()=>{
    $("#promo_start")[0].setCustomValidity('');
    $("#promo_end")[0].setCustomValidity('');
    $("#promo_price").prop("required",false);
  });

  // $("#promo_start").change(()=>{
  //   $("#promo_price").prop("required",true);
  //   $("#promo_start")[0].setCustomValidity("");
  //   if(!$("#promo_end").val() == ""){
  //     let a = new Date($("#promo_start").val());
  //     let b = new Date($("#promo_end").val());
  //     if(a > b){
  //       $("#promo_start")[0].setCustomValidity("Promotion Start Date Cannot Late Than Promotion End Date");
  //     }else{
  //       $("#promo_start")[0].setCustomValidity("");
  //     }
  //   }else{
  //     $("#promo_end")[0].setCustomValidity("Promotion End Date Cannot Be Empty");
  //   }
  // });

  // $("#promo_end").change(()=>{
  //   $("#promo_price").prop("required",true);
  //   $("#promo_end")[0].setCustomValidity("");
  //   if(!$("#promo_start").val() == ""){
  //     let a = new Date($("#promo_start").val());
  //     let b = new Date($("#promo_end").val());
  //     if(a > b){
  //       $("#promo_end")[0].setCustomValidity("Promotion End Date Cannot Early Than Promotion Start Date");
  //     }else{
  //       $("#promo_end")[0].setCustomValidity("");
  //     }
  //   }else{
  //     $("#promo_start")[0].setCustomValidity("Promotion Start Date Cannot Be Empty");
  //   }
  // });

  // $("input[type=submit]").click(()=>{
  //   if(($("#promo_start").val() != "" || $("#promo_end").val() != "") && $("#promo_price").val() == "")
  //     $("#promo_price").prop("required",true);
  // });

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

  $("#product_sync").click(function(){
    Swal.fire({
      title: 'Are you sure?',
      text: "All Branches Will Sync This Product Again.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes'
    }).then((result) => {
      if (result.isConfirmed) {
        let barcode = $("input[name=barcode]").val();
        $.get('{{route('ajaxTriggerProductSync')}}',
        {
          'barcode':barcode,
        },function(data){ 
          console.log(data);
          if(data){
            swal.fire({
              title:'Update Successful',
              text:'Items Sync Status Has Been Reset',
              icon:'success',
              confirmButtonText: 'OK',
              allowOutsideClick: false,
              allowEscapeKey: false,
            });
          }else{
            swal.fire('Delete Fail','Delete Unsuccessful, Please Contact IT Support','error');
          }
        },'json');
      }
    })
  });

  // $("#wholesales_start").change(()=>{
  //   $("#wholesales_start")[0].setCustomValidity("");
  //   if(!$("#wholesales_end").val() == ""){
  //     let a = new Date($("#wholesales_start").val());
  //     let b = new Date($("#wholesales_end").val());
  //     if(a > b){
  //       $("#wholesales_start")[0].setCustomValidity("Wholesales Start Date Cannot Late Than Wholesales End Date");
  //     }else{
  //       $("#wholesales_start")[0].setCustomValidity("");
  //     }
  //   }else{
  //     $("#wholesales_end")[0].setCustomValidity("Wholesales End Date Cannot Be Empty");
  //   }
  // });

  // $("#wholesales_end").change(()=>{
  //   $("#wholesales_end")[0].setCustomValidity("");
  //   if(!$("#wholesales_start").val() == ""){
  //     let a = new Date($("#wholesales_start").val());
  //     let b = new Date($("#wholesales_end").val());
  //     if(a > b){
  //       $("#wholesales_end")[0].setCustomValidity("Wholesales End Date Cannot Early Than Wholesales Start Date");
  //     }else{
  //       $("#wholesales_end")[0].setCustomValidity("");
  //     }
  //   }else{
  //     $("#wholesales_start")[0].setCustomValidity("Wholesales Start Date Cannot Be Empty");
  //   }
  // });

  // Start Normal Wholesales
  // $("#normal_wholesales_price,#normal_wholesales_quantity").change(()=>{
  //   if($("#normal_wholesales_price").val() != "" || $("#normal_wholesales_quantity").val() != ""){
  //     $("#normal_wholesales_price").prop("required",true);
  //     $("#normal_wholesales_quantity").prop("required",true);
  //   }else{
  //     $("#normal_wholesales_price").prop("required",false);
  //     $("#normal_wholesales_quantity").prop("required",false);  
  //   }
  // });

  // $("#normal_wholesales_price2,#normal_wholesales_quantity2").change(()=>{
  //   if($("#normal_wholesales_price2").val() != "" || $("#normal_wholesales_quantity2").val() != ""){
  //     $("#normal_wholesales_price2").prop("required",true);
  //     $("#normal_wholesales_quantity2").prop("required",true);
  //   }else{
  //     $("#normal_wholesales_price2").prop("required",false);
  //     $("#normal_wholesales_quantity2").prop("required",false);  
  //   }
  // });

  // $("#normal_wholesales_price3,#normal_wholesales_quantity3").change(()=>{
  //   if($("#normal_wholesales_price3").val() != "" || $("#normal_wholesales_quantity3").val() != ""){
  //     $("#normal_wholesales_price3").prop("required",true);
  //     $("#normal_wholesales_quantity3").prop("required",true);
  //   }else{
  //     $("#normal_wholesales_price3").prop("required",false);
  //     $("#normal_wholesales_quantity3").prop("required",false);  
  //   }
  // });

  // $("#normal_wholesales_price4,#normal_wholesales_quantity4").change(()=>{
  //   if($("#normal_wholesales_price4").val() != "" || $("#normal_wholesales_quantity4").val() != ""){
  //     $("#normal_wholesales_price4").prop("required",true);
  //     $("#normal_wholesales_quantity4").prop("required",true);
  //   }else{
  //     $("#normal_wholesales_price4").prop("required",false);
  //     $("#normal_wholesales_quantity4").prop("required",false);  
  //   }
  // });

  // $("#normal_wholesales_price5,#normal_wholesales_quantity5").change(()=>{
  //   if($("#normal_wholesales_price5").val() != "" || $("#normal_wholesales_quantity5").val() != ""){
  //     $("#normal_wholesales_price5").prop("required",true);
  //     $("#normal_wholesales_quantity5").prop("required",true);
  //   }else{
  //     $("#normal_wholesales_price5").prop("required",false);
  //     $("#normal_wholesales_quantity5").prop("required",false);  
  //   }
  // });

  // $("#normal_wholesales_price6,#normal_wholesales_quantity6").change(()=>{
  //   if($("#normal_wholesales_price6").val() != "" || $("#normal_wholesales_quantity6").val() != ""){
  //     $("#normal_wholesales_price6").prop("required",true);
  //     $("#normal_wholesales_quantity6").prop("required",true);
  //   }else{
  //     $("#normal_wholesales_price6").prop("required",false);
  //     $("#normal_wholesales_quantity6").prop("required",false);  
  //   }
  // });

  // $("#normal_wholesales_price7,#normal_wholesales_quantity7").change(()=>{
  //   if($("#normal_wholesales_price7").val() != "" || $("#normal_wholesales_quantity7").val() != ""){
  //     $("#normal_wholesales_price7").prop("required",true);
  //     $("#normal_wholesales_quantity7").prop("required",true);
  //   }else{
  //     $("#normal_wholesales_price7").prop("required",false);
  //     $("#normal_wholesales_quantity7").prop("required",false);  
  //   }
  // });
  //End Normal Wholesale

  // Promotion Wholesale Start
  // $("#wholesales_price,#wholesales_quantity").change(()=>{
  //   if($("#wholesales_price").val() != "" || $("#wholesales_quantity").val() != ""){
  //     $("#wholesales_price").prop("required",true);
  //     $("#wholesales_quantity").prop("required",true);
  //   }else{
  //     $("#wholesales_price").prop("required",false);
  //     $("#wholesales_quantity").prop("required",false);  
  //   }
  // });

  // $("#wholesales_price2,#wholesales_quantity2").change(()=>{
  //   if($("#wholesales_price2").val() != "" || $("#wholesales_quantity2").val() != ""){
  //     $("#wholesales_price2").prop("required",true);
  //     $("#wholesales_quantity2").prop("required",true);
  //   }else{
  //     $("#wholesales_price2").prop("required",false);
  //     $("#wholesales_quantity2").prop("required",false);  
  //   }
  // });

  // $("#wholesales_price3,#wholesales_quantity3").change(()=>{
  //   if($("#wholesales_price3").val() != "" || $("#wholesales_quantity3").val() != ""){
  //     $("#wholesales_price3").prop("required",true);
  //     $("#wholesales_quantity3").prop("required",true);
  //   }else{
  //     $("#wholesales_price3").prop("required",false);
  //     $("#wholesales_quantity3").prop("required",false);  
  //   }
  // });

  // $("#wholesales_price4,#wholesales_quantity4").change(()=>{
  //   if($("#wholesales_price4").val() != "" || $("#wholesales_quantity4").val() != ""){
  //     $("#wholesales_price4").prop("required",true);
  //     $("#wholesales_quantity4").prop("required",true);
  //   }else{
  //     $("#wholesales_price4").prop("required",false);
  //     $("#wholesales_quantity4").prop("required",false);  
  //   }
  // });

  // $("#wholesales_price5,#wholesales_quantity5").change(()=>{
  //   if($("#wholesales_price5").val() != "" || $("#wholesales_quantity5").val() != ""){
  //     $("#wholesales_price5").prop("required",true);
  //     $("#wholesales_quantity5").prop("required",true);
  //   }else{
  //     $("#wholesales_price5").prop("required",false);
  //     $("#wholesales_quantity5").prop("required",false);  
  //   }
  // });

  // $("#wholesales_price6,#wholesales_quantity6").change(()=>{
  //   if($("#wholesales_price6").val() != "" || $("#wholesales_quantity6").val() != ""){
  //     $("#wholesales_price6").prop("required",true);
  //     $("#wholesales_quantity6").prop("required",true);
  //   }else{
  //     $("#wholesales_price6").prop("required",false);
  //     $("#wholesales_quantity6").prop("required",false);  
  //   }
  // });

  // $("#wholesales_price7,#wholesales_quantity7").change(()=>{
  //   if($("#wholesales_price7").val() != "" || $("#wholesales_quantity7").val() != ""){
  //     $("#wholesales_price7").prop("required",true);
  //     $("#wholesales_quantity7").prop("required",true);
  //   }else{
  //     $("#wholesales_price7").prop("required",false);
  //     $("#wholesales_quantity7").prop("required",false);  
  //   }
  // });

  $("select[name=measurement]").change(function(){
    changeStep();
  });

  changeStep();

  $("#supplier_list").click(function(){
    $("#supplier_table").modal('show');
  });

  $("#add_new_supplier").click(function(){
    $("#supplier_selector").modal('show');
  });

  $("#submit_supplier").click(function(){
    let supplier_id = $("#supplier_id").val();
    let product_id = {{$product->id}};
    $.get('{{route('ajaxAddSupplier')}}',
      {
        'supplier_id':supplier_id,
        'product_id':product_id,
      },function(data){
        let count = parseInt($("#supplier_details tr").length);
        let html = "";
        html += "<tr>";
        html += `<td>${++count}</td>`;
        html += `<td>${data.supplier_code}</td>`;
        html += `<td>${data.supplier_name}</td>`;
        html += `<td>${data.contact_number}</td>`;
        html += `<td><button class="btn btn-alert" onclick='window.location.reload()'>Refresh</button></td>`;
        html += "</tr>";
        $("#supplier_details").append(html);
        $("#supplier_selector").modal('hide');
      },'json');  
  });

  $(".delete_supplier").click(function(){
    let e = $(this).parents().eq(1);
    let id = $(this).attr('val');
    $.get('{{route('ajaxDeleteSupplier')}}',
    {
      'id':id,
    },function(data){
      if(data){
        swal.fire('Successful','Delete Successful','success');
        e.remove();
      }else{
        swal.fire('Unsuccessful','Delete Unuccessful Please Try Again','error');
      }
    },'json');
  });

  $("#history").DataTable({
    searching: false,
  });
	
});

function changeStep(){
  if($("select[name=measurement]").val() == 'unit'){
    $("#normal_wholesales_quantity").attr('step',1);
    $("#normal_wholesales_quantity2").attr('step',1);
    $("#normal_wholesales_quantity3").attr('step',1);
    $("#normal_wholesales_quantity4").attr('step',1);
    $("#normal_wholesales_quantity5").attr('step',1);
    $("#normal_wholesales_quantity6").attr('step',1);
    $("#normal_wholesales_quantity7").attr('step',1);
    $("#normal_wholesales_quantity").attr('min',2);
    $("#normal_wholesales_quantity2").attr('min',2);
    $("#normal_wholesales_quantity3").attr('min',2);
    $("#normal_wholesales_quantity4").attr('min',2);
    $("#normal_wholesales_quantity5").attr('min',2);
    $("#normal_wholesales_quantity6").attr('min',2);
    $("#normal_wholesales_quantity7").attr('min',2);
    $("#wholesales_quantity").attr('step',1);
    $("#wholesales_quantity2").attr('step',1);
    $("#wholesales_quantity3").attr('step',1);
    $("#wholesales_quantity4").attr('step',1);
    $("#wholesales_quantity5").attr('step',1);
    $("#wholesales_quantity6").attr('step',1);
    $("#wholesales_quantity7").attr('step',1);
    $("#wholesales_quantity").attr('min',2);
    $("#wholesales_quantity2").attr('min',2);
    $("#wholesales_quantity3").attr('min',2);
    $("#wholesales_quantity4").attr('min',2);
    $("#wholesales_quantity5").attr('min',2);
    $("#wholesales_quantity6").attr('min',2);
    $("#wholesales_quantity7").attr('min',2);
  }else{
    $("#normal_wholesales_quantity").attr('step',0.001);
    $("#normal_wholesales_quantity2").attr('step',0.001);
    $("#normal_wholesales_quantity3").attr('step',0.001);
    $("#normal_wholesales_quantity4").attr('step',0.001);
    $("#normal_wholesales_quantity5").attr('step',0.001);
    $("#normal_wholesales_quantity6").attr('step',0.001);
    $("#normal_wholesales_quantity7").attr('step',0.001);
    $("#wholesales_quantity").attr('step',0.001);
    $("#wholesales_quantity2").attr('step',0.001);
    $("#wholesales_quantity3").attr('step',0.001);
    $("#wholesales_quantity4").attr('step',0.001);
    $("#wholesales_quantity5").attr('step',0.001);
    $("#wholesales_quantity6").attr('step',0.001);
    $("#wholesales_quantity7").attr('step',0.001);
    $("#normal_wholesales_quantity").attr('min',0.001);
    $("#normal_wholesales_quantity2").attr('min',0.001);
    $("#normal_wholesales_quantity3").attr('min',0.001);
    $("#normal_wholesales_quantity4").attr('min',0.001);
    $("#normal_wholesales_quantity5").attr('min',0.001);
    $("#normal_wholesales_quantity6").attr('min',0.001);
    $("#normal_wholesales_quantity7").attr('min',0.001);
    $("#wholesales_quantity").attr('min',0.001);
    $("#wholesales_quantity2").attr('min',0.001);
    $("#wholesales_quantity3").attr('min',0.001);
    $("#wholesales_quantity4").attr('min',0.001);
    $("#wholesales_quantity5").attr('min',0.001);
    $("#wholesales_quantity6").attr('min',0.001);
    $("#wholesales_quantity7").attr('min',0.001);
  }
}
</script>
@if(session()->has('result'))
<script>
	$("#msg").text("Product Update Successful");
	$("#result").modal('toggle');
</script>
@endif

@endsection