@extends('layouts.app')
<title>Hamper Management</title>
@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<style>
  .container{
    max-width: 1600px;
  }

  .table td{
    vertical-align: baseline;
    padding: 0.15rem;
  }

  .toggle-handle{
    background-color: #ede31a;
    border-radius: 0px;
  }

  .toggle{
    border: 2px solid grey;
  }

  .toggle-on.btn{
    background-color: #0babe3;
  }

  .toggle-off.btn {
    padding-left: 18px;
  }

  .modal_create_hamper .row .col-md-12{
    margin-top: 10px;
  }

</style>
<div class="container">
  <h2 align="center">Hamper Management</h2>
  <div class="card" style="border-radius: 1.25rem">
    <div class="card-title" style="padding: 10px">
      <h4>Hamper List</h4>
    </div>
    <div>
      <button type="button" id="create_hamper_btn" class="btn btn-primary" style="float:right;margin-right: 10px;">Create Hamper</button>
    </div>
    <div class="card-body">
      <form method="post" action="#">
        <div class="table-responsive">
          <table class="table" style="width:100%">
            <thead>
              <th>No</th>
              <th>Barcode</th>
              <th>Hamper Name</th>
              <th>Hamper Price</th>
              <th>Creator</th>
              <th>Last Update</th>
              <th></th>
              <th></th>
            </thead>
            @foreach($hamper as $index => $result)
              <tr>
                <td>{{ $index +1 }}</td>
                <td>{{ $result->barcode }}</td>
                <td>{{ $result->name }}</td>
                <td>Rm {{ number_format($result->price,2) }}</td>
                <td>{{ $result->creator_name }}</td>
                <td>{{ date("d-M-Y h:i:s A",strtotime($result->updated_at)) }}</td>
                <td><button type="button" class="btn btn-primary modify" ref-id="{{$result->id}}">Check</button></td>
                <td><button type="button" class="btn btn-danger delete" ref-id="{{$result->id}}">Delete</button></td>
              </tr>
            @endforeach
          </table>
        </div>
      </form>
      <div>{{ $hamper->links() }}</div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit_hamper" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">Modify Hamper</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <label>Voucher Code</label><br/>
            <input class="form-control" name="edit_code" type="text" readonly="true" />
          </div>
          <div class="col-md-12">
            <label>Voucher Name</label><br/>
            <input class="form-control" name="edit_name" type="text"/>
          </div>
          <div class="col-md-12">
            <label>Discount Type</label><br/>
            <select id="edit_dis" name="dis_type" class="form-control">
              <option value="fixed">Fixed Amount</option>
              <option value="percentage">Percentage</option>
            </select>
          </div>
          <div class="col-md-12">
            <label>Amount</label><br/>
            <input class="form-control" name="edit_amount" type="number" step="0.01" min="0.01"/>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="edit">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="create_hamper" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 100%;width:60%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Create Hamper</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body modal_create_hamper">
        <div class="row">
          <div class="col-md-12">
            <label>Hamper Barcode</label><br/>
            <input class="form-control" name="barcode" type="text"/>
          </div><br/>

          <div class="col-md-12">
            <label>Hamper Name</label><br/>
            <input class="form-control" name="name" type="text"/>
          </div><br/>

          <div class="col-md-12">
            <label>Price</label><br/>
            <input class="form-control" name="price" type="number" step="0.01" min="0.01"/>
          </div><br/>

          <div class="col-md-12">
            <label>Product List</label>
            <div style="float:right">
              <input type="text" id="product_barcode" placeholder="Barcode" />
              <input type="number" id="product_quantity" placeholder="Quantity"/>
              <button class="btn btn-outline" id="add_product_list">Add Product</button>
            </div>

            <ul id="hamper_product_list">
              
            </ul>

          </div><br/>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="create">Created</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){

    localStorage.setItem('product_list',"");

    $("#create_hamper_btn").click(function(){
      $("#create_hamper").modal('show');
    });

    $("#add_product_list").click(function(){
      let barcode = $("#product_barcode").val();
      let quantity = $("#product_quantity").val();

      if(barcode != "" && quantity != ""){
        $.get("{{route('ajaxAddHamperProduct')}}",
        {
          'barcode':barcode,
          'quantity':quantity,
        },function(data){
          if(data == 'null'){
            swal.fire('Error','Barcode Not Found','error');
            $("#product_barcode").val("");
            $("#product_barcode").focus();
          }else{
            let product_list = processProductList(data,quantity);
            $("#hamper_product_list").empty();
            product_list.forEach((result,index) => {
              $("#hamper_product_list").append(`<li>${result.product_name} (qty:${result.quantity})<i class="fa fa-times removeItem" onclick="removeItem(${index})" target='${index}' style="cursor: pointer;margin-left: 5px; color:red"></i></li>`)
            });
          }
        },'json');
      }else{
        swal.fire('Error','Barcode & Quantity Cannot Be Empty','error');
      }
    });

  });

  function processProductList(item,quantity)
  {
    let product_list = localStorage.getItem('product_list');
    if(product_list == ''){
      item.quantity = quantity;
      product_list = [item];
    }else{
      product_list = JSON.parse(product_list);
      item.quantity = quantity;
      product_list.push(item);
    }
    product_list = JSON.stringify(product_list);
    localStorage.setItem('product_list',product_list);

    return JSON.parse(product_list);
  }

  function removeProductList(index)
  {
    let product_list = JSON.parse(localStorage.getItem('product_list'));
    product_list.splice(index,1);
    product_list = JSON.stringify(product_list);
    localStorage.setItem('product_list',product_list);

    return JSON.parse(product_list);
  }

  function removeItem(index)
  {
    console.log(index);
    let product_list = removeProductList(index);
    console.log(product_list);
    $("#hamper_product_list").empty();
    product_list.forEach((result,index) => {
      $("#hamper_product_list").append(`<li>${result.product_name} (qty:${result.quantity})<i class="fa fa-times removeItem" onclick="removeItem(${index})" target='${index}' style="cursor: pointer;margin-left: 5px; color:red"></i></li>`)
    });
  }

</script>

@endsection