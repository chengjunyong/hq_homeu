<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Branch;
use App\Mail\SendMail;

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

    public function sendEmail($email, $information)
    {
      $email_cc = null;

      if(isset($information['cc']))
      {
        $email_cc = $information['cc'];
      }

      $mail = Mail::to($email);

      if($email_cc)
      {
        $mail->cc($email_cc);
      }
    
      $mail->send(new SendMail($information));
    }

    public function testMail()
    {
      $email = "ycheng391@gmail.com";
      $info = [
        "message" => "",
        "files" => [
          "image/hq_icon.png"
        ],
        "to" => "Ethan",
        "from" => "Home(U) Sdh Bhd",
      ];

      $this->sendEmail($email, $info);

      dd("sent");
    }
  }
