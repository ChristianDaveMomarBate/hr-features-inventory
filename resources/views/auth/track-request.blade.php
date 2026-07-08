@if($itemRequest)
  <div class="card border-0 shadow-sm rounded-4 mt-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title fw-bold mb-0">Request #{{ $itemRequest->id }}</h5>
        @if($itemRequest->status === 'Pending')
          <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>
        @elseif($itemRequest->status === 'Approved')
          <span class="badge bg-success px-3 py-2 rounded-pill">Approved</span>
        @elseif($itemRequest->status === 'Adjusted')
          <span class="badge bg-info text-dark px-3 py-2 rounded-pill">Adjusted</span>
        @elseif($itemRequest->status === 'Cancelled')
          <span class="badge bg-danger px-3 py-2 rounded-pill">Cancelled</span>
        @endif
      </div>
      
      <div class="row g-2 mb-3 small">
        <div class="col-6">
          <span class="text-muted d-block">Requester:</span>
          <span class="fw-semibold">{{ $itemRequest->requester_name }}</span>
        </div>
        <div class="col-6">
          <span class="text-muted d-block">Department:</span>
          <span class="fw-semibold">{{ $itemRequest->department }}</span>
        </div>
        <div class="col-12 mt-3">
          <span class="text-muted d-block mb-1">Requested Items:</span>
          <div style="max-height: 180px; overflow-y: auto;">
            <table class="table table-sm table-bordered mb-0">
              <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                <tr>
                  <th>Item</th>
                  <th class="text-center" width="80">Qty</th>
                  @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
                  <th class="text-center text-success" width="80">Appr</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @if($itemRequest->requestItems->count() > 0)
                  @foreach($itemRequest->requestItems as $reqItem)
                  <tr>
                    <td>{{ $reqItem->item->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $reqItem->requested_quantity }}</td>
                    @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
                    <td class="text-center text-success fw-bold">{{ $reqItem->approved_quantity ?? '-' }}</td>
                    @endif
                  </tr>
                  @endforeach
                @elseif($itemRequest->item_id)
                  <tr>
                    <td>{{ $itemRequest->item->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $itemRequest->requested_quantity }}</td>
                    @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
                    <td class="text-center text-success fw-bold">{{ $itemRequest->approved_quantity ?? '-' }}</td>
                    @endif
                  </tr>
                @else
                  <tr><td colspan="3" class="text-center text-muted">No items found</td></tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-6">
          <span class="text-muted d-block">Date:</span>
          <span class="fw-semibold">{{ $itemRequest->created_at->format('M d, Y h:i A') }}</span>
        </div>
      </div>

      @if($itemRequest->admin_note)
      <div class="alert alert-light border rounded-3 small mb-0">
        <i class="fas fa-comment-alt text-muted me-1"></i> <strong>Admin Note:</strong> {{ $itemRequest->admin_note }}
      </div>
      @endif

      @if($itemRequest->status === 'Approved')
        <div class="alert alert-success mt-3 mb-0 small">
          <i class="fas fa-check-circle me-1"></i> Your request has been approved.
        </div>
      @elseif($itemRequest->status === 'Adjusted')
        <div class="alert alert-info mt-3 mb-0 small">
          <i class="fas fa-info-circle me-1"></i> Your request was approved with adjusted quantity.
        </div>
      @elseif($itemRequest->status === 'Cancelled')
        <div class="alert alert-danger mt-3 mb-0 small">
          <i class="fas fa-times-circle me-1"></i> Your request has been cancelled.
        </div>
      @endif

      <div class="mt-3 text-end">
        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="showTrackModal()">
          <i class="fas fa-print me-1"></i> View / Print Receipt
        </button>
      </div>

    </div>
  </div>

  <!-- Receipt Modal for Tracking -->
  <div class="modal fade" id="trackReceiptModal" tabindex="-1" aria-labelledby="trackReceiptModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow">
          <div class="modal-header bg-dark text-white border-bottom-0" style="border-bottom: 4px solid #10b981 !important;">
          <div>
            <h5 class="modal-title fw-bold mb-0" id="trackReceiptModalLabel" style="font-family: 'Outfit', sans-serif;">PHRMDO Inventory System</h5>
            <small class="opacity-75">Official Item Request Slip</small>
          </div>
          <button type="button" class="btn-close btn-close-white close text-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="d-none">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4 text-start" id="trackReceiptPrintableArea">
          <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <span class="text-muted fw-bold">Request ID</span>
            <span class="fw-bold text-dark">#{{ $itemRequest->id }}</span>
          </div>
          <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <span class="text-muted fw-bold">Status</span>
            <span>
              @if($itemRequest->status === 'Pending')
                <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>
              @elseif($itemRequest->status === 'Approved')
                <span class="badge bg-success rounded-pill px-3">Approved</span>
              @elseif($itemRequest->status === 'Adjusted')
                <span class="badge bg-info text-dark rounded-pill px-3">Adjusted</span>
              @elseif($itemRequest->status === 'Cancelled')
                <span class="badge bg-danger rounded-pill px-3">Cancelled</span>
              @else
                <span class="badge bg-secondary rounded-pill px-3">{{ $itemRequest->status }}</span>
              @endif
            </span>
          </div>
          <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <span class="text-muted fw-bold">Date Submitted</span>
            <span class="text-dark fw-medium">{{ $itemRequest->created_at->format('M d, Y h:i A') }}</span>
          </div>
          <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <span class="text-muted fw-bold">Requester Name</span>
            <span class="text-dark fw-medium">{{ $itemRequest->requester_name }}</span>
          </div>
          <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <span class="text-muted fw-bold">Department / Division</span>
            <span class="text-dark fw-medium text-end" style="max-width: 60%;">{{ $itemRequest->department }}</span>
          </div>
            <div class="mb-3">
            <span class="text-muted fw-bold d-block mb-2">Requested Items</span>
            <table class="table table-sm table-bordered mb-0" style="table-layout: fixed; width: 100%;">
              <colgroup>
                <col style="width: 60%;">
                <col style="width: 20%;">
                @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
                <col style="width: 20%;">
                @endif
              </colgroup>
              <thead class="table-light">
                <tr>
                  <th>Item</th>
                  <th class="text-center">Req Qty</th>
                  @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
                  <th class="text-center text-success">Appr Qty</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @if($itemRequest->requestItems->count() > 0)
                  @foreach($itemRequest->requestItems as $reqItem)
                  <tr>
                    <td class="text-truncate" style="max-width: 0;">{{ $reqItem->item->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $reqItem->requested_quantity }}</td>
                    @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
                    <td class="text-center fw-bold text-success">{{ $reqItem->approved_quantity ?? '-' }}</td>
                    @endif
                  </tr>
                  @endforeach
                @elseif($itemRequest->item_id)
                  <tr>
                    <td class="text-truncate" style="max-width: 0;">{{ $itemRequest->item->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $itemRequest->requested_quantity }}</td>
                    @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
                    <td class="text-center fw-bold text-success">{{ $itemRequest->approved_quantity ?? '-' }}</td>
                    @endif
                  </tr>
                @else
                  <tr><td colspan="3" class="text-center text-muted">No items found</td></tr>
                @endif
              </tbody>
            </table>
          </div>
          @if($itemRequest->purpose)
          <div class="d-flex justify-content-between pb-2 mb-2">
            <span class="text-muted fw-bold">Purpose</span>
            <span class="text-dark fw-medium text-end" style="max-width: 60%;">{{ $itemRequest->purpose }}</span>
          </div>
          @endif
          @if($itemRequest->admin_note)
          <div class="d-flex justify-content-between pb-2 border-top pt-2">
            <span class="text-muted fw-bold">Admin Note</span>
            <span class="text-dark fw-medium text-end" style="max-width: 60%;">{{ $itemRequest->admin_note }}</span>
          </div>
          @endif
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success px-4 rounded-pill" onclick="printTrackReceiptModal()">
            <i class="fas fa-print me-1"></i> Print Receipt
          </button>
        </div>
      </div>
    </div>
  </div>

@else
  <div class="alert alert-warning rounded-3">
    <i class="fas fa-exclamation-triangle me-2"></i>Request ID #{{ $requestId }} not found. Please check and try again.
  </div>
@endif
