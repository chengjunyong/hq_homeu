<html>
<head>
<title>Stock Balance (Stock Kawalan) Report</title>

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
    <h3 style="margin:20px 30%;border:1px solid black">Stock Balance (Stock Kawalan) Report</h3>
  </div>

  <div class="main" style="padding-bottom: 2.5rem;">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead>
        <tr>
          <th rowspan="2">Bil</th>
          <th rowspan="2">Department</th>
          <th rowspan="2">Ketageri</th>
          <th rowspan="2">Barcord</th>
          <th rowspan="2">Product Name</th>
          <th colspan="12" style="text-align: center">Stock Balance</th>
        </tr>
        <tr>
          <td colspan="5"></td>
          <td style="border-right: 1px black solid;">Warehouse</td>
          @foreach($branches as $result)
            <td style="border-right: 1px black solid;">{{$result->branch_name}}</td>
          @endforeach
        </tr>
      </thead>
      <tbody class="border">
        @foreach($data as $key => $result)
          <tr>
            <td>{{$key +1}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>

<script>


</script>