<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate(rules: [
            'sku' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create(attributes: $data);
        return response()->json(data: $product, status: 201);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(rules: [
            'sku' => 'sometimes|nullable|string|max:255',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $product->update(attributes: $data);
        return response()->json(data: $product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(data: null, status: 204);
    }
}