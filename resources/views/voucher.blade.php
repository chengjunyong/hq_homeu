@extends('layouts.app')
<title>Voucher Management</title>
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
</style>
<div class="container">
  <h2 align="center">Voucher Management</h2>
  <div class="card" style="border-radius: 1.25rem">
    <div class="card-title" style="padding: 10px">
      <h4>Voucher List</h4>
    </div>
    <div>
      <button type="button" id="create_voucher_btn" class="btn btn-primary" style="float:right;margin-right: 10px;">Create Voucher</button>
    </div>
    <div class="card-body">
      <form method="post" action="#">
        <div class="table-responsive">
          <table class="table" style="width:100%">
            <thead>
              <th>No</th>
              <th>Code</th>
              <th>Name</th>
              <th>Discount Type</th>
              <th>Amount</th>
              <th style="text-align: center">Status</th>
              <th>Creator</th>
              <th>Last Update</th>
              <th></th>
              <th></th>
            </thead>
            @foreach($voucher as $index => $result)
              <tr>
                <td>{{$index+1}}</td>
                <td>{{$result->code}}</td>
                <td>{{$result->name}}</td>
                <td align="center">{{ ($result->type == 'fixed') ? 'Fixed' : 'Percentage' }}</td>
                <td>{{$result->amount}}</td>
                <td align="center">
                  <input type="checkbox" class="status" ref-id="{{$result->id}}" data-toggle="toggle" data-on="Active" data-off="Deactivate" {{ ($result->active == 1) ? 'checked' : '' }}>
                </td>
                <td>{{$result->creator_name}}</td>
                <td>{{date("d-M-Y H:i:s A",strtotime($result->updated_at))}}</td>
                <td><button type="button" class="btn btn-primary modify" ref-id="{{$result->id}}">Modify</button></td>
                <td><button type="button" class="btn btn-danger delete" ref-id="{{$result->id}}">Delete</button></td>
              </tr>
            @endforeach
          </table>
        </div>
      </form>
      <div>{{ $voucher->links() }}</div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit_voucher" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">Modify Voucher</h5>
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

<div class="modal fade" id="create_voucher" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Create Voucher</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <label>Voucher Code</label><br/>
            <input class="form-control" name="create_code" type="text"/>
          </div>
          <div class="col-md-12">
            <label>Voucher Name</label><br/>
            <input class="form-control" name="create_name" type="text"/>
          </div>
          <div class="col-md-12">
            <label>Discount Type</label><br/>
            <select id="create_dis" name="dis_type" class="form-control">
              <option value="fixed">Fixed Amount</option>
              <option value="percentage">Percentage</option>
            </select>
          </div>
          <div class="col-md-12">
            <label>Amount</label><br/>
            <input class="form-control" name="create_amount" type="number" step="0.01" min="0.01"/>
          </div>
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

  $(".modify").click(function(){
    $.post('{{route('getVoucher')}}',
    {
      '_token': '{{csrf_token()}}',
      'type': 'read',
      'id': $(this).attr('ref-id'),
    },function(data){
      $("input[name=edit_code]").val(data['code']);
      $("input[name=edit_name]").val(data['name']);
      $("input[name=edit_amount]").val(data['amount']);
      $("#edit_dis").val(data['type']);
      $("#edit_voucher").modal();
    },'json');
  });


  $("#create_voucher_btn").click(()=>{
    $("#create_voucher").modal();
  });

  $("#create").click(()=>{
    if(!$("input[name=create_code]").val() || !$("input[name=create_name]").val() || !$("input[name=create_amount]").val()){
      swal.fire('Empty Result','Please Fill Up All The Data','error');
    }else if(parseFloat($("input[name=create_amount]").val()) < 0.01){
      swal.fire('Invalid Value','Please Change A Valid Amount','error');
    }else if($("#create_dis").val() == "percentage" && $("input[name=create_amount]").val() > 100){
      swal.fire('Invalid Value','Percentage Discount Cannot Exceed 100%','error');
    }else{
      $.post('{{route('postVoucher')}}',
      {
        'type' : 'validate_code',
        'code' : $("input[name=create_code]").val(),
        '_token' : '{{ csrf_token() }}' ,
      },function(data){
        if(data == false){
          swal.fire('Voucher Code Exist','Please Use Another Code To Create New Voucher','error');
        }else{
          $.post('{{route('postVoucher')}}',
          {
            'type'    : 'create',
            'code'    : $("input[name=create_code]").val(),
            'name'    : $("input[name=create_name]").val(),
            'dis_type': $("#create_dis").val(),
            'amount'  : $("input[name=create_amount]").val(),
            '_token'  : '{{ csrf_token()}}',
          },function(data){
            $("#create_voucher").modal('hide');
            if(data == true){
              swal.fire({
                'title': 'Success',
                'text': 'Voucher Created Successful',
                'icon': 'success',
                'allowOutsideClick': false,
              }).then((result)=>{
                if(result.isConfirmed){
                  location.reload();
                }else{
                  location.reload();
                }
              });
            }else{
              swal.fire('Failed','Please Contact IT Support Or Try Again','error');
            }
          },'json');
        }
      },'json');
    }
  });

  $("#edit").click(function(){
    if(!$("input[name=edit_code]").val() || !$("input[name=edit_name]").val() || !$("input[name=edit_amount]").val()){
      swal.fire('Empty Result','Please Fill Up All The Data','error');
    }else if(parseFloat($("input[name=edit_amount]").val()) < 0.01){
      swal.fire('Invalid Value','Please Change A Valid Amount','error');
    }else if($("#edit_dis").val() == "percentage" && $("input[name=edit_amount]").val() > 100){
      swal.fire('Invalid Value','Percentage Discount Cannot Exceed 100%','error');
    }else{
      $.post('{{route('postVoucher')}}',
      {
        'type' : 'edit',
        'code' : $("input[name=edit_code]").val(),
        'name' : $("input[name=edit_name]").val(),
        'dis_type': $("#edit_dis").val(),
        'amount' : $("input[name=edit_amount]").val(),
        '_token' : '{{ csrf_token() }}' ,
      },function(data){
        $("#edit_voucher").modal('hide');
        if(data){
          swal.fire({
            'title': 'Success',
            'text': 'Voucher Edit Successful',
            'icon': 'success',
            'allowOutsideClick': false,
          }).then((result)=>{
            if(result.isConfirmed){
              location.reload();
            }else{
              location.reload();
            }
          });
        }
      },'json');
    }
  });

  $(".status").change(function(){
    let status = ($(this).prop('checked') ? 1 : 0);
    $.post("{{route('postVoucher')}}",
    {
      '_token': '{{csrf_token()}}',
      'id': $(this).attr('ref-id'),
      'status': status,
      'type': 'status',
    },function(data){
      if(data){
        swal.fire('Successful','Voucher Status Change Successful','success');
      }else{
        swal.fire({
          'title': 'Error',
          'text': 'Voucher Status Change Unsuccessful, Please Contact IT Support Or Try Again',
          'icon': 'error',
          'allowOutsideClick': false,
        }).then((result)=>{
          if(result.isConfirmed){
            location.reload();
          }else{
            location.reload();
          }
        });
      }

    },'json');
  });

  $(".delete").click(function(){
    swal.fire({
      title: 'Delete Voucher',
      html: 'Are you sure to delete this voucher',
      icon:'warning',
      confirmButtonText:'Delete It',
      showCancelButton: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $.post('{{route('getVoucher')}}',
        {
          '_token': '{{csrf_token()}}',
          'type': 'delete',
          'id': $(this).attr('ref-id'),
        },function(data){
          swal.fire({
            'title': 'Success',
            'text': 'Voucher Delete Successful',
            'icon': 'success',
            'allowOutsideClick': false,
          }).then((result)=>{
            if(result.isConfirmed){
              location.reload();
            }else{
              location.reload();
            }
          });
        },'json');
      }
    });

  });

});
</script>

@endsection