@extends('layouts.app')
<title>Goods Return</title>
@section('content')
<style>
  .title {
    font-size: 20px;
  }
  .row{
    margin-bottom: 13px;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 33px;
  }

  .select2-container .select2-selection--single{
    height: 36px;
  }

  .select2-container--default .select2-selection--single{
    border: 1px solid #ced4da;
  }

  .scroll::-webkit-scrollbar{
    width: 25px;
  }

  .scroll::-webkit-scrollbar-track {
    background-color: #ddd1d1;
    border-radius: 20px;
  }

  .scroll::-webkit-scrollbar-thumb {
    background-color: #46575d;
    border-radius: 20px;
    border: 6px solid transparent;
    background-clip: content-box;
  }

</style>

<div class="container" style="padding-bottom: 5vh;max-width: 1300px;">
  <div class="card" style="margin-top: 10px">

    <div class="card-title" style="margin:5px;">
      <h3>Goods Return</h3>
    </div>

    <div class="card-body">
      <form action="{{route('postGoodReturn')}}" method="post">
        @csrf
        <div class="row">
          <div class="col-md-4">
            <label class="title">Good Return No :</label>
          </div>
          <div class="col-md-8">
            <input type="text" name="gr_no" class="form-control" readonly value="{{$gr_no}}" />
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label class="title">Date :</label>
          </div>
          <div class="col-md-8">
            <input type="date" name="gr_date" class="form-control" value="{{date('Y-m-d',strtotime(now()))}}"/>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label class="title">Reference No :</label>
          </div>
          <div class="col-md-8">
            <input type="text" name="ref_no" class="form-control" placeholder="CN, Invoice, Do, PO" />
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label class="title">Supplier :</label>
          </div>
          <div class="col-md-8">
            <select name="supplier_id" class="form-control" id="supplier_id">
              @foreach($supplier as $result)
                <option value="{{$result->id}}">{{$result->supplier_name}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <input type="text" name="searched_value" class="form-control" style="width: 25%;float:right;margin-left:5px;" placeholder="Fill In Barcode" />
            <button id="check_barcode" type="button" class="btn btn-secondary" style="float:right">Check Barcode</button>
          </div>
        </div>

        <div class="row scroll" style="overflow-y: scroll;height: 55vh;">
          <div class="col-md-12">
            <table class="table table-bordered">
              <thead>
                <th>Barcode</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Cost</th>
                <th>Sub Total</th>
                <th></th>
              </thead>
              <tbody id="purchase_list">
              @if(count($tmp) == 0)
                <tr id="no_data">
                  <td colspan=4 align="center">No data</td>
                </tr>
              @endif
              @foreach($tmp as $result)
                <tr class="data" id="{{$result->barcode}}">
                  <td>{{$result->barcode}}</td>
                  <td>{{$result->product_name}}</td>
                  <td>{{$result->quantity}}</td>
                  <td>{{number_format($result->cost,3)}}</td>
                  <td val="{{$result->total}}">{{number_format($result->total,2)}}</td>
                  <td align="center" style="width:25%">
                    <button type="button" class="btn btn-secondary edit" val="{{$result->barcode}}" style="margin-right: 20px;">Edit</button>
                    <button type="button" class="btn btn-success delete" val="{{$result->id}}">Delete</button>
                  </td>
                </tr>
              @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td colspan=5 style="border:2px solid gray">Total Product :</td>
                  <td align="right" style="border:2px solid gray" id="total_product">{{$total->product}}</td>
                </tr>
                <tr>
                  <td colspan=5 style="border:2px solid gray">Total Quantity :</td>
                  <td align="right" style="border:2px solid gray" id="total_quantity">{{$total->quantity}}</td>
                </tr>
                <tr>
                  <td colspan=5 style="border:2px solid gray">Total Amount :</td>
                  <td align="right" style="border:2px solid gray">Rm <label id="total_amount" val="{{$total->amount}}" style="margin-bottom: 0px;">{{number_format($total->amount,2)}}</label></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div class="row" style="margin-top: 20px;">
          <div class="col-md-12" style="text-align: center;">
            <input class="btn btn-primary" value="Submit" type="submit"/>
          </div>
        </div>

      </div>
    </form>

  </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="add_product" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Fill In Purchase Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-3">
            Barcode
          </div>
          <div class="col-9">
            <input type="text" readonly id="modal_barcode" class="form-control" />
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            Product Name
          </div>
          <div class="col-9">
            <input type="text" readonly id="modal_product_name" class="form-control" />
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            Measurement
          </div>
          <div class="col-9">
            <input type="text" readonly id="modal_measurement" class="form-control" />
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            Quantity
          </div>
          <div class="col-9">
            <input type="number" min=1 id="modal_quantity" class="form-control" />
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            Cost
          </div>  
          <div class="col-9">
            <input type="number" min=0.01 step=0.001 id="modal_cost" class="form-control" />
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            Total
          </div>
          <div class="col-9">
            <input type="number" step=0.01 id="modal_total" class="form-control" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="modal_submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  $('#supplier_id').select2();

  $("input[name=searched_value]").keydown(function(e){
    if(e.keyCode == 13){
      e.preventDefault();
      $("#check_barcode").click();
    }
  });

  $("#check_barcode").click(function(data){
    $.get('{{route('ajaxSearchBar')}}', 
    {
      'barcode':$("input[name=searched_value]").val(),

    },function(data){
      if(data != false){
        $("#add_product").modal('show');
        $("#modal_barcode").val(data['barcode']);
        $("#modal_product_name").val(data['product_name']);
        $("#modal_measurement").val(data['measurement']);
        $("#modal_quantity").val('');
        $("#modal_cost").val(parseFloat(data['cost']).toFixed(3));
        $("#modal_total").val(''); 
      }else{
        Swal.fire('Error','Barcode Not Found','error');
      }
      $("input[name=searched_value]").val('');

    },'json');
  });

  $("#modal_submit").click(function(data){
    $("#modal_submit").prop('disabled',true);
    let qty = $("#modal_quantity").val();
    let decimals = (qty!=Math.floor(qty))?(qty.toString()).split('.')[1].length:0;
    if($("#modal_cost").val() == "" || $("#modal_cost").val() < 0){
      Swal.fire('Error','Value Cost Invalid','error');
      $("#modal_submit").prop('disabled',false);
    }else if($("#modal_quantity").val() == "" || $("#modal_quantity").val() <= 0){
      Swal.fire('Error','Value Quantity Invalid','error');
      $("#modal_submit").prop('disabled',false);
    }else if($("#modal_measurement").val() == 'unit' && $("#modal_quantity").val() % 1 != 0){
      Swal.fire('Error','Quantity Value Cannot Be Decimal','error');
      $("#modal_submit").prop('disabled',false);
    }else if($("#modal_measurement").val() != 'unit' && decimals > 3){
      Swal.fire('Error','Quantity Value Cannot Exceed 3 Decimal places','error');
      $("#modal_submit").prop('disabled',false);
    }else{
      $.get('{{route('ajaxAddGoodReturnItem')}}',
      {
        'barcode':$("#modal_barcode").val(),
        'product_name':$("#modal_product_name").val(),
        'measurement': $("#modal_measurement").val(),
        'quantity': $("#modal_quantity").val(),
        'cost': $("#modal_cost").val(),
        'total': $("#modal_total").val(),
      },function(data){
        if(data != false){
          $("#no_data").remove();
          $("#"+data['barcode']).remove();
          let display_cost = parseFloat(data['cost']).toFixed(3);
          let display_total = data['total'];
          let html = `<tr class="data" id=${data['barcode']}>`;
          html += `<td>${data['barcode']}</td>`;
          html += `<td>${data['product_name']}</td>`;
          html += `<td>${data['quantity']}</td>`;
          html += `<td>${display_cost}</td>`;
          html += `<td val=${data['total']}>${display_total}</td>`;
          html += `<td align="center" style="width:25%"><button type="button" class="btn btn-secondary edit" style="margin-right: 20px;" val="${data['barcode']}">Edit</button><button type="button" class="btn btn-success delete" val=${data['id']}>Delete</button></td>`
          html += `</tr>`;
          $("#purchase_list").prepend(html);
          $("#add_product").modal('hide');
          declareDelete();
          declareEdit();
          calTotal();
        }else{
          Swal.fire('Error','Please Try Again','error');
        }
        $("#modal_submit").prop('disabled',false);
      },'json');
    }
  });

  $("form").submit(function(e){
    e.preventDefault();
    Swal.fire({
      title: 'Important',
      html: 'Please make sure all the information is correct. This action is irreversible.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: "I'm Confirm",
    }).then((result) => {
      if(result.isConfirmed){
        $("input[type=submit]").prop('disabled',true);
        $("form").unbind('submit').submit();
      }
    });
  });

  $("#modal_cost").keyup(function(){
    let quantity = $("#modal_quantity").val();
    let cost = $(this).val();
    let total = quantity * cost;
    $("#modal_total").val(total.toFixed(3));
  });

  $("#modal_quantity").keyup(function(){
    let quantity = $(this).val();
    let cost = $("#modal_cost").val();
    let total = quantity * cost;
    $("#modal_total").val(total.toFixed(3));
  });
  

  $("#modal_total").keyup(function(){
    let quantity = $("#modal_quantity").val();
    let total = $(this).val();
    let cost = total / quantity;
    $("#modal_cost").val(cost.toFixed(3));
  });

declareDelete();
declareEdit();

});

function convertNumber(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function declareDelete(){
  $(".delete").click(function(){
    let target = $(this).parent().parent();
    $.get("{{route('ajaxDeleteGoodReturnItem')}}",
    {
      'id':$(this).attr('val'),
    },function(data){
      target.remove();
      calTotal();
    },'json');
  });
}

function declareEdit(){
  $(".edit").click(function(){
    let barcode = $(this).attr('val');
    $("input[name=searched_value]").val(barcode);
    $("#check_barcode").click();
  });
}

function calTotal(){
  let total_quantity = 0;
  let total_product = 0;
  let total_amount = 0;
  $(".data").each(function(i){
    cost = parseFloat($(this).children().eq(3).text());
    quantity = parseFloat($(this).children().eq(2).text());
    total = parseFloat($(this).children().eq(4).attr('val'));

    total_quantity += quantity;
    total_product = i+1;
    total_amount += total;
  });

  $("#total_product").text(total_product);
  $("#total_quantity").text(total_quantity.toFixed(3));
  $("#total_amount").text(convertNumber(total_amount.toFixed(2)));
}

</script>
@if(session()->has('fail'))
<script>
  swal.fire('Empty','No items added in list','warning');
</script>
@elseif(session()->has('success'))
<script>
  window.open("{{ route('getPrintGr',session()->get('gr')) }}");
</script>
@endif

@endsection