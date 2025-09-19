<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders with associated supplier.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "supplier_id": 3,
     *     "order_number": "PO-12345",
     *     "status": "pending",
     *     "total_amount": 1000.00,
     *     "order_date": "2025-09-20",
     *     "expected_date": "2025-09-30",
     *     "notes": "Urgent order",
     *     "created_at": "2025-09-19T16:40:00Z",
     *     "updated_at": "2025-09-19T16:40:00Z",
     *     "supplier": {
     *       "id": 3,
     *       "name": "Supplier Name"
     *     }
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return PurchaseOrder::with(relations: 'supplier')->get();
    }

    /**
     * Display the specified purchase order with supplier details.
     *
     * @urlParam purchaseOrder int required The ID of the purchase order.
     *
     * @response {
     *   "id": 1,
     *   "supplier_id": 3,
     *   "order_number": "PO-12345",
     *   "status": "pending",
     *   "total_amount": 1000.00,
     *   "order_date": "2025-09-20",
     *   "expected_date": "2025-09-30",
     *   "notes": "Urgent order",
     *   "created_at": "2025-09-19T16:40:00Z",
     *   "updated_at": "2025-09-19T16:40:00Z",
     *   "supplier": {
     *     "id": 3,
     *     "name": "Supplier Name"
     *   }
     * }
     *
     * @param PurchaseOrder $purchaseOrder
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        return $purchaseOrder->load(relations: 'supplier');
    }

    /**
     * Store a newly created purchase order.
     *
     * @bodyParam supplier_id int required Supplier ID. Example: 3
     * @bodyParam order_number string required Unique order number. Example: PO-12345
     * @bodyParam status string Nullable Order status. One of: pending, approved, received, cancelled.
     * @bodyParam total_amount numeric Nullable Total amount. Example: 1000.00
     * @bodyParam order_date date Nullable Order date. Example: 2025-09-20
     * @bodyParam expected_date date Nullable Expected delivery date. Example: 2025-09-30
     * @bodyParam notes string Nullable Additional notes.
     *
     * @response 201 {
     *   "id": 1,
     *   "supplier_id": 3,
     *   "order_number": "PO-12345",
     *   "status": "pending",
     *   "total_amount": 1000.00,
     *   "order_date": "2025-09-20",
     *   "expected_date": "2025-09-30",
     *   "notes": "Urgent order",
     *   "created_at": "2025-09-19T16:40:00Z",
     *   "updated_at": "2025-09-19T16:40:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_number' => 'required|string|unique:purchase_orders,order_number',
            'status' => 'nullable|in:pending,approved,received,cancelled',
            'total_amount' => 'nullable|numeric|min:0',
            'order_date' => 'nullable|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $purchaseOrder = PurchaseOrder::create(attributes: $validated);

        return response()->json(data: $purchaseOrder, status: 201);
    }

    /**
     * Update the specified purchase order.
     *
     * @urlParam purchaseOrder int required The ID of the purchase order.
     * @bodyParam supplier_id int Nullable Supplier ID.
     * @bodyParam order_number string Nullable Unique order number.
     * @bodyParam status string Nullable Order status.
     * @bodyParam total_amount numeric Nullable Total amount.
     * @bodyParam order_date date Nullable Order date.
     * @bodyParam expected_date date Nullable Expected delivery date.
     * @bodyParam notes string Nullable Additional notes.
     *
     * @response {
     *   "id": 1,
     *   "supplier_id": 3,
     *   "order_number": "PO-12346",
     *   "status": "approved",
     *   "total_amount": 1100.00,
     *   "order_date": "2025-09-20",
     *   "expected_date": "2025-10-01",
     *   "notes": "Approved order",
     *   "created_at": "2025-09-19T16:40:00Z",
     *   "updated_at": "2025-09-19T17:00:00Z"
     * }
     *
     * @param Request $request
     * @param PurchaseOrder $purchaseOrder
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate(rules: [
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'order_number' => 'sometimes|string|unique:purchase_orders,order_number,' . $purchaseOrder->id,
            'status' => 'sometimes|in:pending,approved,received,cancelled',
            'total_amount' => 'nullable|numeric|min:0',
            'order_date' => 'nullable|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $purchaseOrder->update(attributes: $validated);

        return response()->json(data: $purchaseOrder);
    }

    /**
     * Remove the specified purchase order.
     *
     * @urlParam purchaseOrder int required The ID of the purchase order.
     *
     * @response 204 {}
     *
     * @param PurchaseOrder $purchaseOrder
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return response()->json(data: null, status: 204);
    }
}
