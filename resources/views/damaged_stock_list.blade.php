@extends('layouts.app')
<title>Damaged Stock List</title>
@section('content')
<style>
	.container{
		min-width:95%;
		margin-top: 10px;
	}

	.table td{
		padding:5px !important;
	}
</style>

<div class="container">
	<div class="card">
		<div class="title">
			<h4 style="margin: 20px">Damaged Stock List</h4>
		</div>
		<div>
			<a href="{{route('getDamagedStockHistory')}}" style="float:right;margin-right: 20px">
        <button class="btn btn-primary">Good Returns History</button>
      </a>
      <select class="form-control" id="supplier_id" style="float:left;margin-left: 20px;width: 35%">
        @foreach($supplier as $result)
          <option value="{{$result->id}}">{{$result->supplier_name}}</option>
        @endforeach
      </select>
		</div>
		<div class="card-body">
			<div class="table table-responsive">
				<table id="history" style="width:100%">
					<thead style="background: #b8b8efd1">
						<tr>
							<td>No</td>
							<td>From Do Number</td>
							<td>Product Name</td>
							<td>Damaged Quantity</td>
							<td>Price Per Unit</td>
							<td>Total Price</td>
							<td>Remark</td>
						</tr>
					</thead>
					<tbody>
						@foreach($do_detail as $key => $result)
							<tr>
								<td>{{$key + 1}}</td>
								<td><a href="{{route('getDoHistoryDetail',$result->do_number)}}">{{$result->do_number}}</a></td>
								<td>{{$result->product_name}}</td>
								<td>{{$result->stock_lost_quantity}}</td>
								<td>Rm {{number_format($result->price,2)}}</td>
								<td>Rm {{number_format($result->price * $result->stock_lost_quantity,2)}}</td>
								<td>{{($result->remark != null) ? $result->remark : 'No Remark'}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="paginate" style="float:right;margin-top: 15px">
					{{ $do_detail->links() }}
				</div>
				<div style="margin-top: 3.5rem;text-align: center">
					<button id="gr" class="btn btn-primary">Generate Good Return</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){

	$("#gr").click(function(){
		Swal.fire({
	  	title: 'Generate Good Return Form',
	  	icon: 'info',
	  	text: 'After generate Good Return Form, those items will be clear in the list. Please make sure before proceed',
	  	showCancelButton: true,
	  	confirmButtomText: 'Confirm Generate'
		}).then((result) =>{
			if(result.isConfirmed){
				let token = '{{ csrf_token() }}';
				let send = 'true';
				if($("tbody").html().trim() == ""){
					send = 'false';
				}
				$.post('{{route('postDamagedStock')}}',
					{
						'_token':token,
						'result':send,
            'supplier_id' : $("#supplier_id").val(),
					},
					function(data){
						if(data['redirect'] != null){
							window.open(`${data['redirect']}`);
							window.location.reload();
						}
					},'json');
			}
		});
	});

});

</script>
@endsection