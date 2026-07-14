<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\NewItemRequest;
use Illuminate\Support\Facades\DB;

class ItemRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requester_name'               => 'required|string|max:255',
            'department'                   => 'required|string|max:255',
            'purpose'                      => 'nullable|string',
            'items'                        => 'required|array|min:1',
            'items.*.item_id'              => 'required|exists:inventory_items,id',
            'items.*.requested_quantity'   => 'required|integer|min:1',
        ]);

        $itemRequest = DB::transaction(function () use ($validated) {
            $itemRequest = \App\Models\ItemRequest::create([
                'requester_name' => $validated['requester_name'],
                'department'     => $validated['department'],
                'purpose'        => $validated['purpose'],
                'status'         => 'Pending',
            ]);

            foreach ($validated['items'] as $item) {
                \App\Models\ItemRequestItem::create([
                    'item_request_id'    => $itemRequest->id,
                    'inventory_item_id'  => $item['item_id'],
                    'requested_quantity' => $item['requested_quantity'],
                ]);
            }

            return $itemRequest;
        });

        // Notify admins about the new request using Laravel's notification system
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewItemRequest($itemRequest));
        }

        return redirect()->back()
            ->with('new_request_id', $itemRequest->id)
            ->with('show_receipt_modal', true)
            ->with('success', 'Your request has been submitted successfully!');
    }

    public function track(Request $request)
    {
        $requestId   = $request->input('request_id');
        $itemRequest = null;
        if ($requestId) {
            $itemRequest = \App\Models\ItemRequest::with(['item', 'approver', 'requestItems.item'])->find($requestId);
        }

        return view('auth.track-request', compact('itemRequest', 'requestId'));
    }

    public function receipt($id)
    {
        $itemRequest = \App\Models\ItemRequest::with(['item', 'requestItems.item'])->findOrFail($id);
        return view('auth.request-receipt', compact('itemRequest'));
    }

    public function updateStatus(Request $request, $id)
    {
        $itemRequest = \App\Models\ItemRequest::findOrFail($id);

        $validated = $request->validate([
            'status'            => 'required|in:Approved,Adjusted,Cancelled',
            'approve_items'     => 'nullable|array',
            'item_quantities'   => 'nullable|array',
            'approved_quantity' => 'nullable|integer|min:0',
            'admin_note'        => 'nullable|string',
            'item_remarks'      => 'nullable|array',
        ]);

        $status = $validated['status'];

        if ($status === 'Cancelled') {
            $itemRequest->update([
                'status'      => 'Cancelled',
                'admin_note'  => $validated['admin_note'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            return redirect()->back()->with('success', 'Request #' . $id . ' has been cancelled.');
        }

        if (in_array($status, ['Approved', 'Adjusted'])) {
            $isMultiItem = $itemRequest->requestItems()->exists();

            if ($isMultiItem) {
                try {
                    \Illuminate\Support\Facades\DB::transaction(function () use ($itemRequest, $validated, $status) {
                        $items = $itemRequest->requestItems()->with('item')->get();
                        
                        $approvedItemsIds = $validated['approve_items'] ?? [];
                        $itemQuantities = $validated['item_quantities'] ?? [];
                        
                        // Check all stock first
                        foreach ($items as $reqItem) {
                            if (!in_array($reqItem->id, $approvedItemsIds)) continue;

                            $qty = $itemQuantities[$reqItem->id] ?? 0;
                            if ($qty > $reqItem->item->stock) {
                                throw new \Exception('Cannot approve: Insufficient stock for ' . $reqItem->item->name . ' (Only ' . $reqItem->item->stock . ' available).');
                            }
                        }
                        
                        // Deduct stock and log transactions
                        foreach ($items as $reqItem) {
                            if (!in_array($reqItem->id, $approvedItemsIds)) {
                                $reqItem->update(['approved_quantity' => 0, 'remarks' => $validated['item_remarks'][$reqItem->id] ?? null]);
                                continue;
                            }
                            
                            $qty = $itemQuantities[$reqItem->id] ?? 0;
                            if ($qty <= 0) {
                                $reqItem->update(['approved_quantity' => 0, 'remarks' => $validated['item_remarks'][$reqItem->id] ?? null]);
                                continue;
                            }

                            $inventoryItem = $reqItem->item;
                            $inventoryItem->stock -= $qty;
                            $inventoryItem->save();
                            
                            $itemRemarks = $validated['item_remarks'][$reqItem->id] ?? null;

                            $reqItem->update([
                                'approved_quantity' => $qty,
                                'remarks'           => $itemRemarks
                            ]);

                            \App\Models\StockTransaction::create([
                                'inventory_item_id' => $inventoryItem->id,
                                'user_id'           => auth()->id(),
                                'type'              => 'out',
                                'quantity'          => $qty,
                                'reference'         => 'Item Request #' . $itemRequest->id . ' — approved for ' . $itemRequest->requester_name,
                                'balance'           => $inventoryItem->stock,
                            ]);
                        }
                        
                        $itemRequest->update([
                            'status'      => $status,
                            'admin_note'  => $validated['admin_note'] ?? null,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    });

                    \App\Services\ReportService::generateMonthlyReportJson();
                    
                    return redirect()->back()->with('success', 'Multi-item Request #' . $itemRequest->id . ' has been processed successfully.');
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }

            } else {
                $approvedItemsIds = $validated['approve_items'] ?? [];
                if (!in_array('single', $approvedItemsIds)) {
                    $approvedQty = 0;
                } else {
                    $approvedQty = $validated['approved_quantity'] ?? $itemRequest->requested_quantity;
                }
                
                $inventoryItem = $itemRequest->item;

                if ($approvedQty > $inventoryItem->stock) {
                    return redirect()->back()->with('error', 'Cannot approve a quantity greater than available stock (' . $inventoryItem->stock . ').');
                }

                if ($approvedQty > 0) {
                    // Deduct stock
                    $inventoryItem->stock -= $approvedQty;
                    $inventoryItem->save();

                    // Record audit trail
                    \App\Models\StockTransaction::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'user_id'           => auth()->id(),
                        'type'              => 'out',
                        'quantity'          => $approvedQty,
                        'reference'         => 'Item Request #' . $itemRequest->id . ' — approved for ' . $itemRequest->requester_name,
                        'balance'           => $inventoryItem->stock,
                    ]);
                }

                // Determine actual status: Adjusted if qty differs from request
                $finalStatus = ($approvedQty < $itemRequest->requested_quantity && $status === 'Approved') ? 'Adjusted' : $status;

                $singleRemark = $validated['item_remarks']['single'] ?? null;

                $itemRequest->update([
                    'status'            => $finalStatus,
                    'approved_quantity' => $approvedQty,
                    'admin_note'        => $validated['admin_note'],
                    'remarks'           => $singleRemark,
                    'approved_by'       => auth()->id(),
                    'approved_at'       => now(),
                ]);

                \App\Services\ReportService::generateMonthlyReportJson();

                return redirect()->back()->with('success', 'Request #' . $id . ' has been processed successfully.');
            }
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }

    public function revert($id)
    {
        $itemRequest = \App\Models\ItemRequest::findOrFail($id);

        if (!in_array($itemRequest->status, ['Approved', 'Adjusted'])) {
            return redirect()->back()->with('error', 'Only Approved or Adjusted requests can be reverted.');
        }

        DB::transaction(function () use ($itemRequest) {
            $isMultiItem = $itemRequest->requestItems()->exists();

            if ($isMultiItem) {
                // Restore stock for each item in a multi-item request
                $items = $itemRequest->requestItems()->with('item')->get();
                foreach ($items as $reqItem) {
                    if ($reqItem->approved_quantity && $reqItem->item) {
                        $reqItem->item->stock += $reqItem->approved_quantity;
                        $reqItem->item->save();

                        // Log the reversal in audit trail
                        \App\Models\StockTransaction::create([
                            'inventory_item_id' => $reqItem->item->id,
                            'user_id'           => auth()->id(),
                            'type'              => 'in',
                            'quantity'          => $reqItem->approved_quantity,
                            'reference'         => 'REVERT: Item Request #' . $itemRequest->id . ' — stock restored for ' . $itemRequest->requester_name,
                            'balance'           => $reqItem->item->stock,
                        ]);

                        // Clear the approved quantity
                        $reqItem->update(['approved_quantity' => null]);
                    }
                }
            } else {
                // Single-item request
                $inventoryItem = $itemRequest->item;
                if ($inventoryItem && $itemRequest->approved_quantity) {
                    $inventoryItem->stock += $itemRequest->approved_quantity;
                    $inventoryItem->save();

                    \App\Models\StockTransaction::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'user_id'           => auth()->id(),
                        'type'              => 'in',
                        'quantity'          => $itemRequest->approved_quantity,
                        'reference'         => 'REVERT: Item Request #' . $itemRequest->id . ' — stock restored for ' . $itemRequest->requester_name,
                        'balance'           => $inventoryItem->stock,
                    ]);
                }
            }

            // Reset the request back to Pending
            $itemRequest->update([
                'status'            => 'Pending',
                'approved_quantity' => null,
                'approved_by'       => null,
                'approved_at'       => null,
                'admin_note'        => null,
            ]);
        });

        \App\Services\ReportService::generateMonthlyReportJson();

        return redirect()->back()->with('success', 'Request #' . $id . ' has been reverted to Pending. Stock has been restored.');
    }

    public function destroy($id)
    {
        $itemRequest = \App\Models\ItemRequest::findOrFail($id);
        $itemRequest->delete();

        return redirect()->back()->with('success', 'Request #' . $id . ' has been deleted successfully.');
    }
}
