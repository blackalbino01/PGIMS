<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    /**
     * Display a listing of transactions with related bank accounts.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "bank_account_id": 2,
     *     "type": "credit",
     *     "amount": 1000.00,
     *     "reference": "INV-12345",
     *     "description": "Payment received",
     *     "transaction_date": "2025-09-19",
     *     "created_at": "2025-09-19T19:00:00Z",
     *     "updated_at": "2025-09-19T19:00:00Z",
     *     "bankAccount": {
     *       "id": 2,
     *       "bank_name": "First Bank",
     *       "account_number": "1234567890"
     *     }
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Transactions::with(relations: 'bankAccount')->get();
    }

    /**
     * Display the specified transaction with bank account details.
     *
     * @urlParam transaction int required The ID of the transaction.
     *
     * @response {
     *   "id": 1,
     *   "bank_account_id": 2,
     *   "type": "credit",
     *   "amount": 1000.00,
     *   "reference": "INV-12345",
     *   "description": "Payment received",
     *   "transaction_date": "2025-09-19",
     *   "created_at": "2025-09-19T19:00:00Z",
     *   "updated_at": "2025-09-19T19:00:00Z",
     *   "bankAccount": {
     *     "id": 2,
     *     "bank_name": "First Bank",
     *     "account_number": "1234567890"
     *   }
     * }
     *
     * @param Transactions $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Transactions $transaction)
    {
        return $transaction->load(relations: 'bankAccount');
    }

    /**
     * Store a new transaction inside a database transaction block.
     *
     * @bodyParam bank_account_id int required Related bank account ID. Example: 2
     * @bodyParam type string required Transaction type: credit or debit. Example: credit
     * @bodyParam amount numeric required Transaction amount. Minimum 0. Example: 1000.00
     * @bodyParam reference string Nullable Transaction reference or invoice ID.
     * @bodyParam description string Nullable Additional details about the transaction.
     * @bodyParam transaction_date date Nullable Date of the transaction.
     *
     * @response 201 {
     *   "id": 10,
     *   "bank_account_id": 2,
     *   "type": "credit",
     *   "amount": 1000.00,
     *   "reference": "INV-12345",
     *   "description": "Payment received",
     *   "transaction_date": "2025-09-19",
     *   "created_at": "2025-09-19T19:05:00Z",
     *   "updated_at": "2025-09-19T19:05:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
            'transaction_date' => 'nullable|date',
        ]);

        $transaction = null;

        DB::transaction(callback: function () use ($validated, &$transaction): void {
            $transaction = Transactions::create(attributes: $validated);
        });

        return response()->json(data: $transaction, status: 201);
    }

    /**
     * Update the specified transaction inside a database transaction.
     *
     * @urlParam transaction int required The ID of the transaction.
     * @bodyParam bank_account_id int Nullable Updated bank account ID.
     * @bodyParam type string Nullable Updated transaction type.
     * @bodyParam amount numeric Nullable Updated amount. Minimum 0.
     * @bodyParam reference string Nullable Updated reference.
     * @bodyParam description string Nullable Updated description.
     * @bodyParam transaction_date date Nullable Updated transaction date.
     *
     * @response {
     *   "id": 10,
     *   "bank_account_id": 2,
     *   "type": "debit",
     *   "amount": 500.00,
     *   "reference": "PAY-56789",
     *   "description": "Payment refund",
     *   "transaction_date": "2025-09-20",
     *   "created_at": "2025-09-19T19:05:00Z",
     *   "updated_at": "2025-09-20T10:00:00Z"
     * }
     *
     * @param Request $request
     * @param Transactions $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Transactions $transaction)
    {
        $validated = $request->validate(rules: [
            'bank_account_id' => 'sometimes|exists:bank_accounts,id',
            'type' => 'sometimes|in:credit,debit',
            'amount' => 'sometimes|numeric|min:0',
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
            'transaction_date' => 'nullable|date',
        ]);

        DB::transaction(callback: function () use ($validated, $transaction): void {
            $transaction->update(attributes: $validated);
        });

        return response()->json(data: $transaction);
    }

    /**
     * Delete the specified transaction.
     *
     * @urlParam transaction int required The ID of the transaction.
     *
     * @response 204 {}
     *
     * @param Transactions $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Transactions $transaction)
    {
        DB::transaction(callback: function () use ($transaction): void {
            $transaction->delete();
        });

        return response()->json(data: null, status: 204);
    }
}