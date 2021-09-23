<html>
<head>
<title>Date Range Sales Report</title>
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
    <h4 style="margin:20px 30%;border:1px solid black">Date Range Sales Report</h3>
  </div>

  <div class="second">
    <table style="float:right">
      <tr>
        <td><b>Date From</b></td>
        <td>:</td>
        <td align="right">{{ date('d-M-Y',strtotime($from_date)) }}</td>
      </tr>
      <tr>
        <td><b>Date To</b></td>
        <td>:</td>
        <td align="right">{{ date('d-M-Y',strtotime($to_date)) }}</td>
      </tr>
      <tr>
        <td><b>Generate by</b></td>
        <td>:</td>
        <td align="right">{{ $user->name }}</td>
      </tr>
    </table>
  </div>
  <br/><br/><br/>
  <div class="main" style="">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;margin-bottom:20px;">
        <thead>
          <td align="center" style="width: 1%;">No</td>
          <td align="center" style="width: 3%;">Cashier</td>
          <td align="center" style="width: 10%;">Invoice No</td>
          <td align="center" style="width: 5%;">Type</td>
          <td align="center" style="width: 10%;">Round Off</td>
          <td align="center" style="width: 10%;">Total</td>
          <td align="center" style="width: 8%;">Date</td>
        </thead>
        <tbody>
          @foreach($transaction as $index => $result)
            <tr>
              <td align="center">{{$index +1}}</td>
              <td>{{$result->cashier_name}}</td>
              <td>{{$result->transaction_no}}</td>
              <td align="center">{{$result->payment_type_text}}</td>
              <td align="right">Rm {{ $result->round_off }}</td>
              <td align="right">Rm {{number_format($result->total,2)}}</td>
              <td align="center">{{ date('d-M-Y h:i:s a',strtotime($result->transaction_date))}}</td>
            </tr>
          @endforeach
          <tr>
            <td align="center" colspan="4" style="font-weight:bold;">Total</td>
            <td align="right" style="font-weight:bold;">Rm {{number_format($transaction->sum('round_off'),2)}}</td>
            <td align="right" style="font-weight:bold;">Rm {{number_format($transaction->sum('total'),2)}}</td>
          </tr>
        </tbody>
    </table>
  </div>

</div>

<script>


</script>