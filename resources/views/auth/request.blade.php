<div id="request-section"
     class="tab-section animate-fade-in request-section-hidden"
     data-track-url="{{ route('kiosk.request.track') }}"
     data-thanks-url="{{ asset('sound/thanks.mp3') }}">

  {{-- Hidden data element: tells JS to auto-switch to Track tab after submit --}}
  @if(session('new_request_id'))
    <div id="requestAutoTrackData"
         data-request-id="{{ session('new_request_id') }}"
         class="request-hidden"></div>
  @endif


  <div class="rg-centering-wrapper">
  <div class="request-glass-card">
    <!-- Header -->
    <div class="rg-header">
      <h3><i class="fas fa-hand-holding-box me-2 request-header-icon"></i>Item Request</h3>
      <p>Submit and track your inventory requests</p>
    </div>

    <!-- Body -->
    <div class="rg-body">

      <!-- Tab Pills -->
      <ul class="nav nav-pills nav-fill" id="requestTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="submit-request-tab"
            type="button" onclick="switchRequestTab('submit-request')">
            <i class="fas fa-paper-plane me-1"></i> Submit Request
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="track-request-tab"
            type="button" onclick="switchRequestTab('track-request')">
            <i class="fas fa-search me-1"></i> Track Request
          </button>
        </li>
      </ul>

      <div class="tab-content" id="requestTabContent">
        <!-- ── Submit Tab ── -->
        <div class="tab-pane request-tab-pane-active" id="submit-request" role="tabpanel">

          @php
            $requestErrorMessages = collect([
              'requester_name',
              'department',
              'purpose',
              'items',
              'items.*.item_id',
              'items.*.requested_quantity',
            ])->flatMap(fn ($field) => $errors->get($field))->unique();
          @endphp

          @if($requestErrorMessages->isNotEmpty())
            <div class="alert alert-danger rounded-3 alert-dismissible mb-3 py-2 request-alert-danger">
              <i class="fas fa-exclamation-circle me-2"></i>
              @foreach($requestErrorMessages as $error) {{ $error }}<br> @endforeach
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <form method="POST" action="{{ route('kiosk.request.store') }}">
            @csrf
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="requester_name" class="form-label">Full Name <span class="request-required">*</span></label>
                <input type="text" class="form-control @error('requester_name') is-invalid @enderror"
                  id="requester_name" name="requester_name" value="{{ old('requester_name') }}"
                  required placeholder="e.g. Juan Dela Cruz">
                @error('requester_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="department" class="form-label">Department / Division <span class="request-required">*</span></label>
                <select class="form-select @error('department') is-invalid @enderror" id="department" name="department" required>
                  <option value="" disabled selected>Select department...</option>
                  @foreach($divisions ?? [] as $div)
                    <option value="{{ $div }}" {{ old('department') === $div ? 'selected' : '' }}>{{ $div }}</option>
                  @endforeach
                </select>
                @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="mb-3 border rounded p-3 bg-light">
              <label class="form-label d-block mb-3 fw-bold text-dark">Requested Items <span class="request-required">*</span></label>
              
              <div id="itemsContainer" style="max-height: 170px; overflow-y: auto; overflow-x: hidden; padding-right: 6px; margin-bottom: 10px;">
                <div class="row align-items-center item-row mb-2">
                  <div class="col-md-7 mb-2 mb-md-0">
                    <select class="form-select" name="items[0][item_id]" required>
                      <option value="" disabled selected>Select an item...</option>
                      @foreach($request_items ?? [] as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->category }})</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-3 mb-2 mb-md-0">
                    <input type="number" class="form-control" name="items[0][requested_quantity]" value="1" min="1" placeholder="Qty" required>
                  </div>
                  <div class="col-md-2 text-md-end text-start">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-item-btn" disabled><i class="fas fa-trash-alt"></i></button>
                  </div>
                </div>
              </div>
              
              <button type="button" class="btn btn-sm btn-outline-primary mt-2 rounded-pill bg-white" onclick="addRequestItemRow()">
                <i class="fas fa-plus me-1"></i> Add Another Item
              </button>
            </div>

            <div class="mb-3">
              <label for="purpose" class="form-label">Purpose <span class="request-optional">(Optional)</span></label>
              <textarea class="form-control @error('purpose') is-invalid @enderror"
                id="purpose" name="purpose" rows="2"
                placeholder="Briefly describe why you need this item...">{{ old('purpose') }}</textarea>
              @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-rg-primary mt-1">
              <i class="fas fa-paper-plane me-2"></i> Submit Request
            </button>
          </form>
        </div>

        <!-- ── Track Tab ── -->
        <div class="tab-pane request-tab-pane-hidden" id="track-request" role="tabpanel">

          @if(session('success'))
            <div class="alert alert-dismissible mb-3 py-2 d-flex align-items-center gap-2 request-alert-success">
              <i class="fas fa-check-circle fa-lg"></i>
              <div>
                <div class="fw-bold request-success-title">Request Submitted!</div>
                <div class="request-success-message">{{ session('success') }}</div>
              </div>
              <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <div class="track-box">
            <h5><i class="fas fa-search me-2"></i>Track Your Request</h5>
            <p>Enter the Request ID you received to see your request status.</p>
            <form id="trackRequestForm" onsubmit="event.preventDefault(); trackRequest();">
              <div class="input-group overflow-hidden request-track-input-group">
                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                <input type="number" id="track_request_id"
                  class="form-control"
                  placeholder="e.g. 12" required min="1">
                <button class="btn-rg-primary px-4 request-track-submit" type="submit">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </form>
          </div>

          <div id="trackResultContainer">
            <div class="text-center">
              <i class="fas fa-clipboard-list fa-2x mb-2 d-block request-empty-icon"></i>
              Status details will appear here.
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  </div>{{-- /.rg-centering-wrapper --}}
</div>{{-- /#request-section --}}

<!-- Receipt Modal -->
@if(session('show_receipt_modal') && isset($submittedRequest))
<div class="modal fade" id="requestReceiptModal" tabindex="-1" aria-labelledby="requestReceiptModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white border-bottom-0" style="border-bottom: 4px solid #10b981 !important;">
        <div>
          <h5 class="modal-title fw-bold mb-0" id="requestReceiptModalLabel" style="font-family: 'Outfit', sans-serif;">PHRMDO Inventory System</h5>
          <small class="opacity-75">Official Item Request Slip</small>
        </div>
        <button type="button" class="btn-close btn-close-white close text-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="d-none">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4" id="receiptPrintableArea">
        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
          <span class="text-muted fw-bold">Request ID</span>
          <span class="fw-bold text-dark">#{{ $submittedRequest->id }}</span>
        </div>
        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
          <span class="text-muted fw-bold">Status</span>
          <span>
            @if($submittedRequest->status === 'Pending')
              <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>
            @elseif($submittedRequest->status === 'Approved')
              <span class="badge bg-success rounded-pill px-3">Approved</span>
            @elseif($submittedRequest->status === 'Adjusted')
              <span class="badge bg-info text-dark rounded-pill px-3">Adjusted</span>
            @elseif($submittedRequest->status === 'Cancelled')
              <span class="badge bg-danger rounded-pill px-3">Cancelled</span>
            @else
              <span class="badge bg-secondary rounded-pill px-3">{{ $submittedRequest->status }}</span>
            @endif
          </span>
        </div>
        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
          <span class="text-muted fw-bold">Date Submitted</span>
          <span class="text-dark fw-medium">{{ $submittedRequest->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
          <span class="text-muted fw-bold">Requester Name</span>
          <span class="text-dark fw-medium">{{ $submittedRequest->requester_name }}</span>
        </div>
        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
          <span class="text-muted fw-bold">Department / Division</span>
          <span class="text-dark fw-medium text-end" style="max-width: 60%;">{{ $submittedRequest->department }}</span>
        </div>
        <div class="mb-3">
          <span class="text-muted fw-bold d-block mb-2">Requested Items</span>
          <table class="table table-sm table-bordered">
            <thead class="table-light">
              <tr>
                <th>Item</th>
                <th class="text-center" width="80">Qty</th>
              </tr>
            </thead>
            <tbody>
              @if(isset($submittedRequest) && $submittedRequest->requestItems->count() > 0)
                @foreach($submittedRequest->requestItems as $reqItem)
                <tr>
                  <td>{{ $reqItem->item->name ?? 'N/A' }}</td>
                  <td class="text-center">{{ $reqItem->requested_quantity }}</td>
                </tr>
                @endforeach
              @elseif(isset($submittedRequest) && $submittedRequest->item_id)
                <tr>
                  <td>{{ $submittedRequest->item->name ?? 'N/A' }}</td>
                  <td class="text-center">{{ $submittedRequest->requested_quantity }}</td>
                </tr>
              @else
                <tr><td colspan="2" class="text-center text-muted">No items found</td></tr>
              @endif
            </tbody>
          </table>
        </div>
        @if($submittedRequest->purpose)
        <div class="d-flex justify-content-between pb-2">
          <span class="text-muted fw-bold">Purpose</span>
          <span class="text-dark fw-medium text-end" style="max-width: 60%;">{{ $submittedRequest->purpose }}</span>
        </div>
        @endif
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success px-4 rounded-pill" onclick="printReceiptModal()">
          <i class="fas fa-print me-1"></i> Print Receipt
        </button>
      </div>
    </div>
  </div>
</div>
@endif


<script>
  // ── Switch Tabs manually (Bootstrap 4 compatibility) ────────────────────────
  function switchRequestTab(tabId) {
    // Toggle visibility via CSS class (avoids !important inline-style conflicts)
    document.querySelectorAll('#requestTabContent .tab-pane').forEach(function(pane) {
      pane.classList.remove('request-tab-pane-active');
      pane.classList.add('request-tab-pane-hidden');
    });

    var targetPane = document.getElementById(tabId);
    if (targetPane) {
      targetPane.classList.remove('request-tab-pane-hidden');
      targetPane.classList.add('request-tab-pane-active');
    }

    // Update active pill
    document.querySelectorAll('#requestTab .nav-link').forEach(function(link) {
      link.classList.remove('active');
    });
    var activeLink = document.getElementById(tabId + '-tab');
    if (activeLink) activeLink.classList.add('active');
  }

  // ── Track Request via AJAX ──────────────────────────────────────────────────
  function trackRequest() {
    var reqId     = document.getElementById('track_request_id').value.trim();
    var container = document.getElementById('trackResultContainer');
    if (!reqId) return;

    container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    var trackUrl = document.getElementById('request-section').getAttribute('data-track-url');

    fetch(trackUrl + '?request_id=' + encodeURIComponent(reqId), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (res) {
      if (!res.ok) throw new Error('Server error');
      return res.text();
    })
    .then(function (html) {
      container.innerHTML = html;
    })
    .catch(function () {
      container.innerHTML = '<div class="alert alert-danger rounded-3"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load status. Please try again.</div>';
    });
  }

  // ── Auto-switch to Track tab & load status after a successful submission ────
  document.addEventListener('DOMContentLoaded', function () {
    var autoData = document.getElementById('requestAutoTrackData');
    if (!autoData) return;

    var newId = autoData.getAttribute('data-request-id');
    if (!newId) return;

    // Check if we need to show the receipt modal instead
    var showReceiptModal = {{ session('show_receipt_modal') ? 'true' : 'false' }};

    // Wait for initAuthTabs() in script.js to finish setting up tabs
    setTimeout(function () {
      // 1. Show the #request section and activate the nav link
      document.querySelectorAll('.tab-section').forEach(function (s) {
        s.style.display = s.id === 'request-section' ? 'block' : 'none';
      });
      document.querySelectorAll('.navbar-nav .nav-link').forEach(function (l) {
        l.classList.toggle('active', l.dataset.target === 'request');
      });

      if (showReceiptModal) {
         // Play thanks sound on successful submission
         var requestSection = document.getElementById('request-section');
         var thanksUrl = requestSection ? requestSection.getAttribute('data-thanks-url') : '';
         if (thanksUrl) {
           var thanksAudio = new Audio(thanksUrl);
           thanksAudio.currentTime = 0;
           thanksAudio.play().catch(function() {});
         }

         // Show Modal
         var modalEl = document.getElementById('requestReceiptModal');
         if (modalEl) {
             document.body.appendChild(modalEl); // Fix z-index backdrop issue
             if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
                 jQuery(modalEl).modal('show');
             } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                 var modal = new bootstrap.Modal(modalEl);
                 modal.show();
             }
         }
      } else {
        // 2. Switch inner pill to Track tab using our manual JS
        switchRequestTab('track-request');

        // 3. Pre-fill ID and fire AJAX lookup
        setTimeout(function () {
          var input = document.getElementById('track_request_id');
          if (input) {
            input.value = newId;
            trackRequest();
          }
        }, 120);
      }
    }, 250);
  });

  function printReceiptModal() {
      var printContent = document.getElementById('receiptPrintableArea').innerHTML;
      var originalContent = document.body.innerHTML;
      
      // Basic print styles to make it look clean
      var printStyles = `
          <style>
              body { font-family: 'Inter', sans-serif; padding: 20px; color: #000; background: #fff; }
              .text-muted { color: #555 !important; }
              .fw-bold { font-weight: bold; }
              .fw-medium { font-weight: 500; }
              .text-end { text-align: right; }
              .d-flex { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px dashed #ccc; padding-bottom: 10px; }
              .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; border: 1px solid #000; }
              .header { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
              .header h2 { margin: 0; padding: 0; font-family: 'Outfit', sans-serif; }
              .header p { margin: 5px 0 0 0; }
              .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
              .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
              .text-center { text-align: center; }
          </style>
      `;

      var printWindow = window.open('', '', 'height=600,width=800');
      printWindow.document.write('<html><head><title>Print Receipt</title>');
      printWindow.document.write(printStyles);
      printWindow.document.write('</head><body>');
      printWindow.document.write('<div class="header"><h2>PHRMDO Inventory System</h2><p>Official Item Request Slip</p></div>');
      printWindow.document.write(printContent);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.focus();
      
      // Delay printing to allow styles to apply
      setTimeout(function() {
          printWindow.print();
          printWindow.close();
      }, 250);
  }

  function showTrackModal() {
      var modalEl = document.getElementById('trackReceiptModal');
      if (!modalEl) return;
      document.body.appendChild(modalEl); // Fix z-index backdrop issue
      
      if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
          jQuery(modalEl).modal('show');
      } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          var modal = bootstrap.Modal.getInstance ? bootstrap.Modal.getInstance(modalEl) : null;
          if (!modal) {
              modal = new bootstrap.Modal(modalEl);
          }
          modal.show();
      }
  }

  function printTrackReceiptModal() {
      var printContent = document.getElementById('trackReceiptPrintableArea');
      if (!printContent) return;
      
      var printStyles = `
          <style>
              body { font-family: 'Inter', sans-serif; padding: 20px; color: #000; background: #fff; }
              .text-muted { color: #555 !important; }
              .fw-bold { font-weight: bold; }
              .fw-medium { font-weight: 500; }
              .text-end { text-align: right; }
              .d-flex { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px dashed #ccc; padding-bottom: 10px; }
              .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; border: 1px solid #000; }
              .header { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
              .header h2 { margin: 0; padding: 0; font-family: 'Outfit', sans-serif; }
              .header p { margin: 5px 0 0 0; }
              .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
              .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
              .text-center { text-align: center; }
          </style>
      `;

      var printWindow = window.open('', '', 'height=600,width=800');
      printWindow.document.write('<html><head><title>Print Receipt</title>');
      printWindow.document.write(printStyles);
      printWindow.document.write('</head><body>');
      printWindow.document.write('<div class="header"><h2>PHRMDO Inventory System</h2><p>Official Item Request Slip</p></div>');
      printWindow.document.write(printContent.innerHTML);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.focus();
      
      setTimeout(function() {
          printWindow.print();
          printWindow.close();
      }, 250);
  }

  let itemRowCounter = 1;
  function addRequestItemRow() {
    const container = document.getElementById('itemsContainer');
    const firstRow = container.querySelector('.item-row');
    const newRow = firstRow.cloneNode(true);
    
    // Reset values and update names
    const select = newRow.querySelector('select');
    const input = newRow.querySelector('input[type="number"]');
    const removeBtn = newRow.querySelector('.remove-item-btn');
    
    select.name = `items[${itemRowCounter}][item_id]`;
    select.value = '';
    
    input.name = `items[${itemRowCounter}][requested_quantity]`;
    input.value = '1';
    
    removeBtn.disabled = false;
    removeBtn.onclick = function() {
        newRow.remove();
        updateRemoveButtons();
    };
    
    container.appendChild(newRow);
    itemRowCounter++;
    updateRemoveButtons();
  }

  function updateRemoveButtons() {
    const rows = document.querySelectorAll('#itemsContainer .item-row');
    rows.forEach((row, index) => {
        const btn = row.querySelector('.remove-item-btn');
        if (rows.length === 1) {
            btn.disabled = true;
            btn.onclick = null;
        } else {
            btn.disabled = false;
            btn.onclick = function() {
                row.remove();
                updateRemoveButtons();
            };
        }
    });
  }
</script>
