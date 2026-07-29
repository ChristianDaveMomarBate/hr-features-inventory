@php
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
@endphp

<div id="inventory-registry"
     class="page {{ (isset($activePageId) && $activePageId === 'inventory-registry') ? 'active-page' : '' }}"
     data-is-admin="{{ $currentUser->isAdmin() ? 'true' : 'false' }}"
     data-can-manage="{{ $currentUser->isAdmin() ? 'true' : 'false' }}"
     data-store-url="{{ route('inventory.store') }}"
     data-update-base-url="{{ url('/inventory/update') }}">
    <div class="dashboard-main-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="dashboard-title mb-0">
                <span class="dashboard-title-badge">StockWise - Inventory Registery</span>
            </h1>
        </div>
        @include('InventoryDashboard.navbar')
    </div>

    <div id="successAlert" class="alert alert-success d-none"></div>

    <div class="card inventory-registry-card registry-fit-card">
        <div class="card-body">
            <form id="inventoryFilterForm" method="GET" action="{{ route('dashboard', 'inventory-registry') }}" class="row g-3 align-items-end mb-4">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Search</label>
                    <input id="inventorySearchInput" name="search" type="search" class="form-control" placeholder="" value="{{ request('search') }}">
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

                    <button class="action-btn" type="submit">
                        <i class="bi bi-search"></i>
                        <span>Search</span>
                    </button>

                    <a class="action-btn action-pdf" href="{{ route('inventory.export.pdf') }}">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <span>PDF</span>
                    </a>

                    <a class="action-btn action-excel" href="{{ route('inventory.export.excel') }}">
                        <i class="bi bi-file-earmark-excel"></i>
                        <span>Excel</span>
                    </a>

                    @if($currentUser->isAdmin())
                        <button class="action-btn action-add" type="button" data-action="open-add-item">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    @endif

                </div>
            </form>

            <div class="table-responsive registry-table-wrap">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th><a href="{{ route('dashboard', array_merge(request()->query(), ['page' => 'inventory-registry', 'sort_by' => 'code', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">Code <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ route('dashboard', array_merge(request()->query(), ['page' => 'inventory-registry', 'sort_by' => 'name', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">Item <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ route('dashboard', array_merge(request()->query(), ['page' => 'inventory-registry', 'sort_by' => 'category', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">Category <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ route('dashboard', array_merge(request()->query(), ['page' => 'inventory-registry', 'sort_by' => 'type', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">Type <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th>Units</th>
                            <th><a href="{{ route('dashboard', array_merge(request()->query(), ['page' => 'inventory-registry', 'sort_by' => 'location', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">Location <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ route('dashboard', array_merge(request()->query(), ['page' => 'inventory-registry', 'sort_by' => 'stock', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">Current Stock <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th>Total Stock In</th>
                            <th><a href="{{ route('dashboard', array_merge(request()->query(), ['page' => 'inventory-registry', 'sort_by' => 'minimum', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">Minimum <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th><a href="{{ route('dashboard', array_merge(request()->query(), ['page' => 'inventory-registry', 'sort_by' => 'date_registered', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">Registered <i class="bi bi-arrow-down-up small text-muted"></i></a></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTable">
                        @forelse($inventoryItems as $item)
                            @php $itemStockIn = $stockInTotals[$item->id] ?? 0; @endphp
                            <tr id="inventory-item-{{ $item->id }}" style="transition: background 0.5s;">
                                <td class="fw-semibold">{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->category }}</td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $item->type ?? 'Consumable' }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $item->display_unit }}</div>
                                    @if(($item->stock_unit ?? $item->unit) !== $item->display_unit || ($item->units_per_stock_unit ?? 1) > 1)
                                        <div class="text-muted small">1 {{ $item->stock_unit }} = {{ $item->units_per_stock_unit }} {{ $item->display_unit }}</div>
                                    @endif
                                </td>
                                <td><span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $item->location ?? '—' }}</span></td>
                                <td>
                                    <span class="badge {{ $item->stock <= $item->minimum ? 'bg-danger' : 'badge-soft' }}">
                                        {{ $item->display_stock }}
                                    </span>
                                    @if($item->bulk_equivalent)
                                        <div class="text-muted small mt-1">{{ $item->bulk_equivalent }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-75">{{ number_format($itemStockIn) }} {{ $item->display_unit }}</span>
                                </td>
                                <td>{{ $item->minimum }}</td>
                                <td>{{ $item->date_registered ? \Carbon\Carbon::parse($item->date_registered)->format('M d, Y') : '' }}</td>
                             <td class="text-end">
    @if($currentUser->isAdmin())

        <button type="button"
                class="btn btn-sm btn-outline-primary"
                data-edit-item-id="{{ $item->id }}">
            <i class="bi bi-pencil"></i>
        </button>

        <form action="{{ url('/inventory/delete/' . $item->id) }}"
              method="POST"
              class="d-inline delete-form">
            @csrf
            @method('DELETE')

            <button type="button"
                    class="btn btn-sm btn-outline-danger delete-btn">
                <i class="bi bi-trash"></i>
            </button>
        </form>

    @endif
</td>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.delete-btn').forEach(button => {

        button.addEventListener('click', function () {

            const form = this.closest('.delete-form');

            Swal.fire({
                title: 'Delete Item?',
                text: 'Are you sure you want to delete this item?',
                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: '<i class="bi bi-trash3 me-1"></i> Delete',
                cancelButtonText: 'Cancel',

                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',

                reverseButtons: true,

                customClass: {
                    popup: 'stockwise-swal',
                    confirmButton: 'swal-delete-btn',
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {

                if (result.isConfirmed) {
                    form.submit();
                }

            });

        });

    });

});
</script>
<style>
.stockwise-swal {
    border-radius: 14px !important;
    padding: 22px !important;
}

.stockwise-swal .swal2-title {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: #1f2937 !important;
}

.stockwise-swal .swal2-html-container {
    font-size: 13px !important;
    color: #6b7280 !important;
}

.swal-delete-btn,
.swal-cancel-btn {
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 9px 16px !important;
}
</style>
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
            
            <div class="registry-pagination mt-4">
                {{ $inventoryItems->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @if($currentUser->isAdmin())
    <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content inventory-modal">

            <form id="inventoryForm" method="POST" action="{{ route('inventory.store') }}">
                @csrf

                <input type="hidden" name="_method" id="methodOverride" value="POST">

                <!-- Header -->
                <div class="modal-header inventory-header">
                    <div>
                        <div class="modal-title" id="modalTitle">
                            <i class="bi bi-box-seam me-2"></i>
                            Add Inventory Item
                        </div>
                        <div class="modal-subtitle">
                            Register item details and stock information
                        </div>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body inventory-body">

                    <!-- Item Information -->
                    <div class="section-title">
                        <i class="bi bi-info-circle"></i>
                        <span>Item Information</span>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Item Code</label>
                            <input
                                name="code"
                                class="form-control"
                                placeholder="e.g. ITM-001"
                                required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Item Name</label>
                            <input
                                name="name"
                                class="form-control"
                                placeholder="Enter item name"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                @foreach($inventoryCategories as $category)
                                    <option value="{{ $category }}">
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Item Type</label>
                            <select name="type" class="form-select" required>
                                @foreach($itemTypes as $type)
                                    <option value="{{ $type }}">
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Stock Unit</label>
                            <select name="stock_unit" class="form-select" required>
                                @foreach($inventoryUnits as $unit)
                                    <option value="{{ $unit }}">
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">
                                e.g. box, ream, pack
                            </div>
                        </div>

                    </div>

                    <input type="hidden"
                           name="issue_unit"
                           id="hidden_issue_unit"
                           value="pcs">

                    <input type="hidden"
                           name="units_per_stock_unit"
                           value="1">

                    <!-- Stock Information -->
                    <div class="section-title section-divider">
                        <i class="bi bi-bar-chart"></i>
                        <span>Stock Information</span>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Date Registered</label>
                            <input
                                type="date"
                                name="date_registered"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity</label>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                name="stock"
                                class="form-control"
                                placeholder="0"
                                required>
                            <div class="form-hint">
                                Quantity in stock units
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Minimum Stock Level</label>
                            <input
                                type="number"
                                min="0"
                                name="minimum"
                                class="form-control"
                                placeholder="0"
                                required>
                            <div class="form-hint">
                                Low-stock threshold
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>
                                Storage Location
                            </label>

                            <input
                                type="text"
                                name="location"
                                class="form-control"
                                placeholder="e.g. Cabinet A, Shelf 2">
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer inventory-footer">

                    <button
                        type="button"
                        class="btn btn-light inventory-cancel"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary inventory-save">
                        <i class="bi bi-check2 me-1"></i>
                        Save Item
                    </button>

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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("inventorySearchInput");
        if (!searchInput) return;

        const phrases = [
            "Search by Item Code...",
            "Search by Item Name...",
            "Search by Category...",
            "Search by Item Type...",
            "Search by Unit..."
        ];
        
        let phraseIndex = 0;
        let charIndex = 0;
        let isDeleting = false;

        function typeEffect() {
            // Stop if user is focused on the input so it doesn't distract them while typing
            if (document.activeElement === searchInput && searchInput.value.length > 0) {
                setTimeout(typeEffect, 1000);
                return;
            }

            const currentPhrase = phrases[phraseIndex];

            if (isDeleting) {
                searchInput.setAttribute("placeholder", currentPhrase.substring(0, charIndex - 1));
                charIndex--;
                if (charIndex === 0) {
                    isDeleting = false;
                    phraseIndex = (phraseIndex + 1) % phrases.length;
                    setTimeout(typeEffect, 500); // Pause before typing new word
                } else {
                    setTimeout(typeEffect, 40); // Deleting speed
                }
            } else {
                searchInput.setAttribute("placeholder", currentPhrase.substring(0, charIndex + 1));
                charIndex++;
                if (charIndex === currentPhrase.length) {
                    isDeleting = true;
                    setTimeout(typeEffect, 2000); // Pause when word is fully typed
                } else {
                    setTimeout(typeEffect, 80); // Typing speed
                }
            }
        }

        // Start typing effect slightly after load
        setTimeout(typeEffect, 500);
    });
</script>


@php $highlightId = request('highlight'); @endphp
@if($highlightId)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const row = document.getElementById('inventory-item-{{ $highlightId }}');
    if (row) {
        // Scroll to it
        setTimeout(function() {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Flash highlight animation
            row.style.transition = 'background 0s';
            row.style.background = '#fef9c3';
            setTimeout(function() {
                row.style.transition = 'background 1.5s ease';
                row.style.background = '';
            }, 1500);
        }, 600);
    }
});
</script>
@endif
