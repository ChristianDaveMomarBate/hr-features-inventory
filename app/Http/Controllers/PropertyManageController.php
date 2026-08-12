<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyManage;

class PropertyManageController extends Controller
{

    public function data(Request $request)
    {
        $query = PropertyManage::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('property_no', 'like', "%{$search}%")
                    ->orWhere('item_description', 'like', "%{$search}%")
                    ->orWhere('PAR_number', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhere('current_user', 'like', "%{$search}%");
            });
        }
        if ($request->filled('property_no')) {
            $query->where('property_no', 'like', '%' . $request->property_no . '%');
        }
        if ($request->filled('date_acquired')) {
            $query->whereDate('date_acquired', $request->date_acquired);
        }
        if ($request->filled('unit')) {
            $query->where('unit_of_measurement', $request->unit);
        }
        if ($request->filled('current_user')) {
            $query->where('current_user', 'like', '%' . $request->current_user . '%');
        }
        return response()->json(
            $query->latest()->paginate(10)
        );
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_no'          => 'required|string|max:255',
            'item_description'     => 'required|string',
            'date_acquired'        => 'required|date',
            'unit_of_measurement'  => 'required|string|max:100',
            'quantity'             => 'required|integer|min:1',
            'unit_value'           => 'required|numeric|min:0',
            'total_cost'           => 'required',
            'PAR_number'           => 'required|string|max:255',
            'remarks'              => 'nullable|string',
            'current_user'         => 'required|string|max:255',
            'attachment' => 'nullable|file|mimetypes:application/pdf|max:10240',
        ]);
        $validated['total_cost'] = str_replace(
            ',',
            '',
            $validated['total_cost']
        );
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store(
                'property_attachments',
                'public'
            );
            $validated['attachment'] = $path;
        }
        $validated['status'] = 'Active';
        $property = PropertyManage::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Property added successfully.',
            'data'    => $property
        ]);
    }
    public function delete(Request $request)
    {
        $property = PropertyManage::find($request->id);
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found.'
            ], 404);
        }
        $property->delete();
        return response()->json([
            'success' => true,
            'message' => 'Record succesfully deleted!'
        ]);
    }

    public function update(Request $request)
    {
        $property = PropertyManage::find($request->id);
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found.'
            ], 404);
        }
        $request->validate([
            'property_no' => 'required|string|max:255',
            'item_description' => 'required|string',
            'date_acquired' => 'required|date',
            'unit_of_measurement' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'unit_value' => 'required|numeric|min:0',
            'PAR_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'current_user' => 'nullable|string|max:255',
        ]);
        $quantity = $request->quantity;
        $unitValue = $request->unit_value;
        $totalCost = $quantity * $unitValue;
        $property->property_no = $request->property_no;
        $property->item_description = $request->item_description;
        $property->date_acquired = $request->date_acquired;
        $property->unit_of_measurement = $request->unit_of_measurement;
        $property->quantity = $quantity;
        $property->unit_value = $unitValue;
        $property->total_cost = $totalCost;
        $property->PAR_number = $request->PAR_number;
        $property->remarks = $request->remarks;
        $property->current_user = $request->current_user;
        $property->save();
        return response()->json([
            'success' => true,
            'message' => 'Property updated successfully.',
            'data' => $property
        ]);
    }
}
