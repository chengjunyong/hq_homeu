@extends('layouts.app')

@section('content')
<style>
.row > .col-md-12,.col-md-6{
	margin-top: 10px;
}
</style>
<div class="container">
	<h2 align="center">Create Supplier</h2>
	<div class="card" style="border-radius: 1.25rem">
		<div class="card-title" style="padding: 10px">
			<h4>Supplier Information</h4>
		</div>
		<div class="card-body">
			<form id="form">
				@csrf
				<div class="row">
					<div class="col-md-12">
						<label>Supplier Name</label>
						<input type="text" name="supplier_name" class="form-control" required>
					</div>
					<div class="col-md-6">
						<label>Supplier Code</label>
						<input type="text" name="supplier_code" class="form-control">
					</div>
					<div class="col-md-6">
						<label>Contact</label>
						<input type="text" name="contact_number" class="form-control">
					</div>
					<div class="col-md-6">
						<label>Email</label>
						<input type="text" name="email" class="form-control">
					</div>
					<div class="col-md-6">
						<label>Address Line 1</label>
						<input type="text" name="address1" class="form-control">
					</div>
					<div class="col-md-6">
						<label>Address Line 2</label>
						<input type="text" name="address2" class="form-control">
					</div>
					<div class="col-md-6">
						<label>Address Line 3</label>
						<input type="text" name="address3" class="form-control">
					</div>
					<div class="col-md-6">
						<label>Address Line 4</label>
						<input type="text" name="address4" class="form-control">
					</div>

					<div class="col-md-12" style="text-align: center;margin-top: 20px">
						<input type="button" class="btn btn-primary" value="Create">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$("input[type=button]").click(function(){

		if($("input[name=supplier_name]").val() != ""){
			$.post("{{route('postCreateSupplier')}}",
				$("form").serialize(),
				function(data){
					if(data){
						Swal.fire({
							icon:'success',
							title:'Success',
							text:'Update Successful',
						});
					}else{
						Swal.fire({
							icon:'warning',
							title:'Warning',
							text:'Update Unsuccessful, Please Contact IT Support',
						});
					}
				},"json");
		}else{
			Swal.fire({
				icon:'error',
				title:'Error',
				text:'Supplier Name Cannot Left Blank',
			});
		}
	});
});
</script>

@endsection