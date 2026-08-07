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
            $query->latest()->paginate(11)
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
        ]);

        // Remove commas from formatted amount
        $validated['total_cost'] = str_replace(',', '', $validated['total_cost']);

        $validated['status'] = 'Active';

        $property = PropertyManage::create($validated);

        // AJAX request
        if ($request->ajax()) {

            return response()->json([
                'success' => true,
                'message' => 'Property added successfully.',
                'data'    => $property
            ]);
        }

        // Normal form submit
        return redirect()
            ->route('dashboard', ['page' => 'property-management'])
            ->with('success', 'Property added successfully.');
    }
}
