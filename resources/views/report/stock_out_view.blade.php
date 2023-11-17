<html>
<head>
<title>Stock Out Report</title>
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

  .detail tr td{
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

  thead > tr > td {
    font-weight: bold;
  }

  .detail > tbody > tr > td {
    padding: 5px;
  }

  th{
    border: 2px solid black;
    padding:5px;
  }

  tfoot > tr > td {
    border: 2px solid black !important;
    padding:5px;
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
    <h4 style="margin:20px 30%;border:1px solid black">Stock Out Report</h3>
  </div>

  <div class="second">
    <table>
      <tr>
        <td><b>From Branch</b></td>
        <td>:</td>
        <td align="right">{{ $response['from_branch'] }}</td>
      </tr>
      <tr>
        <td><b>To Branch</b></td>
        <td>:</td>
        <td align="right">{{ $response['to_branch'] }}</td>
      </tr>
      <tr>
        <td><b>Date From</b></td>
        <td>:</td>
        <td align="right">{{ date('d-M-Y',strtotime($response['from_date'])) }}</td>
      </tr>
      <tr>
        <td><b>Date To</b></td>
        <td>:</td>
        <td align="right">{{ date('d-M-Y',strtotime($response['to_date'])) }}</td>
      </tr>
      <tr>
        <td><b>Generate by</b></td>
        <td>:</td>
        <td align="right">{{ $response['user']->name }}</td>
      </tr>
    </table>
  </div>

  <div class="main" style="margin-top:25px;margin-bottom:50px;">
    <table class="detail" style="width:100%;border-collapse: collapse;">
        <thead>
          <th>No</th>
          <th>Barcode</th>
          <th>Product Name</th>
          <th style="text-align: right;">Cost</th>
          <th style="text-align: right;">Quantity</th>
          <th style="text-align: right;">Total Value</th>
        </thead>
        <tbody>
          @php $lumpsum = 0; @endphp
          @foreach($data as $record)
            @php $lumpsum += $record->product->cost * $record->total_quantity; @endphp
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $record->barcode }}</td>
              <td>{{ $record->product_name }}</td>
              <td align="right">{{ number_format($record->product->cost,2) }}</td>
              <td align="right">{{ number_format($record->total_quantity,2) }}</td>
              <td align="right">Rm {{ number_format($record->product->cost * $record->total_quantity,2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td align="center" colspan="4"><b>Total</b></td>
            <td align="right"><b>{{ number_format($data->sum('total_quantity'),2) }}</b></td>
            <td align="right"><b>Rm {{ number_format($lumpsum,2) }}</b></td>
          </tr>
        </tfoot>
    </table>
  </div>

</div>

<script>


</script>