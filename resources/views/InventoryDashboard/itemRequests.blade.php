<div id="item-requests" class="page {{ $activePageId === 'item-requests' ? 'active-page' : '' }}">
    <div class="dashboard-main-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="dashboard-title mb-0">
            <span class="dashboard-title-badge">StockWise - Item Requested Manage</span>
        </h1>
        @include('InventoryDashboard.navbar')
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table stockwise-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3 text-secondary fw-semibold">ID</th>
                            <th class="py-3 text-secondary fw-semibold">Requester</th>
                            <th class="py-3 text-secondary fw-semibold">Division</th>
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
                            <td class="ps-4 py-3">
                                <span class="request-id">#{{ $req->id }}</span>
                            </td>
                            <td class="py-3">{{ $req->requester_name }}</td>
                            <td class="py-3">{{ $req->department }}</td>
                            <td class="py-3 fw-medium">
                                @if($req->requestItems->count() > 0)
                                {{ $req->requestItems->count() }} Item(s)
                                <small class="d-block small text-muted fw-normal">Click review to see items.</small>
                                @elseif($req->item)
                                {{ $req->item->name }}
                                <span class="stock-badge ms-1" title="Available Stock">{{ $req->item->stock }}</span>
                                @else
                                <span class="text-danger">Item Not Found</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($req->requestItems->count() > 0)
                                <div><span class="fw-bold">-</span></div>
                                @else
                                @php $reqUnit = optional($req->item)->display_unit ?? 'pcs'; @endphp
                                <div>Req: <span class="fw-bold">{{ $req->requested_quantity }} {{ $reqUnit }}</span></div>
                                @if(in_array($req->status, ['Approved', 'Adjusted']))
                                <div class="text-success small">Appr: <span class="fw-bold">{{ $req->approved_quantity }} {{ $reqUnit }}</span></div>
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
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill me-1" data-bs-toggle="modal" data-bs-target="#reviewRequestModal{{ $req->id }}">
                                    Review
                                </button>
                                @else
                                <button type="button" class="btn btn-sm btn-light rounded-pill text-muted me-1" data-bs-toggle="modal" data-bs-target="#viewRequestModal{{ $req->id }}">
                                    View
                                </button>
                                @endif
                                <button type="button" onclick="printRequest('{{ route('kiosk.request.receipt', $req->id) }}')" class="btn btn-sm me-1" style="border: 1px solid #ced4da; color: #4989d6; border-radius: 6px; background: transparent;">
                                    <i class="fas fa-print"></i> Print
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDeleteModal('{{ route('admin.requests.destroy', $req->id) }}', '{{ $req->control_number }}')">
                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                </button>

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

            <div class="item-requests-pagination mt-4 d-flex justify-content-between align-items-center px-4 pb-4">
                <div class="text-muted small">
                    Showing {{ $itemRequests->firstItem() ?? 0 }} to {{ $itemRequests->lastItem() ?? 0 }} of {{ $itemRequests->total() }} records
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">

                    <div class="text-muted small">
                        Showing 
                        <strong>{{ $itemRequests->firstItem() ?? 0 }}</strong>
                        -
                        <strong>{{ $itemRequests->lastItem() ?? 0 }}</strong>
                        of
                        <strong>{{ $itemRequests->total() }}</strong>
                        requests
                    </div>

                    <div class="d-flex align-items-center gap-2">

                        <a href="{{ $itemRequests->previousPageUrl() ?? '#' }}"
                        class="pagination-btn {{ $itemRequests->onFirstPage() ? 'disabled' : '' }}">
                            <i class="bi bi-chevron-left"></i>
                            Previous
                        </a>

                        <div class="page-indicator">
                            Page {{ $itemRequests->currentPage() }}
                            <span>/</span>
                            {{ $itemRequests->lastPage() }}
                        </div>

                        <a href="{{ $itemRequests->nextPageUrl() ?? '#' }}"
                        class="pagination-btn {{ !$itemRequests->hasMorePages() ? 'disabled' : '' }}">
                            Next
                            <i class="bi bi-chevron-right"></i>
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals rendered outside of table to prevent HTML layout breaking -->
@foreach($itemRequests as $req)
@if($req->status === 'Pending')
<!-- Review Modal (For Pending) -->
<div class="modal fade" id="reviewRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title fw-bold">Review Request #{{ $req->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.requests.status', $req->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">

                    {{-- Requester Info --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <span class="text-muted d-block small fw-bold text-uppercase">Requisitioner</span>
                            <span class="fw-semibold">{{ $req->requester_name }}</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block small fw-bold text-uppercase">Division</span>
                            <span class="fw-semibold">{{ $req->department }}</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block small fw-bold text-uppercase">Date requested</span>
                            <span class="fw-semibold"> {{ $req->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="text-muted d-block small fw-bold text-uppercase mb-1">Purpose</span>
                                <span class="fw-semibold text-dark text-break">{{ $req->purpose ?: 'No purpose provided.' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="mb-3">
                        <span class="text-muted fw-bold d-block mb-2">Requested Items</span>
                        <div style="max-height: 350px; overflow-y: auto; border: 1px solid #000;">
                            <table class="table mb-0" style="border-collapse: collapse; width: 100%; font-size: 13px;">
                                <thead style="position: sticky; top: 0; z-index: 1;">
                                    <tr style="background: #003087; color: #fff;">
                                        <th style="border: 1px solid #000; padding: 7px 10px; width: 4%; text-align: center;" title="Check to approve this item">✓</th>
                                        <th style="border: 1px solid #000; padding: 7px 10px; width: 5%; text-align: center;">No.</th>
                                        <th style="border: 1px solid #000; padding: 7px 10px; width: 32%;">Item Description</th>
                                        <th style="border: 1px solid #000; padding: 7px 10px; width: 13%; text-align: center;">Quantity</th>
                                        <th style="border: 1px solid #000; padding: 7px 10px; width: 26%; text-align: center;">Remarks</th>
                                        <th style="border: 1px solid #000; padding: 7px 10px; width: 20%; text-align: center;">Received</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($req->requestItems->count() > 0)
                                    @foreach($req->requestItems as $idx => $reqItem)
                                    <tr id="req-row-{{ $req->id }}-{{ $reqItem->id }}">
                                        <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">
                                            <input type="checkbox" name="approve_items[]" value="{{ $reqItem->id }}" class="form-check-input item-approve-check" style="width: 18px; height: 18px; cursor: pointer;" checked onchange="toggleItemRow(this, '{{ $req->id }}', '{{ $reqItem->id }}')">
                                        </td>
                                        <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">{{ $idx + 1 }}</td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;">
                                            {{ $reqItem->item->name ?? 'N/A' }}
                                            @if(($reqItem->item->stock ?? 0) < $reqItem->requested_quantity)
                                                <span class="badge bg-danger ms-1" title="Low stock">Low Stock</span>
                                                @endif
                                                <div class="text-muted" style="font-size:11px;">Stock: {{ $reqItem->item->stock ?? 0 }}</div>
                                        </td>
                                        <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">
                                            <div style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                                                <input type="number" name="item_quantities[{{ $reqItem->id }}]" id="qty-{{ $req->id }}-{{ $reqItem->id }}" value="{{ $reqItem->requested_quantity }}" min="0" max="{{ $reqItem->item->stock ?? 999 }}" class="form-control form-control-sm text-center border p-1" style="width: 55px;" title="Requested: {{ $reqItem->requested_quantity }} | Stock: {{ $reqItem->item->stock ?? 0 }}">
                                                <span class="text-muted" style="font-size: 12px; white-space: nowrap;">{{ $reqItem->item->display_unit ?? 'pcs' }}</span>
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #000; padding: 4px 6px; text-align: center;">
                                            <input type="text" name="item_remarks[{{ $reqItem->id }}]" placeholder="e.g. Available, Partial..." class="form-control form-control-sm border-0" style="font-size:12px; padding: 3px 6px;">
                                        </td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                    </tr>
                                    @endforeach
                                    @for($e = $req->requestItems->count(); $e < 8; $e++) <tr>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">{{ $e + 1 }}</td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        </tr>
                                        @endfor
                                        @else
                                        <tr id="req-row-{{ $req->id }}-single">
                                            <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">
                                                <input type="checkbox" name="approve_items[]" value="single" class="form-check-input item-approve-check" style="width: 18px; height: 18px; cursor: pointer;" checked onchange="toggleItemRow(this, '{{ $req->id }}', 'single')">
                                            </td>
                                            <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">1</td>
                                            <td style="border: 1px solid #000; padding: 6px 10px;">
                                                {{ $req->item->name ?? 'N/A' }}
                                                <div class="text-muted" style="font-size:11px;">Stock: {{ $req->item->stock ?? 0 }}</div>
                                            </td>
                                            <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">
                                                <input type="number" name="approved_quantity" id="qty-{{ $req->id }}-single" value="{{ $req->requested_quantity }}" min="0" max="{{ $req->item->stock ?? 999 }}" class="form-control form-control-sm text-center border-0 p-0" style="width: 60px; margin: 0 auto;" title="Requested: {{ $req->requested_quantity }} | Stock: {{ $req->item->stock ?? 0 }}">
                                            </td>
                                            <td style="border: 1px solid #000; padding: 4px 6px; text-align: center;">
                                                <input type="text" name="item_remarks[single]" placeholder="e.g. Available, Partial..." class="form-control form-control-sm border-0" style="font-size:12px; padding: 3px 6px;">
                                            </td>
                                            <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        </tr>
                                        @for($e = 1; $e < 8; $e++) <tr>
                                            <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                            <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">{{ $e + 1 }}</td>
                                            <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                            <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                            <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                            <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                            </tr>
                                            @endfor
                                            @endif
                                </tbody>
                            </table>
                        </div>
                        <label class="small fw-semibold text-secondary mb-2 d-block"></br>
                            Administrative Remarks
                        </label>

                        <textarea class="form-control" rows="3" name="admin_note" placeholder="Provide remarks..." style="
                                border:1px solid #dbe3ec;
                                border-radius:12px;
                                resize:none;
                                padding:14px;
                                box-shadow:none;
                                background:#fff;
                            ">
                        </textarea>
                    </div>

                </div>
                <div class="modal-footer border-top border-light bg-light d-flex justify-content-between flex-wrap gap-2" style="position: sticky; bottom: 0; z-index: 10;">
                    <button type="button" onclick="printRequest('{{ route('kiosk.request.receipt', $req->id) }}', '{{ $req->id }}')" class="btn btn-outline-secondary text-nowrap"><i class="fas fa-print me-1"></i> Print</button>
                    <div class="d-flex gap-2 align-items-center flex-wrap justify-content-end">
                        <div class="d-flex align-items-center">
                            <label class="form-label fw-semibold mb-0 me-2">Decision:</label>
                            <select class="form-select form-select-sm d-inline-block w-auto rounded-3" name="status" required style="min-width:190px;">
                                <option value="" disabled selected>Select action...</option>
                                <option value="Approved">Approve Checked Items</option>
                                <option value="Adjusted">Approve with Adjustment</option>
                                <option value="Cancelled">Cancel / Reject</option>
                            </select>
                        </div>

                        <button type="button" class="btn btn-light text-secondary text-nowrap" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4 fw-bold text-nowrap"><i class="fas fa-save me-1"></i> Save &amp; Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@else
<!-- View Modal (For Processed) -->
<div class="modal fade" id="viewRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title fw-bold">Request #{{ $req->id }} Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <span class="text-muted d-block small fw-bold text-uppercase">Requester</span>
                        <span class="fw-semibold">{{ $req->requester_name }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted d-block small fw-bold text-uppercase">Division</span>
                        <span class="fw-semibold">{{ $req->department }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted d-block small fw-bold text-uppercase">Status</span>
                        <span class="fw-bold">{{ $req->status }}</span>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted d-block small fw-bold text-uppercase mb-1">Purpose</span>
                            <span class="fw-semibold text-dark text-break">{{ $req->purpose ?: 'No purpose provided.' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="mb-3">
                    <span class="text-muted fw-bold d-block mb-2">Requested Items</span>
                    <div style="max-height: 350px; overflow-y: auto; border: 1px solid #000;">
                        <table class="table mb-0" style="border-collapse: collapse; width: 100%; font-size: 13px;">
                            <thead style="position: sticky; top: 0; z-index: 1;">
                                <tr style="background: #003087; color: #fff;">
                                    <th style="border: 1px solid #000; padding: 7px 10px; width: 5%; text-align: center;">No.</th>
                                    <th style="border: 1px solid #000; padding: 7px 10px; width: 40%;">Item Description</th>
                                    <th style="border: 1px solid #000; padding: 7px 10px; width: 15%; text-align: center;">Quantity</th>
                                    <th style="border: 1px solid #000; padding: 7px 10px; width: 20%; text-align: center;">Remarks</th>
                                    <th style="border: 1px solid #000; padding: 7px 10px; width: 20%; text-align: center;">Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($req->requestItems->count() > 0)
                                @foreach($req->requestItems as $idx => $reqItem)
                                <tr>
                                    <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">{{ $idx + 1 }}</td>
                                    <td style="border: 1px solid #000; padding: 6px 10px;">{{ $reqItem->item->name ?? 'N/A' }}</td>
                                    <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">
                                        @if(in_array($req->status, ['Approved', 'Adjusted']) && $reqItem->approved_quantity)
                                        <strong>{{ $reqItem->approved_quantity }}</strong>
                                        @else
                                        {{ $reqItem->requested_quantity }}
                                        @endif
                                    </td>
                                    <td style="border: 1px solid #000; padding: 6px 10px;">{{ $reqItem->remarks }}</td>
                                    <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                </tr>
                                @endforeach
                                @for($e = $req->requestItems->count(); $e < 8; $e++) <tr>
                                    <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">{{ $e + 1 }}</td>
                                    <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                    <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                    <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                    <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                    </tr>
                                    @endfor
                                    @else
                                    <tr>
                                        <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">1</td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;">{{ $req->item->name ?? 'N/A' }}</td>
                                        <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">
                                            @if(in_array($req->status, ['Approved', 'Adjusted']))
                                            <strong>{{ $req->approved_quantity }}</strong>
                                            @else
                                            {{ $req->requested_quantity }}
                                            @endif
                                        </td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;">{{ $req->remarks }}</td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                    </tr>
                                    @for($e = 1; $e < 8; $e++) <tr>
                                        <td style="border: 1px solid #000; padding: 6px 10px; text-align: center;">{{ $e + 1 }}</td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        <td style="border: 1px solid #000; padding: 6px 10px;"></td>
                                        </tr>
                                        @endfor
                                        @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($req->admin_note)
                <div class="alert alert-light border rounded-3 mt-3 mb-0 small">
                    <i class="fas fa-comment-alt text-muted me-1"></i> <strong>Admin Note:</strong> {{ $req->admin_note }}
                </div>
                @endif
            </div>
            <div class="modal-footer border-top border-light bg-light d-flex justify-content-between">
                <button type="button" onclick="printRequest('{{ route('kiosk.request.receipt', $req->id) }}')" class="btn btn-outline-secondary"><i class="fas fa-print me-1"></i> Print</button>
                <div class="d-flex gap-2">
                    @if(in_array($req->status, ['Approved', 'Adjusted']))
                    <button type="button" class="btn btn-warning text-dark d-flex align-items-center gap-2" onclick="openRevertConfirm({{ $req->id }}, '{{ route('admin.requests.revert', $req->id) }}')">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Revert to Pending
                    </button>
                    @endif
                    <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Close</button>
                </div>
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

    function printRequest(url, reqId = null) {
        if (reqId) {
            const form = document.querySelector(`#reviewRequestModal${reqId} form`);
            if (form) {
                const checkboxes = form.querySelectorAll('input.item-approve-check:checked');
                let previewItems = [];
                checkboxes.forEach(cb => {
                    const itemId = cb.value;
                    const row = document.getElementById(`req-row-${reqId}-${itemId}`);
                    if (row) {
                        let desc = row.cells[2].innerText.split('\n')[0].trim();
                        if (desc.includes('Low Stock')) {
                            desc = desc.replace('Low Stock', '').trim();
                        }
                        const qtyInput = document.getElementById(`qty-${reqId}-${itemId}`);
                        const remarksInput = row.querySelector(`input[name^="item_remarks"]`);

                        let unit = '';
                        if (qtyInput && qtyInput.nextElementSibling) {
                            unit = qtyInput.nextElementSibling.innerText.trim();
                        }

                        let qtyValue = qtyInput ? qtyInput.value : '';
                        if (qtyValue && unit) {
                            qtyValue += ' ' + unit;
                        }

                        previewItems.push({
                            desc: desc
                            , qty: qtyValue
                            , remarks: remarksInput ? remarksInput.value : ''
                        });
                    }
                });

                if (previewItems.length > 0) {
                    const previewJson = encodeURIComponent(JSON.stringify(previewItems));
                    const separator = url.includes('?') ? '&' : '?';
                    url = url + separator + 'preview_data=' + previewJson;
                }
            }
        }

        // Use a hidden iframe to print without opening a new tab
        let iframe = document.getElementById('print-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'print-iframe';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }

        iframe.onload = function() {
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }, 500);
        };
        iframe.src = url;
    }

    /**
     * When a checkbox is unchecked, dim the row and zero out the quantity.
     * When re-checked, restore the row and quantity.
     */
    function toggleItemRow(checkbox, reqId, itemId) {
        const row = document.getElementById(`req-row-${reqId}-${itemId}`);
        const qtyInput = document.getElementById(`qty-${reqId}-${itemId}`);
        if (!row) return;

        if (checkbox.checked) {
            // Restore row
            row.style.opacity = '1';
            row.style.background = '';
            if (qtyInput) {
                qtyInput.disabled = false;
                // Restore previous value from data attribute
                if (qtyInput.dataset.prevVal) {
                    qtyInput.value = qtyInput.dataset.prevVal;
                }
            }
            // Re-enable remarks input
            const remarksInput = row.querySelector('input[name^="item_remarks"]');
            if (remarksInput) remarksInput.disabled = false;
        } else {
            // Dim row
            row.style.opacity = '0.4';
            row.style.background = '#f8f8f8';
            if (qtyInput) {
                // Save current value before zeroing
                qtyInput.dataset.prevVal = qtyInput.value;
                qtyInput.value = 0;
                qtyInput.disabled = true;
            }
            // Disable remarks input
            const remarksInput = row.querySelector('input[name^="item_remarks"]');
            if (remarksInput) remarksInput.disabled = true;
        }
    }

    /* ===== Modern Revert Confirmation Modal ===== */
    function openRevertConfirm(requestId, actionUrl) {
        document.getElementById('revertRequestId').textContent = '#' + requestId;
        document.getElementById('revertConfirmForm').action = actionUrl;
        const modal = new bootstrap.Modal(document.getElementById('revertConfirmModal'));
        modal.show();
    }

</script>

{{-- ===== Modern Revert Confirmation Modal ===== --}}
<div class="modal fade" id="revertConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

            {{-- Amber accent top bar --}}
            <div style="height: 5px; background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>

            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width:48px; height:48px; background: #fef3c7;">
                        <i class="bi bi-arrow-counterclockwise" style="font-size: 22px; color: #d97706;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color: #1e293b;">Revert Request</h6>
                        <span class="text-muted" style="font-size: 13px;">Request <span id="revertRequestId" class="fw-semibold text-dark"></span></span>
                    </div>
                </div>

                <p class="mb-0" style="font-size: 14px; color: #475569; line-height: 1.6;">
                    This will <strong>restore all stock quantities</strong> back to the inventory and
                    set the request status to <span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size:12px;">Pending</span>.
                    The admin can then review and re-approve it.
                </p>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2 justify-content-end">
                <button type="button" class="btn btn-light text-muted fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <form id="revertConfirmForm" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn fw-semibold rounded-pill px-4 d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #1e293b; border: none;">
                        <i class="bi bi-arrow-counterclockwise"></i> Yes, Revert
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===================== DELETE CONFIRM MODAL ===================== --}}
<div class="modal fade" id="deleteRequestModal" tabindex="-1" aria-labelledby="deleteRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-0 px-4 pt-4" style="background: linear-gradient(135deg, #fff1f2, #ffe4e6);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 48px; height: 48px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trash-alt" style="color: #dc2626; font-size: 18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="deleteRequestModalLabel" style="color: #991b1b;">Delete Request</h5>
                        <p class="mb-0" style="font-size: 0.78rem; color: #b91c1c;">This action cannot be undone</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body px-4 py-3" style="background: linear-gradient(135deg, #fff1f2, #ffe4e6);">
                <div style="background: white; border-radius: 12px; padding: 1.25rem; border: 1px solid #fecaca;">
                    <p class="mb-1" style="font-size: 14px; color: #475569;">
                        You are about to permanently delete:
                    </p>
                    <p class="fw-bold mb-0" id="deleteRequestLabel" style="color: #dc2626; font-size: 1rem;"></p>
                    <p class="mt-2 mb-0" style="font-size: 13px; color: #94a3b8;">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        All data associated with this request will be permanently removed.
                    </p>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2" style="background: linear-gradient(135deg, #fff1f2, #ffe4e6);">
                <button type="button" class="btn btn-light text-muted fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteRequestForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn fw-semibold rounded-pill px-4 d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; border: none;">
                        <i class="fas fa-trash-alt"></i> Yes, Delete
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function openDeleteModal(actionUrl, controlNumber) {
        document.getElementById('deleteRequestForm').action = actionUrl;
        document.getElementById('deleteRequestLabel').textContent = controlNumber;
        var modal = new bootstrap.Modal(document.getElementById('deleteRequestModal'));
        modal.show();
    }

</script>
