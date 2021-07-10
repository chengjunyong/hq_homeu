@extends('layouts.app')
<title>Stock Purchase (Invoice)</title>
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

<div class="container" style="padding-bottom: 5vh;">
  <div class="card" style="margin-top: 10px">

    <div class="card-title" style="margin:5px;">
      <h4>Purchase Information</h4>
    </div>

    <div class="card-body">
      <form action="" method="post">
        @csrf
        <div class="row">
          <div class="col-md-4">
            <label class="title">Invoice Date :</label>
          </div>
          <div class="col-md-8">
            <input type="date" name="invoice_date" class="form-control" required />
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label class="title">Invoice No :</label>
          </div>
          <div class="col-md-8">
            <input type="text" name="invoice_no" class="form-control" required />
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
                <th>Costs</th>
                <th>Quantity</th>
              </thead>
              <tbody id="purchase_list">
              @foreach($tmp as $result)
                <tr id="{{$result->barcode}}">
                  <td>{{$result->barcode}}</td>
                  <td>{{$result->product_name}}</td>
                  <td>{{$result->cost}}</td>
                  <td>{{$result->quantity}}</td>
                </tr>
              @endforeach
              </tbody>
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
            Barcode : 
          </div>
          <div class="col-9">
            <input type="text" readonly id="modal_barcode" class="form-control" />
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            Product Name : 
          </div>
          <div class="col-9">
            <input type="text" readonly id="modal_product_name" class="form-control" />
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            Cost : 
          </div>
          <div class="col-9">
            <input type="number" min=0.01 step=0.01 id="modal_cost" class="form-control" />
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            Quantity : 
          </div>
          <div class="col-9">
            <input type="number" min=1 id="modal_quantity" class="form-control" />
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
      }else{
        Swal.fire('Error','Barcode Not Found','error');
      }
      $("input[name=searched_value]").val('');

    },'json');
  });

  $("#modal_submit").click(function(data){
    $("#modal_submit").prop('disabled',true);

    if($("#modal_cost").val() == "" || $("#modal_cost").val() <= 0){
      Swal.fire('Error','Value Cost Invalid','error');
      $("#modal_submit").prop('disabled',false);
    }else if($("#modal_quantity").val() == "" || $("#modal_quantity").val() <= 0 || $("#modal_quantity").val() % 1 != 0){
      Swal.fire('Error','Value Quantity Invalid','error');
      $("#modal_submit").prop('disabled',false);
    }else{
      $.get('{{route('ajaxAddPurchaseListItem')}}',
      {
        'barcode':$("#modal_barcode").val(),
        'product_name':$("#modal_product_name").val(),
        'cost': $("#modal_cost").val(),
        'quantity': $("#modal_quantity").val(),
      },function(data){
        if(data != false){
          $("#"+data['barcode']).remove();
          let html = `<tr>`;
          html += `<td>${data['barcode']}</td>`;
          html += `<td>${data['product_name']}</td>`;
          html += `<td>${data['cost']}</td>`;
          html += `<td>${data['quantity']}</td>`;
          html += `</tr>`;
          $("#purchase_list").prepend(html);
          $("#add_product").modal('hide');
        }else{
          Swal.fire('Error','Please Try Again','error');
        }
        $("#modal_submit").prop('disabled',false);
      },'json');
    }


  });

});
</script>
@endsection