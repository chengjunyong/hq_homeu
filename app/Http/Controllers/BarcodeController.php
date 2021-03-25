<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\branch;

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
}
