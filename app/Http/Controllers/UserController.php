<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\user_access_control;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    public function getUserAccessControl()
    {
      $url = route('home')."?p=other_menu";
      
      $user_list = User::leftJoin('user_access_control', 'user_access_control.user_id', '=', 'users.id')->select('users.*', 'user_access_control.access_control')->get();

      foreach($user_list as $user_detail)
      {
        $user_detail->user_type_text = $this->userTypeText($user_detail->user_type);
      }

      $user_group = $this->userGroup();
      $user_access_control = $this->UserAccessControl();
      $default_access_control = $this->defaultAccessControl();

      return view('user_access_control', compact('user_list', 'user_group', 'user_access_control', 'default_access_control', 'url'));
    }

    public function createNewUser(Request $request)
    {
      $user_check = User::where('username', $request->new_user_username)->first();

      if($user_check)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Username has been used, please user another username.";

        return response()->json($response);
      }

      $user_detail = User::create([
        'user_type' => $request->new_user_role,
        'name' => $request->new_user_name,
        'username' => $request->new_user_username,
        'email' => uniqid()."@test.com",
        'password' => Hash::make($request->new_user_password),
      ]);

      $access = "";
      if($request->new_access_control)
      {
        if(count($request->new_access_control) > 0)
        {
          foreach($request->new_access_control as $access_control)
          {
            $access .= $access_control.",";
          }

          $access = substr($access, 0, -1);
        }
      }

      user_access_control::create([
        'user_id' => $user_detail->id,
        'access_control' => $access
      ]);

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Success.";

      return response()->json($response);
    }

    public function editUser(Request $request)
    { 
      $user_check = User::where('username', $request->edit_user_username)->where('id', '<>', $request->edit_user_id)->first();
      if($user_check)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Username has been used, please user another username.";

        return response()->json($response);
      }

      $edit_query = [
        'name' => $request->edit_user_name,
        'user_type' => $request->edit_user_role,
        'username' => $request->edit_user_username
      ];

      if($request->edit_user_password)
      {
        $edit_query['password'] = Hash::make($request->edit_user_password);
      }

      User::where('id', $request->edit_user_id)->update($edit_query);

      $access = "";
      if($request->access_control)
      {
        if(count($request->access_control) > 0)
        {
          foreach($request->access_control as $access_control)
          {
            $access .= $access_control.",";
          }

          $access = substr($access, 0, -1);
        }
      }

      user_access_control::updateOrCreate([
        'user_id' => $request->edit_user_id
      ],[
        'access_control' => $access
      ]);

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Success.";

      return response()->json($response);
    }

    public function userGroup()
    {
      $group = [
        [
          'value' => 1,
          'name' => 'Admin'
        ],
        [
          'value' => 2,
          'name' => 'Staff',
        ],
        [
          'value' => 3,
          'name' => 'Driver'
        ]
      ];

      return $group;
    }

    public function userTypeText($value)
    {
      $group_list = $this->userGroup();

      foreach($group_list as $group)
      {
        if($value == $group['value'])
        {
          return $group['name'];
        }
      }

      return "Admin ( Default )";
    }

    public function UserAccessControl()
    {
      $access_control = [
        [
          'group' => 'Branch',
          'value' => 1,
          'name' => 'Branch Setup',
          'route' => 'getBranch'
        ],
        [
          'group' => 'Branch',
          'value' => 2,
          'name' => 'Branch Stock Checklist',
          'route' => 'getBranchStockList'
        ],
        [
          'group' => 'Branch',
          'value' => 3,
          'name' => 'Branch Restock List',
          'route' => 'getBranchRestock'
        ],
        [
          'group' => 'Branch',
          'value' => 4,
          'name' => 'Delivery Order History',
          'route' => 'getDoHistory'
        ],
        [
          'group' => 'Branch',
          'value' => 5,
          'name' => 'Restock Confirmation',
          'route' => 'getRestocklist'
        ],
        [
          'group' => 'Branch',
          'value' => 6,
          'name' => 'Branch Restock History',
          'route' => 'getRestockHistory'
        ],
        [
          'group' => 'Branch',
          'value' => 7,
          'name' => 'Damaged Stock List',
          'route' => 'getDamagedStock'
        ],
        [
          'group' => 'Branch',
          'value' => 8,
          'name' => 'Stock Lost List',
          'route' => 'getStockLost'
        ],
        [
          'group' => 'Product',
          'value' => 9,
          'name' => 'Product Check List',
          'route' => 'getProductList'
        ],
        [
          'group' => 'Product',
          'value' => 10,
          'name' => 'Add Product',
          'route' => 'getAddProduct'
        ],
        [
          'group' => 'Product',
          'value' => 11,
          'name' => 'Product Price & Cost Setting',
          'route' => 'getProductConfig'
        ],
        [
          'group' => 'Sales',
          'value' => 12,
          'name' => 'Sales Report',
          'route' => 'getSalesReport'
        ],
        [
          'group' => 'Sales',
          'value' => 13,
          'name' => 'Daily Report',
          'route' => 'getDailyReport'
        ],
        [
          'group' => 'Sales',
          'value' => 14,
          'name' => 'Branch Sales Report',
          'route' => 'getBranchReport'
        ],
      ];

      return $access_control;
    }

    public function checkAccessControl()
    {
      $user = Auth::user();

      if($user)
      {
        $user_access_control = user_access_control::where('user_id', $user->id)->first();

        // default admin account, able to access every view
        if(!$user_access_control)
        {
          return true;
        }
        else
        {
          $access_control_list = $this->UserAccessControl();
          $access = $user_access_control->access_control;

          $route_name = Route::currentRouteName();
          $value = "";
          foreach($access_control_list as $access_control)
          {
            if(strtolower($route_name) == strtolower($access_control['route']))
            {
              $value = $access_control['value'];
              break;
            }
          }

          $access_array = explode(",", $access);
          foreach($access_array as $access_value)
          {
            if($access_value == $value)
            {
              return true;
            }
          }

          return false;
        }
      }
      else
      {
        return false;
      }
    }

    public function defaultAccessControl()
    {
      $default_access_control = [
        [
          'user_type' => '1',
          'access' => "1,2,3,4,5,6,7,8,9,10"
        ],
        [
          'user_type' => '2',
          'access' => "1,2,3,4,5,6,7,8,9,10"
        ],
        [
          'user_type' => '3',
          'access' => ""
        ]
      ];

      return $default_access_control;
    }

    public function accessControlByRole($user_type)
    {
      $access = "";

      $default_access_control = $this->defaultAccessControl();

      foreach($default_access_control as $access_control)
      {
        if($access_control['user_type'] == $user_type)
        {
          $access = $access_control['access'];
          break;
        }
      }

      return $access;
    }

    public function testingPage()
    {
      return view('testing');
    }
}
