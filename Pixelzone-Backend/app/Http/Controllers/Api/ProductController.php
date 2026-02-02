<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
        public function index()
    {
        $products = Product::with('category')->get();
        return response()->json(['success' => true, 'data' => $products], 200);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|url',
        ]);

        $product = Product::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'category_id' => $validatedData['category_id'],
            'image' => $validatedData['image'],
        ]);

        return response()->json(['success' => true, 'data' => $product], 201);
    }

    public function show($id)
    {
        $product = Product::with('category')->find($id);
        if ($product) {
            return response()->json(['success' => true, 'data' => $product], 200);
        }
        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'price' => 'sometimes|required|numeric',
                'category_id' => 'sometimes|required|exists:categories,id',
                'image' => 'sometimes|required|url',
            ]);
            $product->update($validatedData);
            return response()->json(['success' => true, 'data' => $product], 200);
        }
        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }


    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Product deleted'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }
}
