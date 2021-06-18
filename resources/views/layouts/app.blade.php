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
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <link rel="stylesheet" href="{{ asset('bootstrap-4.0.0/css/bootstrap.min.css')}}"/>
  <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css')}}"/>
  <link rel="stylesheet" href="{{ asset('datatable/datatables.min.css')}}"/>
  <link href='https://fonts.googleapis.com/css?family=ABeeZee' rel='stylesheet'>

  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{ asset('iCheck/all.css') }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('iCheck/square/blue.css') }}">
  <!-- iCheck 1.0.1 -->
  <script src="{{ asset('iCheck/icheck.min.js') }}"></script>

  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}">
  <!-- Select2 -->
  <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>

  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-WZVW9DB');</script>
  <!-- End Google Tag Manager -->


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
    right:8px;
    background-color:#0C9;
    color:#FFF;
    border-radius:50px;
    text-align:center;
    box-shadow: 2px 2px 3px #999;
    z-index: 99;
  }
</style>
<body style="background: linear-gradient(90deg, rgb(66 183 245) 0%, rgb(66 245 189 / 70%) 100%);">

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WZVW9DB"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <button class="float" onclick="window.location.assign('{{ (isset($url)) ? $url : "" }}')" style="border:none">
    <i class="fa fa-arrow-left" style="font-size: 40px;"></i>
  </button>
  <div id="app">

   @yield('content')
   
 </div>
 <script>

 </script>
</body>
</html>
