<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index()
    {
        return Customer::all();
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        return $customer;
    }

    /**
     * Store a new customer.
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

        $customer = Customer::create(attributes: $validated);

        return response()->json(data: $customer, status: 201);
    }

    /**
     * Update the specified customer.
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
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(data: null, status: 204);
    }
}
