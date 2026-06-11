<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\AuditTrail;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $validTypes = Auth::user()->isAdmin() ? 'in,out,adjustment' : 'in,out';

        // Validate the batch items array
        $request->validate([
            'items'                      => 'required|array|min:1',
            'items.*.inventory_item_id'  => 'required|exists:inventory_items,id',
            'items.*.type'               => 'required|in:' . $validTypes,
            'items.*.quantity'           => 'required|integer|min:1',
            'items.*.reference'          => 'nullable|string|max:255',
            'items.*.remarks'            => 'nullable|string',
        ]);

        $items = $request->input('items');

        $errors = [];

        DB::transaction(function () use ($items, $reference, $remarks, &$errors) {
            foreach ($items as $index => $row) {
                $item = InventoryItem::findOrFail($row['inventory_item_id']);
                $type = strtolower($row['type']);
                $qty  = (int) $row['quantity'];

                // Check stock for 'out' transactions
                if ($type === 'out' && $item->stock < $qty) {
                    $errors[] = "Insufficient stock for <strong>{$item->name}</strong>. Available: {$item->stock}, Requested: {$qty}.";
                    continue;
                }

                $oldStock = $item->stock;

                $reference = $row['reference'] ?? null;
                $remarks   = $row['remarks']   ?? null;

                // Save transaction
                StockTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type'              => $type,
                    'quantity'          => $qty,
                    'reference'         => $reference,
                    'remarks'           => $remarks,
                ]);

                // Update stock
                if ($type === 'in') {
                    $item->stock += $qty;
                } elseif ($type === 'out') {
                    $item->stock -= $qty;
                } elseif ($type === 'adjustment') {
                    $item->stock = $qty;
                }
                $item->save();

                // Low stock alert
                if (in_array($type, ['out', 'adjustment'], true) && $item->stock <= $item->minimum) {
                    User::where('role', 'admin')->get()->each->notify(new LowStockAlert($item));
                }

                // Audit trail
                AuditTrail::create([
                    'user_id'        => Auth::id(),
                    'action'         => 'Stock ' . ucfirst($type),
                    'module'         => 'Stock Management',
                    'item_reference' => $item->code,
                    'old_value'      => "Stock: {$oldStock}",
                    'new_value'      => "Stock: {$item->stock} (Qty: {$qty})",
                    'remarks'        => $remarks,
                ]);
            }
        });

        if (!empty($errors)) {
            return redirect()->route('dashboard', ['page' => 'stock-management'])
                ->with('error', implode('<br>', $errors));
        }

        return redirect()->route('dashboard', ['page' => 'stock-management'])
            ->with('success', 'Stock transaction(s) recorded successfully.');
    }
}
