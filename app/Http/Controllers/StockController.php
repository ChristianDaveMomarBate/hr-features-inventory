<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\User;
use App\Notifications\LowStockAlert;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.type' => 'required|in:in,out,adjustment',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.handled_by' => 'required|string|max:255',
            'items.*.reference' => 'nullable|string|max:255',
            'items.*.remarks' => 'nullable|string',
        ]);

        $errors = [];

        DB::transaction(function () use ($request, $user, &$errors) {

            foreach ($request->items as $row) {

                $item = InventoryItem::findOrFail($row['inventory_item_id']);

                $type = strtolower($row['type']);
                $qty = (int) $row['quantity'];

                if ($type === 'out' && $item->stock < $qty) {
                    $errors[] = "Insufficient stock for <strong>{$item->name}</strong>. Available: {$item->display_stock}, Requested: {$qty} {$item->display_unit}.";

                    continue;
                }

                $oldStock = $item->stock;

                StockTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type' => $type,
                    'quantity' => $qty,
                    'handled_by' => $row['handled_by'],
                    'reference' => $row['reference'] ?? null,
                    'remarks' => $row['remarks'] ?? null,
                ]);

                match ($type) {
                    'in' => $item->stock += $qty,
                    'out' => $item->stock -= $qty,
                    'adjustment' => $item->stock = $qty,
                };

                $item->save();

                if (
                    in_array($type, ['out', 'adjustment'], true) &&
                    $item->stock <= $item->minimum
                ) {
                    Notification::send(
                        User::where('role', 'admin')->get(),
                        new LowStockAlert($item)
                    );
                }

                AuditTrail::create([
                    'user_id' => $user->id,
                    'action' => 'Stock '.ucfirst($type),
                    'module' => 'Stock Management',
                    'item_reference' => $item->code,
                    'old_value' => "Stock: {$oldStock}",
                    'new_value' => "Stock: {$item->display_stock} (Qty: {$qty} {$item->display_unit})",
                    'remarks' => trim(
                        "Handled by: {$row['handled_by']}".
                            (! empty($row['remarks']) ? " | {$row['remarks']}" : '')
                    ),
                ]);
            }
        });

        ReportService::generateMonthlyReportJson();

        if ($errors) {
            return redirect()
                ->route('dashboard', ['page' => 'stock-management'])
                ->with('error', implode('<br>', $errors));
        }

        return redirect()
            ->route('dashboard', ['page' => 'stock-management'])
            ->with('success', 'Stock transaction(s) recorded successfully.');
    }

    public function destroy($id)
    {
        $tx = StockTransaction::with('inventoryItem')->findOrFail($id);
        $item = $tx->inventoryItem;

        // Reverse the stock effect
        if ($item) {
            $oldStock = $item->stock;

            if ($tx->type === 'in') {
                $item->stock -= $tx->quantity;
            } elseif ($tx->type === 'out') {
                $item->stock += $tx->quantity;
            } elseif ($tx->type === 'adjustment') {
                // Cannot reliably reverse adjustments, just log it
            }

            $item->stock = max(0, $item->stock);
            $item->save();

            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'Deleted Transaction',
                'module' => 'Stock Management',
                'item_reference' => $item->code,
                'old_value' => "Stock: {$oldStock} | Tx Type: {$tx->type}, Qty: {$tx->quantity}",
                'new_value' => "Stock: {$item->stock} (transaction deleted)",
                'remarks' => 'Transaction deleted by admin.',
            ]);
        }

        $tx->delete();
        ReportService::generateMonthlyReportJson();

        return redirect()->route('dashboard', ['page' => 'stock-management'])
            ->with('success', 'Transaction deleted and stock reversed successfully.');
    }

    public function update(Request $request, $id)
    {
        $tx = StockTransaction::with('inventoryItem')->findOrFail($id);
        $item = $tx->inventoryItem;

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'handled_by' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $newQty = (int) $request->input('quantity');
        $oldQty = $tx->quantity;

        DB::transaction(function () use ($tx, $item, $newQty, $oldQty, $request) {
            $oldStock = $item ? $item->stock : 0;

            if ($item) {
                // Reverse old transaction
                if ($tx->type === 'in') {
                    $item->stock -= $oldQty;
                } elseif ($tx->type === 'out') {
                    $item->stock += $oldQty;
                }

                // Apply new transaction
                if ($tx->type === 'in') {
                    $item->stock += $newQty;
                } elseif ($tx->type === 'out') {
                    $item->stock -= $newQty;
                }

                $item->stock = max(0, $item->stock);
                $item->save();

                // Low stock alert
                if ($tx->type === 'out' && $item->stock <= $item->minimum) {
                    User::where('role', 'admin')->get()->each->notify(new LowStockAlert($item));
                }

                AuditTrail::create([
                    'user_id' => Auth::id(),
                    'action' => 'Updated Transaction',
                    'module' => 'Stock Management',
                    'item_reference' => $item->code,
                    'old_value' => "Stock: {$oldStock} | Qty: {$oldQty}",
                    'new_value' => "Stock: {$item->stock} | Qty: {$newQty}",
                    'remarks' => 'Transaction updated by admin.',
                ]);
            }

            $tx->update([
                'quantity' => $newQty,
                'handled_by' => $request->input('handled_by'),
                'reference' => $request->input('reference'),
                'remarks' => $request->input('remarks'),
            ]);
        });

        ReportService::generateMonthlyReportJson();

        return redirect()->route('dashboard', ['page' => 'stock-management'])
            ->with('success', 'Transaction updated successfully.');
    }
}
