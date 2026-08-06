@php
    $currentUser = auth()->user();
    $validDashboardPages = [
        'inventory-registry',
        'stock-management',
        'item-requests',
        'analytics',
        'audit-trails',
        'reports'
    ];
    $lastSegment = request()->segment(count(request()->segments()));
    $activePageId = in_array($lastSegment, $validDashboardPages) ? $lastSegment : 'dashboard';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHRMDO StockWise</title>
    @include('style.style')
    <link href="{{ asset('vendor/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-hri.png') }}">
    <link href="{{ asset('vendor/@fontsource/inter/index.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tom-select/dist/css/tom-select.bootstrap5.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/chart.js/dist/chart.umd.js') }}"></script>
    <link href="{{ asset('design/dashboardstyle/index.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="dashboard-mobile-bar">
    <button type="button" class="dashboard-menu-btn" data-action="toggle-sidebar" aria-label="Open navigation">
      <i class="bi bi-list"></i>
    </button>
    <div class="dashboard-mobile-title"> StockWise</div>
  </div>
  <div class="dashboard-container">
    @include('InventoryDashboard.sidebar')
    <div class="main-content">
      <div id="dashboard" class="page {{ $activePageId === 'dashboard' ? 'active-page' : '' }}">
          <div class="dashboard-main-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="dashboard-title mb-0">
                    <span class="dashboard-title-badge">StockWise Dashboard</span>
                </h1>
              @include('InventoryDashboard.navbar')
          </div>
            @php
            $metrics = [
                ['title' => 'Total Items',    'id' => 'totalItems',    'icon' => 'bi-box-seam',          'color' => 'primary'],
                ['title' => 'Current Stock',  'id' => 'currentStock',  'icon' => 'bi-boxes',             'color' => 'success'],
                ['title' => 'Stock In',       'id' => 'totalStockIn',  'icon' => 'bi-box-arrow-in-down', 'color' => 'info'],
                ['title' => 'Stock Out',      'id' => 'totalStockOut', 'icon' => 'bi-box-arrow-up',      'color' => 'warning'],
                ['title' => 'Low Stock',      'id' => 'lowStockItems', 'icon' => 'bi-exclamation-triangle','color' => 'danger'],
            ];
            @endphp

            <div class="row g-4 mb-4">
                @foreach($metrics as $metric)
                    <div class="col-12 col-sm-6 col-xl">
                        <div class="metric-card-flat">
                            <div class="metric-content">
                                <div>
                                    <span class="metric-label">{{ $metric['title'] }}</span>
                                    <h2 id="{{ $metric['id'] }}">0</h2>
                                </div>

                                <div class="metric-icon bg-{{ $metric['color'] }}-soft text-{{ $metric['color'] }}">
                                    <i class="bi {{ $metric['icon'] }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

          <div class="dashboard-fit-grid">
              <div class="chart-card dashboard-trend-card d-flex flex-column">
                  <div class="dashboard-card-heading">
                      <h5 class="fw-bold  mb-1">Stock In vs Stock Out Trends</h5>
                      <p class="text-muted small mb-0">Historical overview</p>
                  </div>
                  <div class="position-relative flex-grow-1 chart-area-trend">
                      <canvas id="trendChart"></canvas>
                  </div>
              </div>

              <div class="chart-card dashboard-distribution-card d-flex flex-column align-items-center">
                  <div class="dashboard-card-heading w-100 text-start">
                      <h5 class="fw-bold  mb-1">Inventory Distribution</h5>
                      <p class="text-muted small mb-0">Current Stock vs Stock Out</p>
                  </div>
                  <div class="position-relative w-100 flex-grow-1 d-flex align-items-center justify-content-center chart-area-donut">
                      <canvas id="distributionChart"></canvas>
                      <div class="donut-overlay position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center">
                          <span class="donut-total fs-2 fw-black text-dark" id="donutTotal">0</span>
                          <span class="donut-caption text-muted">Total Logged</span>
                      </div>
                  </div>
                  <div class="dashboard-chart-legend w-100 d-flex justify-content-center gap-4 small">
                      <div class="d-flex align-items-center gap-2">
                          <span class="legend-dot legend-dot-current rounded-circle"></span>
                          <span class="fw-medium text-secondary">Current (<span id="donutCurrent">0</span>)</span>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                          <span class="legend-dot legend-dot-out rounded-circle"></span>
                          <span class="fw-medium text-secondary">Out (<span id="donutOut">0</span>)</span>
                      </div>
                  </div>
              </div>

              <div class="chart-card p-0 overflow-hidden dashboard-low-stock-card d-flex flex-column">
                  <div class="dashboard-section-header border-bottom border-light bg-white d-flex justify-content-between align-items-center">
                      <h5 class="fw-bold  mb-0">Low Stock Alerts</h5>
                      <span class="badge bg-danger">{{ $lowStockAlertItems->count() }}</span>
                  </div>
                  <div class="list-group list-group-flush dashboard-scroll-list">
                      @forelse($lowStockAlertItems->take(5) as $item)
                          <div class="list-group-item px-4 py-3">
                              <div class="d-flex justify-content-between gap-3">
                                  <div>
                                      <div class="fw-semibold text-dark">{{ $item->name }}</div>
                                      <small class="text-muted">{{ $item->code }} / {{ $item->category }}</small>
                                  </div>
                                  <span class="badge bg-danger align-self-start">{{ $item->display_stock }} / {{ number_format($item->minimum) }} {{ $item->display_unit }}</span>
                              </div>
                          </div>
                      @empty
                          <div class="p-4 text-center text-muted">No low stock items.</div>
                      @endforelse
                  </div>
              </div>

              <div class="chart-card p-0 overflow-hidden dashboard-audit-card d-flex flex-column">
                  <div class="dashboard-section-header border-bottom d-flex justify-content-between align-items-end" style="border-color:#f1f5f9!important;">
                      <div>
                          <h5 class="fw-bold  mb-1">Recent Audit Actions</h5>
                          <p class="text-muted small mb-0">Live operational ledger logs</p>
                      </div>
                      <a href="#" class="fw-semibold text-decoration-none small text-teal-600" data-page-link="audit-trails">View All</a>
                  </div>
                  <div class="table-responsive dashboard-scroll-list">
                      <table class="table table-hover table-modern mb-0 border-0">
                          <thead>
                              <tr>
                                  <th class="ps-4 py-3 border-0">User</th>
                                  <th class="py-3 border-0">Action</th>
                                  <th class="py-3 border-0">Reference Item</th>
                                  <th class="text-end pe-4 py-3 border-0">Timestamp</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($auditTrails->take(5) as $log)
                              @php
                                  $kioskUserName = null;
                                  if (!$log->user && strtolower($log->module) === 'kiosk' && $log->remarks && str_contains($log->remarks, 'Kiosk request by:')) {
                                      $extracted = str_replace('Kiosk request by:', '', $log->remarks);
                                      $parts = explode(' - ', trim($extracted));
                                      $kioskUserName = $parts[0] ?? trim($extracted);
                                  }
                              @endphp
                              <tr>
                                  <td class="ps-4 py-3">
                                      @if($log->user)
                                      <div class="d-flex align-items-center gap-3">
                                          <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:12px;">
                                              {{ substr($log->user->name, 0, 1) }}
                                          </div>
                                          <div>
                                              <p class="fw-semibold text-dark mb-0">{{ $log->user->name }}</p>
                                              <p class="text-muted mb-0" style="font-size:11px;">{{ ucfirst($log->user->role) }}</p>
                                          </div>
                                      </div>
                                      @elseif($kioskUserName)
                                      <div class="d-flex align-items-center gap-3">
                                          <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:12px;">
                                              {{ strtoupper(substr($kioskUserName, 0, 1)) }}
                                          </div>
                                          <div>
                                              <p class="fw-semibold text-dark mb-0">{{ $kioskUserName }} <span class="badge bg-light text-dark border ms-1" style="font-size: 9px;">Kiosk</span></p>
                                              <p class="text-muted mb-0" style="font-size:11px;">Kiosk User</p>
                                          </div>
                                      </div>
                                      @else
                                      <div class="d-flex align-items-center gap-3">
                                          <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:12px;">
                                              ?
                                          </div>
                                          <div>
                                              <p class="fw-semibold text-dark mb-0">System</p>
                                              <p class="text-muted mb-0" style="font-size:11px;">System</p>
                                          </div>
                                      </div>
                                      @endif
                                  </td>
                                  <td class="py-3">
                                      @if(str_contains(strtolower($log->action), 'in'))
                                          <span class="badge bg-emerald-50 text-emerald-500 border border-success border-opacity-25 px-2 py-1">
                                              <span style="color:black;"><i class="bi bi-arrow-down-right"></i>  {{ $log->action }} </span>
                                          </span>
                                      @elseif(str_contains(strtolower($log->action), 'out'))
                                          <span class="badge bg-amber-50 text-amber-500 border border-warning border-opacity-25 px-2 py-1">
                                              <i class="bi bi-arrow-up-right"></i> {{ $log->action }}
                                          </span>
                                      @else
                                          <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                              <i class="bi bi-record-circle"></i> {{ $log->action }}
                                          </span>
                                      @endif
                                  </td>
                                  <td class="py-3 fw-medium text-secondary">
                                      {{ $log->item_reference }}
                                  </td>
                                  <td class="py-3 text-end pe-4 text-muted" style="font-size:12px;">
                                      {{ $log->created_at->diffForHumans() }}
                                  </td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>

    @include('InventoryDashboard.inventoryRegistry')
    @include('InventoryDashboard.stockManagement')
    @include('InventoryDashboard.itemRequests')
    @include('InventoryDashboard.analytics')
    @include('InventoryDashboard.auditTrails')
    @include('InventoryDashboard.reports')
    
    </div>
  </div>

  <script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/tom-select/dist/js/tom-select.complete.min.js') }}"></script>
  <script id="valid-dashboard-pages-data" type="application/json">@json($validDashboardPages)</script>

  <!-- Global Toast Notification -->
  <div id="toastContainer" style="position:fixed;top:24px;right:24px;z-index:9999;min-width:320px;"></div>

  @if(session('success'))
  <div id="flashSuccess" data-message="{{ session('success') }}" hidden></div>
  @endif

  @if(session('error'))
  <div id="flashError" data-message="{{ session('error') }}" hidden></div>
  @endif

  @vite('resources/js/inventory/script.js')

  @include('InventoryDashboard.accountModals')

  @if(session('login_success'))
      <script>
          document.addEventListener('DOMContentLoaded', function () {
              const loginAudio = new Audio('{{ asset("sound/logging in.mp3") }}');
              loginAudio.currentTime = 0;
              loginAudio.play().catch(function(e) {
                  console.log("Audio play failed:", e);
              });
          });
      </script>
  @endif


</body>
</html>

