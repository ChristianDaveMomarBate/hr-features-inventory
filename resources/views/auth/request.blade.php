<div id="request-section"
     class="tab-section animate-fade-in request-section-hidden"
     data-track-url="{{ route('kiosk.request.track') }}">

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

          @if($errors->any())
            <div class="alert alert-danger rounded-3 alert-dismissible mb-3 py-2 request-alert-danger">
              <i class="fas fa-exclamation-circle me-2"></i>
              @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
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

            <div class="row">
              <div class="col-md-8 mb-3">
                <label for="item_id" class="form-label">Item Needed <span class="request-required">*</span></label>
                <select class="form-select @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                  <option value="" disabled selected>Select an item...</option>
                  @foreach($request_items ?? [] as $item)
                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                      {{ $item->name }} ({{ $item->category }})
                    </option>
                  @endforeach
                </select>
                @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-4 mb-3">
                <label for="requested_quantity" class="form-label">Quantity <span class="request-required">*</span></label>
                <input type="number" class="form-control @error('requested_quantity') is-invalid @enderror"
                  id="requested_quantity" name="requested_quantity"
                  value="{{ old('requested_quantity', 1) }}" min="1" required>
                @error('requested_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
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

    // Wait for initAuthTabs() in script.js to finish setting up tabs
    setTimeout(function () {
      // 1. Show the #request section and activate the nav link
      document.querySelectorAll('.tab-section').forEach(function (s) {
        s.style.display = s.id === 'request-section' ? 'block' : 'none';
      });
      document.querySelectorAll('.navbar-nav .nav-link').forEach(function (l) {
        l.classList.toggle('active', l.dataset.target === 'request');
      });

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
    }, 250);
  });
</script>
