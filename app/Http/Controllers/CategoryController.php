<?php

namespace App\Http\Controllers;

use App\Category;
use App\Department;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
        $categories = Category::join('department','department.id','=','category.department_id')
                                ->orderBy('department.department_name','ASC')
                                ->orderBy('category.category_name','ASC')
                                ->paginate(15);

        return view('category.index',compact('categories'))->with(['url' => $this->url]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::orderBy('department_name','ASC')->get();

        return view('category.create',compact('departments'))->with(['url' => $this->url]);
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
            'department' => 'required',
            'name' => 'required|unique:category,category_name',
        ]);

        Category::create([
            'department_id' => $request->department,
            'category_name' => $request->name,
            'category_code' => $request->code,
        ]);

        return redirect()->route('category.index')->with(['success' => 'Create Successful']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $departments = Department::orderBy('department_name','ASC')->get();

        return view('category.create',compact('departments','category'))->with(['url' => $this->url]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validate = $request->validate([
            'department' => 'required',
            'name' => 'required|unique:category,category_name,'.$category->id,
        ]);

        $category->update([
            'category_code' => $request->code,
            'category_name' => $request->name,
            'department_id' => $request->department,
        ]);

        return redirect()->route('category.index')->with(['success' => 'Update Successful']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
