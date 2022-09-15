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
          <th rowspan="2">Category</th>
          <th rowspan="2">Barcode</th>
          <th rowspan="2">Product Name</th>
          <th colspan="12" style="text-align: center">Stock Balance</th>
        </tr>
        <tr>
          @foreach($branches as $result)
            <td style="border-right: 1px black solid;text-align:center">{{$result->branch_name}}</td>
          @endforeach
          @if($warehouse != null)
            <td style="border-right: 1px black solid;text-align:center">Warehouse</td>
          @endif
        </tr>
      </thead>
      <tbody class="border">
        @foreach($data as $key => $result)
          <tr>
            <td>{{$key +1}}</td>
            <td>{{$result['category']}}</td>
            <td>{{$result['barcode']}}</td>
            <td>{{$result['product_name']}}</td>
            @foreach($result['branch_qty'] as $branch_qty)
              <td style="text-align:right">{{$branch_qty}}</td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>

<script>


</script>