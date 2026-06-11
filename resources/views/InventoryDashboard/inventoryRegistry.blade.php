@php
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

<div id="inventory-registry" class="page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Inventory Registry</h1>
            <p class="text-muted mb-0">Register and maintain inventory item records.</p>
        </div>
    </div>

    <div id="successAlert" class="alert alert-success d-none"></div>

    <div class="card inventory-registry-card">
        <div class="card-body">
            <form id="inventoryFilterForm" class="row g-3 align-items-end mb-4" onsubmit="filterItems(event)">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Search</label>
                    <input id="searchInput" type="search" class="form-control" placeholder="Code, item, category, type, or unit" oninput="filterItems()">
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Category</label>
                    <select id="categoryFilter" class="form-select" onchange="filterItems()">
                        <option value="">All categories</option>
                        @foreach($inventoryCategories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Stock Status</label>
                    <select id="stockFilter" class="form-select" onchange="filterItems()">
                        <option value="">All</option>
                        <option value="low">Low stock</option>
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
                    @if(auth()->user()->isAdmin())
                        <button class="btn btn-primary" type="button" onclick="openAddItem()">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Unit</th>
                            <th>Location</th>
                            <th>Current Stock</th>
                            <th>Total Stock In</th>
                            <th>Minimum</th>
                            <th>Registered</th>
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
                                <td>
                                    <span class="badge {{ $item->stock <= $item->minimum ? 'bg-danger' : 'badge-soft' }}">
                                        {{ $item->stock }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-75">{{ $itemStockIn }}</span>
                                </td>
                                <td>{{ $item->minimum }}</td>
                                <td><span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $item->location ?? '—' }}</span></td>
                                <td>{{ $item->date_registered ? \Carbon\Carbon::parse($item->date_registered)->format('M d, Y') : '' }}</td>
                                <td class="text-end">
                                    @if(auth()->user()->isAdmin())
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditItem('{{ $item->id }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ url('/inventory/delete/' . $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
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
        </div>
    </div>

    @if(auth()->user()->isAdmin())
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
    {!! json_encode($inventoryItems) !!}
</script>
<script id="transactions-data" type="application/json">
    {!! json_encode($stockTransactions ?? []) !!}
</script>
<script id="stock-in-totals" type="application/json">
    {!! json_encode($stockInTotals ?? []) !!}
</script>

<script>
    let inventoryItems = JSON.parse(document.getElementById('inventory-data').textContent);
    const canManageInventory = @json(auth()->user()->isAdmin());

    function openAddItem() {
        if (!canManageInventory) return;
        const form = document.getElementById('inventoryForm');
        form.reset();
        form.action = "{{ route('inventory.store') }}";
        document.getElementById('methodOverride').value = "POST";
        document.getElementById('modalTitle').textContent = "Add Inventory Item";
        form.elements['type'].value = 'Consumable';
        form.elements['unit'].value = 'pcs';
        new bootstrap.Modal(document.getElementById("itemModal")).show();
    }

    function openEditItem(id) {
        if (!canManageInventory) return;
        const item = inventoryItems.find(i => i.id == id);
        if(!item) return;

        const form = document.getElementById('inventoryForm');
        form.action = "/inventory/update/" + id;
        document.getElementById('methodOverride').value = "PUT";
        document.getElementById('modalTitle').textContent = "Edit Inventory Item";

        form.elements['code'].value = item.code;
        form.elements['name'].value = item.name;
        form.elements['category'].value = item.category;
        form.elements['type'].value = item.type || 'Consumable';
        form.elements['unit'].value = item.unit;
        form.elements['stock'].value = item.stock;
        form.elements['minimum'].value = item.minimum;
        form.elements['description'].value = item.description || '';
        form.elements['location'].value = item.location || '';
        form.elements['date_registered'].value = item.date_registered ? item.date_registered.split('T')[0] : '';

        new bootstrap.Modal(document.getElementById("itemModal")).show();
    }

    const stockInTotalsMap = JSON.parse(document.getElementById('stock-in-totals').textContent || '{}');

    function renderItems(items = inventoryItems) {
        const table = document.getElementById("inventoryTable");
         
        if (items.length === 0) {
            table.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        No inventory items found.
                    </td>
                </tr>
            `;
            return;
        }

        table.innerHTML = items.map(function (item) {
            const isLow = item.stock <= item.minimum;
            const dateStr = item.date_registered ? new Date(item.date_registered).toLocaleDateString("en-US", {month: "short", day: "2-digit", year: "numeric"}) : '';
            const stockIn = stockInTotalsMap[item.id] || 0;
            const actionButtons = canManageInventory ? `
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditItem('${item.id}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="/inventory/delete/${item.id}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                            <input type="hidden" name="_token" value="${document.querySelector('input[name=_token]').value}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
            ` : '';

            return `
                <tr>
                    <td class="fw-semibold">${item.code}</td>
                    <td>${item.name}</td>
                    <td>${item.category}</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">${item.type || 'Consumable'}</span></td>
                    <td>${item.unit}</td>
                    <td><span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>${item.location || '—'}</span></td>
                    <td>
                        <span class="badge ${isLow ? 'bg-danger' : 'badge-soft'}">
                            ${item.stock}
                        </span>
                    </td>
                    <td><span class="badge bg-success bg-opacity-75">${stockIn}</span></td>
                    <td>${item.minimum}</td>
                    <td>${dateStr}</td>
                    <td class="text-end">
                        ${actionButtons}
                    </td>
                </tr>
            `;
        }).join("");
    }

    function filterItems(event) {
        if (event) {
            event.preventDefault();
        }

        const search = document.getElementById("searchInput").value.trim().toLowerCase();
        const category = document.getElementById("categoryFilter").value;
        const stockStatus = document.getElementById("stockFilter").value;

        const filtered = inventoryItems.filter(function (item) {
            const searchableText = [
                item.code,
                item.name,
                item.category,
                item.type,
                item.unit,
                item.description,
                item.location
            ].join(' ').toLowerCase();

            const matchesSearch = search === "" || searchableText.includes(search);

            const matchesCategory = category === "" || item.category === category;
            const matchesStock = stockStatus === "" || item.stock <= item.minimum;

            return matchesSearch && matchesCategory && matchesStock;
        });

        renderItems(filtered);
    }

    document.addEventListener("DOMContentLoaded", function () {
        updateDashboardCards();
    });

    function updateDashboardCards() {
        const totalItems = inventoryItems.length;

        // Process actual stock transactions
        const rawTx = JSON.parse(document.getElementById('transactions-data').textContent || '[]');
        
        let totalStockInTx = 0;
        let totalStockOut = 0;

        rawTx.forEach(function(tx) {
            if(tx.type === 'in') totalStockInTx +=Number(tx.quantity);
            if(tx.type === 'out') totalStockOut += Number(tx.quantity);
        });

        // Current stock in the registry
        let currentTotalStock = inventoryItems.reduce(function (total, item) {
            return total + Number(item.stock || 0);
        }, 0);

        // Since older items might not have a StockTransaction for their initial stock,
        // the true "Total Stock In" historically is the Current Stock + Total Stock Out
        // (assuming no manual adjustments were made). We use the larger of the two to be safe.
        let totalStockIn = Math.max(totalStockInTx, currentTotalStock + totalStockOut);

        const lowStockItems = inventoryItems.filter(function (item) {
            return item.stock <= item.minimum;
        }).length;

        if(document.getElementById("totalItems")) document.getElementById("totalItems").textContent = totalItems;
        if(document.getElementById("currentStock")) document.getElementById("currentStock").textContent = currentTotalStock;
        if(document.getElementById("totalStockIn")) document.getElementById("totalStockIn").textContent = totalStockIn;
        if(document.getElementById("totalStockOut")) document.getElementById("totalStockOut").textContent = totalStockOut;
        if(document.getElementById("lowStockItems")) document.getElementById("lowStockItems").textContent = lowStockItems;

        if (typeof initOrUpdateCharts === 'function') {
            initOrUpdateCharts(totalStockIn, totalStockOut, currentTotalStock);
        }
    }
</script>
