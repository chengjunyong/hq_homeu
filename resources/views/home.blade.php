@extends('layouts.app')

@section('content')
<style>
  body{
    overflow-x: hidden;
  }

  .footer{
    position: fixed;
    bottom: 0;
    left: 0;
    background-color: #e4b109;
    height:50px;
    width:100%;
    text-align: center;
  }

  .logout{
    margin-top: 5px;
    margin-right: 5px;
    text-align: right;
    font-size:22px;
  }

  .col-md-4{
    font-size:850%;
    color:#2b407b;
  }

  h2{
    color:black;
  }

  a { 
    text-decoration: none;
    color:#2b407b;
  }

</style>
<div class="row" style="background-color: #e4b109;padding:15px 0px 15px 0px;">
  <div class="col-md-12">
    <h2 align="center">Homeu Management System</h2>
  </div>
</div>
<div class="logout">
   <form action="{{route('logout')}}" method="post">
     @csrf
     <lable>Hello {{$user->name}}</lable><br/>
     <input type="submit" class="logout btn btn-primary" value="Logout" style="color:white"/>
   </form> 
</div>

<div class="container" id="first">
  <div class="row" style="text-align: center">
    <div class="col-md-4">
      <a href="#">
        <i class="fa fa-shopping-bag"></i>
        <h2>Product</h2>
      </a>
    </div>
    <div class="col-md-4">
      <a href="#">
        <i class="fa fa-cubes"></i><br/>
        <h2>Stock</h2>
      </a>
    </div>
    <div class="col-md-4">
      <a href="#">
        <i class="fa fa-chart-bar"></i>
        <h2>Report</h2>
      </a>
    </div>
  </div>
  <div class="row" style="text-align: center;margin-top: 25px">
    <div class="col-md-4">
      <a href="#">
        <i class="fa fa-coins"></i>
        <h2>Sales</h2>
      </a>
    </div>
    <div class="col-md-4">
      <a href="#" id="btn_other">
        <i class="fa fa-question-circle"></i>
        <h2>Others</h2>
      </a>
    </div>
    <div class="col-md-4">
    </div>
  </div>
</div>

<div class="container" id="other" hidden>
  <div class="row" style="text-align: center">
    <div class="col-md-12">
      <button class="btn btn-secondary backmain">Main</button>
    </div>
    <div class="col-md-4">
      <a href="{{route('getBranch')}}">
        <i class="fa fa-store"></i>
        <h2>Branch Setup</h2>
      </a>
    </div>
  </div>
</div>

<div class="footer">
  <label style="font-weight: 600;margin-top: 10px">Design & Developed By Team Homeu</label>
</div>

<script>
  $("#btn_other").click(function(){
    showSecond($("#first"),$("#other"));
  });

  $(".backmain").click(function(){
    showMain($(this));
  });

function showSecond(hide,show){
  hide.prop('hidden',true);
  show.prop('hidden',false);
}

function showMain(hide){
  $("#first").prop('hidden',false);
  hide.parent().parent().parent().prop('hidden',true);
}
</script>
@endsection
