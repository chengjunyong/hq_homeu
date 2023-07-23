@extends('layouts.app')
<title>Branch Sales Report</title>
@section('content')
<script src="{{ asset('datatable/datatables.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('datatable/datatables.min.css')}}"/>
<script src="{{ asset('js/md5.min.js') }}"></script>
<style>
  body{
    background: #f9fafb;
  }

  #sales_report{
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
    <h2 align="center">Stock Balance History</h2>
    <div class="card-body">
      <div class="row">
        <ol>
          @foreach($files->sortByDesc('created_at') as $file)
            <li>
              <a href="{{ $file['path']}}" targe="_blank">
                <label>{{ $file['name'] }}</label> 
              </a>
            </li>
          @endforeach
        </ol>

      </div>
    </div>
  </div>
</div>


@endsection