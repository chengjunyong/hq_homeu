<html>
<head>
<title>Print Deliver Order</title>
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-DEVX15R54N"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-DEVX15R54N');
  </script>
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
		border-right: 1px solid black;
		padding: 5px 5px;
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

	<div class="header">
		<h2>HOME U(M) SDN BHD</h2>
		<h4>(125272-P)</h4>
		<h3 style="margin:20px 30%;border:1px solid black">WAREHOUSE TRANSFER</h3>
	</div>

	<div class="second">
		<table style="float:left">
			<tr>
				<td><b>From</b></td>
				<td>:</td>
				<td>{{$do_list->from}}</td>
			</tr>
			<tr>
				<td><b>To</b></td>
				<td>:</td>
				<td>{{$do_list->to}}</td>
			</tr>
			<tr>
				<td><b>Description</b></td>
				<td>:</td>
				<td>{{$do_list->description}}</td>
			</tr>
		</table>
		<table style="float:right">
			<tr>
				<td><b>Date</b></td>
				<td>:</td>
				<td align="right">20/01/2021</td>
			</tr>
			<tr>
				<td><b>DO Number</b></td>
				<td>:</td>
				<td align="right">{{$do_list->do_number}}</td>
			</tr>
			<tr>
				<td><b>Reference</b></td>
				<td>:</td>
				<td align="right">.......</td>
			</tr>
		</table>
	</div>
	<br/><br/><br/>

	<div class="main" style="padding-bottom: 2.5rem;">
		<table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
			<thead style="background: #adade0;">
				<th style="width:15%" align="left">BARCODE</th>
				<th>ITEMS</th>
				<th>QTY</th>
				<th>PRICE</th>
			</thead>
			<tbody class="border">
				@foreach($do_detail as $result)
					<tr>
						<td>{{ $result->barcode }}</td>
						<td>{{ $result->product_name }}</td>
						<td align="center">{{ number_format($result->quantity,0) }}</td>
						<td align="right">{{ number_format($result->price,2) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="footer">
		<table style="text-align: center;width:100%;font-weight: bold;">
			<tr>
				<td>
					<label>Issuer Signature</label><br/><br/><br/>
					<label>_____________________</label>
				</td>
				<td>
					<label>Driver Signature</label><br/><br/><br/>
					<label>______________________</label>
				</td>
				<td>
					<label>Branch Signature</label><br/><br/><br/>
					<label>______________________</label>
				</td>
			</tr>
		</table>
	</div>

</div>	
<script>
	window.print();
</script>
</body>
</html>