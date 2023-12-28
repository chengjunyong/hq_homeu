<?php

namespace App\Http\Controllers;

use App\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
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
        $subCategories = SubCategory::orderBy('name','ASC')->paginate(15);

        return view('sub_category.index',compact('subCategories'))->with(['url' => $this->url]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sub_category.create')->with(['url' => $this->url]);
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
            'name' => 'required|unique:sub_categories,name',
        ]);

        SubCategory::create([
            'name' => $request->name,
            'updated_by' => auth()->user()->id,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('sub_category.index')->with(['success' => 'Create Successful']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function show(SubCategory $subCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(SubCategory $subCategory)
    {
        return view('sub_category.create',compact('subCategory'))->with(['url' => $this->url]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        $validate = $request->validate([
            'name' => 'required|unique:sub_categories,name,'.$subCategory->id,
        ]);

        $subCategory->update([
            'name' => $request->name,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route('sub_category.index')->with(['success' => 'Update Successful']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubCategory $subCategory)
    {
        //
    }
}
