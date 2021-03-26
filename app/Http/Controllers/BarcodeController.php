<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Branch;
use App\Branch_product;
use App\Branch_stock_history;

class BarcodeController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth', 'user_access']);
    }

    public function getCheckStockPage()
    {
      $branch_list = branch::get();
      return view('barcode.index', compact('branch_list'));
    }

    public function getProductByBarcode(Request $request)
    {
      $product_detail = branch_product::where('branch_id', $request->branch_id)->where('barcode', $request->barcode)->first();
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

      $branch_product = branch_product::where('id', $request->product_id)->first();

      if(!$branch_product)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Branch product not found.";

        return response()->json($response);
      }

      branch_stock_history::create([
        'user_id' => $user->id,
        'branch_product_id' => $request->product_id,
        'barcode' => $branch_product->barcode,
        'product_name' => $branch_product->product_name,
        'old_stock_count' => $branch_product->quantity,
        'new_stock_count' => $request->stock_count,
        'difference_count' => ($branch_product->quantity - $request->stock_count)
      ]);

      branch_product::where('id', $request->product_id)->update([
        'quantity' => $request->stock_count
      ]);

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Success";
      $response->product_detail = $branch_product;

      return response()->json($response);
    }
}
