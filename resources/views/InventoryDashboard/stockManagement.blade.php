@php
    /** @var \App\Models\User $currentUser */
    $currentUser ??= auth()->user();
@endphp

<div id="stock-management" class="page {{ (isset($activePageId) && $activePageId === 'stock-management') ? 'active-page' : '' }}" data-is-admin="{{ $currentUser->isAdmin() ? 'true' : 'false' }}" data-admin-name="{{ $currentUser->name }}">
    <div class="d-flex justify-content-between align-items-center mb-4 stock-header">
        <div>
            <h1 class="dashboard-title mb-0">
                <span class="dashboard-title-badge">StockWise - Stock Manage</span>
            </h1>
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
                    <table class="table align-middle mb-0 stock-table" style="font-size:13px;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="ps-4 py-3 border-0 text-uppercase text-secondary fw-semibold" style="font-size:11px;letter-spacing:.08em;">Date & Time</th>
                                <th class="py-3 border-0 text-uppercase text-secondary fw-semibold" style="font-size:11px;letter-spacing:.08em;">Transaction</th>
                                <th class="py-3 border-0 text-uppercase text-secondary fw-semibold" style="font-size:11px;letter-spacing:.08em;">Inventory Item</th>
                                <th class="py-3 border-0 text-uppercase text-secondary fw-semibold text-center" style="font-size:11px;letter-spacing:.08em;">Quantity</th>
                                <th class="py-3 border-0 text-uppercase text-secondary fw-semibold" style="font-size:11px;letter-spacing:.08em;">Handled By</th>

                                @if($currentUser->isAdmin())
                                    <th class="py-3 pe-4 border-0 text-end text-uppercase text-secondary fw-semibold" style="font-size:11px;letter-spacing:.08em;">
                                        Actions
                                    </th>
                                @endif
                            </tr>
                        </thead>

                        <tbody id="txTableBody">

                        @forelse($stockTransactions as $i => $tx)

                        <tr class="border-bottom" style="border-color:#eef2f7!important;">

                            {{-- Date --}}
                            <td class="ps-4 py-3">

                                <div class="d-flex align-items-center gap-3">

                                    <div style="
                                        width:38px;
                                        height:38px;
                                        border-radius:10px;
                                        background:#f8fafc;
                                        border:1px solid #e5e7eb;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        color:#64748b;">
                                        <i class="bi bi-calendar2-event"></i>
                                    </div>

                                    <div>
                                        <div class="fw-semibold text-dark">
                                            {{ $tx->created_at->format('M d, Y') }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $tx->created_at->format('h:i A') }}
                                        </small>
                                    </div>

                                </div>

                            </td>

                            {{-- Type --}}
                            <td class="py-3">

                                @if($tx->type=='in')

                                    <span style="
                                        display:inline-flex;
                                        align-items:center;
                                        gap:6px;
                                        padding:6px 12px;
                                        background:#ecfdf5;
                                        color:#059669;
                                        border-radius:999px;
                                        font-size:12px;
                                        font-weight:600;">

                                        <i class="bi bi-arrow-down-circle"></i>
                                        Stock In

                                    </span>

                                @elseif($tx->type=='out')

                                    <span style="
                                        display:inline-flex;
                                        align-items:center;
                                        gap:6px;
                                        padding:6px 12px;
                                        background:#fef2f2;
                                        color:#dc2626;
                                        border-radius:999px;
                                        font-size:12px;
                                        font-weight:600;">

                                        <i class="bi bi-arrow-up-circle"></i>
                                        Stock Out

                                    </span>

                                @else

                                    <span style="
                                        display:inline-flex;
                                        align-items:center;
                                        gap:6px;
                                        padding:6px 12px;
                                        background:#eff6ff;
                                        color:#2563eb;
                                        border-radius:999px;
                                        font-size:12px;
                                        font-weight:600;">

                                        <i class="bi bi-arrow-repeat"></i>
                                        Adjustment

                                    </span>

                                @endif

                            </td>

                            {{-- Item --}}
                            <td class="py-3">

                                @if($tx->inventoryItem)

                                    <div class="fw-semibold text-dark">
                                        {{ $tx->inventoryItem->code }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $tx->inventoryItem->name }}
                                    </small>

                                @else

                                    <span class="text-muted fst-italic">
                                        Deleted Item
                                    </span>

                                @endif

                            </td>

                            {{-- Qty --}}
                            <td class="py-3 text-center">

                                <span style="
                                    display:inline-block;
                                    min-width:80px;
                                    padding:6px 12px;
                                    border-radius:8px;
                                    font-weight:700;
                                    font-size:12px;
                                    background:{{ $tx->type=='out' ? '#fff1f2' : '#f0fdf4' }};
                                    color:{{ $tx->type=='out' ? '#e11d48' : '#16a34a' }};">

                                    {{ $tx->type=='out' ? '-' : '+' }}
                                    {{ number_format($tx->quantity) }}

                                </span>

                                <div class="text-muted mt-1" style="font-size:11px;">
                                    {{ $tx->inventoryItem?->display_unit }}
                                </div>

                            </td>

                            {{-- Handled --}}
                            <td class="py-3">

                                <div class="d-flex align-items-center gap-2">

                                    <div style="
                                        width:34px;
                                        height:34px;
                                        border-radius:50%;
                                        background:#2563eb;
                                        color:white;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        font-size:13px;
                                        font-weight:700;">

                                        {{ strtoupper(substr($tx->handled_by ?: '?',0,1)) }}

                                    </div>

                                    <span class="fw-medium">
                                        {{ $tx->handled_by ?: '-' }}
                                    </span>

                                </div>

                            </td>

                            {{-- Actions --}}
                            @if($currentUser->isAdmin())

                           <td class="py-3 pe-4 text-end">

                            <div class="d-inline-flex align-items-center gap-1">
                            <!-- Edit -->
                                <button
                                    type="button"
                                    class="btn btn-sm edit-tx-btn"
                                    title="Edit Transaction"
                                    data-tx-id="{{ $tx->id }}"
                                    data-tx-quantity="{{ $tx->quantity }}"
                                    data-tx-handled-by="{{ json_encode($tx->handled_by) }}"
                                    data-tx-item-name="{{ json_encode($tx->inventoryItem ? $tx->inventoryItem->name : 'Deleted Item') }}"
                                    style="
                                        width:38px;
                                        height:38px;
                                        padding:0;
                                        border:1px solid #e5e7eb;
                                        background:#ffffff;
                                        color:#64748b;
                                        border-radius:12px;
                                        display:inline-flex;
                                        align-items:center;
                                        justify-content:center;
                                        box-shadow:0 1px 2px rgba(15,23,42,.05);
                                        transition:all .2s cubic-bezier(.4,0,.2,1);"
                                    onmouseover="
                                        this.style.background='#eff6ff';
                                        this.style.borderColor='#bfdbfe';
                                        this.style.color='#2563eb';
                                        this.style.boxShadow='0 4px 12px rgba(37,99,235,.15)';
                                        this.style.transform='translateY(-2px)';
                                    "
                                    onmouseout="
                                        this.style.background='#ffffff';
                                        this.style.borderColor='#e5e7eb';
                                        this.style.color='#64748b';
                                        this.style.boxShadow='0 1px 2px rgba(15,23,42,.05)';
                                        this.style.transform='translateY(0)';
                                    ">
                                    <i class="bi bi-pencil-square" style="font-size:15px;"></i>
                                </button>

                                <!-- Delete -->
                                <form
                                    action="{{ route('stock.destroy', $tx->id) }}"
                                    method="POST"
                                    class="d-inline delete-transaction-form">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        title="Delete Transaction"
                                        style="
                                            width:36px;
                                            height:36px;
                                            padding:0;
                                            border:none;
                                            background:transparent;
                                            color:#64748b;
                                            border-radius:10px;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            transition:all .18s ease;"
                                        onmouseover="
                                            this.style.background='#fef2f2';
                                            this.style.color='#dc2626';
                                            this.style.transform='translateY(-1px)';
                                        "
                                        onmouseout="
                                            this.style.background='transparent';
                                            this.style.color='#64748b';
                                            this.style.transform='translateY(0)';
                                        ">
                                        <i class="bi bi-trash3"></i>
                                    </button>

                                </form>

                            </div>

                        </td>

                            @endif

                        </tr>

                        @empty

                        <tr>

                            <td colspan="{{ $currentUser->isAdmin() ? 6 : 5 }}" class="py-5 text-center">

                                <i class="bi bi-inboxes text-secondary" style="font-size:50px;"></i>

                                <h6 class="mt-3 fw-semibold">
                                    No Transactions Found
                                </h6>

                                <small class="text-muted">
                                    Stock transactions will appear here once inventory movements are recorded.
                                </small>

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
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-sm" style="border-radius:18px;overflow:hidden;">

            <form id="editTxForm" method="POST" action="">
                @csrf
                @method('PUT')

                <!-- Header -->
                <div class="modal-header border-0 px-4 pt-4 pb-3">

                    <div>
                        <h5 class="modal-title fw-bold mb-1" style="font-size:1.1rem;">
                            Edit Transaction
                        </h5>

                        <small class="text-muted">
                            Update the transaction details below.
                        </small>
                    </div>
                </div>

                <!-- Body -->
                <div class="modal-body px-4 pb-3">

                    <!-- Item -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold text-secondary small mb-2">
                            Inventory Item
                        </label>

                        <input
                            type="text"
                            id="editTxItemName"
                            class="form-control"
                            readonly
                            style="
                                height:46px;
                                background:#f8fafc;
                                border:1px solid #e5e7eb;
                                border-radius:10px;
                                font-weight:600;">

                    </div>

                    <!-- Quantity -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold mb-2">
                            Quantity
                        </label>

                        <input
                            type="number"
                            name="quantity"
                            id="editTxQuantity"
                            class="form-control"
                            min="1"
                            required
                            style="
                                height:46px;
                                border-radius:10px;">

                    </div>

                    <!-- Handled By -->
                    <div>

                        <label class="form-label fw-semibold mb-2">
                            Handled By
                        </label>

                        <input
                            type="text"
                            name="handled_by"
                            id="editTxHandledBy"
                            class="form-control"
                            required
                            style="
                                height:46px;
                                border-radius:10px;">

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 px-4 pb-4 pt-2 justify-content-end">

                    <button
                        type="button"
                        class="btn"
                        data-bs-dismiss="modal"
                        style="
                            min-width:95px;
                            height:42px;
                            border:1px solid #e5e7eb;
                            background:#fff;
                            color:#475569;
                            border-radius:10px;
                            font-weight:600;">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn"
                        style="
                            min-width:140px;
                            height:42px;
                            background:#2563eb;
                            color:#fff;
                            border:none;
                            border-radius:10px;
                            font-weight:600;
                            box-shadow:0 4px 12px rgba(37,99,235,.15);">
                        <i class="bi bi-check2 me-1"></i>
                        Save Changes
                    </button>

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

    document.addEventListener('submit', function (e) {
    const form = e.target;

    if (!form.classList.contains('delete-transaction-form')) return;

    e.preventDefault();

    Swal.fire({
        title: 'Delete Transaction?',
        html: `
            <div style="font-size:14px;color:#6b7280;">
                This transaction will be permanently deleted.
                <br><br>
                <strong style="color:#dc2626;">
                    The inventory stock will be automatically reversed.
                </strong>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true,
        buttonsStyling: false,
        customClass: {
            popup: 'rounded-4 shadow',
            confirmButton: 'btn btn-danger px-4',
            cancelButton: 'btn btn-light border px-4 me-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

</script>
@endif
