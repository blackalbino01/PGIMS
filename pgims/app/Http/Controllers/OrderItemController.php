<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    /**
     * Display a listing of order items with related products and orders.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "order_id": 10,
     *     "product_id": 5,
     *     "quantity": 3,
     *     "unit_price": 50.00,
     *     "line_total": 150.00,
     *     "created_at": "2025-09-19T09:30:00Z",
     *     "updated_at": "2025-09-19T09:30:00Z",
     *     "product": {
     *       "id": 5,
     *       "name": "Product A"
     *     },
     *     "order": {
     *       "id": 10,
     *       "customer_id": 1,
     *       "total_amount": 500.00,
     *       "status": "completed"
     *     }
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return OrderItem::with(relations: ['product', 'order'])->get();
    }

    /**
     * Display the specified order item with product and order.
     *
     * @urlParam orderItem int required The ID of the order item.
     *
     * @response {
     *   "id": 1,
     *   "order_id": 10,
     *   "product_id": 5,
     *   "quantity": 3,
     *   "unit_price": 50.00,
     *   "line_total": 150.00,
     *   "created_at": "2025-09-19T09:30:00Z",
     *   "updated_at": "2025-09-19T09:30:00Z",
     *   "product": {
     *     "id": 5,
     *     "name": "Product A"
     *   },
     *   "order": {
     *     "id": 10,
     *     "customer_id": 1,
     *     "total_amount": 500.00,
     *     "status": "completed"
     *   }
     * }
     *
     * @param OrderItem $orderItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(OrderItem $orderItem)
    {
        return $orderItem->load(relations: ['product', 'order']);
    }

    /**
     * Store a new order item.
     *
     * @bodyParam order_id int required ID of the order. Example: 10
     * @bodyParam product_id int required ID of the product. Example: 5
     * @bodyParam quantity int required Quantity ordered. Minimum 1. Example: 3
     * @bodyParam unit_price numeric required Unit price of the product. Example: 50.00
     * @bodyParam line_total numeric required Total line amount. Example: 150.00
     *
     * @response 201 {
     *   "id": 1,
     *   "order_id": 10,
     *   "product_id": 5,
     *   "quantity": 3,
     *   "unit_price": 50.00,
     *   "line_total": 150.00,
     *   "created_at": "2025-09-19T09:30:00Z",
     *   "updated_at": "2025-09-19T09:30:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @urlParam orderItem int required The ID of the order item.
     * @bodyParam order_id int Nullable Updated order ID.
     * @bodyParam product_id int Nullable Updated product ID.
     * @bodyParam quantity int Nullable Updated quantity.
     * @bodyParam unit_price numeric Nullable Updated unit price.
     * @bodyParam line_total numeric Nullable Updated line total.
     *
     * @response {
     *   "id": 1,
     *   "order_id": 10,
     *   "product_id": 5,
     *   "quantity": 4,
     *   "unit_price": 45.00,
     *   "line_total": 180.00,
     *   "created_at": "2025-09-19T09:30:00Z",
     *   "updated_at": "2025-09-19T10:00:00Z"
     * }
     *
     * @param Request $request
     * @param OrderItem $orderItem
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @urlParam orderItem int required The ID of the order item.
     *
     * @response 204 {}
     *
     * @param OrderItem $orderItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(OrderItem $orderItem)
    {
        DB::transaction(callback: function () use ($orderItem): void {
            $orderItem->delete();
        });

        return response()->json(data: null, status: 204);
    }
}
