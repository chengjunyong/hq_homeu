<html>
<head>
<title>Item Based Sales Report</title>
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
    border-left: 1px solid black;
    border-bottom: 1px solid black;
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
<link rel="stylesheet" href="{{ asset('bootstrap-4.0.0/css/bootstrap.min.css')}}"/>
<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WZVW9DB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<div style="margin: 0 30px;">

  <div class="header">
    <h4>HOME U(M) SDN BHD</h2>
    <h5>(125272-P)</h4>
    <h5>Tel: 09-7744243 &nbsp Email: assist@homeu.com.my</h4>
    <h4 style="margin:20px 30%;border:1px solid black">ITEM BASED SALES REPORT</h3>
  </div>

  <div class="first">
    <table style="width:100%">
      <tr>
        <td><b>Date From</b></td>
        <td>:</td>
        <td align="right">{{ $from }}</td>
      </tr>
      <tr>
        <td><b>Date To</b></td>
        <td>:</td>
        <td align="right">{{ $to }}</td>
      </tr>
      <tr>
        <td><b>Branches</b></td>
        <td>:</td>
        <td align="right">{{ $branches->implode('branch_name',', ') }}</td>
      </tr>
      <tr>
        <td><b>Generate by</b></td>
        <td>:</td>
        <td align="right">{{ auth()->user()->name }}</td>
      </tr>
    </table>
  </div>

  <div class="main">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead style="background: #adade0;">
        <th>No</th>
        <th>Barcode</th>
        <th>Product</th>
        <th>Sold Quantity</th>
        <th>Unit Price</th>
        <th>Total</th>
      </thead>
      <tbody class="border">
        @foreach($details as $result)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $result->barcode }}</td>
            <td>{{ $result->product_name }}</td>
            <td>{{ $result->total_quantity }}</td>
            <td>{{ number_format($result->price,2) }}</td>
            <td>{{ number_format($result->price * $result->total_quantity,2) }}</td>
          </tr>
        @endforeach
      </tbody>
      {{-- <tfoot>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>{{ $details->sum('total_quantity')}}</td>
          <td></td>
          <td>{{ $details->sum('total') }}</td>
        </tr>
      </tfoot> --}}
    </table>
  </div>

  <div class="mt-2 float-right">
    {{ $details->appends(request()->query())->links() }}
  </div>

</div>

<script>


</script>