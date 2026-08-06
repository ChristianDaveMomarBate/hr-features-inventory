<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\ItemRequest;
use App\Models\ItemRequestItem;
use App\Models\User;
use App\Notifications\NewItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KioskController extends Controller
{
    // Divisions available in the kiosk
    private const DIVISIONS = [
        'Administrative Division',
        'Compensation & Benefits Division',
        'Office of the OIC-PHRMDO',
        'Performance Management Learning & Development/Wellness Division',
    ];

    /**
     * Show the public kiosk page (no auth required).
     * Returns available (in-stock) items for selection.
     */
    public function index()
    {
        return redirect()->route('login')->withFragment('kiosk');
    }

    /**
     * Handle the kiosk supply request submission (public, no auth).
     */
    public function store(Request $request)
    {
        $request->validate([
            'requester_name' => 'required|string|max:255',
            'division' => 'required|string|in:'.implode(',', self::DIVISIONS),
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $requesterName = trim($request->input('requester_name'));
        $division = $request->input('division');
        $requestItems = $request->input('items');

        $errors = [];
        $receiptItems = [];
        $itemRequest = null;

        $itemRequest = DB::transaction(function () use (
            $requesterName,
            $division,
            $requestItems,
            &$errors,
            &$receiptItems
        ) {
            // First check if items have enough stock
            foreach ($requestItems as $row) {
                $item = InventoryItem::find($row['id']);

                if (! $item) {
                    continue;
                }

                $qty = (int) $row['quantity'];

                if ($item->stock < $qty) {
                    $errors[] = "Insufficient stock for '{$item->name}'. Available: {$item->display_stock}, Requested: {$qty} {$item->display_unit}.";
                }
            }

            if (! empty($errors)) {
                return null;
            }

            // Create Item Request
            $itemRequest = ItemRequest::create([
                'requester_name' => $requesterName,
                'department' => $division, // Map division to department
                'purpose' => null,      // Kiosk doesn't have purpose
                'status' => 'Pending',
            ]);

            foreach ($requestItems as $row) {
                $item = InventoryItem::find($row['id']);

                if (! $item) {
                    continue;
                }

                $qty = (int) $row['quantity'];

                ItemRequestItem::create([
                    'item_request_id' => $itemRequest->id,
                    'inventory_item_id' => $item->id,
                    'requested_quantity' => $qty,
                ]);

                // We don't deduct stock here anymore. It will be deducted on approval.
                $receiptItems[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'quantity' => $qty,
                    'unit' => $item->display_unit,
                    'remaining_stock' => $item->stock,
                    'remaining_display_stock' => $item->display_stock,
                    'bulk_equivalent' => $item->bulk_equivalent,
                ];
            }

            return $itemRequest;
        });

        if (! empty($errors)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'errors' => $errors,
                ], 422);
            }

            return back()->with('kiosk_errors', $errors)->withInput();
        }

        if ($itemRequest) {
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewItemRequest($itemRequest));
            }
        }

        $receipt = [
            'number' => $itemRequest->control_number,
            'requester_name' => $requesterName,
            'division' => $division,
            'submitted_at' => now()->format('M d, Y h:i A'),
            'items' => $receiptItems,
            'total_quantity' => collect($receiptItems)->sum('quantity'),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'receipt' => $receipt,
            ]);
        }

        return back()->with('kiosk_receipt', $receipt);
    }
}
