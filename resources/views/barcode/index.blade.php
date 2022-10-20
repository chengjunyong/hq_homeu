<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <title>Barcode Stock Checking</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-WZVW9DB');</script>
  <!-- End Google Tag Manager -->

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
    
  .header { display: flex; background: #fff; height: 70px; align-items: center; }
  .header_menu_icon { font-size: 20px; padding: 20px; cursor: pointer; }
  .header_menu_icon:hover { background: #eee; }
  .header_title { text-align: center; padding-right: 57.5px; text-align: center; flex: 1; }
  .header_menu { position: fixed; width: 350px; height: 100%; overflow-y: scroll; left: -350px; top: 0px; background: #eee; z-index: 2; transition: left 300ms linear; }
  .header_menu.active { left: 0px; }

  .scanner-body { margin : 20px; }
  .quagga-box { display: none; position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; padding: 50px 20px; background: #fff; text-align: center; }
  #interactive { position: relative; max-width: 640px; max-height: 480px; margin: auto; }
  #interactive video, #interactive canvas { float: left; width: 100%; height: 100%; }
  #interactive canvas { position: absolute; left: 0px; top: 0px; }

  .close { position: absolute; top: 10px; right: 20px; font-size: 30px; color: #666; cursor: pointer; }
  .floating_menu { position: fixed; right: 20px; bottom: 50px; }
  .scan_icon { width: 50px; height: 50px; border: 1px solid #ccc; border-radius: 50%; text-align: center; line-height: 50px; box-shadow: 1px 1px 5px 0px #999; background: #d2e8ff; cursor: pointer; }
  .scan_icon:hover { box-shadow: 1px 1px 10px 3px #999; }
  .menu_detail ul { list-style-type: none; padding: 0; margin: 0; }
  .menu_detail ul li { border-bottom: 1px solid #ccc; padding-bottom: 10px; }
  .menu_detail ul li:first-child a { padding-top: 10px; }
  .menu_detail ul li a { display: block; cursor: pointer; padding: 5px 10px 0px 10px; cursor: pointer; color: #000; }
  .menu_detail ul li a:hover { text-decoration: none; }
  .menu_detail ul li label { padding: 5px 10px; margin: 0px; }
  .menu_detail ul li select { margin: 0 20px; width: calc(100% - 40px); }
  .menu_detail ul li:hover { background: #ccc; }
  .menu_detail ul li.dark:hover { background: #eee; }
  .history { display: none; position: fixed; left: 0px; top: 0px; height: 100%; width: 100%; background: #fff; padding: 30px; }
  .history table { margin: auto; }
  .icheck label { cursor: pointer; }

  .black_panel { display: none; position: fixed; left: 0%; top: 0px; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 1; cursor: pointer; }

  </style>


</head>
<body style="background: #eee;">
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WZVW9DB"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <div class="header">
    <div class="header_menu_icon">
      <i class="fas fa-bars"></i>
    </div>
    <div class="header_menu">
      <div class="menu_detail">
        <ul>
          <li>
            <a href="#" id="close_menu">
              <i class="fas fa-chevron-left"></i>
            </a>
          </li>
          <li><a href="{{ route('home') }}"> Homepage </a></li>
          <li><a href="#" id="show_history">History </a></li>
          <li>
            <label>Camera</label>
            <select class="form-control" id="deviceSelection"></select>
          </li>
          <li class="dark">
            <label>Barcode type</label>
            @foreach($barcode_type as $type)
              <div class="barcode_type">
                <div class='checkbox icheck' style="display: block;">
                  <label style="width: 100%;">
                    <input class='form-check-input icheck barcode_type' type='checkbox' name='barcode_type[]' value='{{ $type }}' /> {{ $type }}
                  </label>
                </div>
              </div>
            @endforeach
          </li>
          <li>
            <label>Patch Size</label>
            <select class="form-control" id="patchSize">
              <option value="x-small">x-small</option>
              <option value="small">small</option>
              <option value="medium" selected>medium</option>
              <option value="large">large</option>
              <option value="x-large">x-large</option>
            </select>
          </li>
          <li><a href="#" id="logout">Logout </a></li>
        </ul>
      </div>
    </div>
    <div class="header_title">
      <h4>Stock mangement</h4>
    </div>
  </div>

  <div class="scanner-body">
    <div class="card">
      <div class="card-body">
        <div class="row">
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
        </div>
      </div>
    </div>

    <div class="card" style="margin-top: 20px;">
      <div class="card-body">
        <div class="row" id="product_info">

          <div class="col-12">
            <label>Barcode</label>
            <input type="text" class="form-control" id="manual_barcode" />
            <button type="button" id="manual_barcode_btn" class="btn btn-primary" style="margin-top: 10px;">Search</button>
            <hr>
          </div>
          <div class="col-12">
            <label>Product name : </label>
            <label id="product_name"></label>
          </div>

          <div class="col-12">
            <label>Product barcode :</label>
            <label id="product_barcode"></label>
          </div>

          <div class="col-12">
            <label>Product measurement :</label>
            <label style="text-transform: capitalize;" id="product_measurement"></label>
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
            <label>Reorder Level </label>
            <input type="number" class="form-control" name="reorder_level" /> 
          </div>

          <div class="col-12 form-group">
            <label>Recommend Quantity </label>
            <input type="number" class="form-control" name="recommend_qty" /> 
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

    <div class="quagga-box" id="quagga-box">
      <div class="close" id="close-quagga">
        <i class="fas fa-times"></i>
      </div>
      <div id="interactive" class="viewport"></div>
    </div>

    <p id="result"></p>
  </div>

  <div class="floating_menu">
    <div class="scan_icon">
      <i class="fas fa-camera"></i>
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
    <div class="table-responsive">
      <table class="table" id="history_table">
        <thead style="width: 100%;">
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
  </div> 

  <div class="black_panel"></div>

</body>

<script>

  var scan_value = null;
  var cameraFeed = document.getElementById("interactive");
  var freeze = 0;
  var deviceId = "";
  var barcode_type = ["code_128_reader", "ean_reader", "i2of5_reader"];
  var patchSize = "medium";
  var time;
  var product_detail = null;

  $(document).ready(function(){

    $('.form-check-input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });

    for(var a = 0; a < barcode_type.length; a++)
    {
      $("input.barcode_type[value="+barcode_type[a]+"]").iCheck('check');
    }

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
        setTimeout(function(){
          deviceId = $("#deviceSelection option:first-child").attr("value");
          if(deviceId)
          {
            initQuagga();
            cameraFeed.getElementsByTagName("video")[0].pause();
          }
        }, 500);
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
      $("#quagga-box").hide();
      
      cameraFeed.getElementsByTagName("video")[0].pause();
    });

    $("select[name='branch']").on('change', function(){
      var selected_branch = $("select[name='branch']").val();
      if(selected_branch == 0)
      {
        Swal.fire(
          'Failed!',
          "Please select branch before you proceed.",
          'error'
        );
        return;
      }

      scan_value = null;
      if(!deviceId)
      {
        deviceId = $("#deviceSelection option:first-child").attr("value");
        initQuagga();
      }
      cameraFeed.getElementsByTagName("video")[0].load();
      freeze = 1;
      setTimeout(function(){
        freeze = 0;
      },300);

      $("#quagga-box").show();
    });

    $(".scan_icon").click(function(){
      var stock_type = $("input[name='stock_type']:checked").val();
      if(stock_type == "branch")
      {
        var selected_branch = $("select[name='branch']").val();
        if(selected_branch == 0)
        {
          Swal.fire(
            'Failed!',
            "Please select branch before you proceed.",
            'error'
          );
          return;
        }
      }

      if(!deviceId)
      {
        deviceId = $("#deviceSelection option:first-child").attr("value");
        initQuagga();
      }
    
      scan_value = null;
      $("#quagga-box").show();
      cameraFeed.getElementsByTagName("video")[0].load();
      freeze = 1;
      setTimeout(function(){
        freeze = 0;
      },300);
    });

    $("#submit_stock").click(function(){
      $("#submit_stock").attr("disabled", true);
      submitStock();
    });

    $("#logout").click(function(){
      $("#logout_form").submit();
    });

    $("#close_history").click(function(){
      $("#history_box").hide();
    });

    $("#show_history").click(function(){
      $(".header_menu").removeClass("active");
      $(".black_panel").fadeOut();
      $("#history_box").show();
    });

    $("select[name=department]").change(function(){
      var department_id = $(this).val();
      $("select[name=category] option").hide();
      $("select[name=category] option[value=0]").show();
      $("select[name=category]").val(0);
      $("select[name=category] option[department_id="+department_id+"]").show();
    })

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
      deviceId = $(this).val();
      Quagga.stop();
      initQuagga();
    });

    $("#patchSize").on('change', function(){
      patchSize = $(this).val();
      Quagga.stop();
      initQuagga();
    });

    $("input.barcode_type").on('ifChanged', function(){
      barcode_type = [];
      $("input.barcode_type").each(function(){
        if($(this).is(":checked"))
        {
          barcode_type.push($(this).val());
        }
      });

      Quagga.stop();
      initQuagga();
    });

    $(".header_menu_icon").click(function(){
      $(".header_menu").addClass("active");
      $(".black_panel").fadeIn();
    });

    $(".black_panel, #close_menu").click(function(){
      $(".header_menu").removeClass("active");
      $(".black_panel").fadeOut();
    });

    $("#manual_barcode").on('keyup', function(e){
      if(e.which == 13)
      {
        checkProductBarcode($(this).val());
      }
    });

    $("#manual_barcode_btn").click(function(){
      checkProductBarcode($("#manual_barcode").val());
    });

    $("input[name='stock_count']").on('keyup', function(){
      if(product_detail)
      {
        if(product_detail.measurement == "kilogram" || product_detail.measurement == "meter")
        {
          limitDecimal($(this), 3);
        }
        else
        {
          var new_val = parseInt($(this).val());
          $(this).val(new_val); 
        }
      }
      else
      {
        var new_val = parseInt($(this).val());
        $(this).val(new_val); 
      }
    });

  });

  function initQuagga()
  {
    var quaggaOption = {
      inputStream : {
        name : "Live",
        type : "LiveStream",
        target: document.querySelector('#interactive'),    // Or '#yourElement' (optional)
        constraints: {
          width: {min: 640},
          height: {min: 480},
          aspectRatio: {min: 1, max: 100},
          facingMode: "environment", // or user
          deviceId: deviceId
        }
      },
      decoder :{
        readers : barcode_type
      },
      numOfWorkers: 2,
      frequency: 10,
      locate: true,
      locator :{
        halfSample: true,
        patchSize: patchSize, // x-small, small, medium, large, x-large
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

    Quagga.init(quaggaOption, function(err) {
        if (err) {
          console.log(err);
          return
        }
        console.log("Initialization finished. Ready to start");
        Quagga.start();
    });

    Quagga.onProcessed(function(result) {
      var drawingCtx = Quagga.canvas.ctx.overlay,
        drawingCanvas = Quagga.canvas.dom.overlay;

      if (result) {
        if (result.boxes) {
          drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
          result.boxes.filter(function (box) {
            return box !== result.box;
          }).forEach(function (box) {
            Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
          });
        }

        if (result.box) {
          Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
        }

        if (result.codeResult && result.codeResult.code) {
          Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
        }
      }
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
        Swal.fire(
          'Failed!',
          "Please select branch before you proceed.",
          'error'
        );
        return;
      }
    }

    $.post("{{ route('getProductByBarcode') }}", {"_token" : "{{ csrf_token() }}", "barcode" : barcode, "stock_type" : stock_type, "branch_id" : selected_branch }, function(result){
      try {
        cameraFeed.getElementsByTagName("video")[0].pause();
      }
      catch(err) {
        console.log(err.message);
      }
      
      if(result.error == 0)
      {
        $("#quagga-box").hide();
        product_detail = result.product_detail;
        $("#product_name").html(product_detail.product_name);
        $("#product_barcode").html(product_detail.barcode);
        $("#product_id").val(product_detail.id);

        $("#product_measurement").html("");
        if(product_detail.measurement)
        {
          $("#product_measurement").html(product_detail.measurement);
        }

        $("#stock_type").val(result.stock_type);

        $("select[name='department']").val(product_detail.department_id);
        $("select[name='category']").val(product_detail.category_id);

        $("input[name='reorder_level']").val(product_detail.reorder_level);

        if(result.stock_type == "warehouse"){
          $("input[name='recommend_qty']").val(product_detail.reorder_quantity);
        }else{
          $("input[name='recommend_qty']").val(product_detail.recommend_quantity);
        }
        
        $("#submit_stock").attr("disabled", false);
        $("#manual_barcode").val("");
        $("input[name='stock_count']").val("");

        try {
          cameraFeed.getElementsByTagName("video")[0].load();
        }
        catch(err) {
          console.log(err.message);
        }

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
    var reorder_level = $("input[name='reorder_level']").val();
    var recommend_qty = $("input[name='recommend_qty']").val();

    if(stock_count == "")
    {
      Swal.fire(
        'Failed!',
        "Stock count cannot be empty.",
        'error'
      );

      $("#submit_stock").attr("disabled", false);
      return;
    }

    $.post("{{ route('updateBranchStockByScanner') }}", {"_token" : "{{ csrf_token() }}", "product_id" : product_id, "stock_count" : stock_count, "department_id" : department_id, "category_id" : category_id, "stock_type" : stock_type, "reorder_level" : reorder_level, "recommend_qty" : recommend_qty }, function(result){
      $("#submit_stock").attr("disabled", false);
      if(result.error == 0)
      {
        $("#product_name").html("");
        $("#product_barcode").html("");
        $("#product_id").val("");

        $("select[name='department']").val($("select[name='department'] option:first-child").val());
        $("select[name='category']").val($("select[name='category'] option:first-child").val());

        $("input[name='stock_count']").val("");
        scan_value = null;

        product_detail = result.product_detail;
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

        product_detail = null;
      }
      else
      {
        Swal.fire(
          'Failed!',
          result.message,
          'error'
        );
      }
    }).fail(function(){
      $("#submit_stock").attr("disabled", false);
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

  function limitDecimal(_this, total_decimal)
  {
    var number = _this.val();
    if(number.includes("."))
    {
      let split_number = number.split(".");
      let decimal = split_number[1];
      if(decimal.length > total_decimal)
      {
        let new_decimal = decimal.substring(0, (total_decimal + 1));
        new_decimal = "0."+new_decimal;
        new_decimal = parseFloat(new_decimal).toFixed(total_decimal);
        new_decimal_array = new_decimal.split(".");
        let new_number = split_number[0]+"."+new_decimal_array[1];

        _this.val(new_number);
      }
    }
  }

</script>

</html>