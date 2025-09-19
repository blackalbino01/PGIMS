<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockRequisition;

class StockRequisitionController extends Controller
{
    /**
     * Display a listing of stock requisitions with related stores, approver, and items.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "from_store_id": 2,
     *     "to_store_id": 3,
     *     "status": "pending",
     *     "approved_by": 5,
     *     "created_at": "2025-09-19T16:44:00Z",
     *     "updated_at": "2025-09-19T16:44:00Z",
     *     "fromStore": { "id": 2, "name": "Store A" },
     *     "toStore": { "id": 3, "name": "Store B" },
     *     "approvedBy": { "id": 5, "name": "Manager" },
     *     "items": [
     *       / Array of requisition items /
     *     ]
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return StockRequisition::with(relations: ['fromStore', 'toStore', 'approvedBy', 'items'])->get();
    }



    /**
     * Store a newly created stock requisition.
     *
     * @bodyParam from_store_id int required ID of the source store.
     * @bodyParam to_store_id int required ID of the destination store (different from from_store_id).
     * @bodyParam status string required Status of the requisition. Example: pending
     * @bodyParam approved_by int Nullable User ID who approved the requisition.
     *
     * @response 201 {
     *   "id": 1,
     *   "from_store_id": 2,
     *   "to_store_id": 3,
     *   "status": "pending",
     *   "approved_by": 5,
     *   "created_at": "2025-09-19T16:44:00Z",
     *   "updated_at": "2025-09-19T16:44:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'status' => 'required|string',
            'approved_by' => 'nullable|exists:users,id',
        ]);

        $stockRequisition = StockRequisition::create(attributes: $validated);

        return response()->json(data: $stockRequisition, status: 201);
    }

    /**
     * Display a specific stock requisition with details.
     *
     * @urlParam stockRequisition int required The ID of the stock requisition.
     *
     * @response {
     *   "id": 1,
     *   "from_store_id": 2,
     *   "to_store_id": 3,
     *   "status": "pending",
     *   "approved_by": 5,
     *   "created_at": "2025-09-19T16:44:00Z",
     *   "updated_at": "2025-09-19T16:44:00Z",
     *   "fromStore": { "id": 2, "name": "Store A" },
     *   "toStore": { "id": 3, "name": "Store B" },
     *   "approvedBy": { "id": 5, "name": "Manager" },
     *   "items": [
     *     / Array of requisition items /
     *   ]
     * }
     *
     * @param StockRequisition $stockRequisition
     * @return \Illuminate\Http\JsonResponse
     */

    public function show(StockRequisition $stockRequisition)
    {
        return $stockRequisition->load(relations: ['fromStore', 'toStore', 'approvedBy', 'items']);
    }

    /**
     * Update the specified stock requisition.
     *
     * @urlParam stockRequisition int required The ID of the stock requisition.
     * @bodyParam from_store_id int Nullable ID of the source store.
     * @bodyParam to_store_id int Nullable ID of the destination store (must be different from from_store_id).
     * @bodyParam status string Nullable Status of the requisition.
     * @bodyParam approved_by int Nullable User ID who approved the requisition.
     *
     * @response {
     *   "id": 1,
     *   "from_store_id": 2,
     *   "to_store_id": 3,
     *   "status": "approved",
     *   "approved_by": 5,
     *   "created_at": "2025-09-19T16:44:00Z",
     *   "updated_at": "2025-09-19T17:00:00Z"
     * }
     *
     * @param Request $request
     * @param StockRequisition $stockRequisition
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, StockRequisition $stockRequisition)
    {
        $validated = $request->validate(rules: [
            'from_store_id' => 'sometimes|exists:stores,id',
            'to_store_id' => 'sometimes|exists:stores,id|different:from_store_id',
            'status' => 'sometimes|string',
            'approved_by' => 'nullable|exists:users,id',
        ]);

        $stockRequisition->update(attributes: $validated);

        return response()->json(data: $stockRequisition);
    }

    /**
     * Remove the specified stock requisition.
     *
     * @urlParam stockRequisition int required The ID of the stock requisition.
     *
     * @response 204 {}
     *
     * @param StockRequisition $stockRequisition
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( StockRequisition $stockRequisition)
    {
        $stockRequisition->delete();

        return response()->json(data: null, status: 204);
    }
}