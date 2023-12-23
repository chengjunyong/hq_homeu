<html>
<head>
<title>Daily Report</title>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WZVW9DB');</script>
<!-- End Google Tag Manager -->

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

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WZVW9DB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<div style="margin: 0 30px;">

  <div class="header">
    <h4>HOME U(M) SDN BHD</h4>
    <h4>(125272-P)</h4>
    <h4 style="margin:20px 30%;border:1px solid black;margin-top: 10px">Branch Cashier Report</h4>
    <h5 style="margin: 0 !important;">Report Date</h5>
    <h5>({{ date("d-M-Y", strtotime($selected_date)) }} to {{ date("d-M-Y", strtotime($selected_date2)) }})</h5>
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
        <th style="text-align: right">CASHIER NAME</th>
        <th style="text-align: right">CASH</th>
        <th style="text-align: right">CARD</th>
        <th style="text-align: right">T & GO</th>
        <th style="text-align: right">Maybank QRPay</th>
        <th style="text-align: right">Grab Pay</th>
        <th style="text-align: right">Cheque</th>
        <th style="text-align: right">Boost</th>
        <th style="text-align: right">E-banking</th>
        <!-- <th style="text-align: right">OTHER</th> -->
        <th style="text-align: right">PANDAMART</th>
        <th style="text-align: right">GRABMART</th>
        <th style="text-align: right">TOTAL</th>
      </thead>
      <tbody class="border" style="border-bottom-color:black !important">
        @foreach($cashier_list as $cashier)
          <tr>
            <td style="text-align: right;">{{ $cashier->cashier_name }}</td>
            @foreach($cashier->payment_type as $type)
              <td style="text-align: right;">{{ number_format($type->total, 2) }} <br/> <b>Transaction Qty: {{ $transaction->where('cashier_name',$cashier->cashier_name)->where('payment_type',$type->type)->count() }}</b></td>
            @endforeach
            <td style="text-align: right;">{{ number_format($cashier->total, 2) }} <br/> <b>Transaction Qty: {{ $transaction->where('cashier_name',$cashier->cashier_name)->count() }}</b></td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">Jumlah :</td>
          @foreach($total_payment_type as $total_payment)
            <td style="text-align: right;border:1px solid black; padding: 5px 5px;">{{ number_format($total_payment->total, 2) }}</td>
          @endforeach
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">{{ number_format($total, 2) }}</td>
        </tr>
      </tfoot>
    </table>

    <br><br>
    <h5 style="margin: 0 !important; text-align: center;">Lapuran Tunai Harian</h5>
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead style="background: #adade0;">
        <th style="text-align: right">CASHIER NAME</th>
        <th style="text-align: right">Modal Pagi</th>
        <th style="text-align: right">Tambah<br>Modal</th>
        <th style="text-align: right">Kutipan<br>Tunai</th>
        <th style="text-align: right">Modal utk<br>esok</th>
        <th style="text-align: right">Expenses /<br>Purchases</th>
        <th style="text-align: right">Refund</th>
        <th style="text-align: right">Baki Tunai Syarikat</th>
      </thead>
      <tbody class="border" style="border-bottom-color:black !important">
        @foreach($cashier_list as $cashier)
          <tr>
            <td style="text-align: right;">{{ $cashier->cashier_name }}</td>
            <td style="text-align: right;">{{ number_format($cashier->opening, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->float_in, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->cash, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->opening, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->float_out, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->refund, 2) }}</td>
            <td style="text-align: right;">{{ number_format($cashier->remain, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td style="text-align: right;border:1px solid black; padding: 5px 5px;">Jumlah :</td>
          @foreach($cashier_total as $cashier_total_detail)
            <td style="text-align: right;border:1px solid black; padding: 5px 5px;">{{ number_format($cashier_total_detail, 2) }}</td>
          @endforeach
        </tr>
      </tfoot>
    </table>
  </div>

</div>

<script>
</script>