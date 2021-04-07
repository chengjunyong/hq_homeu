<html>
<head>
<title>Daily Report</title>
</head>
<style>
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

  h4{
    margin: 0px;
  }

  .footer{

  }

</style>
<link rel="stylesheet" href="{{ asset('bootstrap-4.0.0/css/bootstrap.min.css')}}"/>
<body>
<div style="margin: 0 30px;">

  <div class="header">
    <h4>HOME U(M) SDN BHD</h4>
    <h4>(125272-P)</h4>
    <h4 style="margin:20px 30%;border:1px solid black;margin-top: 10px">Branch Sales Report</h4>
    <h5 style="margin: 0 !important;">Report Date</h5>
    <h5>({{ date("d-M-Y", strtotime($_GET['report_date_from'])) }} - {{ date("d-M-Y", strtotime($_GET['report_date_to'])) }})</h5>
  </div>

  <div class="second">
    <table style="float:left; margin-bottom: 20px;">
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
      <tr>
        <td><b>Branch : </b></td>
        <td>:</td>
        <td align="right">{{ $branch->branch_name }}</td>
      </tr>
    </table>
  </div>


  <div class="main" style="padding-bottom: 2.5rem;">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead style="background: #adade0;">
        <th style="text-align: right">BRANCH NAME</th>
        <th style="text-align: right">CASH</th>
        <th style="text-align: right">CREDIT CARD</th>
        <th style="text-align: right">T & GO</th>
        <th style="text-align: right">OTHER</th>
        <th style="text-align: right">CREDIT SALES</th>
        <th style="text-align: right">TOTAL</th>
      </thead>
      <tbody class="border" style="border-bottom-color:black !important">
        @foreach($cashier_transaction as $cashier)
          <tr>
            <td style="text-align: right;">{{ $cashier->cashier_name }}</td>
            <td style="text-align: right;">{{ number_format($cashier->cash, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->credit_card, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->tng, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->other, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->credit_sales, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->total, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">Jumlah :</td>
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">{{ number_format($total_summary->cash, 2) }}</td>
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">{{ number_format($total_summary->credit_card, 2) }}</td>
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">{{ number_format($total_summary->tng, 2) }}</td>
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">{{ number_format($total_summary->other, 2) }}</td>
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">{{ number_format($total_summary->credit_sales, 2) }}</td>
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">{{ number_format($total_summary->total, 2) }}</td>
        </tr>
      </tfoot>
    </table>
  </div>

</div>

<script>
</script>