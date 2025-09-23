<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class StoreController extends Controller
{
    /**
     * Display a listing of stores.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "name": "Main Store",
     *     "address": "123 Main Street",
     *     "phone": "123-456-7890",
     *     "created_at": "2025-09-19T17:03:00Z",
     *     "updated_at": "2025-09-19T17:03:00Z"
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Store::all();
    }



    /**
     * Store a newly created store.
     *
     * @bodyParam name string required Name of the store. Example: Main Store
     * @bodyParam address string Nullable Store address. Example: 123 Main Street
     * @bodyParam phone string Nullable Store phone number. Example: 123-456-7890
     *
     * @response 201 {
     *   "id": 1,
     *   "name": "Main Store",
     *   "address": "123 Main Street",
     *   "phone": "123-456-7890",
     *   "created_at": "2025-09-19T17:03:00Z",
     *   "updated_at": "2025-09-19T17:03:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'name' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $store = Store::create(attributes: $validated);
        return response()->json(data: $store, status: 201);
    }

    /**
     * Display the specified store.
     *
     * @urlParam store int required The ID of the store.
     *
     * @response {
     *   "id": 1,
     *   "name": "Main Store",
     *   "address": "123 Main Street",
     *   "phone": "123-456-7890",
     *   "created_at": "2025-09-19T17:03:00Z",
     *   "updated_at": "2025-09-19T17:03:00Z"
     * }
     *
     * @param Store $store
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Store $store)
    {
        return $store;
    }

    /**
     * Update the specified store.
     *
     * @urlParam store int required The ID of the store.
     * @bodyParam name string Nullable Updated name.
     * @bodyParam address string Nullable Updated address.
     * @bodyParam phone string Nullable Updated phone.
     *
     * @response {
     *   "id": 1,
     *   "name": "Updated Store",
     *   "address": "456 New Street",
     *   "phone": "987-654-3210",
     *   "created_at": "2025-09-19T17:03:00Z",
     *   "updated_at": "2025-09-19T17:15:00Z"
     * }
     *
     * @param Request $request
     * @param Store $store
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Store $store)
    {
         $validated = $request->validate(rules: [
            'name' => 'sometimes|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $store->update(attributes: $validated);
        return response()->json(data: $store);
    }

    /**
     * Remove the specified store.
     *
     * @urlParam store int required The ID of the store.
     *
     * @response 204 {}
     *
     * @param Store $store
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Store $store)
    {
        $store->delete();
        return response()->json(data: null, status: 204);
    }
}
