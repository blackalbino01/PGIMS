<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\BankAccount;
use App\Models\Transactions;
use App\Models\Store;
use Carbon\Carbon;

class ReportingController extends Controller
{

    /**
     * Return dashboard data aligned with existing models and UI
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminDashboard()
    {
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek();
        $startOfQuarter = $today->copy()->firstOfQuarter();

        // Sales for today and cost of goods sold via Order and related OrderItems & Products
        $ordersToday = Order::with('items.product')->whereDate('created_at', $today)->get();
        $salesForToday = $ordersToday->sum('total_amount');

        $costOfGoodsSold = $ordersToday->sum(function ($order) {
            return $order->items->sum(function ($item) {
                return $item->quantity * $item->product->price; // Use product cost price if available
            });
        });

        // Expenses inferred from Transactions of type 'expense'
        $expensesForToday = Transactions::whereDate('transaction_date', $today)
                            ->where('type', 'expense')
                            ->sum('amount');

        $profitForToday = $salesForToday - $costOfGoodsSold - $expensesForToday;

        // Payment method sales breakdown assumed stored in Order->payment_method
        $paymentsToday = Order::whereDate('created_at', $today)
            ->selectRaw('payment_method, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(fn($item) => [$item->payment_method => $item->total]);

        $profitMargin = $salesForToday > 0 ? round(($profitForToday / $salesForToday) * 100, 1) : 0;

        // Inventory summary (stock and retail)
        $products = Product::all();
        $stockValue = $products->sum(fn($p) => $p->inventory->sum('quantity') * $p->price); // Assume price=cost price
        $retailValue = $stockValue; // Adjust if retail price differs on product model

        // Trend data - sales by hour today for chart
        $trendData = Order::selectRaw('HOUR(created_at) as hour, SUM(total_amount) as total')
            ->whereDate('created_at', $today)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->mapWithKeys(fn($row) => [$row->hour => $row->total]);

        // Cash flow this quarter from Transactions by type and bank account
        $cashReceived = Transactions::where('type', 'received')->where('transaction_date', '>=', $startOfQuarter)->sum('amount');
        $cashSent = Transactions::where('type', 'sent')->where('transaction_date', '>=', $startOfQuarter)->sum('amount');

        $bankReceived = Transactions::where('type', 'received')->where('transaction_date', '>=', $startOfQuarter)->sum('amount');
        $bankSent = Transactions::where('type', 'sent')->where('transaction_date', '>=', $startOfQuarter)->sum('amount');

        $cashBalance = Transactions::where('type', 'balance')->sum('amount');
        $bankBalance = BankAccount::sum('balance');

        // Profit and Loss this week
        $revenue = Order::whereDate('created_at', '>=', $startOfWeek)->sum('total_amount');
        $operatingExpense = Transactions::where('type', 'expense')->whereDate('transaction_date', '>=', $startOfWeek)->sum('amount');
        $profit = $revenue - $operatingExpense;

        // Debtors & Creditors (in Customers and Suppliers if you have Supplier model)
        $debtors = Customer::sum('balance');
        // Since Supplier model missing balance field, use 0 for creditors
        $creditors = 0;
        $netPosition = $debtors - $creditors;

        // Locations with sales counts last 30 days via Store->orders relation
        $locations = Store::withCount(['orders' => function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        }])->get(['id', 'name']);

        return response()->json([
            'sales_for_today' => $salesForToday,
            'cost_of_goods_sold' => $costOfGoodsSold,
            'expenses_for_today' => $expensesForToday,
            'profit_for_today' => $profitForToday,
            'payment_methods' => $paymentsToday,
            'profit_margin' => $profitMargin,
            'inventory_summary' => [
                'stock_value' => $stockValue,
                'retail_value' => $retailValue,
            ],
            'trend' => $trendData,
            'cashflow' => [
                'cash' => [
                    'received' => $cashReceived,
                    'sent' => $cashSent,
                    'balance' => $cashBalance,
                ],
                'bank' => [
                    'received' => $bankReceived,
                    'sent' => $bankSent,
                    'balance' => $bankBalance,
                ],
            ],
            'profit_loss' => [
                'profit' => $profit,
                'revenue' => $revenue,
                'operating_expense' => $operatingExpense,
            ],
            'debtors_creditors' => [
                'debtors' => $debtors,
                'creditors' => $creditors,
                'net_position' => $netPosition,
            ],
            'locations' => $locations,
        ]);
    }


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
