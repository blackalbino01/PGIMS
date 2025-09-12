<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(Order::with('items.product','customer')->paginate(25));
    }

    public function show(Order $order)
    {
        $order->load('items.product','customer');
        return response()->json($order);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = null;

        DB::transaction(function () use ($data, &$order) {
            $order = Order::create(['customer_id' => $data['customer_id'] ?? null, 'total' => 0, 'status' => 'completed']);

            $total = 0;
            foreach ($data['items'] as $it) {
                $product = Product::lockForUpdate()->findOrFail($it['product_id']);

                if ($product->stock < $it['quantity']) {
                    throw new \Exception('Insufficient stock for product: ' . $product->name);
                }

                $linePrice = bcmul((string)$product->price, (string)$it['quantity'], 2);
                $total = bcadd((string)$total, (string)$linePrice, 2);

                $item = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $it['quantity'],
                    'unit_price' => $product->price,
                    'line_total' => $linePrice,
                ]);

                // decrement stock
                $product->decrement('stock', $it['quantity']);
            }

            $order->update(['total' => $total]);
        });

        $order->load('items.product','customer');
        return response()->json($order, 201);
    }
}
