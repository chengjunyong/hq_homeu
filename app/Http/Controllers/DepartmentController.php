<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public $url;

    public function __construct()
    {
        $this->url = route('home')."?p=product_menu";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::orderBy('department_name','ASC')->paginate(15);

        return view('department.index',compact('departments'))->with(['url' => $this->url]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('department.create')->with(['url' => $this->url]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|unique:department,department_name',
        ]);

        Department::create([
            'department_name' => $request->name,
            'updated_by' => auth()->user()->id,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('department.index')->with(['success' => 'Create Successful']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        return view('department.create',compact('department'))->with(['url' => $this->url]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $validate = $request->validate([
            'name' => 'required|unique:department,department_name,'.$department->id,
        ]);

        $department->update([
            'department_name' => $request->name,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route('department.index')->with(['success' => 'Update Successful']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
