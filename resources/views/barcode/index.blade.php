<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-DEVX15R54N"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-DEVX15R54N');
  </script>
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

  .close { position: absolute; top: 10px; right: 20px; font-size: 30px; color: #666; cursor: pointer; }
  .floating_menu { position: fixed; right: 20px; bottom: 20px; }
  .menu_icon { width: 50px; height: 50px; border: 1px solid #ccc; border-radius: 50%; text-align: center; line-height: 50px; box-shadow: 1px 1px 5px 0px #999; background: #d2e8ff; cursor: pointer; }
  .menu_icon:hover { box-shadow: 1px 1px 10px 3px #999; }
  .menu_detail { display: none; position: absolute; left: -250px; width: 300px; text-align: left; bottom: 60px; line-height: 30px; border: 1px solid #ccc; background: #fff; }
  .menu_detail ul { list-style-type: none; padding: 0; margin: 0; }
  .menu_detail ul li:first-child a { padding-top: 10px; }
  .menu_detail ul li:last-child a { padding-bottom: 10px; }
  .menu_detail ul li a { display: block; cursor: pointer; padding: 5px 10px; cursor: pointer; color: #000; }
  .menu_detail ul li a:hover { text-decoration: none; }
  .menu_detail ul li:hover { background: #ccc; }
  .history { display: none; position: fixed; left: 0px; top: 0px; height: 100%; width: 100%; background: #fff; padding: 30px; }
  .history table { margin: auto; }
  .icheck label { cursor: pointer; }

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
            <label style="width: 100%;">Stock type</label>
            <div class='checkbox icheck' style="display: inline-block; margin-right: 20px;">
              <label>
                <input class='form-check-input icheck' type='radio' name='stock_type' value='branch' checked /> Branch
              </label>
            </div>

            <div class='checkbox icheck' style="display: inline-block; margin-right: 20px;">
              <label>
                <input class='form-check-input icheck' type='radio' name='stock_type' value='warehouse' /> Warehouse
              </label>
            </div>

            <hr/>
          </div>

          <div class="col-12" id="branch_list">
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

          <div class="col-12">
            <label>Department : </label>
            <select class="form-control" name="department">
              @foreach($department_list as $department)
                <option value={{ $department->id }}>{{ $department->department_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12">
            <label>Category : </label>
            <select class="form-control" name="category">
              @foreach($category_list as $category)
                <option style="display: none;" department_id="{{ $category->department_id }}" value={{ $category->id }}>{{ $category->category_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 form-group">
            <label>Stock count </label>
            <input type="number" class="form-control" name="stock_count" /> 
          </div>

          <div class="col-12 form-group">
            <input type="hidden" id="product_id" />
            <input type="hidden" id="stock_type" />
            <button type="button" class="btn btn-success" id="submit_stock" disabled>Submit</button>
            <br>
          </div>

        </div>
      </div>
    </div>

    <div id="quagga-scanner">
      <div class="close" id="close-quagga">
        <i class="fas fa-times"></i>
      </div>
    </div>

    <p id="result"></p>
  </div>

  <div class="floating_menu">
    <div class="menu_icon">
      <i class="fas fa-bars"></i>
    </div>
    <div class="menu_detail">
      <ul>
        <li>
          <select class="form-control" id="deviceSelection"></select>
        </li>
        <li><a href="{{ route('home') }}"> Homepage </a></li>
        <li><a href="#" id="show_history">History </a></li>
        <li><a href="#" id="logout">Logout </a></li>
      </ul>
    </div>
  </div>

  <form method="post" action="{{route('logout')}}" id="logout_form">
    @csrf
  </form>

  <div class="history" id="history_box">
    <div class="close" id="close_history">
      <i class="fas fa-times"></i>
    </div>

    <h4>Branch Check Stock History</h4>
    <table class="table table-responsive" id="history_table">
      <thead>
        <th>Branch</th>
        <th>Barcode</th>
        <th>Product Name</th>
        <th>Updated Stock</th>
        <th>Created at</th>
      </thead>
      <tbody>
        @foreach($branch_stock_history as $history)
          <tr>
            <td>{{ $history->branch_name }}</td>
            <td>{{ $history->barcode }}</td>
            <td>{{ $history->product_name }}</td>
            <td>{{ $history->new_stock_count }}</td>
            <td>{{ $history->created_at }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div style="float:right;margin-top: 5px">
      {{$branch_stock_history->links()}}
    </div>
  </div> 

</body>

<script>

  var scan_value = null;
  var cameraFeed = document.getElementById("quagga-scanner");
  var freeze = 0;
  var time;

  $(document).ready(function(){

    $('.form-check-input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });

    if (hasGetUserMedia())
    {
      var errorCallback = function(e) {
        Swal.fire(
          'Failed!',
          "Your block the permission of camera, please reset the permission to proceed.",
          'error'
        );
      };

      navigator.getUserMedia({ video: true, audio: false }, function(localMediaStream) {
        captureCamera();
        var deviceId = $("#deviceSelection option:first-child").attr("value");
        if(!deviceId)
        {
          deviceId = "";
        }
        initQuagga(deviceId);
        cameraFeed.getElementsByTagName("video")[0].pause();
      }, errorCallback);
    }
    else
    {
      Swal.fire(
        'Failed!',
        "Cannot get any data. Make sure your url is HTTPS",
        'error'
      );
    }

    $("#close-quagga").click(function(){
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
      var stock_type = $("input[name='stock_type']:checked").val();
      if(stock_type == "branch")
      {
        var selected_branch = $("select[name='branch']").val();
        if(selected_branch == 0)
        {
          alert("Please select branch before you proceed.");
          return;
        }
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

    $("#logout").click(function(){
      $("#logout_form").submit();
    });

    $("#close_history").click(function(){
      $("#history_box").hide();
    });

    $("#show_history").click(function(){
      $("#history_box").show();
    });

    $(".floating_menu").click(function(event){
      if($(".menu_detail").css("display") == "block" && !$(event.target).closest('.menu_detail').length)
      {
        $(".menu_detail").fadeOut();
      }
      else
      {
        $(".menu_detail").fadeIn();
      }
    });

    $("select[name=department]").change(function(){
      var department_id = $(this).val();
      $("select[name=category] option").hide();
      $("select[name=category] option[value=0]").show();
      $("select[name=category]").val(0);
      $("select[name=category] option[department_id="+department_id+"]").show();
    })

    $(document).click(function(event){
      console.log($(event.target).closest('.floating_menu').length);
      if (!$(event.target).closest('.floating_menu').length) {
        $(".menu_detail").fadeOut();
      }
    });

    $("input[name='stock_type']").on('ifChanged', function(){
      var stock_type = $(this).val();
      if(stock_type == "branch")
      {
        $("#branch_list").show();
      }
      else if(stock_type == "warehouse")
      {
        $("#branch_list").hide();
      }
    });

    $("#deviceSelection").on('change', function(){
      var deviceId = $(this).val();
      setTimeout(function(){
        $(".menu_detail").fadeOut();
      },100);
      Quagga.stop();
      initQuagga(deviceId);
    });

  });

  function initQuagga(deviceId)
  {
    var quaggaOption = {
      inputStream : {
        name : "Live",
        type : "LiveStream",
        target: document.querySelector('#quagga-scanner'),    // Or '#yourElement' (optional)
        constraints: {
          width: {min: 640},
          height: {min: 480},
          aspectRatio: {min: 1, max: 100},
          facingMode: "environment", // or user
          deviceId: deviceId
        }
      },
      decoder :{
        readers : ["code_128_reader", "ean_reader", "i2of5_reader"]
      },
      numOfWorkers: 2,
      frequency: 10,
      locate: true,
      locator :{
        halfSample: true,
        patchSize: "medium", // x-small, small, medium, large, x-large
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
    };

    console.log(quaggaOption);
    Quagga.init(quaggaOption, function(err) {
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

        clearTimeout(time);
        time = setTimeout(function(){checkProductBarcode(scan_value);},300);

        // checkProductBarcode(scan_value);
      }
    });
  }

  function checkProductBarcode(barcode)
  {
    var stock_type = $("input[name='stock_type']:checked").val();
    var selected_branch = "";
    if(stock_type == "branch")
    {
      selected_branch = $("select[name='branch']").val();
      if(selected_branch == 0)
      {
        alert("Please select branch before you proceed.");
        return;
      }
    }

    $.post("{{ route('getProductByBarcode') }}", {"_token" : "{{ csrf_token() }}", "barcode" : barcode, "stock_type" : stock_type, "branch_id" : selected_branch }, function(result){
      cameraFeed.getElementsByTagName("video")[0].pause();
      if(result.error == 0)
      {
        $("#quagga-scanner").hide();
        var product_detail = result.product_detail;
        $("#product_name").html(product_detail.product_name);
        $("#product_barcode").html(product_detail.barcode);
        $("#product_id").val(product_detail.id);
        $("#stock_type").val(result.stock_type);

        $("select[name='department']").val(product_detail.department_id);
        $("select[name='category']").val(product_detail.category_id);

        $("#submit_stock").attr("disabled", false);

        cameraFeed.getElementsByTagName("video")[0].load();
        freeze = 1;
        setTimeout(function(){
          freeze = 0;
        },300);
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
    var department_id = $("select[name='department']").val();
    var category_id = $("select[name='category']").val();
    var stock_type = $("#stock_type").val();

    if(stock_count == "")
    {
      alert("Stock count cannot be empty");
      return;
    }

    $.post("{{ route('updateBranchStockByScanner') }}", {"_token" : "{{ csrf_token() }}", "product_id" : product_id, "stock_count" : stock_count, "department_id" : department_id, "category_id" : category_id, "stock_type" : stock_type }, function(result){
      if(result.error == 0)
      {
        $("#product_name").html("");
        $("#product_barcode").html("");
        $("#product_id").val("");

        $("select[name='department']").val($("select[name='department'] option:first-child").val());
        $("select[name='category']").val($("select[name='category'] option:first-child").val());

        $("input[name='stock_count']").val("");
        scan_value = null;

        var product_detail = result.product_detail;
        var history = result.history;

        var html = "";
        html += "<tr>";
        html += "<td>"+result.branch_name+"</td>";
        html += "<td>"+product_detail.barcode+"</td>";
        html += "<td>"+product_detail.product_name+"</td>";
        html += "<td>"+stock_count+"</td>";
        html += "<td>"+history.created_at+"</td>";
        html += "</tr>";

        $("#history_table tbody").append(html);

        Swal.fire(
          'Success!',
          "<b>"+product_detail.product_name+"</b> stock was updated.",
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

  function captureCamera()
  {
    var streamLabel = Quagga.CameraAccess.getActiveStreamLabel();

    return Quagga.CameraAccess.enumerateVideoDevices()
    .then(function(devices) {
      function pruneText(text) {
        return text.length > 30 ? text.substr(0, 30) : text;
      }
      var $deviceSelection = document.getElementById("deviceSelection");
      while ($deviceSelection.firstChild) {
        $deviceSelection.removeChild($deviceSelection.firstChild);
      }
      devices.forEach(function(device) {
        var $option = document.createElement("option");
        $option.value = device.deviceId || device.id;
        $option.appendChild(document.createTextNode(pruneText(device.label || device.deviceId || device.id)));
        $option.selected = streamLabel === device.label;
        $deviceSelection.appendChild($option);
      });
    });
  }

  function hasGetUserMedia() {
    return !!(navigator.getUserMedia || navigator.webkitGetUserMedia ||
      navigator.mozGetUserMedia || navigator.msGetUserMedia);
  }

</script>

</html>