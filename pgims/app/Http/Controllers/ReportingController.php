<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportingController extends Controller
{
    /**
     * Get daily sales summary report.
     *
     * @param Request $request
     * @queryParam date string Date to get sales for (Y-m-d format). Defaults to today.
     * @queryParam store_id int[] Optional array of store IDs to filter by.
     * @response {
     *   "date": "2025-09-17",
     *   "total_orders": 100,
     *   "total_sales": 50000.45
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function dailySalesSummary(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
            'store_id' => 'nullable|array',
            'store_id.*' => 'integer|exists:stores,id',
        ]);

        $date = $request->input('date', now()->toDateString());
        $storeIds = $request->input('store_id');

        $query = DB::table('orders')
            ->selectRaw('COUNT(id) as total_orders, SUM(total_amount) as total_sales')
            ->whereDate('created_at', $date);

        if ($storeIds && Schema::hasColumn('orders', 'store_id')) {
            $query->whereIn('store_id', $storeIds);
        }

        $sales = $query->first();

        return response()->json([
            'date' => $date,
            'total_orders' => intval($sales->total_orders ?? 0),
            'total_sales' => floatval($sales->total_sales ?? 0),
        ]);
    }

    /**
     * Get payment breakdown by payment method over a date range.
     *
     * @param Request $request
     * @queryParam start string Start date (Y-m-d). Defaults to one month ago.
     * @queryParam end string End date (Y-m-d). Defaults to today.
     * @queryParam store_id int[] Optional array of store IDs to filter by.
     * @response {
     *   "start_date": "2025-08-17",
     *   "end_date": "2025-09-17",
     *   "payment_methods": [
     *     { "payment_method": "cash", "total": 20000 },
     *     { "payment_method": "card", "total": 30000 }
     *   ]
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentBreakdown(Request $request)
    {
        $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'store_id' => 'nullable|array',
            'store_id.*' => 'integer|exists:stores,id',
        ]);

        $start = $request->input('start', now()->subMonth()->toDateString());
        $end = $request->input('end', now()->toDateString());
        $storeIds = $request->input('store_id');

        $query = DB::table('transactions')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [$start, $end]);

        if ($storeIds && Schema::hasColumn('transactions', 'store_id')) {
            $query->whereIn('store_id', $storeIds);
        }

        $paymentData = $query->groupBy('payment_method')->get();

        return response()->json([
            'start_date' => $start,
            'end_date' => $end,
            'payment_methods' => $paymentData,
        ]);
    }


    /**
     * Generate profit report for a date range.
     *
     * @param Request $request
     * @queryParam start string Start date (Y-m-d). Defaults to one month ago.
     * @queryParam end string End date (Y-m-d). Defaults to today.
     * @queryParam store_id int[] Optional array of store IDs to filter by.
     * @response {
     *  "start_date": "2025-08-17",
     *  "end_date": "2025-09-17",
     *  "profit": 15000.50
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function profitReport(Request $request)
    {
        $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'store_id' => 'nullable|array',
            'store_id.*' => 'integer|exists:stores,id',
        ]);

        $start = $request->input('start', now()->subMonth()->toDateString());
        $end = $request->input('end', now()->toDateString());
        $storeIds = $request->input('store_id');

        $query = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('order_items.created_at', [$start, $end]);

        if ($storeIds && Schema::hasColumn('orders', 'store_id')) {
            $query->whereIn('orders.store_id', $storeIds);
        }

        $profit = $query
            ->selectRaw('SUM((order_items.unit_price - products.price) * order_items.quantity) as profit')
            ->first();

        return response()->json([
            'start_date' => $start,
            'end_date' => $end,
            'profit' => floatval($profit->profit ?? 0),
        ]);
    }

    /**
     * Get inventory stock status by product and store.
     *
     * @param Request $request
     * @queryParam store_id int Optional store ID to filter by.
     * @response [
     *   { "id": 1, "name": "Product A", "quantity": 100, "store_id": 2 },
     *   { "id": 2, "name": "Product B", "quantity": 50, "store_id": 2 }
     * ]
     * @return \Illuminate\Http\JsonResponse
     */
    public function inventoryStatus(Request $request)
    {
        $request->validate([
            'store_id' => 'nullable|integer|exists:stores,id',
        ]);

        $storeId = $request->input('store_id');

        $query = DB::table('inventory')
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', 'inventory.stock as quantity', 'inventory.store_id');

        if ($storeId) {
            $query->where('inventory.store_id', $storeId);
        }

        $stocks = $query->get();

        return response()->json($stocks);
    }

    /**
     * Get customer credit and balance report.
     *
     * @param Request $request
     * @queryParam customer_id int Optional customer ID to filter by.
     * @response [
     *   { "id": 1, "name": "John Doe", "balance": 1200, "credit_limit": 5000 }
     * ]
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerCreditReport(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|integer|exists:customers,id',
        ]);

        $customerId = $request->input('customer_id');

        $query = DB::table('customers')
            ->select('id', 'name', 'balance', 'credit_limit');

        if ($customerId) {
            $query->where('id', $customerId);
        }

        $customers = $query->get();

        return response()->json($customers);
    }

    /**
     * Generate expense report for a date range.
     *
     * @param Request $request
     * @queryParam start string Start date (Y-m-d). Defaults to one month ago.
     * @queryParam end string End date (Y-m-d). Defaults to today.
     * @queryParam store_id int[] Optional array of store IDs to filter by.
     * @response {
     *  "start_date": "2025-08-17",
     *  "end_date": "2025-09-17",
     *  "total_expenses": 8000
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function expenseReport(Request $request)
    {
        $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'store_id' => 'nullable|array',
            'store_id.*' => 'integer|exists:stores,id',
        ]);

        $start = $request->input('start', now()->subMonth()->toDateString());
        $end = $request->input('end', now()->toDateString());
        $storeIds = $request->input('store_id');

        $query = DB::table('transactions')
            ->whereBetween('created_at', [$start, $end])
            ->where('type', 'debit'); // Assuming 'debit' means expense

        if ($storeIds && Schema::hasColumn('transactions', 'store_id')) {
            $query->whereIn('store_id', $storeIds);
        }

        $expenses = $query->select(DB::raw('SUM(amount) as total_expenses'))->first();

        return response()->json([
            'start_date' => $start,
            'end_date' => $end,
            'total_expenses' => floatval($expenses->total_expenses ?? 0),
        ]);
    }
}
