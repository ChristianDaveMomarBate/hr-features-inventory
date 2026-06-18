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
        'pack',
        'bundle',
        'roll',
        'bottle',
        'set',
        'unit',
        'pair',
        'liter',
        'sheet',
        'sheets',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, ?string $page = null)
    {
        $user = $request->user();
        $allowedPages = ['dashboard', 'inventory-registry'];

        $allowedPages = ['dashboard', 'inventory-registry', 'stock-management', 'analytics', 'audit-trails'];

        if ($page && ! in_array($page, $allowedPages, true)) {
            abort(403, 'Unauthorized dashboard page.');
        }

        // Fetch items with search, filter, and pagination
        $query = InventoryItem::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%")
                  ->orWhere('stock_unit', 'like', "%{$search}%")
                  ->orWhere('issue_unit', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('stock_status') && $request->stock_status === 'low') {
            $query->whereColumn('stock', '<=', 'minimum');
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['code', 'name', 'category', 'type', 'unit', 'stock_unit', 'issue_unit', 'location', 'stock', 'minimum', 'date_registered', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $inventoryItems = $query->paginate(25);
        /** @var \Illuminate\Pagination\LengthAwarePaginator $inventoryItems */
        $inventoryItems->withPath(route('dashboard', ['page' => 'inventory-registry']));
        $allInventoryItems = InventoryItem::all(); // Needed for JS charts and edit modals

        $stockTransactions = StockTransaction::with('inventoryItem')->orderBy('created_at', 'desc')->get();
        $auditTrailsQuery = AuditTrail::with('user')->orderBy('created_at', 'desc');

        $auditTrails = $auditTrailsQuery->paginate(15, ['*'], 'audit_page');
        $auditTrails->withPath(route('dashboard', ['page' => 'audit-trails']));
        $lowStockAlertItems = InventoryItem::whereColumn('stock', '<=', 'minimum')
            ->orderBy('name')
            ->get();
        $lowStockNotifications = Auth::user()
            ->unreadNotifications
            ->where('type', LowStockAlert::class);
        $unreadLowStockCount = $lowStockNotifications->count();

        // Per-item stock-in totals (sum of all 'in' type transactions per item)
        $stockInTotals = StockTransaction::where('type', 'in')
            ->selectRaw('inventory_item_id, SUM(quantity) as total_in')
            ->groupBy('inventory_item_id')
            ->pluck('total_in', 'inventory_item_id');

        $paginatedTransactions = null; // kept for view compatibility, pagination is JS-driven

        return view('InventoryDashboard.index', compact(
            'inventoryItems',
            'allInventoryItems',
            'stockTransactions',
            'auditTrails',
            'stockInTotals',
            'lowStockAlertItems',
            'lowStockNotifications',
            'unreadLowStockCount',
            'paginatedTransactions'
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
            'stock_unit' => ['required', 'string', Rule::in(self::UNITS)],
            'issue_unit' => ['required', 'string', Rule::in(self::UNITS)],
            'units_per_stock_unit' => 'required|integer|min:1',
            'stock' => 'required|numeric|min:0',
            'minimum' => 'required|integer|min:0',
            'date_registered' => 'required|date',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255'
        ]);

        $validatedData['unit'] = $validatedData['issue_unit'];
        $validatedData['stock'] = (int) round(((float) $validatedData['stock']) * (int) $validatedData['units_per_stock_unit']);
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

        // Redirect back to preserve pagination and filter state with success message
        return back()->with('success', 'Inventory item added successfully.');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'code' => 'required|unique:inventory_items,code,'.$id,
            'name' => 'required|string|max:255',
            'category' => ['required', 'string', Rule::in(self::CATEGORIES)],
            'type' => ['required', 'string', Rule::in(self::ITEM_TYPES)],
            'stock_unit' => ['required', 'string', Rule::in(self::UNITS)],
            'issue_unit' => ['required', 'string', Rule::in(self::UNITS)],
            'units_per_stock_unit' => 'required|integer|min:1',
            'stock' => 'required|numeric|min:0',
            'minimum' => 'required|integer|min:0',
            'date_registered' => 'required|date',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255'
        ]);

        $validatedData['unit'] = $validatedData['issue_unit'];
        $validatedData['stock'] = (int) round(((float) $validatedData['stock']) * (int) $validatedData['units_per_stock_unit']);
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

        return back()->with('success', 'Inventory item updated successfully.');
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

        return back()->with('success', 'Inventory item deleted successfully.');
    }
}
