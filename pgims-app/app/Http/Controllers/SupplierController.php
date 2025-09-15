<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index()
    {
        return Supplier::all();
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        return $supplier;
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $supplier = Supplier::create(attributes: $validated);

        return response()->json(data: $supplier, status: 201);
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate(rules: [
            'name' => 'sometimes|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $supplier->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $supplier->update(attributes: $validated);

        return response()->json(data: $supplier);
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->json(data: null, status: 204);
    }
}
