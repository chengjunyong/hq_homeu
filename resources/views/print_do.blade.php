<html>
<head>
<title>Print Deliver Order</title>

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
				<td align="right">{{date("d-M-Y",strtotime($do_list->updated_at))}}</td>
			</tr>
			<tr>
				<td><b>DO Number</b></td>
				<td>:</td>
				<td align="right">{{$do_list->do_number}}</td>
			</tr>
			<tr>
				<td><b>Reference</b></td>
				<td>:</td>
				<td align="right"></td>
			</tr>
		</table>
	</div>
	<br/><br/><br/>

	<div class="main" style="padding-bottom: 2.5rem;">
		<table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
			<thead style="background: #adade0;">
        <th align="left" style="width:5%">NO</th>
				<th style="width:15%" align="left">BARCODE</th>
				<th>ITEMS</th>
        <th>UNIT<br/>PRICE</th>
				<th>QTY</th>
				<th>UNIT<br/>COST</th>
				<th>COST</th>
			</thead>
			<tbody class="border">
				@php
					$grant_total = 0;
				@endphp
				@foreach($do_detail as $index => $result)
					@php
						$grant_total += $result->quantity * $result->cost;
					@endphp
					<tr>
            <td>{{$index+1}}</td>
						<td>{{ $result->barcode }}</td>
						<td>{{ $result->product_name }}</td>
            <td align="right">{{ number_format($result->price,2) }}</td>
						<td align="center">
              @if($result->measurement == 'kilogram')
                {{$result->quantity}} (Kg)
              @elseif($result->measurement == 'meter')
                {{number_format($result->quantity,3)}} (M)
              @else
                {{number_format($result->quantity,0)}}
              @endif
            </td>
						<td align="right">{{ number_format($result->cost,4) }}</td>
						<td align="right">{{ number_format($result->quantity * $result->cost,2) }}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr style="border: 1px solid black;">
					<td colspan="4" style="text-align: center;font-weight:bold;border: 1px solid black;padding: 5px;">Total</td>
					<td style="text-align: center;font-weight:bold;border: 1px solid black;padding: 5px;">
						{{ number_format($do_detail->sum('quantity'),2) }}
					</td>
					<td style="text-align: right;font-weight:bold;border: 1px solid black;padding: 5px;">
						{{ number_format($grant_total,2) }}
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="footer">
		<table style="text-align: center;width:100%;font-weight: bold;">
			<tr>
				<td>
					<label>Issuer Signature</label><br/><br/><br/>
					<label>_____________________</label><br/>
          <label>(&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;)</label>
				</td>
				<td>
					<label>Driver Signature</label><br/><br/><br/>
					<label>______________________</label><br/>
          <label>(&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;)</label>
				</td>
				<td>
					<label>Branch Signature</label><br/><br/><br/>
					<label>______________________</label><br/>
          <label>(&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;)</label>
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