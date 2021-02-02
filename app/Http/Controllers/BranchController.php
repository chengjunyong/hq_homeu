<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Branch_product;
use App\Product_list;

class BranchController extends Controller
{
  public function getBranch()
    {
    	$branch = Branch::paginate(6);

    	return view('branch',compact('branch'));
    }

    public function createBranch(Request $request)
    {

    	$branch_id = Branch::create([
						    		'branch_name' => $request->branch_name,
						    		'address' => $request->address,
						    		'contact' => $request->contact_number,
						    		'token' => $request->token
						    	]);

    	// $start = microtime(true);

    	$product_list = Product_list::select('department_id','category_id','barcode','product_name','cost','price','quantity','reorder_level','recommend_quantity','unit_type')
    															->get()
    															->toArray();
			foreach($product_list as $key => $result){
				$product_list[$key]['branch_id'] = $branch_id->id;
				$product_list[$key]['created_at'] = date('Y-m-d H:i:s');
				$product_list[$key]['updated_at'] = date('Y-m-d H:i:s');
			}
			$product_list = array_chunk($product_list,500);
			foreach($product_list as $result){
    		$q = Branch_product::insert($result);
			}

    	// $time = microtime(true) - $start;

    	if($q == true){
    		return "true";
    	}else{
    		return "false";
    	}
    }
}
