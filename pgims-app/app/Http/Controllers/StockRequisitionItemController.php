<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockRequisitionItem;

class StockRequisitionItemController extends Controller
{
    /**
     * Display a listing of stock requisition items with related stock requisition and product.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "stock_requisition_id": 2,
     *     "product_id": 5,
     *     "quantity": 10,
     *     "created_at": "2025-09-19T17:00:00Z",
     *     "updated_at": "2025-09-19T17:00:00Z",
     *     "stockRequisition": {
     *       "id": 2,
     *       "status": "pending",
     *       // other stock requisition fields
     *     },
     *     "product": {
     *       "id": 5,
     *       "name": "Product A"
     *     }
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return StockRequisitionItem::with(relations: ['stockRequisition', 'product'])->get();
    }


    /**
     * Store a newly created stock requisition item.
     *
     * @bodyParam stock_requisition_id int required ID of the stock requisition. Example: 2
     * @bodyParam product_id int required ID of the product. Example: 5
     * @bodyParam quantity int required Quantity requested. Minimum 1. Example: 10
     *
     * @response 201 {
     *   "id": 1,
     *   "stock_requisition_id": 2,
     *   "product_id": 5,
     *   "quantity": 10,
     *   "created_at": "2025-09-19T17:00:00Z",
     *   "updated_at": "2025-09-19T17:00:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'stock_requisition_id' => 'required|exists:stock_requisitions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = StockRequisitionItem::create(attributes: $validated);

        return response()->json(data: $item, status: 201);
    }

    /**
     * Display the specified stock requisition item with related data.
     *
     * @urlParam stockRequisitionItem int required The ID of the stock requisition item.
     *
     * @response {
     *   "id": 1,
     *   "stock_requisition_id": 2,
     *   "product_id": 5,
     *   "quantity": 10,
     *   "created_at": "2025-09-19T17:00:00Z",
     *   "updated_at": "2025-09-19T17:00:00Z",
     *   "stockRequisition": {
     *     "id": 2,
     *     "status": "pending"
     *   },
     *   "product": {
     *     "id": 5,
     *     "name": "Product A"
     *   }
     * }
     *
     * @param StockRequisitionItem $stockRequisitionItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(StockRequisitionItem $stockRequisitionItem)
    {
        return $stockRequisitionItem->load(relations: ['stockRequisition', 'product']);
    }



    /**
     * Update the specified stock requisition item.
     *
     * @urlParam stockRequisitionItem int required The ID of the stock requisition item.
     * @bodyParam stock_requisition_id int Nullable ID of the stock requisition.
     * @bodyParam product_id int Nullable ID of the product.
     * @bodyParam quantity int Nullable Updated quantity.
     *
     * @response {
     *   "id": 1,
     *   "stock_requisition_id": 2,
     *   "product_id": 5,
     *   "quantity": 15,
     *   "created_at": "2025-09-19T17:00:00Z",
     *   "updated_at": "2025-09-19T18:00:00Z"
     * }
     *
     * @param Request $request
     * @param StockRequisitionItem $stockRequisitionItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, StockRequisitionItem $stockRequisitionItem)
    {
        $validated = $request->validate(rules: [
            'stock_requisition_id' => 'sometimes|exists:stock_requisitions,id',
            'product_id' => 'sometimes|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $stockRequisitionItem->update(attributes: $validated);

        return response()->json(data: $stockRequisitionItem);
    }

    /**
     * Remove the specified stock requisition item.
     *
     * @urlParam stockRequisitionItem int required The ID of the stock requisition item.
     *
     * @response 204 {}
     *
     * @param StockRequisitionItem $stockRequisitionItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(StockRequisitionItem $stockRequisitionItem)
    {
        $stockRequisitionItem->delete();
        return response()->json(data: null, status: 204);
    }
}