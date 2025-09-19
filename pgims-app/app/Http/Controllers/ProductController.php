<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "sku": "SKU001",
     *     "name": "Product A",
     *     "description": "A sample product.",
     *     "price": 100.00,
     *     "stock": 50,
     *     "created_at": "2025-09-19T09:35:00Z",
     *     "updated_at": "2025-09-19T09:35:00Z"
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a new product.
     *
     * @bodyParam sku string Nullable product SKU. Example: SKU001
     * @bodyParam name string required Product name. Example: Product A
     * @bodyParam description string Nullable product description.
     * @bodyParam price numeric required Product price (min 0). Example: 100.00
     * @bodyParam stock int required Stock quantity (min 0). Example: 50
     *
     * @response 201 {
     *   "id": 1,
     *   "sku": "SKU001",
     *   "name": "Product A",
     *   "description": "A sample product.",
     *   "price": 100.00,
     *   "stock": 50,
     *   "created_at": "2025-09-19T09:35:00Z",
     *   "updated_at": "2025-09-19T09:35:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Update an existing product.
     *
     * @bodyParam sku string Nullable product SKU. Example: SKU001
     * @bodyParam name string Nullable product name.
     * @bodyParam description string Nullable product description.
     * @bodyParam price numeric Nullable product price (min 0).
     * @bodyParam stock int Nullable stock quantity (min 0).
     *
     * @response {
     *   "id": 1,
     *   "sku": "SKU001",
     *   "name": "Product A Updated",
     *   "description": "Updated description.",
     *   "price": 120.00,
     *   "stock": 30,
     *   "created_at": "2025-09-19T09:35:00Z",
     *   "updated_at": "2025-09-19T09:50:00Z"
     * }
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
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


    /**
     * Remove the specified product.
     *
     * @urlParam product int required The ID of the product.
     *
     * @response 204 {}
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(data: null, status: 204);
    }
}
