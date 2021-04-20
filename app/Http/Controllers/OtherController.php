<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Branch_product;
use App\Product_list;
use App\Transaction;
use App\Transaction_detail;
use App\Product_configure;
use App\Do_detail;
use App\Do_configure;
use App\Do_list;
use App\Damaged_stock_history;
use App\Stock_lost_history;
use App\Supplier;
use Illuminate\Support\Facades\DB;

class OtherController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'user_access']);
  }
    
	public function getSupplier()
  {
    $url = route('home')."?p=other_menu";

    if(isset($_GET['search']) && $_GET['search'] != ""){
      $search = 0;
      $supplier = Supplier::where('supplier_name','LIKE','%'.$_GET['search'].'%')
                                          ->get();
    }else{
      $supplier = Supplier::paginate(25); 
      $search = 1;
    }

  	return view('supplier_list',compact('url','supplier','search'));
	}

	public function getEditSupplier(Request $request)
	{
		$url = route('getSupplier');

		$supplier = Supplier::where('supplier_code',$request->id)->first();

		return view('edit_supplier',compact('url','supplier'));


	}

	public function postEditSupplier(Request $request)
	{
		$address1 = ($request->address1 == null) ? 'null' : $request->address1; 
		$address2 = ($request->address2 == null) ? 'null' : $request->address2;
		$address3 = ($request->address3 == null) ? 'null' : $request->address3;
		$address4 = ($request->address4 == null) ? 'null' : $request->address4;
		$email = ($request->email == null) ? 'null' : $request->email;

		$supplier = Supplier::where('supplier_code',$request->supplier_code)
													->update([
														'supplier_name' => $request->supplier_name,
														'contact_number' => $request->contact_number,
														'email' => $email,
														'address1' => $address1,
														'address2' => $address2,
														'address3' => $address3,
														'address4' => $address4,
													]);

		return response()->json($supplier);
	}

	public function getCreateSupplier()
	{
		$url = route('getSupplier');

		return view('create_supplier',compact('url'));
	}

	public function postCreateSupplier(Request $request)
	{
		try{
			$address1 = ($request->address1 == null) ? 'null' : $request->address1; 
			$address2 = ($request->address2 == null) ? 'null' : $request->address2;
			$address3 = ($request->address3 == null) ? 'null' : $request->address3;
			$address4 = ($request->address4 == null) ? 'null' : $request->address4;
			$email = ($request->email == null) ? 'null' : $request->email;
			Supplier::create([
				'supplier_code' => $request->supplier_code,
				'supplier_name' => $request->supplier_name,
				'contact_number' => $request->contact_number,
				'email' => $email,
				'address1' => $address1,
				'address2' => $address2,
				'address3' => $address3,
				'address4' => $address4,
			]);

			return response()->json(true);

		}catch(Throwable $e){

			return $e;
		}
	}
	
}
