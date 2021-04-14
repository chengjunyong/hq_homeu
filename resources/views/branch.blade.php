@extends('layouts.app')

@section('content')
<script src="{{ asset('datatable/datatables.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('datatable/datatables.min.css')}}"/>
<script src="{{ asset('js/md5.min.js') }}"></script>
<style>
	body{
		background: #f9fafb;
	}

	#branch_list{
		width:100%;
		border:1px solid black;
	}

	tr{
		border:1px solid black;
	}

	td{
		padding: 5px 0px 5px 0px;
	}

  .loader {
	  border: 16px solid #f3f3f3;
	  border-radius: 50%;
	  border-top: 16px solid #3498db;
	  width: 120px;
	  height: 120px;
	  -webkit-animation: spin 2s linear infinite; /* Safari */
	  animation: spin 2s linear infinite;
	  margin:0 auto;
	}

	@keyframes spin {
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}

	.form-control{
		border: 1px solid #5e676f !important;
	}

</style>

	<div class="container">
		<div class="card" style="margin-top: 15px">
			<div class="card-body">
				<h2 align="center">Branch Setup</h2>
				<div class="row">
					<div class="col-md-12">
						<h4>Create New Branchs</h4>
					</div>
				</div>
				<form method="post" action="{{route('createBranch')}}" id="form">
					@csrf
					<div class="container">
						<div class="row">
							<div class="col-md-6">
								<label>Branch Name</label>
								<input type="text" name="branch_name" class="form-control" required>
							</div>
							<div class="col-md-6">
								<label>Contact Number</label>
								<input type="text" name="contact_number" class="form-control" required>
							</div>
						</div>
						<div class="row" style="margin-top: 15px">
							<div class="col-md-12">
								<label>Token Branch (The token must be unique text)</label>
							</div>
							<div class="col-md-8 input-group">
								<input type="text" name="token" class="form-control" required id="token">
								<button type="button" class="btn btn-danger" id="generate" style="margin-left: 10px">Generate Token</button>
							</div>
						</div>

						<div class="row" style="margin-top: 15px">
							<div class="col-md-12">
								<label>Address</label>
								<textarea class="form-control" name="address" required></textarea>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12" style="text-align: center;margin:10px 0px 10px 0px">
								<input type="submit" class="btn btn-primary" value="Create"/>
							</div>
						</div>
					</div>
				</form>

				<div class="row">
					<div class="col-md-12">
						<h4>Branch List</h4>
						<table id="branch_list">
							<thead style="background-color: #403c3c80;text-align: center">
								<tr>
									<th>Branch ID</th>
									<th>Branch Name</th>
									<th>Branch Contact</th>
									<th>Token</th>
									<th>Created Date</th>
									<th>Last Update</th>
									<th></th>
								</tr>
							</thead>
							<tbody style="text-align: center">
								@foreach($branch as $result)
									<tr>
										<td>{{$result->id}}</td>
										<td>{{$result->branch_name}}</td>
										<td>{{$result->contact}}</td>
										<td>{{$result->token}}</td>
										<td>{{$result->created_at}}</td>
										<td>{{$result->updated_at}}</td>
										<td><button class="btn btn-danger modify-branch" data="{{ $result->id }}">Modify</button></a></td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<div style="float:right;">
							{{$branch->links()}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<div class="modal fade" id="load" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create Branch Processing</h5>
      </div>
      <div class="modal-body" style="text-align: center">
        <div class="loader"></div>
        <label style="margin-top: 5px">This process will take some time to execute<br/>Please be patient.</label>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="result" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title"></h5>
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

<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">Edit Branch Information</h5>
      </div>
      <div class="modal-body" style="text-align: center">
        <div class="row" style="text-align: left">
          <div class="col-md-12">
            <label>Branch Name</label>
            <input type="text" id="branch_name" class="form-control">
            <input type="text" id="target_id" hidden />
          </div>
          <div class="col-md-12">
            <label>Branch Contact Number</label>
            <input type="text" id="branch_contact_number" class="form-control">
          </div>
          <div class="col-md-12">
            <label>Branch Address</label>
            <textarea id="branch_address" class="form-control"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="edit_branch">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function(){
	$("#branch_list").dataTable({
		'ordering':false,
		'paging':false,
		'searching':false,
	});

	$("#generate").click(function(){
		let result = md5(Math.random());
		$("#token").val(result);
	})

	$("#form").submit(function(e){
		e.preventDefault();
		$("#load").modal({backdrop: 'static',keyboard: false});

		$.post('{{route('createBranch')}}',
			$("#form").serialize(),
			function(data){
				console.log(data);
				$("#load").modal('toggle');
				if(data == "true"){
					$("#title").text("Successful");
					$("#msg").text("Branch create successful, Refresh in 1 seconds");
				}else{
					$("#title").text("Failed");
					$("#msg").text("Branch create failure, please contact IT support");
				}
				$("#result").modal('toggle');

        setTimeout(()=>{window.location.reload();},1250);

			},'html');
	});

  $(".modify-branch").click(function(){
    let method = "get";
    let id = $(this).attr('data');
    $.get('{{ route('editBranch') }}',
    {
      'method' : method,
      'id'     : id
    },function(data){
      console.log(data);
      $("#target_id").val(data['id']);
      $("#branch_name").val(data['branch_name']);
      $("#branch_contact_number").val(data['contact']);
      $("#branch_address").val(data['address']);
      $("#edit-modal").modal('toggle');
    },'json');
  });

  $("#edit_branch").click(function(){
    $.get('{{route('editBranch')}}',
    {
      'method' : "postBranch",
      'id' : $("#target_id").val(),
      'name' : $("#branch_name").val(),
      'contact' : $("#branch_contact_number").val(),
      'address' : $("#branch_address").val(),
    },function(data){
      if(data == 1){
        window.location.reload();
      }else{
        swal.fire("Fail","Update Unsuccessful","error");
      }

    },'json');
  });

});
		


</script>



@endsection