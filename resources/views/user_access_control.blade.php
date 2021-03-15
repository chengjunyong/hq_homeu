@extends('layouts.app')

@section('content')
<style>
  body{
    background: #f9fafb;
    padding: 20px 0;
  }

  #user_table{
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
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <h4>User Access Control</h4>
          <table id="user_table">
            <thead style="background-color: #403c3c80;text-align: center">
              <tr>
                <th>User role</th>
                <th>Name</th>
                <th>Username</th>
                <th>Edit</th>
              </tr>
            </thead>
            <tbody style="text-align: center">
              @foreach($user_list as $user_detail)
                <tr>
                  <td>{{ $user_detail->user_type_text }}</td>
                  <td>{{ $user_detail->name }}</td>
                  <td>{{ $user_detail->username }}</td>
                  <td>
                    <button type="button" class="btn btn-primary edit_user" user_id="{{ $user_detail->id }}" user_type="{{ $user_detail->user_type }}" name="{{ $user_detail->name }}" username="{{ $user_detail->username }}" access_control="{{ $user_detail->access_control }}">Edit</button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="col-md-12" style="margin-top: 20px;">
          <button type="button" id="add_user" class="btn btn-success">Add New User</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 700px !important;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="new_user_form">
          @csrf
          <div class="form-group">
            <label>User Role</label>
            <select class="form-control" name="new_user_role">
              <option value="0">Please select</option>
              @foreach($user_group as $user_group_detail)
                <option value="{{ $user_group_detail['value'] }}">{{ $user_group_detail['name'] }}</option>
              @endforeach
            </select>
            <span class="invalid-feedback" role="alert"></span>
            <a href="#" id="new_user_more_option">More options</a>
            <div id="new_role_option" style="display: none;">
              <div class="row">
                @foreach($user_access_control as $access_control)
                  <div class="col-md-6">
                    <div class="checkbox icheck" style="display: inline-block; margin-right: 10px;">
                      <label>
                        <input class="form-check-input" type="checkbox" name="new_access_control[]" value="{{ $access_control['value'] }}" /> {{ $access_control['name'] }}
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Name</label>
            <input type="text" name="new_user_name" class="form-control" />
            <span class="invalid-feedback" role="alert"></span>
          </div>

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="new_user_username" class="form-control" />
            <span class="invalid-feedback" role="alert"></span>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" name="new_user_password" class="form-control" />
            <span class="invalid-feedback" role="alert"></span>
          </div>

          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="new_user_password_confirmation" class="form-control" />
            <span class="invalid-feedback" role="alert"></span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitAddUser()">Add</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 700px !important;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="edit_user_form">
          @csrf
          <div class="form-group">
            <label>User Role</label>
            <select class="form-control" name="edit_user_role">
              <option value="0">Please select</option>
              @foreach($user_group as $user_group_detail)
                <option value="{{ $user_group_detail['value'] }}">{{ $user_group_detail['name'] }}</option>
              @endforeach
            </select>
            <span class="invalid-feedback" role="alert"></span>
            <a href="#" id="more_option">More options</a>
            <div id="edit_role_option" style="display: none;"> 
              <div class="row">
                @foreach($user_access_control as $access_control)
                <div class="col-md-6">
                  <div class="checkbox icheck" style="display: inline-block; margin-right: 10px;">
                    <label>
                      <input class="form-check-input" type="checkbox" name="access_control[]" value="{{ $access_control['value'] }}" /> {{ $access_control['name'] }}
                    </label>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Name</label>
            <input type="text" name="edit_user_name" class="form-control" />
            <span class="invalid-feedback" role="alert"></span>
          </div>

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="edit_user_username" class="form-control" />
            <span class="invalid-feedback" role="alert"></span>
          </div>

          <div class="form-group">
            <label>New Password ( Leave it blank if no changes )</label>
            <input type="password" name="edit_user_password" class="form-control" />
            <span class="invalid-feedback" role="alert"></span>
          </div>

          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="edit_user_password_confirmation" class="form-control" />
            <span class="invalid-feedback" role="alert"></span>
          </div>

          <input type="hidden" name="edit_user_id" />
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitEditUser()">Edit</button>
      </div>
    </div>
  </div>
</div>

<script>

  var default_access_control = @json($default_access_control);

  $(document).ready(function(){

    $("#add_user").click(function(){
      $("#new_role_option").hide();

      $("#addUserModal").modal('show');
    });

    $(".edit_user").click(function(){

      $("#edit_role_option").hide();

      var edit_name = $(this).attr("name");
      var edit_username = $(this).attr("username");
      var edit_user_type = $(this).attr("user_type");
      var edit_user_id = $(this).attr("user_id");
      var access_control = $(this).attr("access_control");

      $("select[name='edit_user_role']").val(edit_user_type);
      $("input[name='edit_user_name']").val(edit_name);
      $("input[name='edit_user_username']").val(edit_username);
      $("input[name='edit_user_id']").val(edit_user_id);

      $("input[name='edit_user_password'], input[name='edit_user_password_confirmation']").val("");

      var access_control_array = access_control.split(",");

      $("input.form-check-input[name='access_control[]']").iCheck('uncheck');
      for(var a = 0; a < access_control_array.length; a++)
      {
        if(access_control_array[a] != "")
        {
          $("input.form-check-input[name='access_control[]'][value="+access_control_array[a]+"]").iCheck('check');
        }
      }

      $("#editUserModal").modal('show');
    });

    $('.form-check-input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });

    $("#more_option").click(function(){
      $("#edit_role_option").slideToggle();
    });

    $("#new_user_more_option").click(function(){
      $("#new_role_option").slideToggle();
    });

    $("select[name='edit_user_role'], select[name='new_user_role']").on('change', function(){
      var selected_role = $(this).val();
      var access = "";

      var select_name = $(this).attr("name");
      if(selected_role != 0)
      {
        if(select_name == "edit_user_role")
        {
          $("input.form-check-input[name='access_control[]']").iCheck('uncheck');
        }
        else if(select_name == "new_user_role")
        {
          $("input.form-check-input[name='new_access_control[]']").iCheck('uncheck');
        }
    
        for(var a = 0; a < default_access_control.length; a++)
        {
          if(selected_role == default_access_control[a].user_type)
          {
            access = default_access_control[a].access;
            break;
          }
        }

        if(access != "")
        {
          var access_control_array = access.split(",");
          for(var a = 0; a < access_control_array.length; a++)
          {
            if(access_control_array[a] != "")
            {
              if(select_name == "edit_user_role")
              {
                $("input.form-check-input[name='access_control[]'][value="+access_control_array[a]+"]").iCheck('check');
              }
              else if(select_name == "new_user_role")
              {
                $("input.form-check-input[name='new_access_control[]'][value="+access_control_array[a]+"]").iCheck('check');
              }
            }
          }
        }
      }
    });

  });

  function submitAddUser()
  {
    $("select[name='new_user_role']").removeClass("is-invalid");
    $("input[name='new_user_name']").removeClass("is-invalid");
    $("input[name='new_user_username']").removeClass("is-invalid");
    $("input[name='new_user_password']").removeClass("is-invalid");
    $("input[name='new_user_password_confirmation']").removeClass("is-invalid");

    var new_user_role = $("select[name='new_user_role']").val();
    var new_user_name = $("input[name='new_user_name']").val();
    var new_user_username = $("input[name='new_user_username']").val();
    var new_user_password = $("input[name='new_user_password']").val();
    var new_user_password_confirmation = $("input[name='new_user_password_confirmation']").val();

    var checking = true;
    if(new_user_role == 0)
    {
      $("select[name='new_user_role']").addClass("is-invalid").siblings(".invalid-feedback").html("Please select user role");
      checking = false;
    }

    if(new_user_name == "")
    {
      $("input[name='new_user_name']").addClass("is-invalid").siblings(".invalid-feedback").html("Name cannot be empty.");
      checking = false;
    }

    if(new_user_username == "")
    {
      $("input[name='new_user_username']").addClass("is-invalid").siblings(".invalid-feedback").html("Username cannot be empty.");
      checking = false;
    }

    if(new_user_password == "")
    {
      $("input[name='new_user_password']").addClass("is-invalid").siblings(".invalid-feedback").html("Password cannot be empty.");
      checking = false;
    }

    if(new_user_password_confirmation == "")
    {
      $("input[name='new_user_password_confirmation']").addClass("is-invalid").siblings(".invalid-feedback").html("Password cannot be empty.");
      checking = false;
    }

    if(new_user_password != new_user_password_confirmation)
    {
      $("input[name='new_user_password']").addClass("is-invalid").siblings(".invalid-feedback").html("Password and password confirmation are not same.");
      checking = false;
    }

    if(checking == true)
    {
      $.post("{{ route('createNewUser') }}", $("#new_user_form").serialize(), function(result){
        if(result.error == 1)
        {
          $("input[name='new_user_username']").addClass("is-invalid").siblings(".invalid-feedback").html(result.message);
          return;
        }
        else if(result.error == 0)
        {
          location.reload();
        }
      }).fail(function(){
        alert("Something wrong");
      });
    }
  }

  function submitEditUser()
  {
    $("select[name='edit_user_role']").removeClass("is-invalid");
    $("input[name='edit_user_name']").removeClass("is-invalid");
    $("input[name='edit_user_username']").removeClass("is-invalid");
    $("input[name='edit_user_password']").removeClass("is-invalid");
    $("input[name='edit_user_password_confirmation']").removeClass("is-invalid");

    var edit_user_role = $("select[name='edit_user_role']").val();
    var edit_user_name = $("input[name='edit_user_name']").val();
    var edit_user_username = $("input[name='edit_user_username']").val();
    var edit_user_password = $("input[name='edit_user_password']").val();
    var edit_user_password_confirmation = $("input[name='edit_user_password_confirmation']").val();

    var checking = true;
    if(edit_user_role == 0)
    {
      $("select[name='edit_user_role']").addClass("is-invalid").siblings(".invalid-feedback").html("Please select user role");
      checking = false;
    }

    if(edit_user_name == "")
    {
      $("input[name='edit_user_name']").addClass("is-invalid").siblings(".invalid-feedback").html("Name cannot be empty.");
      checking = false;
    }

    if(edit_user_username == "")
    {
      $("input[name='edit_user_username']").addClass("is-invalid").siblings(".invalid-feedback").html("Username cannot be empty.");
      checking = false;
    }

    if(edit_user_password != edit_user_password_confirmation)
    {
      $("input[name='edit_user_password']").addClass("is-invalid").siblings(".invalid-feedback").html("Password and password confirmation are not same.");
      checking = false;
    }

    if(checking == true)
    {
      $.post("{{ route('editUser') }}", $("#edit_user_form").serialize(), function(result){
        if(result.error == 1)
        {
          $("input[name='edit_user_username']").addClass("is-invalid").siblings(".invalid-feedback").html(result.message);
          return;
        }
        else if(result.error == 0)
        {
          location.reload();
        }
      }).fail(function(){
        alert("Something wrong");
      });
    }
  }

</script>



@endsection