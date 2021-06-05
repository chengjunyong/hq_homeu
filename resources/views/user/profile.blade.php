@extends('layouts.app')
<title>User Profile</title>
@section('content')

<div class="container">
  <h2 align="center">User Profile</h2>
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('updateUserProfile') }}">
        @csrf
        <div class="row">
          <div class="col-12 form-group">
            <div class="row">
              <div class="col-6">
                <label>Name : </label>
              </div>

              <div class="col-6">
                <input class="form-control" type="text" name="name" value="{{ $user->name }}" required />
              </div>
            </div>
          </div>

          <div class="col-12 form-group">
            <div class="row">
              <div class="col-6">
                <label>Username : </label>
              </div>

              <div class="col-6">
                <label>{{ $user->username }}</label>
              </div>
            </div>
          </div>

          <div class="col-12 form-group">
            <div class="row">
              <div class="col-6">
                <label>Password <small> * leave as empty if no changes </small> </label>
              </div>

              <div class="col-6">
                <input class="form-control {{ $errors->any() ? 'is-invalid' : '' }}" type="password" name="password" minlength="8" />
                @if ($errors->first())
                  <span class="invalid-feedback" role="alert">{{ $errors->first() }}</span>
                @endif
              </div>
            </div>
          </div>

          <div class="col-12 form-group">
            <div class="row">
              <div class="col-6">
                <label>Confirm password : </label>
              </div>

              <div class="col-6">
                <input class="form-control" type="password" name="confirmation_password" minlength="8" />
              </div>
            </div>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-success">Update</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>

@if (\Session::has('updated'))
  <div id="user_updated">
@endif

<script>
  
  $(document).ready(function(){
    if($("#user_updated").length > 0)
    {
      Swal.fire(
        'Success!',
        "User updated.",
        'success'
      );
    }
  });

</script>

@endsection