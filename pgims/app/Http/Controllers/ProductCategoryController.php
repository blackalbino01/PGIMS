<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "name": "Beverages",
     *     "description": "Drinks and refreshments",
     *     "created_at": "2025-09-19T09:35:00Z",
     *     "updated_at": "2025-09-19T09:35:00Z"
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return ProductCategory::all();
    }

    /**
     * Store a new product category.
     *
     * @bodyParam name string required Product category name. Example: Beverages
     * @bodyParam description string Nullable Product category description.
     *
     * @response 201 {
     *   "id": 1,
     *   "name": "Beverages",
     *   "description": "Drinks and refreshments",
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
            'name' => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string',
        ]);

        $category = ProductCategory::create(attributes: $data);

        return response()->json(data: $category, status: 201);
    }

    /**
     * Display the specified product category.
     *
     * @urlParam productCategory int required The ID of the product category.
     *
     * @response {
     *   "id": 1,
     *   "name": "Beverages",
     *   "description": "Drinks and refreshments",
     *   "created_at": "2025-09-19T09:35:00Z",
     *   "updated_at": "2025-09-19T09:35:00Z"
     * }
     *
     * @param ProductCategory $productCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ProductCategory $productCategory)
    {
        return response()->json(data: $productCategory);
    }

    /**
     * Update an existing product category.
     *
     * @bodyParam name string Nullable Product category name.
     * @bodyParam description string Nullable Product category description.
     *
     * @response {
     *   "id": 1,
     *   "name": "Beverages Updated",
     *   "description": "Updated description",
     *   "created_at": "2025-09-19T09:35:00Z",
     *   "updated_at": "2025-09-19T10:00:00Z"
     * }
     *
     * @param Request $request
     * @param ProductCategory $productCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $data = $request->validate(rules: [
            'name' => 'sometimes|string|max:255|unique:product_categories,name,' . $productCategory->id,
            'description' => 'nullable|string',
        ]);

        $productCategory->update(attributes: $data);

        return response()->json(data: $productCategory);
    }

    /**
     * Remove the specified product category.
     *
     * @urlParam productCategory int required The ID of the product category.
     *
     * @response 204 {}
     *
     * @param ProductCategory $productCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return response()->json(data: null, status: 204);
    }
};
