<html>
<head>
<title>Stock Balance Report</title>

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
    padding: 2px 2px;
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
    <h3 style="margin:20px 30%;border:1px solid black">Stock Balance Report</h3>
  </div>

  <div class="second">
    <table style="float:left">
      <tr>
        <td><b>Date</b></td>
        <td>:</td>
        <td align="right">{{ $date }}</td>
      </tr>
      <tr>
        <td><b>Branch</b></td>
        <td>:</td>
        <td align="right">
          @foreach($branch as $result)
            {{ $result->branch_name }},
          @endforeach
        </td>
      </tr>
    </table>
    <table style="float:right">
      <tr>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td><b>Balance Stock</b></td>
        <td>:</td>
        <td align="right">{{ number_format($balance_stock[0]->total,2) }}</td>
      </tr>
    </table>
  </div>
  <br/><br/><br/>

  <div class="main" style="padding-bottom: 2.5rem;">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead style="background: #adade0;">
        <th>Bil</th>
        <th>Department</th>
        <th>Ketageri</th>
        <th>Barcord</th>
        <th>Product Name</th>
        <th>Cost</th>
        <th>Qty</th>
        <th>Selling Price</th>
        <th>Total Cost</th>
      </thead>
      <tbody class="border">
        @foreach($stock as $key => $result)
          <tr>
            <td>{{$key+1}}</td>
            <td>{{$result->department_name}}</td>
            <td>{{$result->category_name}}</td>
            <td>{{$result->barcode}}</td>
            <td>{{$result->product_name}}</td>
            <td>{{ number_format($result->cost,2)}}</td>
            <td>{{$result->total_quantity}}</td>
            <td>{{ number_format($result->price,2)}}</td>
            <td>{{ number_format($result->cost * $result->total_quantity,2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>

<script>


</script>