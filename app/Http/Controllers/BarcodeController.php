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
      $branch_list = Branch::get();

      $user = Auth::user();
      $branch_stock_history = Branch_stock_history::where('Branch_stock_history.user_id', $user->id)->leftJoin('branch', 'Branch_stock_history.branch_id', '=', 'Branch.id')->select('Branch_stock_history.*', 'Branch.branch_name')->paginate(15);

      return view('barcode.index', compact('branch_list', 'branch_stock_history'));
    }

    public function getProductByBarcode(Request $request)
    {
      $product_detail = Branch_product::where('branch_id', $request->branch_id)->where('barcode', $request->barcode)->first();
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

      $branch_product = Branch_product::where('id', $request->product_id)->first();

      if(!$branch_product)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Branch product not found.";

        return response()->json($response);
      }

      $branch_detail = Branch::where('id', $branch_product->branch_id)->first();
      $branch_id = null;
      $branch_token = null;

      if($branch_detail)
      {
        $branch_id = $branch_detail->id;
        $branch_token = $branch_detail->token;
      }

      $history = Branch_stock_history::create([
        'user_id' => $user->id,
        'user_name' => $user->name,
        'branch_id' => $branch_id,
        'branch_token' => $branch_token,
        'branch_product_id' => $request->product_id,
        'barcode' => $branch_product->barcode,
        'product_name' => $branch_product->product_name,
        'old_stock_count' => $branch_product->quantity,
        'new_stock_count' => $request->stock_count,
        'difference_count' => ($branch_product->quantity - $request->stock_count)
      ]);

      Branch_product::where('id', $request->product_id)->update([
        'quantity' => $request->stock_count
      ]);

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Success";
      $response->product_detail = $branch_product;
      $response->branch_detail = $branch_detail;
      $response->history = $history;

      return response()->json($response);
    }
}
