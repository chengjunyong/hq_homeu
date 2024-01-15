<html>
<head>
<title>Goods Return</title>
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
    padding: 3px 5px;
  }

  *{
    color-adjust: exact;  
    -webkit-print-color-adjust: exact; 
    print-color-adjust: exact;
  }

  .border {
    border:1px solid black;
  }

</style>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WZVW9DB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

  
<div class="container">

  <div class="header">
    <h2>HOME U(M) SDN BHD</h2>
    <h4>S/36 LOT 1745, CABANG TIGA</h4>
    <h4>PENGKALAN CHEPA, 16100</h4>
    <h4>KOTA BHARU, KELANTAN (AB15809)</h4>
    <h4>(125272-P)</h4>
    <h2 style="margin:20px 30%;border:1px solid black">GOODS RETURN</h2>
  </div>

  <div>
    <table style="width:100%;font-size:19px;margin-bottom: 20px;font-size:16px;">
      <tr>
        <td>
          <strong>{{ucwords($supplier->supplier_name)}}</strong>
          {{ ($supplier->address1 != "null") ? ucwords($supplier->address1)."<br/>" : ''}}
          {{ ($supplier->address1 != "null") ? ucwords($supplier->address2)."<br/>" : ''}}
          {{ ($supplier->address1 != "null") ? ucwords($supplier->address3)."<br/>" : ''}}
        </td>
        <td align="right">
          GR NO: {{$gr->gr_no}}<br/>
          Issue Date: {{$gr->gr_date}}
        </td>
      </tr>
    </table>
  </div>


  <table class="detail" style="width:100%;border-collapse: collapse;">
    <thead style="background: #adade0;">
      <th style="width:5%" align="left">Bil</th>
      <th style="width:15%" align="left">BARCODE</th>
      <th>ITEMS</th>
      <th style="width:13%;text-align: center">QTY</th>
      <th style="width:13%;text-align: right">COST</th>
      <th style="width:13%;text-align: right">TOTAL</th>
    </thead>
    <tbody class="border">
      @foreach($gr_detail as $key => $result)
        <tr>
          <td>{{$key +1}}</td>
          <td>{{$result->barcode}}</td>
          <td>{{$result->product_name}}</td>
          <td style="text-align: center">
            @if($result->measurement == 'unit')
              {{number_format($result->quantity,0)}}
            @elseif($result->measurement == 'kilogram')
              {{number_format($result->quantity,3)}} (kg)
            @elseif($result->measurement == 'meter')
              {{number_format($result->quantity,3)}} (m)
            @endif
          </td>
          <td style="text-align: right">{{ number_format($result->cost,3) }}</td>
          <td style="text-align: right">{{ number_format($result->total_cost,2) }}</td>
        </tr>
      @endforeach

      {{-- @for($a=0;$a<13;$a++)
        <tr>
          <td>1</td>
          <td>1</td>
          <td>asdas das das das dsa dasdasdas das dasdasd as dsa dasd asd asdasdsa dsa asd as das das asd</td>
          <td style="text-align: center">
            1
          </td>
          <td style="text-align: right">1</td>
          <td style="text-align: right">1</td>
        </tr>
      @endfor --}}
      <tr>
        <td colspan="4"><strong>Comment: </strong></td>
        <td align="center"><strong>Total</strong></td>
        <td style="text-align: right"><strong>{{$gr->total_cost}}</strong></td>
      </tr>
    </tbody>
  </table>

  <div style="margin-top:15px">
    <table class="footer" style="text-align: center;width:100%;font-weight: bold;">
      <tr>
        <td>
          <label>Issuer Signature</label><br/><br/><br/>
          <label>_____________________</label><br/>
          <label>(&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;)</label>

        </td>
        <td>
          <label>Authorizer Signature</label><br/><br/><br/>
          <label>______________________</label><br/>
          <label>(&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;)</label>
        </td>
      </tr>
    </table>
  </div>
</div>  

<script>
  window.print();
</script>
</body>
</html>