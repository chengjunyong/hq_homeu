<html>
<head>
<title>Sales Report</title>
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

  .footer{

  }

</style>
<link rel="stylesheet" href="{{ asset('bootstrap-4.0.0/css/bootstrap.min.css')}}"/>
<body>
<div style="margin: 0 30px;">

  <div class="header">
    <h2>HOME U(M) SDN BHD</h2>
    <h4>(125272-P)</h4>
    <h4>Tel: 09-7744243 &nbsp Email: assist@homeu.com.my</h4>
    <h3 style="margin:20px 30%;border:1px solid black">WAREHOUSE TRANSFER</h3>
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
        <th>INVOICE NO</th>
        <th>PAYMENT TYPE</th>
        <th>REFERENCE NO</th>
        <th>SUBTOTAL</th>
        <th>DISCOUNT</th>
        <th>TOTAL</th>
        <th>RECEIVED PAYMENT</th>
        <th>BALANCE</th>
        <th>TRANSACTION DATE</th>
        <th>DETAIL</th>
      </thead>
      <tbody class="border">
        @foreach($transaction as $result)
          <tr>
            <td>{{ $result->transaction_no }}</td>
            <td>{{ $result->payment_type_text }}</td>
            <td>{{ $result->invoice_no }}</td>
            <td>{{ number_format($result->subtotal, 2) }}</td>
            <td>{{ number_format($result->total_discount, 2) }}</td>
            <td>{{ number_format($result->total, 2) }}</td>
            <td>{{ number_format($result->payment, 2) }}</td>
            <td>{{ number_format($result->balance, 2) }}</td>
            <td data-order="{{ $result->transaction_date }}">{{ date('d M Y g:i:s A', strtotime($result->transaction_date)) }}</td>
            <td>
              <a href="{{ route('getSalesReportDetail', ['branch_id' => $result->branch_id, 'id' => $result->branch_transaction_id ]) }}">Detail</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div style="margin-top: 20px;">
      {{ $transaction->links() }}
    </div>
  </div>

</div>  
<script>
  // window.print();
</script>
</body>
</html>