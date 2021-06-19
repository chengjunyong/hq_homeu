<html>
<head>
<title>Branch Check Stock History</title>

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
    <h2>HOME U(M) SDN BHD</h2>
    <h4>(125272-P)</h4>
    <h4>Tel: 09-7744243 &nbsp Email: assist@homeu.com.my</h4>
    <h3 style="margin:20px 30%;border:1px solid black">BRANCH STOCK HISTORY REPORT</h3>
  </div>

  <div class="second">
    <table style="float:right">
      <tr>
        <td><b>Date</b></td>
        <td>:</td>
        <td align="right">{{ $date }}</td>
      </tr>
      <tr>
        <td><b>Generate by</b></td>
        <td>:</td>
        <td align="right">{{ $user->name }}</td>
      </tr>
    </table>
  </div>
  <br/><br/><br/>

  <div class="main" style="padding-bottom: 2.5rem;">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead style="background: #adade0;">
        <th>Barcode</th>
        <th>Product name</th>
        <th>Updated Stock</th>
        <th>Stock ( Difference )</th>
        <th>Updated by</th>
        <th>Updated at</th>
      </thead>
      <tbody class="border">
        @foreach($branch_stock_history as $history)
          <tr>
            <td>{{ $history->barcode }}</td>
            <td>{{ $history->product_name }}</td>
            <td>{{ $history->new_stock_count }}</td>
            <td>{{ $history->difference_count }}</td>
            <td>{{ $history->user_name }}</td>
            <td>{{ $history->created_at }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>

<script>


</script>