<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Branch;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      $this->middleware(['auth', 'user_access']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
      $user = Auth::user();
      $branch = Branch::first();
      if(!$branch){
        $branch = new \stdClass();
        $branch->id = null;
      }

      $target = "na";
      if(isset($_GET['p'])){
        $target = $_GET['p'];
      }

      // $access_control = app('App\Http\Controllers\UserController')->checkAllAccessControl();

      return view('home',compact('user','branch','target'));
    }
  }
