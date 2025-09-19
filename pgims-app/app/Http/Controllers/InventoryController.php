<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items with related store and product.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "store_id": 2,
     *     "product_id": 5,
     *     "quantity": 100,
     *     "created_at": "2025-09-19T09:00:00Z",
     *     "updated_at": "2025-09-19T09:00:00Z",
     *     "store": {
     *       "id": 2,
     *       "name": "Main Store",
     *       // other store fields
     *     },
     *     "product": {
     *       "id": 5,
     *       "name": "Product A",
     *       // other product fields
     *     }
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Inventory::with(relations: ['store', 'product'])->get();
    }


    /**
     * Store a newly created inventory record.
     *
     * @bodyParam store_id int required Store ID reference. Example: 2
     * @bodyParam product_id int required Product ID reference. Example: 5
     * @bodyParam quantity int required Quantity in stock (min 0). Example: 100
     *
     * @response 201 {
     *   "id": 1,
     *   "store_id": 2,
     *   "product_id": 5,
     *   "quantity": 100,
     *   "created_at": "2025-09-19T09:00:00Z",
     *   "updated_at": "2025-09-19T09:00:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::create(attributes: $validated);

        return response()->json(data: $inventory, status: 201);
    }

    /**
     * Display the specified inventory record with related store and product.
     *
     * @urlParam inventory int required Inventory record ID.
     *
     * @response {
     *   "id": 1,
     *   "store_id": 2,
     *   "product_id": 5,
     *   "quantity": 100,
     *   "created_at": "2025-09-19T09:00:00Z",
     *   "updated_at": "2025-09-19T09:00:00Z",
     *   "store": {
     *     "id": 2,
     *     "name": "Main Store"
     *   },
     *   "product": {
     *     "id": 5,
     *     "name": "Product A"
     *   }
     * }
     *
     * @param Inventory $inventory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Inventory $inventory)
    {
        return $inventory->load(relations: ['store', 'product']);
    }


    /**
     * Update the specified inventory record.
     *
     * @urlParam inventory int required Inventory record ID.
     * @bodyParam store_id int Nullable Store ID.
     * @bodyParam product_id int Nullable Product ID.
     * @bodyParam quantity int Nullable Quantity in stock (min 0).
     *
     * @response {
     *   "id": 1,
     *   "store_id": 2,
     *   "product_id": 5,
     *   "quantity": 150,
     *   "created_at": "2025-09-19T09:00:00Z",
     *   "updated_at": "2025-09-19T10:00:00Z"
     * }
     *
     * @param Request $request
     * @param Inventory $inventory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate(rules: [
            'store_id' => 'sometimes|exists:stores,id',
            'product_id' => 'sometimes|exists:products,id',
            'quantity' => 'sometimes|integer|min:0',
        ]);

        $inventory->update(attributes: $validated);

        return response()->json(data: $inventory);
    }

    /**
     * Remove the specified inventory record.
     *
     * @urlParam inventory int required Inventory record ID.
     *
     * @response 204 {}
     *
     * @param Inventory $inventory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return response()->json(data: null, status: 204);
    }
}
