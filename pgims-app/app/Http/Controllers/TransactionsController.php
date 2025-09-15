<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    /**
     * Display a listing of transactions with their bank accounts.
     */
    public function index()
    {
        return Transactions::with(relations: 'bankAccount')->get();
    }

    /**
     * Display the specified transaction with its bank account.
     */
    public function show(Transactions $transaction)
    {
        return $transaction->load(relations: 'bankAccount');
    }

    /**
     * Store a new transaction inside a DB transaction block.
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
     * Update the specified transaction inside a DB transaction.
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
     */
    public function destroy(Transactions $transaction)
    {
        DB::transaction(callback: function () use ($transaction): void {
            $transaction->delete();
        });

        return response()->json(data: null, status: 204);
    }
}