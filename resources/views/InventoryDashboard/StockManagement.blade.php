<div id="stock-management" class="page" data-is-admin="{{ auth()->user()->isAdmin() ? 'true' : 'false' }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold" style="font-size: 32px; color: #111827;">Stock Management</h1>
            <p class="text-muted mb-0">Record stock ins, outs, and adjustments. Add multiple items at once.</p>
        </div>
    </div>


    <div class="row g-4">
        <!-- Batch Transaction Form -->
        <div class="col-12">
            <div class="chart-card p-0 d-flex flex-column overflow-hidden">
                <div class="p-4 border-bottom border-light bg-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">New Transaction</h5>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Multi-Item</span>
                </div>
                <div class="p-4 bg-white">
                    <form method="POST" action="{{ route('stock.store') }}" id="batchStockForm">
                        @csrf

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold mb-0">Items</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">
                                <i class="bi bi-plus-lg me-1"></i>Add Item
                            </button>
                        </div>

                        <!-- Batch Item Rows -->
                        <div id="batchItemsContainer" class="stock-items-grid"></div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Save Transaction(s)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div class="col-12">
            <div class="chart-card p-0 h-100 d-flex flex-column overflow-hidden">
                <div class="p-4 border-bottom border-light bg-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Recent Transactions</h5>
                </div>
                <div class="table-responsive flex-grow-1 bg-white">
                    <table class="table table-hover table-modern align-middle mb-0 border-0">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 border-0">Date</th>
                                <th>Type</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Reference</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockTransactions as $tx)
                                <tr>
                                    <td class="ps-4 text-nowrap py-3 fw-medium text-secondary" style="font-size:13px;">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="py-3">
                                        @if($tx->type == 'in')
                                            <span class="badge bg-emerald-50 text-emerald-500 border border-success border-opacity-25 px-2 py-1">IN</span>
                                        @elseif($tx->type == 'out')
                                            <span class="badge bg-amber-50 text-amber-500 border border-warning border-opacity-25 px-2 py-1">OUT</span>
                                        @else
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">ADJ</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($tx->inventoryItem)
                                            <span class="fw-semibold text-dark">{{ $tx->inventoryItem->code }}</span> - <span class="text-secondary">{{ $tx->inventoryItem->name }}</span>
                                        @else
                                            <span class="text-muted">Deleted Item</span>
                                        @endif
                                    </td>
                                    <td class="py-3 fw-bold text-dark">{{ $tx->quantity }}</td>
                                    <td class="py-3 text-secondary">{{ $tx->reference }}</td>
                                    <td class="py-3"><small class="text-muted">{{ $tx->remarks }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No recent transactions.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="stock-management-items-data">@json($inventoryItems)</script>

<script>
// Pass items data to JS for the batch dropdowns
const stockManagementPage = document.getElementById('stock-management');
const stockManagementItemsData = document.getElementById('stock-management-items-data');
const allInventoryItems = JSON.parse(stockManagementItemsData?.textContent || '[]');
const isAdmin = stockManagementPage?.dataset.isAdmin === 'true';
let rowCount = 0;

function addItemRow() {
    rowCount++;
    const idx = rowCount;
    const container = document.getElementById('batchItemsContainer');

    const typeOptions = isAdmin
        ? `<option value="in">Stock In</option><option value="out">Stock Out</option><option value="adjustment">Adjustment</option>`
        : `<option value="in">Stock In</option><option value="out">Stock Out</option>`;

    const itemOptions = allInventoryItems.map(item =>
        `<option value="${item.id}">${item.code} - ${item.name} (${item.stock} ${item.unit})</option>`
    ).join('');

    const row = document.createElement('div');
    row.className = 'stock-item-card border rounded p-3 bg-light position-relative';
    row.id = `item-row-${idx}`;
    row.innerHTML = `
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeRow(${idx})" title="Remove"></button>
        <div class="row g-2">
            <div class="col-12">
                <label class="form-label small fw-semibold mb-1">Item</label>
                <select name="items[${idx}][inventory_item_id]" class="form-select form-select-sm" required>
                    <option value="">Select item...</option>
                    ${itemOptions}
                </select>
            </div>
            <div class="col-6">
                <label class="form-label small fw-semibold mb-1">Type</label>
                <select name="items[${idx}][type]" class="form-select form-select-sm" required>
                    ${typeOptions}
                </select>
            </div>
            <div class="col-6">
                <label class="form-label small fw-semibold mb-1">Quantity</label>
                <input type="number" name="items[${idx}][quantity]" min="1" class="form-control form-control-sm" placeholder="0" required>
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold mb-1">Reference No. <span class="text-muted fw-normal">(PO, DR, RIS...)</span></label>
                <input type="text" name="items[${idx}][reference]" class="form-control form-control-sm" placeholder="e.g. PO-2026-001">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold mb-1">Remarks</label>
                <input type="text" name="items[${idx}][remarks]" class="form-control form-control-sm" placeholder="Optional note...">
            </div>
        </div>
    `;
    container.appendChild(row);
}

function removeRow(idx) {
    const row = document.getElementById(`item-row-${idx}`);
    if (row) row.remove();
}

// Auto add the first row on load
document.addEventListener('DOMContentLoaded', function() {
    addItemRow();
});
</script>
