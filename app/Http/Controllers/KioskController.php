<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\AuditTrail;
use App\Models\User;
use App\Notifications\LowStockAlert;
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
        $items = InventoryItem::where('stock', '>', 0)
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'category', 'type', 'unit', 'stock_unit', 'issue_unit', 'units_per_stock_unit', 'stock', 'minimum', 'description', 'location']);

        $divisions = self::DIVISIONS;

        return view('kiosk.index', compact('items', 'divisions'));
    }

    /**
     * Handle the kiosk supply request submission (public, no auth).
     */
    public function store(Request $request)
    {
        $request->validate([
            'requester_name'   => 'required|string|max:255',
            'division'         => 'required|string|in:' . implode(',', self::DIVISIONS),
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $requesterName = trim($request->input('requester_name'));
        $division      = $request->input('division');
        $handledBy     = $requesterName . ' – ' . $division;
        $requestItems  = $request->input('items');

        $errors = [];
        $receiptItems = [];

        DB::transaction(function () use ($requestItems, $handledBy, &$errors, &$receiptItems) {
            foreach ($requestItems as $row) {
                $item = InventoryItem::find($row['id']);
                if (!$item) continue;

                $qty = (int) $row['quantity'];

                if ($item->stock < $qty) {
                    $errors[] = "Insufficient stock for <strong>{$item->name}</strong>. Available: {$item->display_stock}, Requested: {$qty} {$item->display_unit}.";
                    continue;
                }

                $oldStock = $item->stock;

                StockTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'out',
                    'quantity'          => $qty,
                    'handled_by'        => $handledBy,
                    'reference'         => 'Kiosk – Stock Out',
                    'remarks'           => "Stock out submitted via kiosk by {$handledBy}.",
                ]);

                $item->stock -= $qty;
                $item->save();

                $receiptItems[] = [
                    'id'              => $item->id,
                    'name'     => $item->name,
                    'quantity' => $qty,
                    'unit'     => $item->display_unit,
                    'remaining_stock' => $item->stock,
                    'remaining_display_stock' => $item->display_stock,
                    'bulk_equivalent' => $item->bulk_equivalent,
                ];

                // Trigger low stock alert for admins
                if ($item->stock <= $item->minimum) {
                    User::where('role', 'admin')->get()->each->notify(new LowStockAlert($item));
                }

                AuditTrail::create([
                    'user_id'        => null,
                    'action'         => 'Stock Out',
                    'module'         => 'Kiosk',
                    'item_reference' => $item->code,
                    'old_value'      => "Stock: {$oldStock}",
                    'new_value'      => "Stock: {$item->stock} (Qty: {$qty})",
                    'remarks'        => "Kiosk request by: {$handledBy}",
                ]);
            }
        });

        if (!empty($errors)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'     => false,
                    'errors' => $errors,
                ], 422);
            }

            return back()->with('kiosk_errors', $errors)->withInput();
        }

        $receipt = [
            'number'         => 'K-' . now()->format('YmdHis'),
            'requester_name' => $requesterName,
            'division'       => $division,
            'submitted_at'   => now()->format('M d, Y h:i A'),
            'items'          => $receiptItems,
            'total_quantity' => collect($receiptItems)->sum('quantity'),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'receipt' => $receipt,
            ]);
        }

        return back()->with('kiosk_receipt', $receipt);
    }
}
