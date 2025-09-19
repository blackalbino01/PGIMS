<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with related items and customer.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "customer_id": 1,
     *     "total_amount": 250.00,
     *     "status": "completed",
     *     "created_at": "2025-09-19T09:00:00Z",
     *     "updated_at": "2025-09-19T09:00:00Z",
     *     "items": [
     *       {
     *         "product_id": 5,
     *         "quantity": 2,
     *         "unit_price": 50.00,
     *         "line_total": 100.00,
     *         "product": {
     *           "id": 5,
     *           "name": "Product A"
     *         }
     *       }
     *     ],
     *     "customer": {
     *       "id": 1,
     *       "name": "Jane Doe"
     *     }
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Order::with(relations: ['items.product', 'customer'])->get();
    }

    /**
     * Display the specified order with items and customer.
     *
     * @urlParam order int required The ID of the order.
     *
     * @response {
     *   "id": 1,
     *   "customer_id": 1,
     *   "total_amount": 250.00,
     *   "status": "completed",
     *   "created_at": "2025-09-19T09:00:00Z",
     *   "updated_at": "2025-09-19T09:00:00Z",
     *   "items": [
     *     {
     *       "product_id": 5,
     *       "quantity": 2,
     *       "unit_price": 50.00,
     *       "line_total": 100.00,
     *       "product": {
     *         "id": 5,
     *         "name": "Product A"
     *       }
     *     }
     *   ],
     *   "customer": {
     *     "id": 1,
     *     "name": "Jane Doe"
     *   }
     * }
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        $order->load(relations: ['items.product', 'customer']);
        return response()->json($order);
    }

    /**
     * Store a new order including order items and stock adjustments.
     *
     * @bodyParam customer_id int Nullable ID of the customer placing the order. Example: 1
     * @bodyParam items array required Array of order items.
     * @bodyParam items.*.product_id int required Product ID. Example: 5
     * @bodyParam items.*.quantity int required Quantity ordered. Minimum 1. Example: 2
     *
     * @response 201 {
     *   "id": 1,
     *   "customer_id": 1,
     *   "total_amount": 250.00,
     *   "status": "completed",
     *   "created_at": "2025-09-19T09:00:00Z",
     *   "updated_at": "2025-09-19T09:00:00Z",
     *   "items": [...],
     *   "customer": {...}
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate(rules: [
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = null;

        DB::transaction(callback: function () use ($data, &$order): void {
           $order = Order::create(attributes: [
                'customer_id' => $validated['customer_id'] ?? null,
                'total_amount' => 0,
                'status' => 'completed',
            ]);

            $total = 0;
            foreach ($data['items'] as $it) {
                $product = Product::lockForUpdate()->findOrFail($it['product_id']);

                if ($product->stock < $it['quantity']) {
                    throw new \Exception(message: 'Insufficient stock for product: ' . $product->name);
                }

                $lineTotal = bcmul(num1: (string)$product->price, num2: (string)$it['quantity'], scale: 2);
                $total = bcadd(num1: (string)$total, num2: $lineTotal, scale: 2);

                OrderItem::create(attributes: [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $it['quantity'],
                    'unit_price' => $product->price,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock', $it['quantity']);
            }

            $order->update(attributes: ['total_amount' => $total]);
        });

        return response()->json(data: $order->load(relations: ['items.product', 'customer']), status: 201);
    }

    /**
     * Update the specified order and manage items and stock.
     *
     * @urlParam order int required The ID of the order.
     * @bodyParam customer_id int Nullable Updated customer ID.
     * @bodyParam status string Nullable Updated order status. One of: pending, processing, completed, cancelled.
     * @bodyParam notes string Nullable Additional notes.
     * @bodyParam items array Nullable Updated list of order items.
     * @bodyParam items.*.product_id int required_with:items Product ID.
     * @bodyParam items.*.quantity int required_with:items Quantity ordered.
     *
     * @response {
     *   "id": 1,
     *   "customer_id": 1,
     *   "total_amount": 300.00,
     *   "status": "completed",
     *   "notes": "Updated notes",
     *   "created_at": "2025-09-19T09:00:00Z",
     *   "updated_at": "2025-09-19T10:00:00Z",
     *   "items": [...],
     *   "customer": {...}
     * }
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate(rules: [
            'customer_id' => 'sometimes|exists:customers,id',
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
        ]);

        DB::transaction(callback: function () use ($validated, $order): void {
            if (array_key_exists(key: 'customer_id', array: $validated)) {
                $order->customer_id = $validated['customer_id'];
            }
            if (array_key_exists(key: 'status', array: $validated)) {
                $order->status = $validated['status'];
            }
            if (array_key_exists(key: 'notes', array: $validated)) {
                $order->notes = $validated['notes'];
            }
            $order->save();

            if (array_key_exists(key: 'items', array: $validated)) {
                foreach ($order->items as $oldItem) {
                    $product = Product::find(id: $oldItem->product_id);
                    if ($product) {
                        $product->increment(column: 'stock', amount: $oldItem->quantity);
                    }
                }

                $order->items()->delete();

                $total = 0;
                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception(message: 'Insufficient stock for product: ' . $product->name);
                    }

                    $lineTotal = bcmul(num1: (string)$product->price, num2: (string)$item['quantity'], scale: 2);
                    $total = bcadd(num1: (string)$total, num2: $lineTotal, scale: 2);

                    OrderItem::create(attributes: [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'line_total' => $lineTotal,
                    ]);

                    $product->decrement('stock', $item['quantity']);
                }

                $order->total_amount = $total;
                $order->save();
            }
        });

        return response()->json(data: $order->load(relations: ['items.product', 'customer']));
    }

    /**
     * Remove the specified order and restore stock quantities.
     *
     * @urlParam order int required The ID of the order.
     *
     * @response 204 {}
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Order $order)
    {
        DB::transaction(callback: function () use ($order): void {
            foreach ($order->items as $item) {
                $product = Product::find(id: $item->product_id);
                if ($product) {
                    $product->increment(column: 'stock', amount: $item->quantity);
                }
            }
            $order->items()->delete();
            $order->delete();
        });

        return response()->json(data: null, status: 204);
    }
}
