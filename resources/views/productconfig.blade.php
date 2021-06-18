@extends('layouts.app')
<title>Product Configuration</title>
@section('content')
<style>
	.form-control{
		border:2px solid #778da2;
		width:60%;
	}
</style>
<div class="container">
	<h2 align="center">Product Configuration</h2>
	<div class="card" style="padding:3% 8%">
		<div class="title">
			<h4>Settings</h4>
		</div>
		<div class="body">
			<form method="post" action="{{route('postProductConfig')}}" id="settings">
				@csrf
				<div class="row">
					<div class="col-md-12">
						<label>Default Sales Price Percentage (Based on cost)</label><br/>
						<input type="number" name="percentage" step="0.01" required class="form-control" value="{{$result->default_price_margin}}" min="0">
					</div>
					<div class="col-md-12" style="margin-top: 20px">
						<label>Default Sales Able Lower Than Cost</label><br/>
						<select name="lower_than_cost" class="form-control">
							<option value="1" {{ ($result->below_sales_price) ? 'selected' : '' }}>Yes</option>
							<option value="0" {{ ($result->below_sales_price) ? '' : 'selected' }}>No</option>
						</select>
					</div>
					<div class="col-md-12" style="margin-top: 5%;">
						<input type="submit" class="btn btn-primary" value="Update">
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
        <h5 class="modal-title" id="title">Update</h5>
      </div>
      <div class="modal-body" style="text-align: center">
        <label style="margin-top: 5px" id="label">Update Successful</label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
	$("#settings").submit(function(e){
		e.preventDefault();

		$.post("{{route('postProductConfig')}}",$("#settings").serialize(),
			function(data){
				console.log(data);
				if(data == "true"){
					$("#label").text('Update Successful');
					$("#result").modal('toggle');
				}else{
					$("#label").text('Update unsuccessful, please contact IT support');
					$("#result").modal('toggle');
				}
			},"html");
	});
</script>

@endsection