{{-- Kiosk Section – Stock Out --}}
<div id="kiosk-section" class="tab-section animate-fade-in" style="display: none;" data-beep-url="{{ asset('sound/beeps.mp3') }}" data-thanks-url="{{ asset('sound/thanks.mp3') }}">
  <div class="kiosk-wrapper">

    {{-- Header --}}
    <div class="kiosk-hero text-center position-relative">
      <div class="kiosk-header-actions" style="position: absolute; top: 20px; right: 20px; display: flex; gap: 10px; z-index: 10;">
        <button type="button" onclick="showKioskTrackModal()" class="kiosk-track-btn" title="Track Request" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 8px 16px; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500; transition: all 0.2s; backdrop-filter: blur(5px);">
          <i class="fas fa-search"></i> <span class="d-none d-md-inline">Track Request</span>
        </button>
        <button type="button" class="kiosk-fullscreen-btn" onclick="toggleKioskFullscreen()" title="Toggle Fullscreen" style="position: static;">
          <i class="fas fa-expand"></i>
        </button>
      </div>
      
      <div class="kiosk-hero-icon-wrap">
        <i class="fas fa-box-open"></i>
      </div>
      <h1 class="kiosk-hero-title">PHRMDO Inventory System</h1>
      <p class="kiosk-hero-sub">Select items, set quantities, enter your name and division, then submit your stock-out.</p>

      {{-- Bible Verse in Hero --}}
      <div class="kiosk-verse-pill" id="kioskVersePill" title="Daily Bible Verse">
        <i class="fas fa-cross kiosk-verse-icon"></i>
        <span class="kiosk-verse-text" id="kioskVerseText">Loading verse...</span>
      </div>

      <div class="kiosk-hero-fullscreen-brand" aria-label="PHRMDO Surigao City Surigao del Norte">
        <img src="{{ asset('images/logo-hri.png') }}" alt="PHRMDO Logo" class="kiosk-hero-fullscreen-logo">
        <div class="kiosk-hero-fullscreen-brand-text">
          <span>PHRMDO</span>
          <span>SURIGAO CITY</span>
          <span>SURIGAO DEL NORTE</span>
        </div>
      </div>
    </div>

    {{-- Receipt --}}
    @php($receipt = session('kiosk_receipt'))
    @if($receipt)
    <div class="kiosk-receipt-overlay" id="kioskReceiptModal" role="dialog" aria-modal="true" aria-labelledby="kioskReceiptTitle">
      <div class="kiosk-receipt-card">
        <div style="position: absolute; top: 20px; right: 20px; display: flex; gap: 8px;">
          <button type="button" class="kiosk-receipt-action-btn print-btn" onclick="window.print()" aria-label="Print receipt" title="Print Receipt" style="background: var(--k-accent); color: white; border: none; border-radius: 8px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;">
            <i class="fas fa-print"></i>
          </button>
          <button type="button" class="kiosk-receipt-action-btn close-btn" onclick="document.getElementById('kioskReceiptModal').remove()" aria-label="Close receipt" title="Close" style="background: #f1f5f9; color: #64748b; border: none; border-radius: 8px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="kiosk-receipt-head">
          <div class="kiosk-receipt-check"><i class="fas fa-check"></i></div>
          <div>
            <div class="kiosk-receipt-title" id="kioskReceiptTitle">Stock Out Receipt</div>
            <div class="kiosk-receipt-sub">Submitted successfully</div>
          </div>
        </div>
        <div class="kiosk-receipt-meta">
          <div><span>Receipt No.</span><strong>{{ $receipt['number'] }}</strong></div>
          <div><span>Date</span><strong>{{ $receipt['submitted_at'] }}</strong></div>
          <div><span>Name</span><strong>{{ $receipt['requester_name'] }}</strong></div>
          <div><span>Division</span><strong>{{ $receipt['division'] }}</strong></div>
        </div>
        <div class="kiosk-receipt-items">
          @foreach($receipt['items'] as $receiptItem)
            <div class="kiosk-receipt-item">
              <span>{{ $receiptItem['name'] }}</span>
              <strong>{{ $receiptItem['quantity'] }} {{ $receiptItem['unit'] }}</strong>
            </div>
          @endforeach
        </div>
        <div class="kiosk-receipt-total">
          <span>Total Quantity</span>
          <strong>{{ $receipt['total_quantity'] }}</strong>
        </div>
      </div>
    </div>
    @endif

    {{-- Errors --}}
    @if(session('kiosk_errors'))
    <div class="kiosk-alert-danger">
      <div class="kiosk-alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
      <div class="kiosk-alert-body">
        <div class="kiosk-alert-title">Some items could not be processed:</div>
        <ul class="kiosk-error-list">
          @foreach(session('kiosk_errors') as $err)
            <li>{!! $err !!}</li>
          @endforeach
        </ul>
      </div>
    </div>
    @endif

    <form method="POST" action="{{ route('kiosk.store') }}" id="kioskForm">
      @csrf
      <div class="kiosk-layout">

        {{-- LEFT: Items --}}
        <div class="kiosk-left">

          {{-- Toolbar --}}
          <div class="kiosk-toolbar">
            <div class="kiosk-search-wrap">
              <i class="fas fa-search kiosk-search-ico"></i>
              <input type="text" id="kioskSearch" class="kiosk-search" placeholder="Search items…" autocomplete="off">
            </div>
            <select id="kioskCatFilter" class="kiosk-cat-filter">
              <option value="">All Categories</option>
              @foreach($items->pluck('category')->unique()->sort() as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
              @endforeach
            </select>
          </div>

          {{-- Grid --}}
          <div class="kiosk-grid" id="kioskGrid">
            @forelse($items as $item)
            <div class="kiosk-card" {!! $item->stock <= 0 ? 'style="opacity:0.6;"' : '' !!}
                 data-id="{{ $item->id }}"
                 data-name="{{ $item->name }}"
                 data-category="{{ $item->category }}"
                 data-unit="{{ $item->display_unit }}"
                 data-stock="{{ $item->stock }}"
                 data-stock-unit="{{ $item->stock_unit ?? $item->unit }}"
                 data-units-per-stock-unit="{{ $item->units_per_stock_unit ?? 1 }}"
                 data-bulk-equivalent="{{ $item->bulk_equivalent }}"
                 tabindex="0" role="button"
                 aria-label="Add {{ $item->name }}">
              <div class="kiosk-card-unit-badge">{{ $item->display_unit }}</div>
              <div class="kiosk-card-ico">
                @if(str_contains(strtolower($item->category), 'it') || str_contains(strtolower($item->category), 'device'))
                  <i class="fas fa-laptop"></i>
                @elseif(str_contains(strtolower($item->category), 'furniture'))
                  <i class="fas fa-chair"></i>
                @elseif(str_contains(strtolower($item->category), 'record') || str_contains(strtolower($item->category), 'document') || str_contains(strtolower($item->category), 'form'))
                  <i class="fas fa-file-alt"></i>
                @elseif(str_contains(strtolower($item->category), 'security'))
                  <i class="fas fa-shield-alt"></i>
                @elseif(str_contains(strtolower($item->category), 'maintenance'))
                  <i class="fas fa-wrench"></i>
                @else
                  <i class="fas fa-box"></i>
                @endif
              </div>
              <div class="kiosk-card-name">{{ $item->name }}</div>
              <div class="kiosk-card-cat">{{ $item->category }}</div>
              <div class="kiosk-card-stock {{ $item->stock <= 0 ? 'text-danger' : ($item->stock <= $item->minimum ? 'low' : '') }}">
                @if($item->stock <= 0)
                  <span class="kiosk-stock-dot" style="background-color: #ef4444;"></span>
                  Not available
                @else
                  <span class="kiosk-stock-dot {{ $item->stock <= $item->minimum ? 'low' : '' }}"></span>
                  {{ $item->display_stock }} available
                @endif
              </div>
              @if($item->bulk_equivalent)
                <div class="kiosk-card-bulk">{{ $item->bulk_equivalent }}</div>
              @endif
              @if($item->stock <= 0)
                <button type="button" class="kiosk-add-btn disabled" style="background-color: #e2e8f0; color: #94a3b8; cursor: not-allowed;" disabled>
                  <i class="fas fa-times"></i> Out of Stock
                </button>
              @else
                <button type="button" class="kiosk-add-btn" onclick="kioskAdd(this)" ontouchend="this.click(); event.preventDefault();">
                  <i class="fas fa-plus"></i> Add
                </button>
              @endif
            </div>
            @empty
            <div class="kiosk-no-items">
              <i class="fas fa-box-open"></i>
              <p>No items currently in stock.</p>
            </div>
            @endforelse
          </div>
          
          {{-- Pagination Controls --}}
          <div class="kiosk-pagination mt-4 d-flex justify-content-between align-items-center" id="kioskPagination" style="display: none !important;">
            <button type="button" class="btn btn-light btn-sm" id="kioskPrevPage" onclick="kioskChangePage(-1)"><i class="fas fa-chevron-left me-1"></i> Prev</button>
            <span class="text-muted small" id="kioskPageInfo">Page 1 of 1</span>
            <button type="button" class="btn btn-light btn-sm" id="kioskNextPage" onclick="kioskChangePage(1)">Next <i class="fas fa-chevron-right ms-1"></i></button>
          </div>
        </div>

        {{-- RIGHT: Cart + Info --}}
        <div class="kiosk-right">

          {{-- Cart --}}
          <div class="kiosk-panel kiosk-cart-panel">
            <div class="kiosk-panel-head">
              <i class="fas fa-shopping-basket"></i> Stock Out List
              <span class="kiosk-badge" id="kioskCount">0</span>
            </div>
            <div class="kiosk-cart-body" id="kioskCartBody">
              <div class="kiosk-cart-empty" id="kioskCartEmpty">
                <i class="fas fa-inbox"></i>
                <p>No items selected.<br>Click an item to add.</p>
              </div>
            </div>
          </div>

          {{-- Requester Info --}}
          <div class="kiosk-panel kiosk-info-panel">
            <div class="kiosk-panel-head">
              <i class="fas fa-user-tag"></i> Employee Information
            </div>
            <div class="kiosk-panel-body">
              <div class="kiosk-field">
                <label class="kiosk-lbl" for="kioskName">
                  <i class="fas fa-user"></i> Full Name <span class="req">*</span>
                </label>
                <input type="text" id="kioskName" name="requester_name"
                  class="kiosk-input @error('requester_name') err @enderror"
                  placeholder="e.g. Juan Dela Cruz"
                  value="{{ old('requester_name') }}"
                  autocomplete="name" required>
                @error('requester_name')
                  <div class="kiosk-field-err">{{ $message }}</div>
                @enderror
              </div>
              <div class="kiosk-field">
                <label class="kiosk-lbl" for="kioskDiv">
                  <i class="fas fa-building"></i> Division <span class="req">*</span>
                </label>
                <select id="kioskDiv" name="division"
                  class="kiosk-select @error('division') err @enderror" required>
                  <option value="" disabled selected>Select your division…</option>
                  @foreach($divisions as $div)
                    <option value="{{ $div }}" {{ old('division') === $div ? 'selected' : '' }}>{{ $div }}</option>
                  @endforeach
                </select>
                @error('division')
                  <div class="kiosk-field-err">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          {{-- Submit --}}
          <button type="submit" class="kiosk-submit" id="kioskSubmit" disabled>
            <i class="fas fa-arrow-circle-down"></i> Submit Stock Out
          </button>
          <p class="kiosk-note">Verify your information before submitting.</p>
        </div>
      </div>
    </form>
    
    {{-- Ticker/Crawl (Visible only in Fullscreen) --}}
    <div class="kiosk-ticker">
      <div class="ticker-clock" id="tickerClock"></div>
      <div class="ticker-crawl">
        <div class="ticker-text-wrapper">
          <div class="ticker-text">
            TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span>
          </div>
          <div class="ticker-text">
            TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span> TATAK LYNDON TATAK LIG-ON! <span class="ticker-dot"></span>
          </div>
        </div>
      </div>
    </div>

    </div>
    
    {{-- Track Request Modal --}}
    <div class="kiosk-receipt-overlay" id="kioskTrackModal" style="display: none; align-items: center; justify-content: center;" role="dialog" aria-modal="true" aria-labelledby="kioskTrackTitle">
      <div class="kiosk-receipt-card" style="width: 100%; max-width: 600px; padding: 2rem;">
        <button type="button" class="kiosk-receipt-close" onclick="closeKioskTrackModal()" aria-label="Close">
          <i class="fas fa-times"></i>
        </button>
        <div class="kiosk-receipt-head mb-4">
          <div class="kiosk-receipt-check" style="background: var(--k-accent);"><i class="fas fa-search"></i></div>
          <div>
            <div class="kiosk-receipt-title" id="kioskTrackTitle">Track Request</div>
            <div class="kiosk-receipt-sub" style="color: #64748b;">Enter your Request ID or Name to check status</div>
          </div>
        </div>
        
        <form id="kioskTrackForm" onsubmit="event.preventDefault(); submitKioskTrack();">
          <div class="kiosk-field">
            <div style="display: flex; gap: 10px;">
              <input type="text" id="kiosk_track_id" class="kiosk-input" placeholder="e.g. 12 or John Doe" required style="flex: 1; border-radius: 8px;">
              <button type="submit" class="kiosk-submit" style="margin-top: 0; width: auto; border-radius: 8px; padding: 0 1.5rem;">
                <i class="fas fa-search"></i> Track
              </button>
            </div>
          </div>
        </form>
        
        <div id="kioskTrackResult" style="margin-top: 1.5rem;">
          <div style="text-align: center; color: #94a3b8; padding: 2rem 0;">
            <i class="fas fa-clipboard-list fa-2x mb-2" style="opacity: 0.5;"></i>
            <p style="margin: 0;">Status details will appear here.</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

