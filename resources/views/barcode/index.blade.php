<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>
  <script src="{{ asset('js/jquery.js') }}"></script>
  <script src="{{ asset('bootstrap-4.0.0/js/bootstrap.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('bootstrap-4.0.0/css/bootstrap.min.css')}}"/>
  <link href='https://fonts.googleapis.com/css?family=ABeeZee' rel='stylesheet'>
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{ asset('iCheck/all.css') }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('iCheck/square/blue.css') }}">
  <!-- iCheck 1.0.1 -->
  <script src="{{ asset('iCheck/icheck.min.js') }}"></script>

  <script src="{{ asset('fontawesome/js/all.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css')}}"/>

  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}">
  <!-- Select2 -->
  <script src="{{ asset('select2/js/select2.full.min.js') }}"></script>

  <script src="{{ asset('quagga/quagga.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

  <style>
    
  .scanner-body { margin : 20px; }
  #quagga-scanner { display: none; position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; padding: 50px 20px 20px 20px; background: #fff; }
  #quagga-scanner video { width: 100%; height: 100%; }
  #quagga-scanner canvas { display: none; }

  .close-quagga { position: absolute; top: 10px; right: 20px; font-size: 30px; color: #666; cursor: pointer; }

  </style>


</head>
<body style="background: #eee;">
  <div class="scanner-body">
    <div class="card">
      <div class="card-body">
        <div class="row">

          <div class="col-12" style="text-align: center;">
            <h4>Stock mangement</h4>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label>Branch</label>
              <select class="form-control" name="branch">
                <option value="0">Select branch</option>
                @foreach($branch_list as $branch)
                  <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-12">
            <a href="#" id="scan_again">Scan again</a>
          </div>
        </div>
      </div>
    </div>

    <div class="card" style="margin-top: 20px;">
      <div class="card-body">
        <div class="row" id="product_info">

          <div class="col-12">
            <label>Product name : </label>
            <label id="product_name"></label>
          </div>

          <div class="col-12">
            <label>Product barcode :</label>
            <label id="product_barcode"></label>
          </div>

          <div class="col-12 form-group">
            <label>Stock count </label>
            <input type="number" class="form-control" name="stock_count" /> 
          </div>

          <div class="col-12 form-group">
            <input type="hidden" id="product_id" />
            <button type="button" class="btn btn-success" id="submit_stock" disabled>Submit</button>
            <br>
            <a href="{{ route('home') }}">Back to home</a>
          </div>

        </div>
      </div>
    </div>

    <div id="quagga-scanner">
      <div class="close-quagga">
        <i class="fas fa-times"></i>
      </div>
    </div>

    <p id="result"></p>
  </div>

</body>

<script>

  var scan_value = null;
  var cameraFeed = document.getElementById("quagga-scanner");
  var freeze = 0;

  $(document).ready(function(){
    Quagga.init({
      inputStream : {
        name : "Live",
        type : "LiveStream",
        target: document.querySelector('#quagga-scanner'),    // Or '#yourElement' (optional)
      },
      decoder :{
        readers : ["code_128_reader"]
      },
      config :{
        numOfWorkers: 0,
        locate: true,
      },
      locator :{
        halfSample: true,
        patchSize: "x-large", // x-small, small, medium, large, x-large
        debug: {
          showCanvas: false,
          showPatches: false,
          showFoundPatches: false,
          showSkeleton: false,
          showLabels: false,
          showPatchLabels: false,
          showRemainingPatchLabels: false,
          boxFromPatches: {
            showTransformed: false,
            showTransformedBox: false,
            showBB: false,
          }
        }
      },
    }, function(err) {
        if (err) {
          console.log(err);
          return
        }
        console.log("Initialization finished. Ready to start");
        Quagga.start();
    });

    Quagga.onDetected(function(data){
      if(freeze == 1)
      {
        return;
      }
      else if(scan_value != data.codeResult.code)
      {
        scan_value = data.codeResult.code;
        checkProductBarcode(scan_value);
      }
    });

    cameraFeed.getElementsByTagName("video")[0].pause();

    $(".close-quagga").click(function(){
      $("#quagga-scanner").hide();
      
      cameraFeed.getElementsByTagName("video")[0].pause();
    });

    $("select[name='branch']").on('change', function(){
      var selected_branch = $("select[name='branch']").val();
      if(selected_branch == 0)
      {
        alert("Please select branch before you proceed.");
        return;
      }

      scan_value = null;
      cameraFeed.getElementsByTagName("video")[0].load();
      freeze = 1;
      setTimeout(function(){
        freeze = 0;
      },300);

      $("#quagga-scanner").show();
    });

    $("#scan_again").click(function(){
      var selected_branch = $("select[name='branch']").val();
      if(selected_branch == 0)
      {
        alert("Please select branch before you proceed.");
        return;
      }

      scan_value = null;
      $("#quagga-scanner").show();
      cameraFeed.getElementsByTagName("video")[0].load();
      freeze = 1;
      setTimeout(function(){
        freeze = 0;
      },300);
    });

    $("#submit_stock").click(function(){
      submitStock();
    });

  });

  function checkProductBarcode(barcode)
  {
    var selected_branch = $("select[name='branch']").val();
    if(selected_branch == 0)
    {
      alert("Please select branch before you proceed.");
      return;
    }

    $.post("{{ route('getProductByBarcode') }}", {"_token" : "{{ csrf_token() }}", "barcode" : barcode, "branch_id" : selected_branch }, function(result){
      cameraFeed.getElementsByTagName("video")[0].pause();
      if(result.error == 0)
      {
        $("#quagga-scanner").hide();
        var product_detail = result.product_detail;
        $("#product_name").html(product_detail.product_name);
        $("#product_barcode").html(product_detail.barcode);
        $("#product_id").val(product_detail.id);

        $("#submit_stock").attr("disabled", false);
      }
      else
      {
        Swal.fire(
          'Failed!',
          "Barcode "+barcode+" not found in the system.",
          'error'
        ).then((result) => {
          scan_value = null;
          cameraFeed.getElementsByTagName("video")[0].load();
          freeze = 1;
          setTimeout(function(){
            freeze = 0;
          },300);
        });
      }
    });
  }

  function submitStock()
  {
    var stock_count = $("input[name='stock_count']").val();
    var product_id = $("#product_id").val();

    if(stock_count == "")
    {
      alert("Stock count cannot be empty");
      return;
    }

    $.post("{{ route('updateBranchStockByScanner') }}", {"_token" : "{{ csrf_token() }}", "product_id" : product_id, "stock_count" : stock_count }, function(result){
      if(result.error == 0)
      {
        $("#product_name").html("");
        $("#product_barcode").html("");
        $("#product_id").val("");

        $("input[name='stock_count']").val("");
        scan_value = null;

        Swal.fire(
          'Success!',
          "<b>"+result.product_detail.product_name+"</b> stock was updated.",
          'success'
        ).then((result) => {
          if (result.isConfirmed) {
            $("#scan_again").click();
          }
        });
      }
      else
      {
        alert(result.message);
      }
    }).fail(function(){
      Swal.fire(
        'Failed!',
        "Something wrong, please refresh the page.",
        'error'
      );
    });
  }

</script>

</html>