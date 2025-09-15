<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */
    public function index()
    {
        return PurchaseOrder::with(relations: 'supplier')->get();
    }

    /**
     * Display the specified purchase order with supplier.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        return $purchaseOrder->load(relations: 'supplier');
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_number' => 'required|string|unique:purchase_orders,order_number',
            'status' => 'nullable|in:pending,approved,received,cancelled',
            'total_amount' => 'nullable|numeric|min:0',
            'order_date' => 'nullable|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $purchaseOrder = PurchaseOrder::create(attributes: $validated);

        return response()->json(data: $purchaseOrder, status: 201);
    }

    /**
     * Update the specified purchase order.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate(rules: [
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'order_number' => 'sometimes|string|unique:purchase_orders,order_number,' . $purchaseOrder->id,
            'status' => 'sometimes|in:pending,approved,received,cancelled',
            'total_amount' => 'nullable|numeric|min:0',
            'order_date' => 'nullable|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $purchaseOrder->update(attributes: $validated);

        return response()->json(data: $purchaseOrder);
    }

    /**
     * Remove the specified purchase order.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return response()->json(data: null, status: 204);
    }
}