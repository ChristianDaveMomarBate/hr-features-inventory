@php
    /** @var \App\Models\User $currentUser */
    $currentUser ??= auth()->user();
@endphp

<div id="stock-management" class="page {{ (isset($activePageId) && $activePageId === 'stock-management') ? 'active-page' : '' }}" data-is-admin="{{ $currentUser->isAdmin() ? 'true' : 'false' }}">
    <div class="d-flex justify-content-between align-items-center mb-4 stock-header">
        <div>
            <h1 class="mb-1 fw-bold animated-text" style="font-size: 32px;">STOCK MANAGEMENT</h1>
            <p class="text-muted mb-0 page-subtitle">Record stock ins, outs, and adjustments. Add multiple items at once.</p>
        </div>
        @include('InventoryDashboard.navbar')
    </div>

    <div class="stock-fit-grid">
        <div class="stock-transaction-panel">
            <div class="chart-card p-0 d-flex flex-column overflow-hidden stock-action-card">
                <div class="p-4 border-bottom border-light bg-white d-flex justify-content-between align-items-center card-header-premium">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary rounded p-2"><i class="bi bi-box-seam"></i></div>
                            New Transaction
                        </h5>
                        <span class="badge badge-premium badge-multi-item">Multi-Item Mode</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-premium-outline d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#recentTransactionsModal">
                        <i class="bi bi-clock-history"></i>
                        View Recent Transactions
                    </button>
                </div>
                <div class="stock-form-body p-4 bg-white">
                    <form method="POST" action="{{ route('stock.store') }}" id="batchStockForm">
                        @csrf

                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-light">
                            <label class="form-label fw-bold text-secondary mb-0 text-uppercase tracking-wide" style="font-size: 0.8rem; letter-spacing: 0.5px;">Items to Process</label>
                            <button type="button" class="btn btn-sm btn-premium-outline" data-action="add-stock-row">
                                <i class="bi bi-plus-circle-fill me-1"></i> Add Row
                            </button>
                        </div>

                        <div id="batchItemsContainer" class="stock-items-grid mb-3"></div>

                        <div class="mt-4 pt-3 border-top border-light d-flex justify-content-end">
                            <button type="submit" class="btn btn-premium-primary px-4 fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2" style="font-size: 0.9rem;">
                                <i class="bi bi-check2-all fs-6"></i> Confirm & Save Transaction(s)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="stock-management-items-data">@json($allInventoryItems)</script>

<div class="modal fade" id="recentTransactionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom border-light card-header-premium">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                    <div class="icon-box bg-secondary bg-opacity-10 text-secondary rounded p-2"><i class="bi bi-clock-history"></i></div>
                    Recent Transactions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-white">
                <div class="table-responsive stock-table-wrap stock-modal-table-wrap">
                    <table class="table table-hover table-modern align-middle mb-0 border-0 stock-table">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 border-0">Date</th>
                                <th>Type</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Handled By</th>
                                @if($currentUser->isAdmin())
                                    <th class="text-end pe-4">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="txTableBody">
                            @forelse($stockTransactions as $i => $tx)
                                <tr class="stock-table-row" data-row-index="{{ $i }}">
                                    <td class="ps-4 text-nowrap py-3 fw-medium text-secondary" style="font-size:13px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="calendar-icon text-muted"><i class="bi bi-calendar2-event"></i></div>
                                            {{ $tx->created_at->format('M d, Y h:i A') }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        @if($tx->type == 'in')
                                            <span class="badge badge-tx-in px-3 py-2 rounded-pill"><i class="bi bi-arrow-down-left-circle me-1"></i> IN</span>
                                        @elseif($tx->type == 'out')
                                            <span class="badge badge-tx-out px-3 py-2 rounded-pill"><i class="bi bi-arrow-up-right-circle me-1"></i> OUT</span>
                                        @else
                                            <span class="badge badge-tx-adj px-3 py-2 rounded-pill"><i class="bi bi-sliders me-1"></i> ADJ</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($tx->inventoryItem)
                                            <div class="fw-bold text-dark">{{ $tx->inventoryItem->code }}</div>
                                            <div class="text-secondary small">{{ $tx->inventoryItem->name }}</div>
                                        @else
                                            <span class="text-muted fst-italic">Deleted Item</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span class="qty-badge {{ $tx->type == 'out' ? 'qty-out' : 'qty-in' }}">
                                            {{ $tx->type == 'out' ? '-' : '+' }}{{ number_format($tx->quantity) }} {{ $tx->inventoryItem?->display_unit }}
                                        </span>
                                    </td>
                                    <td class="py-3 fw-medium text-dark">{{ $tx->handled_by ?: '-' }}</td>
                                    @if($currentUser->isAdmin())
                                        <td class="py-3 text-end pe-4">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-tx-btn" title="Edit transaction"
                                                data-tx-id="{{ $tx->id }}"
                                                data-tx-quantity="{{ $tx->quantity }}"
                                                data-tx-handled-by="{{ json_encode($tx->handled_by) }}"
                                                data-tx-item-name="{{ json_encode($tx->inventoryItem ? $tx->inventoryItem->name : 'Deleted Item') }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('stock.destroy', $tx->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this transaction? The stock will be reversed.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete transaction">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $currentUser->isAdmin() ? 6 : 5 }}" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-icon text-muted mb-3"><i class="bi bi-inboxes" style="font-size: 2.5rem;"></i></div>
                                            <h6 class="text-dark fw-bold">No Transactions Yet</h6>
                                            <p class="text-muted small">Your recent stock movements will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="txPaginationFooter" class="modal-footer bg-white border-top border-light py-3 px-4" style="display:none;">
                <div class="d-flex justify-content-between align-items-center w-100 gap-3">
                    <button type="button" id="txPrevBtn" class="btn btn-sm btn-outline-secondary" onclick="changeTxPage(-1)">
                        <i class="bi bi-chevron-left"></i> Previous
                    </button>
                    <span id="txPageInfo" class="text-muted small fw-medium"></span>
                    <button type="button" id="txNextBtn" class="btn btn-sm btn-outline-secondary" onclick="changeTxPage(1)">
                        Next <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@if($currentUser->isAdmin())
<div class="modal fade" id="editTxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTxForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Item</label>
                        <input type="text" id="editTxItemName" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Quantity <span class="text-muted small">(issue units)</span></label>
                        <input type="number" name="quantity" id="editTxQuantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Handled By</label>
                        <input type="text" name="handled_by" id="editTxHandledBy" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function openEditTx(button) {
        const id = button.dataset.txId;
        const qty = button.dataset.txQuantity;
        const handledBy = JSON.parse(button.dataset.txHandledBy);
        const itemName = JSON.parse(button.dataset.txItemName);

        document.getElementById('editTxForm').action = '/stock/' + id;
        document.getElementById('editTxQuantity').value = qty;
        document.getElementById('editTxHandledBy').value = handledBy;
        document.getElementById('editTxItemName').value = itemName;
        var modal = new bootstrap.Modal(document.getElementById('editTxModal'));
        modal.show();
    }

    document.querySelectorAll('.edit-tx-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            openEditTx(this);
        });
    });
</script>
@endif
