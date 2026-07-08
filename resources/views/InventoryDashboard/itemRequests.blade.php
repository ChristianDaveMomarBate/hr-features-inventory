<div id="item-requests" class="page {{ $activePageId === 'item-requests' ? 'active-page' : '' }}">
    <div class="dashboard-main-header d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold animated-text" style="font-size: 28px;">ITEM REQUESTS</h2>
        @include('InventoryDashboard.navbar')
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-modern mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary fw-semibold">ID</th>
                            <th class="py-3 text-secondary fw-semibold">Requester</th>
                            <th class="py-3 text-secondary fw-semibold">Department</th>
                            <th class="py-3 text-secondary fw-semibold">Item</th>
                            <th class="py-3 text-secondary fw-semibold">Qty</th>
                            <th class="py-3 text-secondary fw-semibold">Purpose</th>
                            <th class="py-3 text-secondary fw-semibold">Status</th>
                            <th class="py-3 text-secondary fw-semibold">Date</th>
                            <th class="pe-4 py-3 text-secondary fw-semibold text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemRequests as $req)
                        <tr>
                            <td class="ps-4 py-3 fw-semibold text-dark">#{{ $loop->iteration }}</td>
                            <td class="py-3">{{ $req->requester_name }}</td>
                            <td class="py-3">{{ $req->department }}</td>
                            <td class="py-3 fw-medium">
                                @if($req->requestItems->count() > 0)
                                    {{ $req->requestItems->count() }} Item(s)
                                    <span class="d-block small text-muted fw-normal">Click Review to see</span>
                                @elseif($req->item)
                                    {{ $req->item->name }} 
                                    <span class="badge bg-light text-dark border ms-1" title="Available Stock">{{ $req->item->stock }}</span>
                                @else
                                    <span class="text-danger">Item Not Found</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($req->requestItems->count() > 0)
                                    <div><span class="fw-bold">-</span></div>
                                @else
                                    <div>Req: <span class="fw-bold">{{ $req->requested_quantity }}</span></div>
                                    @if(in_array($req->status, ['Approved', 'Adjusted']))
                                        <div class="text-success small">Appr: <span class="fw-bold">{{ $req->approved_quantity }}</span></div>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3 text-muted small" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $req->purpose }}">
                                {{ $req->purpose ?: '-' }}
                            </td>
                            <td class="py-3">
                                @if($req->status === 'Pending')
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1">Pending</span>
                                @elseif($req->status === 'Approved')
                                    <span class="badge bg-success rounded-pill px-2 py-1">Approved</span>
                                @elseif($req->status === 'Adjusted')
                                    <span class="badge bg-info text-dark rounded-pill px-2 py-1">Adjusted</span>
                                @elseif($req->status === 'Cancelled')
                                    <span class="badge bg-danger rounded-pill px-2 py-1">Cancelled</span>
                                @endif
                            </td>
                            <td class="py-3 text-muted small">
                                {{ $req->created_at->format('Y-m-d') }}
                            </td>
                            <td class="pe-4 py-3 text-end">
                                @if($req->status === 'Pending')
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill me-1" 
                                        data-bs-toggle="modal" data-bs-target="#reviewRequestModal{{ $req->id }}">
                                        Review
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-light rounded-pill text-muted me-1" 
                                        data-bs-toggle="modal" data-bs-target="#viewRequestModal{{ $req->id }}">
                                        View
                                    </button>
                                @endif
                                <form action="{{ route('admin.requests.destroy', $req->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"
                                        onclick="return confirm('Delete request #{{ $req->id }}? This cannot be undone.');">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2 text-light"></i>
                                No item requests found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($itemRequests->hasPages())
            <div class="d-flex justify-content-end px-4 py-3">
                {{ $itemRequests->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals rendered outside of table to prevent HTML layout breaking -->
@foreach($itemRequests as $req)
    @if($req->status === 'Pending')
    <!-- Review Modal (For Pending) -->
    <div class="modal fade" id="reviewRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom border-light">
                    <h5 class="modal-title fw-bold">Review Request #{{ $req->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.requests.status', $req->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        @if($req->requestItems->count() > 0)
                            <div class="mb-3">
                                <span class="text-muted fw-bold d-block mb-2">Requested Items</span>
                                <table class="table table-sm table-bordered mb-0" style="table-layout: fixed; width: 100%;">
                                    <colgroup>
                                        <col style="width: 60%;">
                                        <col style="width: 20%;">
                                        <col style="width: 20%;">
                                    </colgroup>
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Req Qty</th>
                                            <th class="text-center">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($req->requestItems as $reqItem)
                                        <tr>
                                            <td class="text-truncate" style="max-width: 0;">{{ $reqItem->item->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $reqItem->requested_quantity }}</td>
                                            <td class="text-center fw-bold {{ ($reqItem->item->stock ?? 0) < $reqItem->requested_quantity ? 'text-danger' : 'text-success' }}">
                                                {{ $reqItem->item->stock ?? 0 }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mb-3 mt-4">
                                <label class="form-label fw-semibold">Action</label>
                                <select class="form-select rounded-3" name="status" required>
                                    <option value="" disabled selected>Select action...</option>
                                    <option value="Approved">Approve All</option>
                                    <option value="Cancelled">Cancel Request</option>
                                </select>
                            </div>
                        @else
                            <!-- Old single item view -->
                            <div class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                <span class="text-muted">Item:</span>
                                <span class="fw-bold">{{ $req->item->name ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                <span class="text-muted">Requested Qty:</span>
                                <span class="fw-bold text-primary">{{ $req->requested_quantity }}</span>
                            </div>
                            <div class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                <span class="text-muted">Available Stock:</span>
                                <span class="fw-bold {{ ($req->item->stock ?? 0) < $req->requested_quantity ? 'text-danger' : 'text-success' }}">{{ $req->item->stock ?? 0 }}</span>
                            </div>

                            <div class="mb-3 mt-4">
                                <label class="form-label fw-semibold">Action</label>
                                <select class="form-select rounded-3"
                                    name="status"
                                    id="statusSelect{{ $req->id }}"
                                    data-request-id="{{ $req->id }}"
                                    data-requested-quantity="{{ $req->requested_quantity }}"
                                    required
                                    onchange="toggleQtyInput(this)">
                                    <option value="" disabled selected>Select action...</option>
                                    <option value="Approved">Approve Fully</option>
                                    <option value="Adjusted">Approve with Adjustment</option>
                                    <option value="Cancelled">Cancel / Reject</option>
                                </select>
                            </div>

                            <div class="mb-3" id="qtyInputWrapper{{ $req->id }}" style="display: none;">
                                <label class="form-label fw-semibold">Approved Quantity</label>
                                <input type="number" class="form-control rounded-3" name="approved_quantity" id="approvedQtyInput{{ $req->id }}" 
                                    value="{{ min($req->requested_quantity, $req->item->stock ?? 0) }}" 
                                    max="{{ $req->item->stock ?? 0 }}" min="1">
                                <div class="form-text">Must not exceed available stock ({{ $req->item->stock ?? 0 }}).</div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Admin Note (Optional)</label>
                            <textarea class="form-control rounded-3" name="admin_note" rows="2" placeholder="Reason for adjustment/cancellation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-light bg-light">
                        <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4">Submit Decision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @else
    <!-- View Modal (For Processed) -->
    <div class="modal fade" id="viewRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom border-light">
                    <h5 class="modal-title fw-bold">Request #{{ $req->id }} Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <span class="text-muted d-block small">Requester</span>
                            <span class="fw-semibold">{{ $req->requester_name }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block small">Department</span>
                            <span class="fw-semibold">{{ $req->department }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block small">Status</span>
                            <span class="fw-bold">{{ $req->status }}</span>
                        </div>
                    </div>

                    @if($req->requestItems->count() > 0)
                        <div class="mb-3">
                            <span class="text-muted fw-bold d-block mb-2 small">Requested Items</span>
                            <table class="table table-sm table-bordered mb-0" style="table-layout: fixed; width: 100%;">
                                <colgroup>
                                    <col style="width: 55%;">
                                    <col style="width: 22%;">
                                    @if(in_array($req->status, ['Approved', 'Adjusted']))
                                    <col style="width: 23%;">
                                    @endif
                                </colgroup>
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Req Qty</th>
                                        @if(in_array($req->status, ['Approved', 'Adjusted']))
                                        <th class="text-center text-success">Appr Qty</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($req->requestItems as $reqItem)
                                    <tr>
                                        <td class="text-truncate" style="max-width: 0;">{{ $reqItem->item->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $reqItem->requested_quantity }}</td>
                                        @if(in_array($req->status, ['Approved', 'Adjusted']))
                                        <td class="text-center fw-bold text-success">{{ $reqItem->approved_quantity ?? '-' }}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="row g-3 mb-3 mt-0">
                            <div class="col-6">
                                <span class="text-muted d-block small">Item</span>
                                <span class="fw-semibold">{{ $req->item->name ?? 'N/A' }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block small">Requested Qty</span>
                                <span class="fw-semibold">{{ $req->requested_quantity }}</span>
                            </div>
                            @if(in_array($req->status, ['Approved', 'Adjusted']))
                            <div class="col-6">
                                <span class="text-muted d-block small">Approved Qty</span>
                                <span class="fw-bold text-success">{{ $req->approved_quantity }}</span>
                            </div>
                            @endif
                        </div>
                    @endif
                    
                    @if($req->admin_note)
                    <div class="alert alert-light border rounded-3 mt-3 mb-0 small">
                        <i class="fas fa-comment-alt text-muted me-1"></i> <strong>Admin Note:</strong> {{ $req->admin_note }}
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-top border-light bg-light">
                    <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

<script>
    function toggleQtyInput(selectElement) {
        const requestId = selectElement.dataset.requestId;
        const requestedQuantity = selectElement.dataset.requestedQuantity;
        const wrapper = document.getElementById(`qtyInputWrapper${requestId}`);
        const input = document.getElementById(`approvedQtyInput${requestId}`);

        if (!wrapper || !input) return;

        if (selectElement.value === 'Adjusted') {
            wrapper.style.display = 'block';
            input.required = true;
            return;
        }

        wrapper.style.display = 'none';
        input.required = false;

        if (selectElement.value === 'Approved') {
            input.value = requestedQuantity;
        }
    }
</script>
