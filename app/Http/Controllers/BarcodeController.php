<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Branch;
use App\Branch_product;
use App\Branch_stock_history;
use App\Department;
use App\Category;
use App\Warehouse_stock;

class BarcodeController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth', 'user_access']);
    }

    public function getCheckStockPage()
    {
      $branch_list = Branch::get();
      $department_list = Department::get();
      $category_list = Category::get();

      $barcode_type = [
        'ean_reader',
        'ean_8_reader',
        'upc_reader',
        'upc_e_reader',
        'code_39_reader',
        'code_39_vin_reader',
        'codabar_reader',
        'code_93_reader',
        'code_128_reader',
        'i2of5_reader',
        '2of5_reader'
      ];

      $user = Auth::user();
      $branch_stock_history = Branch_stock_history::where('branch_stock_history.user_id', $user->id)->leftJoin('branch', 'branch_stock_history.branch_id', '=', 'branch.id')->select('branch_stock_history.*', 'branch.branch_name')->paginate(15);

      return view('barcode.index', compact('branch_list', 'branch_stock_history', 'department_list', 'category_list', 'barcode_type'));
    }

    public function getProductByBarcode(Request $request)
    {
      $product_detail = null;
      $stock_type = $request->stock_type;
      if($stock_type == "branch")
      {
        $product_detail = Branch_product::where('branch_id', $request->branch_id)->where('barcode', $request->barcode)->first();
      }
      elseif($stock_type == "warehouse")
      {
        $product_detail = Warehouse_stock::where('barcode', $request->barcode)->first();
      }
      
      if(!$product_detail)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Product not found";

        return response()->json($response);
      }
      else
      {
        $response = new \stdClass();
        $response->product_detail = $product_detail;
        $response->stock_type = $stock_type;
        $response->error = 0;
        $response->message = "Product found.";

        return response()->json($response);
      }
    }

    public function updateBranchStockByScanner(Request $request)
    {
      if(!$request->product_id)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Product ID not found.";

        return response()->json($response);
      }

      if(!$request->stock_count)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Stock count cannot be empty.";

        return response()->json($response);
      }

      $user = Auth::user();
      if(!$user)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "User not found.";

        return response()->json($response);
      }

      if(!$request->stock_type)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Stock type cannot be empty.";

        return response()->json($response);
      }

      $branch_product = null;
      $stock_type = $request->stock_type;
      if($stock_type == "branch")
      {
        $branch_product = Branch_product::where('id', $request->product_id)->first();
      }
      elseif($stock_type == "warehouse")
      {
        $branch_product = Warehouse_stock::where('id', $request->product_id)->first();
      }
      
      if(!$branch_product)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Branch product not found.";

        return response()->json($response);
      }

      $branch_id = null;
      $branch_token = null;
      $branch_name = "";

      if($stock_type == "branch")
      {
        $branch_detail = Branch::where('id', $branch_product->branch_id)->first();
        if($branch_detail)
        {
          $branch_id = $branch_detail->id;
          $branch_token = $branch_detail->token;
          $branch_name = $branch_detail->branch_name;
        }
      }

      $history = Branch_stock_history::create([
        'user_id' => $user->id,
        'user_name' => $user->name,
        'stock_type' => $stock_type,
        'branch_id' => $branch_id,
        'branch_token' => $branch_token,
        'branch_product_id' => $request->product_id,
        'department_id' => $request->department_id,
        'category_id' => $request->category_id,
        'barcode' => $branch_product->barcode,
        'product_name' => $branch_product->product_name,
        'old_stock_count' => $branch_product->quantity,
        'new_stock_count' => $request->stock_count,
        'difference_count' => ($branch_product->quantity - $request->stock_count)
      ]);

      if($stock_type == "branch")
      {
        Branch_product::where('id', $request->product_id)->update([
          'quantity' => $request->stock_count,
          'department_id' => $request->department_id,
          'category_id' => $request->category_id,
          'last_stock_updated_at' => date('Y-m-d H:i:s')
        ]);
      }
      elseif($stock_type == "warehouse")
      {
        Warehouse_stock::where('id', $request->product_id)->update([
          'quantity' => $request->stock_count,
          'department_id' => $request->department_id,
          'category_id' => $request->category_id
        ]);
      }

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Success";
      $response->product_detail = $branch_product;
      $response->branch_name = $branch_name;
      $response->history = $history;

      return response()->json($response);
    }
}
