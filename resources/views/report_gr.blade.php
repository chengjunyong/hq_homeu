<html>
<head>
<title>Goods Return</title>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WZVW9DB');</script>
<!-- End Google Tag Manager -->
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
  
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WZVW9DB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<div class="container">

	<div class="header">
		<h2>HOME U(M) SDN BHD</h2>
		<h4>(125272-P)</h4>
		<h4>Tel: 09-7744243 <br/> Email: support@homeumsd.com</h4>
		<h3 style="margin:20px 30%;border:1px solid black">Goods Return</h3>
	</div>

	<div class="second">
		<table style="float:left">
			<tr>
				<td><b>Return To</b></td>
				<td>:</td>
				<td>{{$supplier->supplier_name}}</td>
			</tr>
			<tr>
				<td><b>Address</b></td>
				<td>:</td>
				<td>{{$supplier->address1}}</td>
			</tr>
			<tr>
				<td><b></b></td>
				<td></td>
				<td>{{$supplier->address2}}</td>
			</tr>
			<tr>
				<td><b></b></td>
				<td></td>
				<td>{{$supplier->address3}}</td>
			</tr>
		</table>
		<table style="float:right">
			<tr>
				<td><b>GR NO</b></td>
				<td>:</td>
				<td align="right">{{$gr[0]->gr_number}}</td>
			</tr>
			<tr>
				<td><b>Return Date</b></td>
				<td>:</td>
				<td align="right">{{date('d-M-Y',strtotime($gr[0]->created_at))}}</td>
			</tr>
			<tr>
				<td><b>Contact</b></td>
				<td>:</td>
				<td align="right">{{$supplier->contact}}</td>
			</tr>
			<tr>
				<td><b>E-mail</b></td>
				<td>:</td>
				<td align="right">{{($supplier->email != 'null') ? $supplier->email : 'None'}}</td>
			</tr>
		</table>
	</div>

	<div class="main" style="padding-bottom: 2.5rem;">
		<table class="detail" style="width:100%;margin-top:140px;border-collapse: collapse;">
			<thead style="background: #adade0;">
				<th style="width:10%">Do Number</th>
				<th>Barcode</th>
				<th>Product</th>
				<th>Quantity</th>
				<th>Unit Price</th>
				<th>Total</th>
			</thead>
			<tbody class="border">
				@foreach($gr as $result)
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