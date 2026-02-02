<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        return response()->json(['success' => true, 'data' => $categories], 200);
    }


    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $category = Category::create($request->only('name'));
        return response()->json(['success' => true, 'data' => $category], 201);
    }


    public function show($id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json(['success' => true, 'data' => $category], 200);
        }
        return response()->json(['success' => false, 'message' => 'Category not found'], 404);
    }


    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if ($category) {
            $request->validate(['name' => 'required|string']);
            $category->update($request->only('name'));
            return response()->json(['success' => true, 'data' => $category], 200);
        }
        return response()->json(['success' => false, 'message' => 'Category not found'], 404);
    }


    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Category not found'], 404);
    }
}
