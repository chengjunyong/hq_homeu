<html>
<head>
<title>Purchase Order</title>
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
		padding: 3px 5px;
	}

	*{
		color-adjust: exact;  
		-webkit-print-color-adjust: exact; 
		print-color-adjust: exact;
	}

	body{
		min-height: 98vh;
	}

	.border {
		border:1px solid black;
	}

	table {
    page-break-inside: auto;
  }
  tr {
    page-break-inside: avoid;
    page-break-after: auto;
  }
  thead {
    display: table-header-group;
  }
  tfoot {
    display: table-footer-group;
  }

</style>
<body>
<div class="container">

	<div class="header">
		<h2>HOME U(M) SDN BHD</h2>
		<h4>(125272-P)</h4>
		<h4>09-7744243</h4>
		<h4>purchase@homeumsd.com</h4>
		<h2 style="margin:20px 30%;">Purchase Order</h2>
	</div>

	<div class="main" style="padding-bottom: 2.5rem;margin-top: 15px;">
		<div class="second">
			<table style="float:left;font-size:19px;margin-bottom: 20px">
				<tr>
					<td>Home(U) Sdh Bhd</td>
				</tr>
				<tr>
					<td>Warehouse Address</td>
				</tr>
				<tr>
					<td>City, State, Postcode</td>
				</tr>
			</table>

			<table style="float:right;font-size:19px">
				<tr>
					<td>PO Number</td>
					<td>:</td>
					<td align="right">{{$po->po_number}}</td>
				</tr>
				<tr>
					<td>Issue Date</td>
					<td>:</td>
					<td align="right">{{$po->issue_date}}</td>
				</tr>
				<tr>
					<td></td>
				</tr>
			</table>
		</div>

		<table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
			<thead style="background: #adade0;">
				<th style="width:15%" align="left">BARCODE</th>
				<th>ITEMS</th>
				<th style="width:5%;text-align: right">QTY</th>
				<th style="width:13%;text-align: right">UNIT PRICE</th>
			</thead>
			<tbody class="border">
				@foreach($po_detail as $result)
					<tr>
						<td>{{$result->barcode}}</td>
						<td>{{$result->product_name}}</td>
						<td style="text-align: right">{{$result->quantity}}</td>
						<td style="text-align: right">{{$result->cost}}</td>
					</tr>
				@endforeach
				<tr>
					<td rowspan="5" colspan="2" style="vertical-align: top">Comment</td>
					<td>Sub Total</td>
					<td style="text-align: right">{{number_format($total,2)}}</td>
				</tr>
				<tr>
					<td>Tax</td>
					<td style="text-align: right">0</td>
				</tr>
				<tr>
					<td>Shpping</td>
					<td style="text-align: right">0</td>
				</tr>
				<tr>
					<td>Total</td>
					<td style="text-align: right">{{number_format($total,2)}}</td>
				</tr>
			</tbody>
		</table>
	</div>

</div>	
<script>
	window.print();
</script>
</body>
</html>