<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('fontawesome/js/all.min.js') }}"></script>
    <script src="{{ asset('datatable/datatables.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}"/>
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css')}}"/>
    <link rel="stylesheet" href="{{ asset('datatable/datatables.min.css')}}"/>
</head>
<body>
    <div id="app">

         @yield('content')

    </div>
</body>
</html>
