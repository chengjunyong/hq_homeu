<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
class api extends Controller
{
    public function testresult()
    {
    	return "Good";
    }

    public function update(Request $request)
    {	
		  $result =	Branch::create([
		    		'id' => $request->branch_id,
		    		'branch_name' => 'testing',
		    	]);

    	return $result;
    }
}
