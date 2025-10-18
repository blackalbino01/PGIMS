<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "category": "Office",
     *     "amount": "5000.00",
     *     "transaction_date": "2025-10-01",
     *     "description": "Paper purchase",
     *     "store_id": 1,
     *     "created_at": "2025-10-01T10:00:00Z",
     *     "updated_at": "2025-10-01T10:00:00Z"
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Expense::all();
    }

    /**
     * Store a new expense.
     *
     * @bodyParam category string Nullable Expense category. Example: Office
     * @bodyParam amount numeric required Expense amount. Example: 5000.00
     * @bodyParam transaction_date date required Date of expense. Example: 2025-10-01
     * @bodyParam description string Nullable Description of the expense.
     * @bodyParam store_id int Nullable Store ID where expense occurred.
     *
     * @response 201 {
     *   "id": 1,
     *   "category": "Office",
     *   "amount": "5000.00",
     *   "transaction_date": "2025-10-01",
     *   "description": "Paper purchase",
     *   "store_id": 1,
     *   "created_at": "2025-10-01T10:00:00Z",
     *   "updated_at": "2025-10-01T10:00:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'store_id' => 'nullable|exists:stores,id',
        ]);

        $expense = Expense::create($data);
        return response()->json($expense, 201);
    }

    /**
     * Display the specified expense.
     *
     * @urlParam expense int required The ID of the expense.
     *
     * @response {
     *   "id": 1,
     *   "category": "Office",
     *   "amount": "5000.00",
     *   "transaction_date": "2025-10-01",
     *   "description": "Paper purchase",
     *   "store_id": 1,
     *   "created_at": "2025-10-01T10:00:00Z",
     *   "updated_at": "2025-10-01T10:00:00Z"
     * }
     *
     * @param Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Expense $expense)
    {
        return response()->json($expense);
    }

    /**
     * Update an existing expense.
     *
     * @bodyParam category string Nullable Expense category. Example: Office
     * @bodyParam amount numeric Nullable Expense amount. Example: 5000.00
     * @bodyParam transaction_date date Nullable Date of expense. Example: 2025-10-01
     * @bodyParam description string Nullable Description of the expense.
     * @bodyParam store_id int Nullable Store ID where expense occurred.
     *
     * @response {
     *   "id": 1,
     *   "category": "Office Updated",
     *   "amount": "5500.00",
     *   "transaction_date": "2025-10-02",
     *   "description": "Updated paper purchase",
     *   "store_id": 1,
     *   "created_at": "2025-10-01T10:00:00Z",
     *   "updated_at": "2025-10-02T11:00:00Z"
     * }
     *
     * @param Request $request
     * @param Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'category' => 'sometimes|nullable|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'transaction_date' => 'sometimes|date',
            'description' => 'nullable|string',
            'store_id' => 'sometimes|nullable|exists:stores,id',
        ]);

        $expense->update($data);
        return response()->json($expense);
    }

    /**
     * Remove the specified expense.
     *
     * @urlParam expense int required The ID of the expense.
     *
     * @response 204 {}
     *
     * @param Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(null, 204);
    }
}
