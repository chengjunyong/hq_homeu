<html>
<head>
<title>Product Sales Report</title>
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
        <td align="right">{{ $from_date }}</td>
      </tr>
      <tr>
        <td><b>Date To</b></td>
        <td>:</td>
        <td align="right">{{ $to_date }}</td>
      </tr>
      <tr>
        <td><b>Barcode</b></td>
        <td>:</td>
        <td align="right">{{ $product_detail->barcode }}</td>
      </tr>
    </table>
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
        <td align="right">{{ date('d-M-Y',strtotime($to_date."-1 day")) }}</td>
      </tr>
      <tr>
        <td><b>Generate by</b></td>
        <td>:</td>
        <td align="right">{{ $user->name }}</td>
      </tr>
    </table>
  </div>

  <center>
    <label><strong>Product Name</strong><br/>{{$product_detail->product_name}}</label>
  </center>

  <div class="main" style="">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead style="background: #adade0;">
        <th style="width:5%;text-align: center">Date</th>
        @foreach($branch as $result)
          <th style="width:5%;text-align: center">{{$result->branch_name}}</th>
        @endforeach
        <th style="width:7%;text-align: center">Total Quantity</th>
        <th style="width:7%;text-align: center">Total Sales</th>
      </thead>
      <tbody class="border">
        @for($a=0;$a<$diff_date;$a++)
          <tr>
            <td align="center">{{ date('d-M-Y',strtotime($from_date."+".$a." day")) }}</td>
            @for($b=0;$b<count($data);$b++)
              <td align="center">{{ ($data[$b][$a]->quantity) ? $data[$b][$a]->quantity : "0"}}</td>
            @endfor
            <td align="center">{{$total_quantity_day[$a]}}</td>
            <td align="right">Rm {{number_format($total_sales_day[$a],2) }}</td>
          </tr>
        @endfor
          <tr>
            <td align="center"><strong>Total</strong></td>
            @for($b=0;$b<count($data);$b++)
              <td align="center"><strong>{{ ($branch_total_quantity[$b]) ? $branch_total_quantity[$b] : "0"}}</strong></td>
            @endfor
            <td align="center"><strong>{{$total_quantity}}</strong></td>
            <td align="right"><strong>Rm {{number_format($total_sales,2)}}</strong></td>
          </tr>
      </tbody>
    </table>
  </div>

</div>

<script>


</script>