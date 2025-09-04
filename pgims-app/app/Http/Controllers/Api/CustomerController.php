<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return response()->json(Customer::paginate(25));
    }

    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($data);
        return response()->json($customer, 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $customer->update($data);
        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(null, 204);
    }
}
