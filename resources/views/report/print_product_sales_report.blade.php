<html>
<head>
<title>Product Sales Report</title>
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
    <h4 style="margin:20px 30%;border:1px solid black">PRODUCT SALES REPORT</h3>
  </div>

  <div class="first">
    <table style="float:left">
      <tr>
        <td><b>Date From</b></td>
        <td>:</td>
        <td align="right">{{ $data['from'] }}</td>
      </tr>
      <tr>
        <td><b>Date To</b></td>
        <td>:</td>
        <td align="right">{{ $data['to'] }}</td>
      </tr>
      <tr>
        <td><b>Barcode</b></td>
        <td>:</td>
        <td align="right">{{ $data['product']->barcode }}</td>
      </tr>
    </table>
  </div>

  <div class="second">
    <table style="float:right">
      <tr>
        <td><b>Date From</b></td>
        <td>:</td>
        <td align="right">{{ date('d-M-Y',strtotime($data['from'])) }}</td>
      </tr>
      <tr>
        <td><b>Date To</b></td>
        <td>:</td>
        <td align="right">{{ date('d-M-Y',strtotime($data['to'])) }}</td>
      </tr>
      <tr>
        <td><b>Generate by</b></td>
        <td>:</td>
        <td align="right">{{ $data['user'] }}</td>
      </tr>
    </table>
  </div>

  <center>
    <label><strong>Product Name</strong><br/>{{$data['product']->product_name}}</label>
  </center>

  <div class="main" style="margin-bottom:30px">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead style="background: #adade0;">
        <th style="width:5%;text-align: center">Date</th>
        @foreach($data['branches'] as $branch)
          <th style="width:5%;text-align: center">{{$branch->branch_name}}</th>
        @endforeach
        <th style="width:7%;text-align: center">Total Quantity</th>
        <th style="width:7%;text-align: center">Total Sales</th>
      </thead>
      <tbody class="border">
        @for($a=0;$a<$daysDifference;$a++)
          <tr>
            <td align="center">{{ date('d-M-Y',strtotime($data['from']."+".$a." day")) }}</td>
            @foreach($data['branches'] as $branch)
              <td align="center">
                @php
                  $result = $details->where('branch_id',$branch->token)->where('tDate',date('Y-m-d',strtotime($data['from']."+".$a." day")));
                  if($result->count() > 0){
                    echo $result->sum('quantity');
                  }else{
                    echo 0;
                  }
                @endphp 
              </td>
            @endforeach
            <td align="center">{{ number_format($details->where('tDate',date('Y-m-d',strtotime($data['from']."+".$a." day")))->sum('quantity'),2) }}</td>
            <td align="right">Rm {{ number_format($details->where('tDate',date('Y-m-d',strtotime($data['from']."+".$a." day")))->sum('total'),2) }}</td>
          </tr>
        @endfor
      </tbody>
      <tfoot class="border" style="font-weight: bold">
        <tr>
          <td>Total</td>
          @foreach($data['branches'] as $branch)
            <td align="center">{{ $details->where('branch_id',$branch->token)->sum('quantity') }}</td>
          @endforeach
          <td align="center">{{ number_format($details->sum('quantity'),2) }}</td>
          <td align="right">Rm {{ number_format($details->sum('total'),2) }}</td>
        </tr>
      </tfoot>
    </table>
  </div>

</div>

<script>


</script>