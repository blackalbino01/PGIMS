<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inventory::with(relations: ['store', 'product'])->get();
    }


    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        return $inventory->load(relations: ['store', 'product']);
    }


    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return response()->json(data: null, status: 204);
    }
}
