@php
    /** @var \App\Models\User $currentUser */
    $currentUser ??= auth()->user();

    $inventoryCategories = [
        'Office Supplies',
        'IT Equipment & Devices',
        'Furniture & Fixtures',
        'HR Records & Document Materials',
        'Forms & HR Documents',
        'Maintenance & Utility Supplies',
        'Security & Accountability Items',
    ];

    $itemTypes = [
        'Consumable',
        'Non-Consumable',
        'Asset',
    ];

    $inventoryUnits = [
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
@endphp

<div
    id="inventory-registry"
    class="page"
    data-can-manage="{{ $currentUser->isAdmin() ? 'true' : 'false' }}"
    data-store-url="{{ route('inventory.store') }}"
    data-update-base-url="{{ url('/inventory/update') }}"
>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Inventory Registry</h1>
            <p class="text-muted mb-0">Register and maintain inventory item records.</p>
        </div>
    </div>

    <div id="successAlert" class="alert alert-success d-none"></div>

    <div class="card inventory-registry-card">
        <div class="card-body">
            <form id="inventoryFilterForm" method="GET" action="{{ route('dashboard', 'inventory-registry') }}" class="row g-3 align-items-end mb-4">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Search</label>
                    <input name="search" type="search" class="form-control" placeholder="Code, item, category, type, or unit" value="{{ request('search') }}">
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All categories</option>
                        @foreach($inventoryCategories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Stock Status</label>
                    <select name="stock_status" class="form-select">
                        <option value="">All</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low stock</option>
                    </select>
                </div>

                <div class="col-lg-4 col-md-6 d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-primary flex-grow-1" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                    <a class="btn btn-outline-danger flex-grow-1" href="{{ route('inventory.export.pdf') }}">
                        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                    </a>
                    <a class="btn btn-outline-success flex-grow-1" href="{{ route('inventory.export.excel') }}">
                        <i class="bi bi-file-earmark-excel me-1"></i>Excel
                    </a>
                    @if($currentUser->isAdmin())
                        <button class="btn btn-primary" type="button" data-action="open-add-item">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'code', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">Code <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">Item <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">Category <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'type', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">Type <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th>Unit</th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'location', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">Location <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'stock', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">Current Stock <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th>Total Stock In</th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'minimum', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">Minimum <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date_registered', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">Registered <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTable">
                        @forelse($inventoryItems as $item)
                            @php $itemStockIn = $stockInTotals[$item->id] ?? 0; @endphp
                            <tr>
                                <td class="fw-semibold">{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->category }}</td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $item->type ?? 'Consumable' }}</span></td>
                                <td>{{ $item->unit }}</td>
                                <td><span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $item->location ?? '—' }}</span></td>
                                <td>
                                    <span class="badge {{ $item->stock <= $item->minimum ? 'bg-danger' : 'badge-soft' }}">
                                        {{ $item->stock }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-75">{{ $itemStockIn }}</span>
                                </td>
                                <td>{{ $item->minimum }}</td>
                                <td>{{ $item->date_registered ? \Carbon\Carbon::parse($item->date_registered)->format('M d, Y') : '' }}</td>
                                <td class="text-end">
                                    @if($currentUser->isAdmin())
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-edit-item-id="{{ $item->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ url('/inventory/delete/' . $item->id) }}" method="POST" class="d-inline" data-confirm="Are you sure you want to delete this item?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    No inventory items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $inventoryItems->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @if($currentUser->isAdmin())
    <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="inventoryForm" method="POST" action="{{ route('inventory.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="methodOverride" value="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add Inventory Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Item Code</label>
                                <input name="code" class="form-control" required>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Item Name</label>
                                <input name="name" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" required>
                                    @foreach($inventoryCategories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Item Type</label>
                                <select name="type" class="form-select" required>
                                    @foreach($itemTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Unit</label>
                                <select name="unit" class="form-select" required>
                                    @foreach($inventoryUnits as $unit)
                                        <option value="{{ $unit }}">{{ $unit }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Date Registered</label>
                                <input type="date" name="date_registered" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Initial Stock</label>
                                <input type="number" min="0" name="stock" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Minimum Stock Level</label>
                                <input type="number" min="0" name="minimum" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-geo-alt me-1 text-primary"></i>Storage Location</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Cabinet A, Shelf 2">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Save Item
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<script id="inventory-data" type="application/json">
    {!! json_encode($allInventoryItems) !!}
</script>
<script id="transactions-data" type="application/json">
    {!! json_encode($stockTransactions ?? []) !!}
</script>
<script id="stock-in-totals" type="application/json">
    {!! json_encode($stockInTotals ?? []) !!}
</script>

