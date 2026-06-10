<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\AuditTrail;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'type' => Auth::user()->isAdmin() ? 'required|in:in,out,adjustment' : 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'remarks' => 'nullable|string'
        ]);

        $item = InventoryItem::findOrFail($validatedData['inventory_item_id']);

        // Check if there is enough stock for 'out' transactions
        if ($validatedData['type'] === 'out' && $item->stock < $validatedData['quantity']) {
            return redirect()->route('dashboard', ['page' => 'stock-management'])
                             ->with('error', 'Insufficient stock for this item.');
        }

        // Save transaction
        $transaction = StockTransaction::create($validatedData);

        $oldStock = $item->stock;

        // Update item stock
        if ($validatedData['type'] === 'in') {
            $item->stock += $validatedData['quantity'];
        } elseif ($validatedData['type'] === 'out') {
            $item->stock -= $validatedData['quantity'];
        } elseif ($validatedData['type'] === 'adjustment') {
            // For adjustment, let's assume the quantity is the absolute adjustment amount, 
            // but normally it could be setting a specific number. 
            // Let's implement adjustment as setting to the exact quantity provided.
            $item->stock = $validatedData['quantity'];
        }
        $item->save();

        if (
            in_array($validatedData['type'], ['out', 'adjustment'], true) &&
            $item->stock <= $item->minimum
        ) {
            User::where('role', 'admin')
                ->get()
                ->each
                ->notify(new LowStockAlert($item));
        }

        // Create Audit Trail
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'Stock ' . ucfirst($validatedData['type']),
            'module' => 'Stock Management',
            'item_reference' => $item->code,
            'old_value' => "Stock: " . $oldStock,
            'new_value' => "Stock: " . $item->stock . " (Qty: " . $validatedData['quantity'] . ")",
            'remarks' => $validatedData['remarks']
        ]);

        return redirect()->route('dashboard', ['page' => 'stock-management'])
                         ->with('success', 'Stock transaction recorded successfully.');
    }
}
