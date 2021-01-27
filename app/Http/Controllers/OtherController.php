<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;

class OtherController extends Controller
{
    public function getBranch()
    {
    	$branch = Branch::get();

    	return view('branch',compact('branch'));
    }

    public function createBranch(Request $request)
    {
    	Branch::create([
    		'branch_name' => $request->branch_name,
    		'address' => $request->address,
    		'contact' => $request->contact_number,
    		'token' => $request->token
    	]);

    	return back()->with('success','Create Successful');
    }
}
