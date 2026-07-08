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
            'approved_quantity' => 'nullable|integer|min:1',
            'admin_note'        => 'nullable|string',
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
                if ($status === 'Adjusted') {
                    return redirect()->back()->with('error', 'Cannot partially adjust a multi-item request from this interface. Please approve fully or cancel.');
                }
                
                try {
                    \Illuminate\Support\Facades\DB::transaction(function () use ($itemRequest, $validated) {
                        $items = $itemRequest->requestItems()->with('item')->get();
                        
                        // Check all stock first
                        foreach ($items as $reqItem) {
                            if ($reqItem->requested_quantity > $reqItem->item->stock) {
                                throw new \Exception('Cannot approve: Insufficient stock for ' . $reqItem->item->name . ' (Only ' . $reqItem->item->stock . ' available).');
                            }
                        }
                        
                        // Deduct stock and log transactions
                        foreach ($items as $reqItem) {
                            $inventoryItem = $reqItem->item;
                            $inventoryItem->stock -= $reqItem->requested_quantity;
                            $inventoryItem->save();
                            
                            $reqItem->update(['approved_quantity' => $reqItem->requested_quantity]);

                            \App\Models\StockTransaction::create([
                                'inventory_item_id' => $inventoryItem->id,
                                'user_id'           => auth()->id(),
                                'type'              => 'out',
                                'quantity'          => $reqItem->requested_quantity,
                                'reference'         => 'Item Request #' . $itemRequest->id . ' — approved for ' . $itemRequest->requester_name,
                                'balance'           => $inventoryItem->stock,
                            ]);
                        }
                        
                        $itemRequest->update([
                            'status'      => 'Approved',
                            'admin_note'  => $validated['admin_note'] ?? null,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    });
                    
                    return redirect()->back()->with('success', 'Multi-item Request #' . $id . ' has been approved successfully.');
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }

            } else {
                $approvedQty   = $validated['approved_quantity'] ?? $itemRequest->requested_quantity;
                $inventoryItem = $itemRequest->item;

                if ($approvedQty > $inventoryItem->stock) {
                    return redirect()->back()->with('error', 'Cannot approve a quantity greater than available stock (' . $inventoryItem->stock . ').');
                }

                // Deduct stock
                $inventoryItem->stock -= $approvedQty;
                $inventoryItem->save();

                // Determine actual status: Adjusted if qty differs from request
                $finalStatus = ($approvedQty < $itemRequest->requested_quantity) ? 'Adjusted' : 'Approved';

                // Record audit trail
                \App\Models\StockTransaction::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'user_id'           => auth()->id(),
                    'type'              => 'out',
                    'quantity'          => $approvedQty,
                    'reference'         => 'Item Request #' . $itemRequest->id . ' — approved for ' . $itemRequest->requester_name,
                    'balance'           => $inventoryItem->stock,
                ]);

                $itemRequest->update([
                    'status'            => $finalStatus,
                    'approved_quantity' => $approvedQty,
                    'admin_note'        => $validated['admin_note'],
                    'approved_by'       => auth()->id(),
                    'approved_at'       => now(),
                ]);

                return redirect()->back()->with('success', 'Request #' . $id . ' has been ' . strtolower($finalStatus) . ' successfully.');
            }
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }

    public function destroy($id)
    {
        $itemRequest = \App\Models\ItemRequest::findOrFail($id);
        $itemRequest->delete();

        return redirect()->back()->with('success', 'Request #' . $id . ' has been deleted successfully.');
    }
}
