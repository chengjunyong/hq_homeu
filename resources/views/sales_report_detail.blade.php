@extends('layouts.app')

@section('content')
<script src="{{ asset('datatable/datatables.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('datatable/datatables.min.css')}}"/>
<script src="{{ asset('js/md5.min.js') }}"></script>
<style>
  body{
    background: #f9fafb;
  }

  #sales_report{
    width:100%;
    border:1px solid black;
  }

  tr{
    border:1px solid black;
  }

  td{
    padding: 5px 0px 5px 0px;
  }

</style>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h4>Sales Report Detail</h4>
      <table id="sales_report">
        <thead style="background-color: #403c3c80;text-align: center">
          <tr>
            <th>Product name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody style="text-align: center">
          @foreach($transaction_detail as $result)
            <tr>
              <td>{{ $result->product_name }}</td>
              <td>{{ $result->quantity }}</td>
              <td>{{ $result->price }}</td>
              <td>{{ number_format($result->total, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="float:right;">
        {{$transaction_detail->links()}}
      </div>
    </div>
  </div>
</div>


<script>


</script>



@endsection