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
              <th>Branch</th>
              <th>Barcode</th>
              <th>Hamper Name</th>
              <th>Hamper Price</th>
              <th>Quantity</th>
              <th>Creator</th>
              <th>Last Update</th>
              <th></th>
              <th></th>
            </thead>
            @foreach($hamper as $index => $result)
              <tr>
                <td>{{ $index +1 }}</td>
                <td>{{ $result->branch->branch_name ?? 'Warehouse' }}</td>
                <td>{{ $result->barcode }}</td>
                <td>{{ $result->name }}</td>
                <td>Rm {{ number_format($result->price,2) }}</td>
                <td>{{ $result->getQuantity()->quantity ?? 0}}</td>
                <td>{{ $result->user->name ?? 'Unknown' }}</td>
                <td>{{ date("d-M-Y h:i:s A",strtotime($result->updated_at)) }}</td>
                <td><button type="button" class="btn btn-primary modify" ref-id="{{$result->id}}">Check</button></td>
                <td><a class="btn btn-secondary" href="{{route('printHamper',$result->id)}}" target="_blank">Print</button></td>
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
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 100%;width:60%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Edit Hamper</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body modal_edit_hamper">
        <div class="row">
          <div class="col-md-12">
            <label>Branch</label><br/>
            <select name="e_branch" class="form-control" disabled>
              @foreach($branches as $branch)
                <option value={{$branch->id}}>{{$branch->branch_name}}</option>
              @endforeach
              <option value="0">Warehouse</option>
            </select>
          </div><br/>

          <div class="col-md-12">
            <label>Hamper Barcode</label><br/>
            <input class="form-control" name="e_barcode" readonly type="text"/>
          </div><br/>

          <div class="col-md-12">
            <label>Hamper Name</label><br/>
            <input class="form-control" name="e_name" type="text"/>
          </div><br/>

          <div class="col-md-12">
            <label>Price</label><br/>
            <input class="form-control" name="e_price" type="number" step="0.01" min="0.01"/>
          </div><br/>

          <div class="col-md-12">
            <label>Quantity</label><br/>
            <input class="form-control" name="e_quantity" type="number" readonly step="1" min="0"/>
          </div><br/>

          <div class="col-md-12">
            <label>Product List</label>
            {{-- <div style="float:right">
              <input type="text" id="e_product_barcode" placeholder="Barcode" />
              <input type="number" id="e_product_quantity" placeholder="Quantity"/>
              <button class="btn btn-outline edit_product_list">Add Product</button>
            </div> --}}

            <ul class="hamper_product_list">
              
            </ul>

          </div><br/>

        </div>
      </div>
      <div class="modal-footer">
        <input type="text" name="hamper_id" hidden />
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="update">Update</button>
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
            <label>Branch</label><br/>
            <select name="branch" class="form-control" >
              @foreach($branches as $branch)
                <option value={{$branch->id}}>{{$branch->branch_name}}</option>
              @endforeach
              <option value="0">Warehouse</option>
            </select>
          </div><br/>

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
            <label>Quantity</label><br/>
            <input class="form-control" name="quantity" type="number" step="1" min="0"/>
          </div><br/>

          <div class="col-md-12">
            <label>Product List</label>
            <div style="float:right">
              <input type="text" id="product_barcode" placeholder="Barcode" />
              <input type="number" id="product_quantity" placeholder="Quantity"/>
              <button class="btn btn-outline add_product_list">Add Product</button>
            </div>

            <ul class="hamper_product_list">
              
            </ul>

          </div><br/>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="create">Create</button>
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

    $(".modify").click(function(){
      let id = $(this).attr("ref-id");
      $.get("{{route('getHamper')}}",
      {
        'id':id,
      },function(data){
        $("select[name=e_branch]").val(data.branch_id);
        $("input[name=e_quantity]").val(data.quantity);
        $("input[name=e_barcode]").val(data.barcode);
        $("input[name=e_name]").val(data.name);
        $("input[name=e_price]").val(data.price);
        $("input[name=hamper_id]").val(data.id);
        localStorage.setItem('product_list',data.product_list);
        let product_list = JSON.parse(data.product_list);
        product_list.forEach((result,index) => {
          $(".hamper_product_list").append(`<li>(${result.barcode}) ${result.product_name} - qty:${result.quantity}</li>`)
        });
      },'json');

      $("#edit_hamper").modal('show');
    })

    $(".add_product_list").click(function(){
      $(".add_product_list").prop('disabled',true);
      let barcode = $("#product_barcode").val();
      let quantity = $("#product_quantity").val();
      let exist = true;
      if(localStorage.getItem('product_list') != ''){
        let array = JSON.parse(localStorage.getItem('product_list'));
        array.forEach((result,index)=>{
          if(result.barcode == barcode){
            exist = false;
          }
        });
      }
      if(barcode.trim() == "" || quantity.trim() == "" || quantity == '0'){
        swal.fire('Error','Barcode & Quantity Cannot Be Empty','error');
      }else if(exist == false){
        swal.fire('Error','Repeat Barcode, Please Delete Previous Barcode','error');
      }else{
        $.get("{{route('ajaxAddHamperProduct')}}",
          {
            'barcode':barcode,
            'quantity':quantity,
          },function(data){
            if(data == 'null'){
              swal.fire('Error','Barcode Not Found','error');
              $("#product_barcode").val("");
              $("#product_quantity").val("");
              $("#product_barcode").focus();
            }else{
              let product_list = processProductList(data,quantity);
              $(".hamper_product_list").empty();
              product_list.forEach((result,index) => {
                $(".hamper_product_list").append(`<li>(${result.barcode}) ${result.product_name} - qty:${result.quantity}<i class="fa fa-times removeItem" onclick="removeItem(${index})" target='${index}' style="cursor: pointer;margin-left: 5px; color:red"></i></li>`)
              });
            }
          },'json');

          $("#product_barcode").val("");
          $("#product_quantity").val("");
          $("#product_barcode").focus();
        }

      $(".add_product_list").prop('disabled',false);
    });

    $(".edit_product_list").click(function(){
      $(".add_product_list").prop('disabled',true);
      let barcode = $("#e_product_barcode").val();
      let quantity = $("#e_product_quantity").val();
      let exist = true;
      if(localStorage.getItem('product_list') != ''){
        let array = JSON.parse(localStorage.getItem('product_list'));
        array.forEach((result,index)=>{
          if(result.barcode == barcode){
            exist = false;
          }
        });
      }
      if(barcode.trim() == "" || quantity.trim() == "" || quantity == '0'){
        swal.fire('Error','Barcode & Quantity Cannot Be Empty','error');
      }else if(exist == false){
        swal.fire('Error','Repeat Barcode, Please Delete Previous Barcode','error');
      }else{
        $.get("{{route('ajaxAddHamperProduct')}}",
          {
            'barcode':barcode,
            'quantity':quantity,
          },function(data){
            if(data == 'null'){
              swal.fire('Error','Barcode Not Found','error');
              $("#e_product_barcode").val("");
              $("#e_product_quantity").val("");
              $("#e_product_barcode").focus();
            }else{
              let product_list = processProductList(data,quantity);
              $(".hamper_product_list").empty();
              product_list.forEach((result,index) => {
                $(".hamper_product_list").append(`<li>(${result.barcode}) ${result.product_name} - qty:${result.quantity}<i class="fa fa-times removeItem" onclick="removeItem(${index})" target='${index}' style="cursor: pointer;margin-left: 5px; color:red"></i></li>`)
              });
            }
          },'json');

          $("#e_product_barcode").val("");
          $("#e_product_quantity").val("");
          $("#e_product_barcode").focus();
        }

      $(".add_product_list").prop('disabled',false);
    });

    $("#create").click(function(){
      let barcode = $("input[name=barcode]").val();
      let name = $("input[name=name]").val();
      let price = $("input[name=price]").val();
      let branch = $("select[name=branch]").val();
      let quantity = $("input[name=quantity]").val();
      let list = localStorage.getItem('product_list');

      if(barcode.trim() == ""){
        swal.fire("Error","Hamper Barcode Cannot Be Empty","error");
      }else if(name.trim() == ""){
        swal.fire("Error","Hamper Name Cannot Be Empty","error");
      }else if(parseFloat(price) <= 0 || price.trim() == ''){
        swal.fire("Error","Hamper Price Cannot Lower Then 0 Or Empty","error");
      }else if(list.trim() == ""){
        swal.fire("Error","Hamper Product List Cannot Be Empty","error");
      }else if(parseFloat(quantity) <= 0 || quantity.trim() == ''){
        swal.fire("Error","Quantity Cannot Lower Then 0 Or Empty","error");
      }else{
        $.get('{{route('getCreateHamper')}}',
        {
          'branch' : branch,
          'barcode':barcode,
          'name':name,
          'price':price,
          'quantity':quantity,
          'product_list':list,
        },function(data){
          if(data.result == false){
            swal.fire('Error',`${data.msg}`,'error');
          }else{
            swal.fire({
              'icon':'success',
              'title':'Success',
              'text':`${data.msg}`,
            }).then(()=>{
              window.location.reload();
            });
          }
        },'json');
      }
    });

    $(".delete").click(function(){
      let id = $(this).attr('ref-id');
      swal.fire({
        'icon':'warning',
        'title':'Delete Hamper',
        'text':'Confirm Delete Hamper, This Action Is Irreversible',
        'confirmButtonText': 'Yes',
        'showCancelButton': true,
      }).then((result)=>{
        if(result.isConfirmed){
          $.get('{{route('ajaxDeleteHamper')}}',{'id':id},function(data){if(data == true) window.location.reload(); else swal.fire('Error','Delete Unsuccessful','error')},'json');
        }
      });
    });

    $("#create_hamper, #edit_hamper").on('hidden.bs.modal',function(){
        localStorage.setItem('product_list','');
        $(".hamper_product_list").empty()
    });

    $("#update").click(function(){
      let name = $("input[name=e_name]").val();
      let price = $("input[name=e_price]").val();
      let id = $("input[name=hamper_id]").val();
      let list = localStorage.getItem('product_list');

      if(name.trim() == ""){
        swal.fire("Error","Hamper Name Cannot Be Empty","error");
      }else if(parseFloat(price) <= 0){
        swal.fire("Error","Hamper Price Cannot Lower Then 0","error");
      }else if(list.trim() == ""){
        swal.fire("Error","Hamper Product List Cannot Be Empty","error");
      }else{
        $.get('{{route('getEditHamper')}}',
        {
          'id': id,
          'name':name,
          'price':price,
          'product_list':list,
        },function(data){
          if(data.result == false){
            swal.fire('Error',`${data.msg}`,'error');
          }else{
            swal.fire({
              'icon':'success',
              'title':'Success',
              'text':`${data.msg}`,
            }).then(()=>{
              window.location.reload();
            });
          }
        },'json');
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
    $(".hamper_product_list").empty();
    product_list.forEach((result,index) => {
      $(".hamper_product_list").append(`<li>${result.product_name} (qty:${result.quantity})<i class="fa fa-times removeItem" onclick="removeItem(${index})" target='${index}' style="cursor: pointer;margin-left: 5px; color:red"></i></li>`)
    });
  }

</script>

@endsection