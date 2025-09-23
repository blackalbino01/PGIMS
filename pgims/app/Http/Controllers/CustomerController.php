<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "name": "Jane Doe",
     *     "gender": "female",
     *     "phone": "1234567890",
     *     "email": "jane@example.com",
     *     "address": "123 Main St",
     *     "birthday": "1990-05-10",
     *     "balance": 2000.00,
     *     "credit_limit": 5000.00,
     *     "notes": "Loyal customer",
     *     "created_at": "2025-09-17T12:00:00Z",
     *     "updated_at": "2025-09-17T12:00:00Z"
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Customer::all();
    }

    /**
     * Display the specified customer.
     *
     * @urlParam customer int required The ID of the customer.
     *
     * @response {
     *   "id": 1,
     *   "name": "Jane Doe",
     *   "gender": "female",
     *   "phone": "1234567890",
     *   "email": "jane@example.com",
     *   "address": "123 Main St",
     *   "birthday": "1990-05-10",
     *   "balance": 2000.00,
     *   "credit_limit": 5000.00,
     *   "notes": "Loyal customer",
     *   "created_at": "2025-09-17T12:00:00Z",
     *   "updated_at": "2025-09-17T12:00:00Z"
     * }
     *
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Customer $customer)
    {
        return $customer;
    }

    /**
     * Store a new customer.
     *
     * @bodyParam name string required Customer's name. Example: Jane Doe
     * @bodyParam gender string Nullable gender. Example: female
     * @bodyParam phone string Nullable phone number. Example: 1234567890
     * @bodyParam email string Nullable unique email. Example: jane@example.com
     * @bodyParam address string Nullable address.
     * @bodyParam birthday date Nullable date of birth. Example: 1990-05-10
     * @bodyParam balance numeric Nullable initial balance. Example: 2000.00
     * @bodyParam credit_limit numeric Nullable credit limit. Example: 5000.00
     * @bodyParam notes string Nullable additional notes.
     *
     * @response 201 {
     *   "id": 1,
     *   "name": "Jane Doe",
     *   "gender": "female",
     *   "phone": "1234567890",
     *   "email": "jane@example.com",
     *   "address": "123 Main St",
     *   "birthday": "1990-05-10",
     *   "balance": 2000.00,
     *   "credit_limit": 5000.00,
     *   "notes": "Loyal customer",
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
            'name' => 'required|string|max:255',
            'gender' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'address' => 'nullable|string',
            'birthday' => 'nullable|date',
            'balance' => 'nullable|numeric|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;

        $customer = Customer::create(attributes: $validated);

        return response()->json(data: $customer, status: 201);
    }

    /**
     * Update the specified customer.
     *
     * @urlParam customer int required The ID of the customer.
     * @bodyParam name string Nullable updated name.
     * @bodyParam gender string Nullable updated gender.
     * @bodyParam phone string Nullable updated phone.
     * @bodyParam email string Nullable updated email.
     * @bodyParam address string Nullable updated address.
     * @bodyParam birthday date Nullable updated birthday.
     * @bodyParam balance numeric Nullable updated balance.
     * @bodyParam credit_limit numeric Nullable updated credit limit.
     * @bodyParam notes string Nullable updated notes.
     *
     * @response {
     *   "id": 1,
     *   "name": "Jane Doe Updated",
     *   "gender": "female",
     *   "phone": "0987654321",
     *   "email": "janeupdated@example.com",
     *   "address": "456 Another St",
     *   "birthday": "1990-05-10",
     *   "balance": 2500.00,
     *   "credit_limit": 5500.00,
     *   "notes": "Updated notes",
     *   "created_at": "2025-09-17T12:00:00Z",
     *   "updated_at": "2025-09-18T15:00:00Z"
     * }
     *
     * @param Request $request
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate(rules: [
            'name' => 'sometimes|string|max:255',
            'gender' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string',
            'birthday' => 'nullable|date',
            'balance' => 'nullable|numeric|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $customer->update(attributes: $validated);

        return response()->json(data: $customer);
    }

    /**
     * Deposit amount to customer's balance.
     *
     * @urlParam customer int required The ID of the customer.
     * @bodyParam amount numeric required Amount to deposit. Minimum 1.
     *
     * @response {
     *   "message": "Deposit successful",
     *   "customer": {
     *     "id": 1,
     *     "balance": 3000.00
     *   }
     * }
     *
     * @param Request $request
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(callback: function () use ($customer, $validated): void {
            $customer->balance += $validated['amount'];
            $customer->save();

        });

        return response()->json(data: [
            'message' => 'Deposit successful',
            'customer' => $customer
        ]);
    }

    /**
     * Remove the specified customer.
     *
     * @urlParam customer int required The ID of the customer.
     *
     * @response 204 {}
     *
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(data: null, status: 204);
    }
}
