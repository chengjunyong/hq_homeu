@extends('layouts.app')
<title>Add Brand</title>
@section('content')
<style>
.row > .col-md-12,.col-md-6{
	margin-top: 10px;
}
</style>
<div class="container">
	<h2 align="center">Add Brand</h2>
	<div class="card" style="border-radius: 1.25rem">
		<div class="card-title" style="padding: 10px">
			<h4>Brand Information</h4>
		</div>
		<div class="card-body">
			<form method="post" action="{{ isset($brand) ? route('brand.update',$brand->id) : route('brand.store')}}">
        @if(isset($brand))
          @method('PUT')
        @endif
				@csrf
				<div class="row">
					<div class="col-md-12">
            <label>Name</label>
            <input type="text" class="form-control" name="name" value="{{ isset($brand) ? $brand->name : '' }}" required/>
            @error('name')
              <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
					</div>
					<div class="col-md-12" style="text-align: center;margin-top: 20px">
						<input type="submit" class="btn btn-primary" value="{{ isset($brand) ? 'Update' : 'Create'}}">
            <input type="reset" class="btn btn-secondary" value="Reset">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<script>
  $(document).ready(function(){

  });
</script>

@endsection