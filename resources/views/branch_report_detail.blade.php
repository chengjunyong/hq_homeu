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

<!--   <div class="second">
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
  </div> -->


  <div class="main" style="padding-bottom: 2.5rem;">
    <table class="detail" style="width:100%;margin-top:30px;border-collapse: collapse;">
      <thead style="background: #adade0;">
        <th>BRANCH NAME</th>
        <th>TOTAL(RM)</th>
      </thead>
      <tbody class="border" style="border-bottom-color: black !important">
        @foreach($selected_branch as $branch)
          <tr>
            <td>{{ $branch->branch_name }}</td>
            <td style="text-align: right;">{{ number_format($branch->branch_total, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>

<script>
</script>