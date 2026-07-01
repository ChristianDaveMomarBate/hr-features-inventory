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
        <div class="col-6">
          <span class="text-muted d-block">Item:</span>
          <span class="fw-semibold">{{ $itemRequest->item->name ?? 'N/A' }}</span>
        </div>
        <div class="col-6">
          <span class="text-muted d-block">Requested Qty:</span>
          <span class="fw-semibold">{{ $itemRequest->requested_quantity }}</span>
        </div>
        @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
        <div class="col-6">
          <span class="text-muted d-block">Approved Qty:</span>
          <span class="fw-semibold text-success">{{ $itemRequest->approved_quantity }}</span>
        </div>
        @endif
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

    </div>
  </div>
@else
  <div class="alert alert-warning rounded-3">
    <i class="fas fa-exclamation-triangle me-2"></i>Request ID #{{ $requestId }} not found. Please check and try again.
  </div>
@endif
