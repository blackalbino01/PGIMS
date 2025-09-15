<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockRequisition;

class StockRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return StockRequisition::with(relations: ['fromStore', 'toStore', 'approvedBy', 'items'])->get();
    }



    /**
     * Store a newly created resource in storage.
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

    public function show(StockRequisition $stockRequisition)
    {
        return $stockRequisition->load(relations: ['fromStore', 'toStore', 'approvedBy', 'items']);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy( StockRequisition $stockRequisition)
    {
        $stockRequisition->delete();

        return response()->json(data: null, status: 204);
    }
}