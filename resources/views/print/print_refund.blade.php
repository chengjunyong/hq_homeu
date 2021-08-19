<html>
<head>
<title>Refund Report</title>
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
    <h4 style="margin:20px 30%;border:1px solid black">Refund Report ({{ date('d-M-Y',strtotime($target_date)) }})</h3>
  </div>

  <div class="first">
    <table style="float:left">
      <tr>
        <td><b>Branch</b></td>
        <td>:</td>
        <td align="right"> {{ $branch->branch_name }}</td>
      </tr>
      <tr>
        <td><b>Total Transaction Refund</b></td>
        <td>:</td>
        <td align="right">{{ count($refund) }}</td>
      </tr>
      <tr>
        <td><b>Total Refund Amount</b></td>
        <td>:</td>
        <td align="right">Rm {{ $refund->sum('total') }}</td>
      </tr>
    </table>
  </div>

  <div class="second">
    <table style="float:right">
      <tr>
        <td><b>Generate Date</b></td>
        <td>:</td>
        <td align="right">{{ date('d-M-Y',strtotime(now())) }}</td>
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
        <td align="center" style="width: 3%;">Refund No</td>
        <td align="center" style="width: 10%;">Counter Name</td>
        <td align="center" style="width: 10%;">Cashier Name</td>
        <td align="center" colspan="2" style="width: 10%;">Refund Date</td>
        <td align="center" style="width: 5%;">Total</td>
      </thead>
      <tbody>
        @foreach($refund as $index => $a)
          <tr>
            <td>{{$index +1 }}</td>
            <td>{{$a->transaction_no}}</td>
            <td align="center">{{$a->cashier_name}}</td>
            <td align="center">{{$a->created_by}}</td>
            <td colspan="2" align="center">{{date("Y-m-d h:i:s A",strtotime($a->refund_created_at))}}</td>
            <td></td>
          </tr>
          <tr>
            <td colspan=2 style="border:none"></td>
            <td><strong>Barcode</strong></td>
            <td><strong>Product</strong></td>
            <td align="right"><strong>Unit Price</strong></td>
            <td align="right"><strong>Quantity</strong></td>
            <td align="right"><strong>Sub Total</strong></td>
          </tr>
          @foreach($refund_detail as $index => $b)
            @if($a->branch_refund_id == $b->branch_refund_id)
              <tr>
                <td colspan="2" style="border:none"></td>
                <td>{{$b->barcode}}</td>
                <td>{{$b->product_name}}</td>
                <td align="right">{{number_format($b->price,2)}}</td>
                <td align="right">{{$b->quantity}}</td>
                <td align="right">{{number_format($b->total,2)}}</td>
              </tr>
            @endif
          @endforeach
          <tr>
            <td colspan="2" style="border:none"></td>
            <td colspan="4" align="center"><strong>Total</strong></td>
            <td align="right"><strong>Rm {{number_format($a->total,2)}}</strong></td>
          </tr>
          <tr>
            <td colspan="7" style="border:none;padding:20px;"></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>