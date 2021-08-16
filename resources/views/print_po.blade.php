<html>
<head>
<title>Purchase Order</title>
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
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WZVW9DB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="container">

	<div class="header">
    <h2>HOME U(M) SDN BHD</h2>
    <h4>S/36 LOT 1745, CABANG TIGA</h4>
    <h4>PENGKALAN CHEPA, 16100</h4>
    <h4>KOTA BHARU, KELANTAN (AB15809)</h4>
    <h4>(125272-P)</h4>
		<h4>purchase@homeumsd.com</h4>
		<h2 style="margin:20px 30%;">Purchase Order</h2>
	</div>

	<div class="main" style="padding-bottom: 2.5rem;margin-top: 15px;">
		<div class="second">
			<table style="float:left;font-size:19px;margin-bottom: 20px;font-size:16px;">
				<tr>
					<td>{{ucwords($supplier->supplier_name)}}</td>
				</tr>
				<tr>
					<td>{{ ($supplier->address1 != "null") ? ucwords($supplier->address1) : ''}}</td>
				</tr>
				<tr>
					<td>{{ ($supplier->address2 != "null") ? ucwords($supplier->address2) : ''}}</td>
				</tr>
        <tr>
          <td>{{ ($supplier->address3 != "null") ? ucwords($supplier->address3) : ''}}</td>
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
        <th style="width:5%" align="left">Bil</th>
				<th style="width:15%" align="left">BARCODE</th>
				<th>ITEMS</th>
				<th text-align: center">QTY</th>
				<th style="width:13%;text-align: center">UNIT PRICE</th>
			</thead>
			<tbody class="border">
				@foreach($po_detail as $key => $result)
					<tr>
            <td>{{$key +1}}</td>
						<td>{{$result->barcode}}</td>
						<td>{{$result->product_name}}</td>
						<td style="text-align: center">
              @if($result->measurement == 'kilogram')
                {{$result->quantity}} (Kg)
              @elseif($result->measurement == 'meter')
                {{number_format($result->quantity,3)}} (M)
              @else
                {{number_format($result->quantity,0)}}
              @endif
            </td>
						<td style="text-align: right">{{ number_format($result->cost,2) }}</td>
					</tr>
				@endforeach
				<tr>
					<td rowspan="5" colspan="3" style="vertical-align: top">Comment</td>
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
<!-- <script>
  window.print();
  window.addEventListener('afterprint', (event) => {
    window.close();
  });
</script> -->
</body>
</html>