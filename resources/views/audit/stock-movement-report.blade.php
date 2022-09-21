<html>
<head>
<title>Item Movement Report</title>

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
    padding: 5px;
  }

  .border > tr > td{
    border: 1px solid black;
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
    <h3 style="margin:20px 30%;border:1px solid black">Item Movement Report</h3>
  </div>

  <div class="item-section" style="text-align: center">
    <label><strong>Date:</strong><br/>{{$date_target}}</label><br/>
    <label><strong>Product:</strong><br/>{{$product->product_name}}<br/>({{$product->barcode}})</label>
  </div>

  <div class="main" style="padding-bottom: 2.5rem;">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead>
        <tr>
          <th style="width:5%;">No</th>
          <th style="width:17%;">Type</th>
          <th style="width:17%;">Transaction No</th>
          <th style="width:13%;">Price</th>
          <th style="width:13%;">Qty</th>
          <th style="width:15%;">Total</th>
          <th style="width:20%;">Transaction Date</th>
        </tr>
      </thead>
      <tbody class="border">
        @foreach($transaction_data as $key => $result)
          <tr>
            <td>{{$key+1}}</td>
            <td>
              @if(str_contains($result->transaction_no,"WTS"))
                Warehouse Transfer
              @else
                Sales
              @endif
            </td>
            <td>{{$result->transaction_no}}</td>
            <td>Rm {{number_format($result->price,2)}}</td>
            <td>
              @if(str_contains($result->transaction_no,"WTS"))
                +{{$result->quantity}}
              @else
                -{{$result->quantity}}
              @endif
            </td>
            <td>Rm {{ number_format($result->price * $result->quantity,2) }}</td>
            <td>{{ date("d-M-Y H:i:s A",strtotime($result->transaction_date))}}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>