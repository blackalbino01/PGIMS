<?php
namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of bank accounts.
     */
    public function index()
    {
        return BankAccount::all();
    }

    /**
     * Show a specific bank account.
     */
    public function show(BankAccount $bankAccount)
    {
        return $bankAccount;
    }

    /**
     * Store a new bank account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'bank_name' => 'required|string',
            'account_number' => 'required|string|unique:bank_accounts,account_number',
            'account_name' => 'required|string',
            'branch' => 'nullable|string',
            'account_type' => 'nullable|string',
            'balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $bankAccount = BankAccount::create(attributes: $validated);

        return response()->json(data: $bankAccount, status: 201);
    }

    /**
     * Update an existing bank account.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate(rules: [
            'bank_name' => 'sometimes|string',
            'account_number' => 'sometimes|string|unique:bank_accounts,account_number,' . $bankAccount->id,
            'account_name' => 'sometimes|string',
            'branch' => 'nullable|string',
            'account_type' => 'nullable|string',
            'balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $bankAccount->update(attributes: $validated);

        return response()->json(data: $bankAccount);
    }

    /**
     * Delete a bank account.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return response()->json(data: null, status: 204);
    }
}