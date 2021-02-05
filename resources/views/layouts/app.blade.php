<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>
  <script src="{{ asset('js/jquery.js') }}"></script>
  <script src="{{ asset('bootstrap-4.0.0/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('fontawesome/js/all.min.js') }}"></script>
  <script src="{{ asset('datatable/datatables.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('bootstrap-4.0.0/css/bootstrap.min.css')}}"/>
  <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css')}}"/>
  <link rel="stylesheet" href="{{ asset('datatable/datatables.min.css')}}"/>
  <link href='https://fonts.googleapis.com/css?family=ABeeZee' rel='stylesheet'>
</head>
<style>
  html{
    height:100%;
  }
  body{
    font-family: 'ABeeZee' !important;
  }

  .float{
    position:fixed;
    width:60px;
    height:60px;
    bottom:40px;
    right:40px;
    background-color:#0C9;
    color:#FFF;
    border-radius:50px;
    text-align:center;
    box-shadow: 2px 2px 3px #999;
    z-index: 99;
  }
</style>
<body style="background: linear-gradient(90deg, rgb(66 183 245) 0%, rgb(66 245 189 / 70%) 100%);">
  <button class="float" onclick="window.location.assign('{{route('home')}}')" style="border:none"><i class="fa fa-arrow-left" style="font-size: 40px;"></i></button>
  <div id="app">

   @yield('content')
   
 </div>
</body>
</html>
