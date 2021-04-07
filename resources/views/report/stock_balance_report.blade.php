<html>
<head>
<title>Stock Balance Report</title>
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
        <td align="right">{{ $branch->branch_name }}</td>
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
            <td>{{$result->quantity}}</td>
            <td>{{ number_format($result->price,2)}}</td>
            <td>{{ number_format($result->cost * $result->quantity,2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>

<script>


</script>