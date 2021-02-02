<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class api extends Controller
{
    public function testresult()
    {
    	return "Good";
    }

    public function receive(Request $request)
    {	
    	return $request;
    }
}
