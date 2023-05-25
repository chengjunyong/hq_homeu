<html>
<head>
<title>{{ $do->do_nunmber }}</title>
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

  td{
    padding:5px;
  }

  table, th, td {
    border: 1px solid;
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
    <h5>S/36 LOT 1745, CABANG TIGA</h5>
    <h5>PENGKALAN CHEPA, 16100</h5>
    <h5>KOTA BHARU, KELANTAN (AB15809)</h5>
    <h5>(125272-P)</h5>
  </div>

  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <label>From:</label>
        <input readonly="" class="form-control" type="text" name="from" value="HQ">
      </div>
      <div class="col-md-6">
        <label>To:</label>
        <input readonly="" class="form-control" type="text" name="to" value="Pengkalan Chepa">
      </div>
      <div class="col-md-6">
        <label>DO Number:</label>
        <input readonly="" class="form-control" type="text" name="do_number" value="WTS008662">
      </div>
      <div class="col-md-6">
        <label>Total Items:</label>
        <input readonly="" class="form-control" type="text" name="total_item" value="24.000">
      </div>
      <div class="col-md-6">
        <label>Date Issue:</label>
        <input readonly="" class="form-control" type="text" name="created_at" value="2023-05-22 19:22:04">
      </div>
      <div class="col-md-6">
        <label>Description:</label>
        <input readonly="" class="form-control" type="text" name="description" value="">
      </div>
    </div>
    <div style="margin-top:25px">
      <table style="width:100%;">
        <thead style="background-color: #b8b8efd1">
          <tr>
            <td>No</td>
            <td style="width:5%">Barcode</td>
            <td>Product Name</td>
            <td>Quantity</td>
            <td align="center" style="width:10%">Restock Quantity</td>
            <td align="center" style="width:10%">Stock Lost Quantity</td>
            <td style="width:10%">Stock Lost Reason</td>
            <td>Remark</td>
          </tr>
        </thead>
        <tbody>
          @foreach($do->details as $index => $result)
            <tr>
              <td>{{ $index +1 }}</td>
              <td style="width:5%">{{ $result->barcode }}</td>
              <td>{{ $result->product_name }}</td>
              <td>{{ $result->quantity }}</td>
              <td align="center" style="width:10%">{{ $result->quantity }}</td>
              <td align="center" style="width:10%">{{ $result->stock_lost_quantity }}</td>
              <td style="width:10%">{{ $result->stock_lost_reason }}</td>
              <td>{{ $result->remark }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>