<html>
<head>
<title>Stock Lost</title>
</head>
<style>
	h2,h4{
		margin: 0;
	}

	.header{
		text-align: center;
	}

	.detail,th{
		border: 1px solid black;
	}

	.border > tr > td{
		border: 1px solid black;
		padding: 5px 5px;
	}

	tfoot > tr > td{
		border:1px solid black;
		font-weight: bold;
	}

	*{
		color-adjust: exact;  
		-webkit-print-color-adjust: exact; 
		print-color-adjust: exact;
	}

	body{
		min-height: 98vh;
	}

	.footer{

	}

</style>
<body>
<div class="container">

	<div class="header" style="margin-bottom: 15px">
		<h2>HOME U(M) SDN BHD</h2>
		<h4>(125272-P)</h4>
		<h4>Tel: 09-7744243 <br/> Email: assist@homeu.com.my</h4>
		<h3 style="margin:20px 30%;border:1px solid black">Stock Lost</h3>
		<h4>Generate Date : {{date('d-M-Y',strtotime($sl[0]->created_at))}} <br/> Stock Lost ID : {{$sl[0]->stock_lost_id}}</h4>
	</div>

	<div class="main" style="padding-bottom: 2.5rem;">
		<table class="detail" style="width:100%;border-collapse: collapse;">
			<thead style="background: #adade0;">
				<th style="width:10%">Do Number</th>
				<th style="width:15%">Barcode</th>
				<th style="width:40%">Product</th>
				<th style="width:7.5%">Quantity</th>
				<th style="width:7.5%">Unit Price</th>
				<th style="width:20%">Total</th>
			</thead>
			<tbody class="border">
				@foreach($sl as $result)
					<tr>
						<td style="text-align: center">{{$result->do_number}}</td>
						<td>{{$result->barcode}}</td>
						<td>{{$result->product_name}}</td>
						<td  style="text-align: center">{{$result->lost_quantity}}</td>
						<td style="text-align: right">{{number_format($result->price_per_unit,2)}}</td>
						<td style="text-align: right">{{number_format($result->total,2)}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3" style="text-align: center">Total</td>
					<td style="text-align: center">{{$total->quantity}}</td>
					<td></td>
					<td style="text-align: right">Rm {{number_format($total->amount,2)}}</td>
				</tr>
			</tfoot>
		</table>
	</div>


</div>	
<script>
	window.print();
</script>
</body>
</html>