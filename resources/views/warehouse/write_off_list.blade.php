@extends('layouts.app')
<title>Stock Write Off List</title>
@section('content')
<style>
  .container{
    min-width: 95%;
  }

  td{
    padding:5px;
  }
</style>
<div class="container" style="padding-bottom: 5vh;">
  <div class="card" style="margin-top: 10px">
    <div class="card-title">
      <h4>Stock Write Off List</h4>
    </div>

    <form>
      @csrf
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <label>Total Items:</label>
            <input readonly class="form-control" type="text" name="total_item" value="{{count($wf_list)}}">
          </div>
          <div class="col-md-6">
            <label>Total Amount:</label>
            <input readonly class="form-control" type="text" name="total_cost" value="{{$wf_list->sum('total')}}">
          </div>
        </div>

        <div style="overflow-y: auto;height:425px;margin-top:25px;width:100%">
          <table class="table-responsive" style="width:100%;display: table !important;">
            <thead style="background-color: #b8b8efd1">
              <tr>
                <td>No</td>
                <td style="width:20%">Barcode</td>
                <td>Product Name</td>
                <td>Qty</td>
                <td align="right">Cost</td>
                <td align="right">Total Amount</td>
                <td align="center">Declared By</td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              @foreach($wf_list as $key => $result)
                <tr id="{{$result->id}}">
                  <td>{{$key+1}}<input type="text" hidden name="id[]" value="{{$result->id}}" /></td>
                  <td>{{$result->barcode}}</td>
                  <td>{{$result->product_name}}</td>
                  <td>{{$result->quantity}}</td>
                  <td align="right">{{number_format($result->cost,2)}}</td>
                  <td align="right">{{number_format($result->total,2)}}</td>
                  <td align="center">{{$result->created_by}}</td>
                  <td><button type="button" class="btn btn-danger delete" value="{{$result->id}}">Delete</button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12" style="text-align: center">
          <input class="btn btn-primary" type="submit" value="Write Off Items"/>
        </div>
      </div>

    </div>
  </form>

</div>
<script>
$(document).ready(function(){
  $(".delete").click(function(){
    let id = $(this).val();
    Swal.fire({
      title: 'Remove Items',
      html: 'Please confirm if you want to remove this item from list',
      icon: 'warning',
      confirmButtonText: `Yes`,
      showCancelButton: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $("#"+id).remove();
        $.get('{{route('ajaxRemoveWriteOffItem')}}',
        {
          'id' : id
        },function(data){
          if(data == true){
            Swal.fire({
              title: 'Success',
              html: 'Remove successful',
              icon: 'success',
              confirmButtonText: `Yes`,
            }).then((result)=>{
              window.location.reload();
            });
          }else{
            Swal.fire('Fail','Item remove fail, please contact IT support','error');
          }
        },'json');
      }
    })
  });

  $("form").submit(function(e){
    e.preventDefault();
    Swal.fire({
      title: 'Generate Write Off Record',
      html: 'Please make sure all the items in the list are correct, this action is irreversible',
      icon: 'warning',
      confirmButtonText: `Yes`,
      showCancelButton: true,
    }).then((result)=>{
      if(result.isConfirmed){
        $.post('{{route('postWriteOffList')}}',$("form").serialize(),
        function(data){
          if(data == true){
            Swal.fire('Success','Write Off Record Generate Completed','success').then(()=>{window.location.assign('{{route('getStockWriteOff')}}')});
          }else{
            Swal.fire('Fail','Something wrong, please try again','error');
          }
        },"json");
      }
    });
      
  });

});
</script>

@endsection