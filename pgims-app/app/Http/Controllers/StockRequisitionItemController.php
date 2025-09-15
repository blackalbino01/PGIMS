<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockRequisitionItem;

class StockRequisitionItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return StockRequisitionItem::with(relations: ['stockRequisition', 'product'])->get();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'stock_requisition_id' => 'required|exists:stock_requisitions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = StockRequisitionItem::create(attributes: $validated);

        return response()->json(data: $item, status: 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockRequisitionItem $stockRequisitionItem)
    {
        return $stockRequisitionItem->load(relations: ['stockRequisition', 'product']);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockRequisitionItem $stockRequisitionItem)
    {
        $validated = $request->validate(rules: [
            'stock_requisition_id' => 'sometimes|exists:stock_requisitions,id',
            'product_id' => 'sometimes|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $stockRequisitionItem->update(attributes: $validated);

        return response()->json(data: $stockRequisitionItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockRequisitionItem $stockRequisitionItem)
    {
        $stockRequisitionItem->delete();
        return response()->json(data: null, status: 204);
    }
}
