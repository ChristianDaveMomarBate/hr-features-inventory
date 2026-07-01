<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\NewItemRequest;

class ItemRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requester_name'     => 'required|string|max:255',
            'department'         => 'required|string|max:255',
            'item_id'            => 'required|exists:inventory_items,id',
            'requested_quantity' => 'required|integer|min:1',
            'purpose'            => 'nullable|string',
        ]);

        $itemRequest = \App\Models\ItemRequest::create([
            'requester_name'     => $validated['requester_name'],
            'department'         => $validated['department'],
            'item_id'            => $validated['item_id'],
            'requested_quantity' => $validated['requested_quantity'],
            'purpose'            => $validated['purpose'],
            'status'             => 'Pending',
        ]);

        // Notify admins about the new request using Laravel's notification system
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewItemRequest($itemRequest));
        }

        return redirect()->back()
            ->with('new_request_id', $itemRequest->id)
            ->with('success', 'Your request has been submitted! Your Request ID is #' . $itemRequest->id . '. Use it to track your request status.');
    }

    public function track(Request $request)
    {
        $requestId   = $request->input('request_id');
        $itemRequest = null;
        if ($requestId) {
            $itemRequest = \App\Models\ItemRequest::with('item', 'approver')->find($requestId);
        }

        return view('auth.track-request', compact('itemRequest', 'requestId'));
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

        if (in_array($status, ['Approved', 'Adjusted'])) {
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

        } elseif ($status === 'Cancelled') {
            $itemRequest->update([
                'status'      => 'Cancelled',
                'admin_note'  => $validated['admin_note'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Request #' . $id . ' has been cancelled.');
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
