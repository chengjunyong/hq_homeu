@extends('layouts.app')
<title>Add Category</title>
@section('content')
<style>
.row > .col-md-12,.col-md-6{
	margin-top: 10px;
}
</style>
<div class="container">
	<h2 align="center">Add Category</h2>
	<div class="card" style="border-radius: 1.25rem">
		<div class="card-title" style="padding: 10px">
			<h4>Category Information</h4>
		</div>
		<div class="card-body">
			<form method="post" action="{{ isset($category) ? route('category.update',$category->id) : route('category.store')}}">
        @if(isset($category))
          @method('PUT')
        @endif
				@csrf
				<div class="row">
          <div class="col-md-12">
            <label>Department</label>
            <select class="form-control" name="department">
              @foreach($departments as $department)
                <option value="{{$department->id}}" {{ (isset($category) && $category->department_id == $department->id) ? 'selected' : ''}}>{{$department->department_name}}</option>
              @endforeach
            </select>
            @error('department')
              <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
					</div>
          <div class="col-md-6">
            <label>Code</label>
            <input type="text" class="form-control" name="code" value="{{ isset($category) ? $category->category_code : '' }}" required/>
            @error('code')
              <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
					</div>
					<div class="col-md-6">
            <label>Name</label>
            <input type="text" class="form-control" name="name" value="{{ isset($category) ? $category->category_name : '' }}" required/>
            @error('name')
              <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
					</div>
					<div class="col-md-12" style="text-align: center;margin-top: 20px">
						<input type="submit" class="btn btn-primary" value="{{ isset($category) ? 'Update' : 'Create'}}">
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