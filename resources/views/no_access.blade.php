<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Error 404 - Not Found</title>
  <meta name="viewport" content="width=device-width">

  <script src="{{ asset('js/jquery.js') }}"></script>
  <script src="{{ asset('bootstrap-4.0.0/js/bootstrap.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('bootstrap-4.0.0/css/bootstrap.min.css')}}"/>

  <style>
    *{ transition: all 0.6s; }

    body{ font-family: 'Lato', sans-serif; color: #888; margin: 0; }

    #main{ display: table; width: 100%; height: 100vh; text-align: center; }

    .fof{ display: table-cell; vertical-align: middle; }

    .fof h1{ font-size: 50px; display: inline-block; padding-right: 12px; animation: type .5s alternate infinite; }

    @keyframes type{
      from{box-shadow: inset -3px 0px 0px #888;}
      to{box-shadow: inset -3px 0px 0px transparent;}
    }

  </style>
</head>
<body>
  
  <div id="main">
    <div class="fof">
      <h1>Error 404</h1>
      <p>Page not found.</p>
      <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
    </div>
  </div>

</body>
</html>