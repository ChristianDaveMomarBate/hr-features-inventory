{{-- Kiosk Section – Stock Out --}}
<div id="kiosk-section" class="tab-section animate-fade-in" style="display: none;" data-beep-url="{{ asset('sound/beeps.mp3') }}" data-thanks-url="{{ asset('sound/thanks.mp3') }}">
  <div class="kiosk-wrapper">

    {{-- Header --}}
    <div class="kiosk-hero text-center position-relative">
      <button type="button" class="kiosk-fullscreen-btn" onclick="toggleKioskFullscreen()" title="Toggle Fullscreen">
        <i class="fas fa-expand"></i>
      </button>
      
      <div class="kiosk-hero-icon-wrap">
        <i class="fas fa-box-open"></i>
      </div>
      <h1 class="kiosk-hero-title">PHRMDO Inventory System</h1>
      <p class="kiosk-hero-sub">Select items, set quantities, enter your name and division, then submit your stock-out.</p>
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
        <button type="button" class="kiosk-receipt-close" onclick="document.getElementById('kioskReceiptModal').remove()" aria-label="Close receipt">
          <i class="fas fa-times"></i>
        </button>
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
            <div class="kiosk-card"
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
              <div class="kiosk-card-stock {{ $item->stock <= $item->minimum ? 'low' : '' }}">
                <span class="kiosk-stock-dot {{ $item->stock <= $item->minimum ? 'low' : '' }}"></span>
                {{ $item->display_stock }} available
              </div>
              @if($item->bulk_equivalent)
                <div class="kiosk-card-bulk">{{ $item->bulk_equivalent }}</div>
              @endif
              <button type="button" class="kiosk-add-btn" onclick="kioskAdd(this)" ontouchend="this.click(); event.preventDefault();">
                <i class="fas fa-plus"></i> Add
              </button>
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
</div>

