@extends('layouts.app')
<title>Delivery Order History</title>
@section('content')
<style>
	.container{
		min-width:95%;
		margin-top: 10px;
	}
</style>

<div class="container">
	<div class="card">
		<div class="title">
			<h4 style="margin: 20px">Delivery Order History</h4>
		</div>
		<div class="card-body">
			<div style="float:right">
				<input type="text" id="search" placeholder="Search DO Number" class="form-control" style="margin-bottom: 15px"/>
			</div>
			<div class="table table-responsive">
				<table id="history" style="width:100%">
					<thead style="background: #b8b8efd1">
						<tr>
							<td>No</td>
							<td>DO Number</td>
							<td>From</td>
							<td>To</td>
							<td>Quantity Item</td>
              <td>Value</td>
							<td>Completed</td>
							<td>Date Issue</td>
							<td></td>
						</tr>
					</thead>
					<tbody>
						@foreach($do_list as $key => $result)
							<tr>
								<td>{{$key + 1}}</td>
								<td><a href="{{route('getDoHistoryDetail',$result->do_number)}}">{{$result->do_number}}</a></td>
								<td>{{$result->from}}</td>
								<td>{{$result->to}}</td>
								<td>{{$result->total_item}}</td>
                <td>Rm {{number_format($result->total_value,2)}}</td>
								<td>{{($result->completed == 0)? 'No' : 'Yes'}}</td>
								<td>{{$result->created_at}}</td>
								<td>
                  <buttton class="btn btn-primary" onclick="window.open('{{route('getPrintDo',$result->do_number)}}')">Print</buttton>
                  <buttton class="btn btn-danger delete" ref_id="{{$result->id}}">Delete</buttton>
                </td>
              </tr>
						@endforeach
					</tbody>
				</table>
				<div style="float:right">{{ $do_list->links() }}</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){

	$("#search").keypress(function(e){
		let header = "{{route('getDoHistory')}}";
		if(e.keyCode == 13){
			let target = $("#search").val();
			header = `${header}?search=${target}`;
			window.location.assign(header);
		}
	});

  $(".delete").click(function(){
    let id = $(this).attr('ref_id');
    swal.fire({
      title:'Delele DO',
      html:'Are you sure to delete this delivery order',
      icon:'warning',
      confirmButtonText:'Delete It',
      showCancelButton: true,
    }).then((result)=>{
      if(result.isConfirmed){       
        $.get('{{route('postDeleteDo')}}',
        {
          'id': id,
        },function(data){
          if(data){
            swal.fire('Successful','Delete Successful. You will be redirect in few second','success');
            setTimeout(()=>{window.location.reload()},'2000');
          }else{
            swal.fire('Error','Delete Unsuccessful, Please Contact IT Support','error');
          }
        },'json');
      }
    });
  });

});
</script>
@endsection