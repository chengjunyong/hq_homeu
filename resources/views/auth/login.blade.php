@extends('layouts.app')

@section('content')
<style>
	body{
		background-color: #000000b5;
	}
	.panel{
		background-color:#7f9bd4;
		width:70%;
		margin: 0 auto;
		border-radius:30px;
		height:60vh;
		font-size:25px;
	}

</style>

<div class="main">
	<div id="login">
		<div class="panel">
			<div id="login-row" class="row justify-content-center align-items-center">
				<div id="login-column" class="col-md-6">
					<div id="login-box" class="col-md-12">
						<form id="login-form" class="form" action="{{route('login')}}" method="post">
							@csrf
							<br/>
							<div style="text-align: center">
								<img src="../storage/image/logo.png"/>
							</div>
							<h3 class="text-center">HQ System Homeu</h3>
							<div class="form-group">
								<label for="username">Username:</label><br>
								<input type="text" name="username" id="username" class="form-control">
							</div>
							<div class="form-group">
								<label for="password">Password:</label><br>
								<input type="password" name="password" id="password" class="form-control">
							</div>
							<div class="form-group"><br/>
								<input type="submit" name="submit" class="btn btn-primary" value="Login" style="width:100%">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
