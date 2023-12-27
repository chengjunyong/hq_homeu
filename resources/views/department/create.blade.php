@extends('layouts.app')
<title>Add Department</title>
@section('content')
<style>
.row > .col-md-12,.col-md-6{
	margin-top: 10px;
}
</style>
<div class="container">
	<h2 align="center">Add Department</h2>
	<div class="card" style="border-radius: 1.25rem">
		<div class="card-title" style="padding: 10px">
			<h4>Department Information</h4>
		</div>
		<div class="card-body">
			<form method="post" action="{{ isset($department) ? route('department.update',$department->id) : route('department.store')}}">
        @if(isset($department))
          @method('PUT')
        @endif
				@csrf
				<div class="row">
					<div class="col-md-12">
            <label>Name</label>
            <input type="text" class="form-control" name="name" value="{{ isset($department) ? $department->department_name : '' }}" required/>
            @error('name')
              <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
					</div>
					<div class="col-md-12" style="text-align: center;margin-top: 20px">
						<input type="submit" class="btn btn-primary" value="{{ isset($department) ? 'Update' : 'Create'}}">
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