<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    /**
    * Display a listing of order items with their products and orders.
    */
    public function index()
    {
        return OrderItem::with(relations: ['product', 'order'])->get();
    }

    /**
    * Display the specified order item with product and order.
    */
    public function show(OrderItem $orderItem)
    {
        return $orderItem->load(relations: ['product', 'order']);
    }

    /**
    * Store a new order item.
    */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
        'order_id' => 'required|exists:orders,id',
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'unit_price' => 'required|numeric|min:0',
        'line_total' => 'required|numeric|min:0',
        ]);

        $orderItem = null;

        DB::transaction(callback: function () use ($validated, &$orderItem): void {
        $orderItem = OrderItem::create(attributes: $validated);
        });

        return response()->json(data: $orderItem, status: 201);
    }

    /**
    * Update an existing order item.
    */
    public function update(Request $request, OrderItem $orderItem)
    {
        $validated = $request->validate(rules: [
        'order_id' => 'sometimes|exists:orders,id',
        'product_id' => 'sometimes|exists:products,id',
        'quantity' => 'sometimes|integer|min:1',
        'unit_price' => 'sometimes|numeric|min:0',
        'line_total' => 'sometimes|numeric|min:0',
        ]);

        DB::transaction(callback: function () use ($validated, $orderItem): void {
        $orderItem->update(attributes: $validated);
        });

        return response()->json(data: $orderItem);
    }

    /**
    * Delete the specified order item.
    */
    public function destroy(OrderItem $orderItem)
    {
        DB::transaction(callback: function () use ($orderItem): void {
            $orderItem->delete();
        });

        return response()->json(data: null, status: 204);
    }
}
