<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "name": "Supplier A",
     *     "contact_name": "John Doe",
     *     "email": "contact@example.com",
     *     "phone": "123-456-7890",
     *     "address": "123 Supplier St.",
     *     "description": "Preferred vendor",
     *     "created_at": "2025-09-19T18:06:00Z",
     *     "updated_at": "2025-09-19T18:06:00Z"
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Supplier::all();
    }

    /**
     * Display the specified supplier.
     *
     * @urlParam supplier int required The ID of the supplier.
     *
     * @response {
     *   "id": 1,
     *   "name": "Supplier A",
     *   "contact_name": "John Doe",
     *   "email": "contact@example.com",
     *   "phone": "123-456-7890",
     *   "address": "123 Supplier St.",
     *   "description": "Preferred vendor",
     *   "created_at": "2025-09-19T18:06:00Z",
     *   "updated_at": "2025-09-19T18:06:00Z"
     * }
     *
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Supplier $supplier)
    {
        return $supplier;
    }

    /**
     * Store a newly created supplier.
     *
     * @bodyParam name string required Supplier name. Example: Supplier A
     * @bodyParam contact_name string Nullable Contact person's name. Example: John Doe
     * @bodyParam email string Nullable Unique contact email. Example: contact@example.com
     * @bodyParam phone string Nullable Contact phone number. Example: 123-456-7890
     * @bodyParam address string Nullable Supplier address.
     * @bodyParam description string Nullable Additional info about the supplier.
     *
     * @response 201 {
     *   "id": 1,
     *   "name": "Supplier A",
     *   "contact_name": "John Doe",
     *   "email": "contact@example.com",
     *   "phone": "123-456-7890",
     *   "address": "123 Supplier St.",
     *   "description": "Preferred vendor",
     *   "created_at": "2025-09-19T18:06:00Z",
     *   "updated_at": "2025-09-19T18:06:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @urlParam supplier int required The ID of the supplier.
     * @bodyParam name string Nullable Updated supplier name.
     * @bodyParam contact_name string Nullable Updated contact name.
     * @bodyParam email string Nullable Updated email.
     * @bodyParam phone string Nullable Updated phone.
     * @bodyParam address string Nullable Updated address.
     * @bodyParam description string Nullable Updated description.
     *
     * @response {
     *   "id": 1,
     *   "name": "Supplier A Updated",
     *   "contact_name": "Jane Smith",
     *   "email": "contactnew@example.com",
     *   "phone": "987-654-3210",
     *   "address": "456 New Supplier St.",
     *   "description": "Updated vendor info",
     *   "created_at": "2025-09-19T18:06:00Z",
     *   "updated_at": "2025-09-19T18:45:00Z"
     * }
     *
     * @param Request $request
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @urlParam supplier int required The ID of the supplier.
     *
     * @response 204 {}
     *
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->json(data: null, status: 204);
    }
}