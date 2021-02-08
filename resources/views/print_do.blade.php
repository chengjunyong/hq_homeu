<html>
<head>
<title>Print Deliver Order</title>
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

</style>
<body>
<div class="header">
	<h2>Company Name</h2>
	<h4>Co No .........</h4>
	<h4>Address Line 1</h4>	
	<h4>Address Line 2</h4>
	<h4>Address Line 3</h4>
	<h4>Tel: 09-xxxxxxx &nbsp Fax: 09-5737591</h4>
	<h3 style="margin:20px 30%;border:1px solid black">WAREHOUSE TRANSFER</h3>
</div>
<div class="second">
	<table style="float:left">
		<tr>
			<td><b>From</b></td>
			<td>:</td>
			<td>HQ Warehouse</td>
		</tr>
		<tr>
			<td><b>To</b></td>
			<td>:</td>
			<td>Branch Testing 2</td>
		</tr>
		<tr>
			<td><b>Description</b></td>
			<td>:</td>
			<td>Some Comment Here</td>
		</tr>
	</table>
	<table style="float:right">
		<tr>
			<td><b>Date</b></td>
			<td>:</td>
			<td>20/01/2021</td>
		</tr>
		<tr>
			<td><b>DO Number</b></td>
			<td>:</td>
			<td>......</td>
		</tr>
		<tr>
			<td><b>Reference</b></td>
			<td>:</td>
			<td>.......</td>
		</tr>
	</table>
</div>
<br/><br/><br/>

<div class="main">
	<table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
		<thead style="background: #adade0;">
			<th>QTY</th>
			<th style="width:15%">BARCODE</th>
			<th>ITEMS</th>
			<th>PRICE</th>
		</thead>
		<tbody class="border">
			@foreach($do_detail as $result)
				<tr>
					<td align="center">{{ number_format($result->quantity,0) }}</td>
					<td>{{ $result->barcode }}</td>
					<td>{{ $result->product_name }}</td>
					<td align="right">{{ number_format($result->price,2) }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>

</body>
</html>