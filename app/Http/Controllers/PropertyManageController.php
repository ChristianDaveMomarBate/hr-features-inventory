<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyManage;
use App\Models\PropertyTransfer;
use App\Models\PropertyTransferItem;

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
    public function fetch(Request $request)
    {
        $search = $request->input('search');

        $properties = PropertyManage::query()
            ->where('status', 'Active')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('property_no', 'like', "%{$search}%")
                        ->orWhere('item_description', 'like', "%{$search}%")
                        ->orWhere('PAR_number', 'like', "%{$search}%")
                        ->orWhere('current_user', 'like', "%{$search}%");
                });
            })
            ->orderBy('property_no')
            ->get([
                'id',
                'property_no',
                'item_description',
                'date_acquired',
                'unit_of_measurement',
                'quantity',
                'unit_value',
                'total_cost',
                'PAR_number',
                'remarks',
                'current_user',
                'status',
                'attachment',
            ]);

        return response()->json([
            'success' => true,
            'data' => $properties,
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'transfer_date' => ['required', 'date'],
            'current_accountable_officer' => ['required', 'string'],
            'current_accountable_officer_office' => ['required', 'string'],
            'transferto_accountable_officer' => ['required', 'string'],
            'transferto_accountable_officer_office' => ['required', 'string'],
            'transfer_reason' => ['nullable', 'string'],
            'transfer_remarks' => ['nullable', 'string'],
            'prepared_by' => ['required', 'string'],
            'prepared_date' => ['required', 'date'],
            'approved_by' => ['required', 'string'],
            'approval_date' => ['required', 'date'],
            'properties' => ['required', 'array', 'min:1'],
            'properties.*.property_id' => ['required'],
            'properties.*.property_no' => ['required', 'string'],
            'properties.*.item_description' => ['required', 'string'],
            'properties.*.par_ics' => ['nullable', 'string'],
            'properties.*.quantity' => ['required', 'numeric', 'min:0'],
            'properties.*.unit_of_measurement' => ['required', 'string'],
            'properties.*.unit_value' => ['required', 'numeric', 'min:0'],
            'properties.*.date_acquired' => ['required', 'date'],
            'properties.*.total_cost' => ['required', 'numeric', 'min:0'],
            'properties.*.condition' => ['required', 'in:Serviceable,Unserviceable,For Repair'],
            'transfer_documents' => ['nullable', 'array'],
            'transfer_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        if (
            strtolower(trim($request->current_accountable_officer)) ===
            strtolower(trim($request->transferto_accountable_officer)) &&
            $request->current_accountable_officer_office ===
            $request->transferto_accountable_officer_office
        ) {
            return response()->json([
                'success' => false,
                'message' => 'The property cannot be transferred to the same accountable officer and office.',
            ], 422);
        }

        foreach ($request->properties as $property) {
            $existingTransfer = PropertyTransferItem::where('property_no', $property['property_no'])
                ->whereHas('propertyTransfer', function ($query) {
                    $query->whereIn('status', ['Pending', 'Acknowledged']);
                })
                ->exists();

            if ($existingTransfer) {
                return response()->json([
                    'success' => false,
                    'message' => "Property {$property['property_no']} already has an active transfer.",
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $transferUuid = (string) Str::uuid();
            $year = now()->year;

            $lastTransfer = PropertyTransfer::whereYear('created_at', $year)
                ->orderByDesc('id')
                ->first();

            $nextNumber = $lastTransfer
                ? ((int) Str::afterLast($lastTransfer->transfer_no, '-') + 1)
                : 1;

            $transferNo = 'PTR-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $documents = [];

            foreach ($request->file('transfer_documents', []) as $file) {
                $extension = $file->getClientOriginalExtension();
                $storedName = Str::random(20) . '.' . $extension;

                $path = $file->storeAs(
                    'property_transfers/' . $transferNo,
                    $storedName,
                    'public'
                );

                $documents[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'file_name' => $storedName,
                    'path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
            }

            $transfer = PropertyTransfer::create([
                'transfer_no' => $transferNo,
                'transfer_date' => $request->transfer_date,
                'items' => count($request->properties),
                'status' => 'Pending',
                'property_uuid' => 'PR' . $transferNo,
                'curent_accountable_officer' => $request->current_accountable_officer,
                'curent_accountable_officer_office' => $request->current_accountable_officer_office,
                'transferto_accountable_officer' => $request->transferto_accountable_officer,
                'transferto_accountable_officer_office' => $request->transferto_accountable_officer_office,
                'transfer_remarks' => $request->transfer_remarks,
                'transfer_attachment' => $documents ?: null,
                'transfer_approval_prepared_by' => $request->prepared_by,
                'transfer_approval_prepared_by_date' => $request->prepared_date,
                'transfer_approval_approved_by' => $request->approved_by,
                'transfer_approval_approved_by_date' => $request->approval_date,
            ]);

            foreach ($request->properties as $property) {
                PropertyTransferItem::create([
                    'transfer_uuids' => $transferUuid,
                    'property_transfer_id' => $transfer->id,
                    'property_no' => $property['property_no'],
                    'item_description' => $property['item_description'],
                    'par_ics' => $property['par_ics'] ?? null,
                    'quantity' => $property['quantity'],
                    'unit_of_measurement' => $property['unit_of_measurement'],
                    'unit_value' => $property['unit_value'],
                    'date_acquired' => $property['date_acquired'],
                    'total_cost' => $property['total_cost'],
                    'condition' => $property['condition'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer saved successfully.',
                'transfer_no' => $transferNo,
                'transfer_uuid' => $transferUuid,
                'transfer_id' => $transfer->id,
                'status' => $transfer->status,
                'items_count' => count($request->properties),
                'documents' => $documents,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Unable to save transfer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function history(Request $request)
    {
        $query = PropertyTransfer::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('transfer_no', 'like', "%{$search}%")
                    ->orWhere('curent_accountable_officer', 'like', "%{$search}%")
                    ->orWhere('transferto_accountable_officer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transfers = $query->orderByDesc('id')->paginate(10);

        return response()->json([
            'success' => true,
            'transfers' => $transfers->items(),
            'current_page' => $transfers->currentPage(),
            'last_page' => $transfers->lastPage(),
            'total' => $transfers->total(),
            'counts' => [
                'pending' => PropertyTransfer::where('status', 'Pending')->count(),
                'approved' => PropertyTransfer::where('status', 'Approved')->count(),
                'total' => PropertyTransfer::count(),
            ],
        ]);
    }

    public function historyAction(Request $request)
    {
        $validated=$request->validate([
            'id'=>'required|integer',
            'action'=>'required|in:approve,disapprove,cancel',
        ]);
        $transfer=PropertyTransfer::find($validated['id']);
        if(!$transfer){
            return response()->json([
                'success'=>false,
                'message'=>'Transfer transaction not found.'
            ],404);
        }
        $status=[
            'approve'=>'Approved',
            'disapprove'=>'Disapproved',
            'cancel'=>'Cancelled',
        ];
        $transfer->update([
            'status'=>$status[$validated['action']],
        ]);
        return response()->json([
            'success'=>true,
            'message'=>'Transfer '.$status[$validated['action']].' successfully.',
            'status'=>$transfer->status,
        ]);
    }
}
