<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\AuditTrail;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    private const CATEGORIES = [
        'Office Supplies',
        'IT Equipment & Devices',
        'Furniture & Fixtures',
        'HR Records & Document Materials',
        'Forms & HR Documents',
        'Maintenance & Utility Supplies',
        'Security & Accountability Items',
    ];

    private const ITEM_TYPES = [
        'Consumable',
        'Non-Consumable',
        'Asset',
    ];

    private const UNITS = [
        'pcs',
        'box',
        'ream',
        'roll',
        'bottle',
        'set',
        'unit',
        'pair',
        'liter',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, ?string $page = null)
    {
        $user = $request->user();
        $allowedPages = ['dashboard', 'inventory-registry'];

        if ($user->isAdmin()) {
            $allowedPages = ['dashboard', 'inventory-registry', 'stock-management', 'analytics', 'audit-trails'];
        } elseif ($user->isStaff()) {
            $allowedPages = ['dashboard', 'inventory-registry', 'stock-management', 'audit-trails'];
        } elseif ($user->isViewer()) {
            $allowedPages = ['dashboard', 'inventory-registry', 'analytics'];
        }

        if ($page && ! in_array($page, $allowedPages, true)) {
            abort(403, 'Unauthorized dashboard page.');
        }

        // Fetch all items from the database to pass to the view
        $inventoryItems = InventoryItem::all();
        $stockTransactions = StockTransaction::with('inventoryItem')->orderBy('created_at', 'desc')->get();
        $auditTrailsQuery = AuditTrail::with('user')->orderBy('created_at', 'desc');

        if ($user->isStaff()) {
            $auditTrailsQuery->where('user_id', $user->id);
        } elseif ($user->isViewer()) {
            $auditTrailsQuery->whereRaw('1 = 0');
        }

        $auditTrails = $auditTrailsQuery->get();
        $lowStockAlertItems = InventoryItem::whereColumn('stock', '<=', 'minimum')
            ->orderBy('name')
            ->get();
        $lowStockNotifications = Auth::user()
            ->unreadNotifications()
            ->where('type', LowStockAlert::class)
            ->latest()
            ->get();
        $unreadLowStockCount = $lowStockNotifications->count();

        // Per-item stock-in totals (sum of all 'in' type transactions per item)
        $stockInTotals = StockTransaction::where('type', 'in')
            ->selectRaw('inventory_item_id, SUM(quantity) as total_in')
            ->groupBy('inventory_item_id')
            ->pluck('total_in', 'inventory_item_id');

        return view('InventoryDashboard.index', compact(
            'inventoryItems',
            'stockTransactions',
            'auditTrails',
            'stockInTotals',
            'lowStockAlertItems',
            'lowStockNotifications',
            'unreadLowStockCount'
        ));
    }

    public function store(Request $request)
    {
        // Validate the incoming data from the form
        $validatedData = $request->validate([
            'code' => 'required|unique:inventory_items',
            'name' => 'required|string|max:255',
            'category' => ['required', 'string', Rule::in(self::CATEGORIES)],
            'type' => ['required', 'string', Rule::in(self::ITEM_TYPES)],
            'unit' => ['required', 'string', Rule::in(self::UNITS)],
            'stock' => 'required|integer|min:0',
            'minimum' => 'required|integer|min:0',
            'date_registered' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $validatedData['stock'] = (int) $validatedData['stock'];
        $validatedData['minimum'] = (int) $validatedData['minimum'];

        // Save into Database
        $item = InventoryItem::create($validatedData);

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'Created Item',
            'module' => 'Inventory Registry',
            'item_reference' => $item->code,
            'old_value' => null,
            'new_value' => 'Name: ' . $item->name . ', Stock: ' . $item->stock,
            'remarks' => 'Item initialized.'
        ]);

        // Redirect back to dashboard > inventory registry tab with success message
        return redirect()->route('dashboard', ['page' => 'inventory-registry'])
                         ->with('success', 'Inventory item added successfully.');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'code' => 'required|unique:inventory_items,code,'.$id,
            'name' => 'required|string|max:255',
            'category' => ['required', 'string', Rule::in(self::CATEGORIES)],
            'type' => ['required', 'string', Rule::in(self::ITEM_TYPES)],
            'unit' => ['required', 'string', Rule::in(self::UNITS)],
            'stock' => 'required|integer|min:0',
            'minimum' => 'required|integer|min:0',
            'date_registered' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $validatedData['stock'] = (int) $validatedData['stock'];
        $validatedData['minimum'] = (int) $validatedData['minimum'];

        $item = InventoryItem::findOrFail($id);
        
        $oldData = $item->toJson();
        $item->update($validatedData);

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'Updated Item',
            'module' => 'Inventory Registry',
            'item_reference' => $item->code,
            'old_value' => $oldData,
            'new_value' => $item->toJson(),
            'remarks' => 'Item updated via Inventory Registry.'
        ]);

        return redirect()->route('dashboard', ['page' => 'inventory-registry'])
                         ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy($id)
    {
        $item = InventoryItem::findOrFail($id);
        $code = $item->code;
        $name = $item->name;
        $item->delete();

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted Item',
            'module' => 'Inventory Registry',
            'item_reference' => $code,
            'old_value' => 'Name: ' . $name,
            'new_value' => null,
            'remarks' => 'Item deleted from Inventory Registry.'
        ]);

        return redirect()->route('dashboard', ['page' => 'inventory-registry'])
                         ->with('success', 'Inventory item deleted successfully.');
    }
}
