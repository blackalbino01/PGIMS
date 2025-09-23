<?php
namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of all bank accounts.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "bank_name": "First Bank",
     *     "account_number": "1234567890",
     *     "account_name": "Business Account",
     *     "branch": "Main Branch",
     *     "account_type": "Savings",
     *     "balance": 15000.00,
     *     "description": "Primary business account",
     *     "created_at": "2025-09-17T12:00:00Z",
     *     "updated_at": "2025-09-17T12:00:00Z"
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return BankAccount::all();
    }

    /**
     * Show details of a specific bank account.
     *
     * @urlParam bankAccount int required The ID of the bank account.
     *
     * @response {
     *   "id": 1,
     *   "bank_name": "First Bank",
     *   "account_number": "1234567890",
     *   "account_name": "Business Account",
     *   "branch": "Main Branch",
     *   "account_type": "Savings",
     *   "balance": 15000.00,
     *   "description": "Primary business account",
     *   "created_at": "2025-09-17T12:00:00Z",
     *   "updated_at": "2025-09-17T12:00:00Z"
     * }
     *
     * @param BankAccount $bankAccount
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(BankAccount $bankAccount)
    {
        return $bankAccount;
    }

    /**
     * Store a new bank account.
     *
     * @bodyParam bank_name string required Name of the bank. Example: First Bank
     * @bodyParam account_number string required Unique account number. Example: 1234567890
     * @bodyParam account_name string required Account owner or designation. Example: Business Account
     * @bodyParam branch string Nullable bank branch. Example: Main Branch
     * @bodyParam account_type string Nullable account type. Example: Savings
     * @bodyParam balance numeric Nullable starting balance. Example: 15000.00
     * @bodyParam description string Nullable description or notes.
     *
     * @response 201 {
     *   "id": 1,
     *   "bank_name": "First Bank",
     *   "account_number": "1234567890",
     *   "account_name": "Business Account",
     *   "branch": "Main Branch",
     *   "account_type": "Savings",
     *   "balance": 15000.00,
     *   "description": "Primary business account",
     *   "created_at": "2025-09-17T12:00:00Z",
     *   "updated_at": "2025-09-17T12:00:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @urlParam bankAccount int required The ID of the bank account.
     * @bodyParam bank_name string Optional name of the bank.
     * @bodyParam account_number string Optional unique account number.
     * @bodyParam account_name string Optional account owner or designation.
     * @bodyParam branch string Nullable bank branch.
     * @bodyParam account_type string Nullable account type.
     * @bodyParam balance numeric Nullable current balance.
     * @bodyParam description string Nullable description or notes.
     *
     * @response {
     *   "id": 1,
     *   "bank_name": "First Bank Updated",
     *   "account_number": "1234567890",
     *   "account_name": "Business Account Updated",
     *   "branch": "Main Branch",
     *   "account_type": "Checking",
     *   "balance": 12000.00,
     *   "description": "Updated description",
     *   "created_at": "2025-09-17T12:00:00Z",
     *   "updated_at": "2025-09-17T13:00:00Z"
     * }
     *
     * @param Request $request
     * @param BankAccount $bankAccount
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @urlParam bankAccount int required The ID of the bank account.
     *
     * @response 204 {}
     *
     * @param BankAccount $bankAccount
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return response()->json(data: null, status: 204);
    }
}
