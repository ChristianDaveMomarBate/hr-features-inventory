@php
    $validDashboardPages = ['inventory-registry'];

    if (auth()->user()->isAdmin() || auth()->user()->isStaff()) {
        $validDashboardPages[] = 'stock-management';
    }

    if (auth()->user()->isAdmin() || auth()->user()->isViewer()) {
        $validDashboardPages[] = 'analytics';
    }

    if (auth()->user()->isAdmin() || auth()->user()->isStaff()) {
        $validDashboardPages[] = 'audit-trails';
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHRMDO INVENTORY SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-hri.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('style.style')
</head>
<body>
  <div class="dashboard-container">
    @include('InventoryDashboard.sidebar')
    <div class="main-content">
      <div id="dashboard" class="page active-page">
          <div class="d-flex justify-content-between align-items-center mb-4">
              <h1 class="mb-0 fw-bold" style="font-size: 32px; color: #111827;">Dashboard</h1>
              <div class="d-flex align-items-center gap-3">
                  <div class="dropdown">
                      <button class="btn btn-light position-relative rounded-circle shadow-sm border border-light d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:44px;height:44px;">
                          <i class="bi bi-bell fs-5 text-secondary"></i>
                          @if(($unreadLowStockCount ?? 0) > 0)
                              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                  {{ $unreadLowStockCount }}
                              </span>
                          @endif
                      </button>
                      <div class="dropdown-menu dropdown-menu-end p-0 shadow border-0" style="width:360px; max-height:420px; overflow-y:auto;">
                          <div class="px-3 py-3 border-bottom bg-white">
                              <h6 class="mb-0 fw-bold">Low Stock Alerts</h6>
                              <small class="text-muted">{{ ($unreadLowStockCount ?? 0) }} unread notification(s)</small>
                          </div>

                          @forelse(($lowStockAlertItems ?? collect()) as $alertItem)
                              @php
                                  $matchingNotification = ($lowStockNotifications ?? collect())->first(function ($notification) use ($alertItem) {
                                      return ($notification->data['inventory_item_id'] ?? null) == $alertItem->id;
                                  });
                              @endphp

                              @if($matchingNotification)
                                  <form method="POST" action="{{ route('notifications.read', $matchingNotification->id) }}">
                                      @csrf
                                      <input type="hidden" name="page" value="dashboard">
                                      <button type="submit" class="dropdown-item text-wrap py-3 border-bottom">
                                          <div class="d-flex justify-content-between gap-3">
                                              <div>
                                                  <div class="fw-semibold text-dark">{{ $alertItem->name }}</div>
                                                  <small class="text-muted">{{ $alertItem->code }}</small>
                                              </div>
                                              <span class="badge bg-danger align-self-start">{{ $alertItem->stock }} / {{ $alertItem->minimum }}</span>
                                          </div>
                                      </button>
                                  </form>
                              @else
                                  <div class="px-3 py-3 border-bottom">
                                      <div class="d-flex justify-content-between gap-3">
                                          <div>
                                              <div class="fw-semibold text-dark">{{ $alertItem->name }}</div>
                                              <small class="text-muted">{{ $alertItem->code }}</small>
                                          </div>
                                          <span class="badge bg-secondary align-self-start">{{ $alertItem->stock }} / {{ $alertItem->minimum }}</span>
                                      </div>
                                  </div>
                              @endif
                          @empty
                              <div class="px-3 py-4 text-center text-muted">
                                  No low stock items.
                              </div>
                          @endforelse
                      </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border border-light">
                      <span class="position-relative d-flex" style="width:10px;height:10px;">
                          <span class="position-absolute w-100 h-100 rounded-circle bg-success opacity-75" style="animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                          <span class="position-relative w-100 h-100 rounded-circle bg-success"></span>
                      </span>
                      <span class="fw-medium text-secondary" id="currentDate" style="font-size:14px;"></span>
                  </div>
              </div>
          </div>



          <!-- Top Row: Role-aware Cards -->
          <div class="row row-cols-1 row-cols-md-3 row-cols-xl-5 g-4 mb-4">
              @if(auth()->user()->isAdmin() || auth()->user()->isViewer())
              <div class="col">
                  <div class="metric-card-modern">
                      <div class="d-flex justify-content-between align-items-start">
                          <div>
                              <p class="metric-title">Total Items</p>
                              <h3 class="metric-value" id="totalItems">0</h3>
                          </div>
                          <div class="metric-icon-modern text-primary bg-primary bg-opacity-10">
                              <i class="bi bi-box-seam"></i>
                          </div>
                      </div>
                  </div>
              </div>
              @endif
              <div class="col">
                  <div class="metric-card-modern">
                      <div class="d-flex justify-content-between align-items-start">
                          <div>
                              <p class="metric-title">Current Stock</p>
                              <h3 class="metric-value" id="currentStock">0</h3>
                          </div>
                          <div class="metric-icon-modern text-teal-600 bg-teal-50">
                              <i class="bi bi-boxes"></i>
                          </div>
                      </div>
                  </div>
              </div>
              @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
              <div class="col">
                  <div class="metric-card-modern">
                      <div class="d-flex justify-content-between align-items-start">
                          <div>
                              <p class="metric-title">Total Stock In</p>
                              <h3 class="metric-value" id="totalStockIn">0</h3>
                          </div>
                          <div class="metric-icon-modern text-emerald-500 bg-emerald-50">
                              <i class="bi bi-box-arrow-in-down"></i>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col">
                  <div class="metric-card-modern">
                      <div class="d-flex justify-content-between align-items-start">
                          <div>
                              <p class="metric-title">Total Stock Out</p>
                              <h3 class="metric-value" id="totalStockOut">0</h3>
                          </div>
                          <div class="metric-icon-modern text-amber-500 bg-amber-50">
                              <i class="bi bi-box-arrow-up"></i>
                          </div>
                      </div>
                  </div>
              </div>
              @endif
              @if(auth()->user()->isAdmin())
              <div class="col">
                  <div class="metric-card-modern">
                      <div class="d-flex justify-content-between align-items-start">
                          <div>
                              <p class="metric-title">Low Stock Items</p>
                              <h3 class="metric-value" id="lowStockItems">0</h3>
                          </div>
                          <div class="metric-icon-modern text-success bg-success bg-opacity-10">
                              <i class="bi bi-check-circle-fill"></i>
                          </div>
                      </div>
                  </div>
              </div>
              @endif
          </div>

          <!-- Middle Row: Charts -->
          <div class="row g-4 mb-4">
              <div class="col-lg-8">
                  <div class="chart-card h-100 d-flex flex-column">
                      <div class="mb-3">
                          <h5 class="fw-bold text-dark mb-1">Stock In vs Stock Out Trends</h5>
                          <p class="text-muted small mb-0">Historical overview</p>
                      </div>
                      <div class="position-relative flex-grow-1" style="min-height: 280px;">
                          <canvas id="trendChart"></canvas>
                      </div>
                  </div>
              </div>
              <div class="col-lg-4">
                  <div class="chart-card h-100 d-flex flex-column align-items-center">
                      <div class="w-100 mb-3 text-start">
                          <h5 class="fw-bold text-dark mb-1">Inventory Distribution</h5>
                          <p class="text-muted small mb-0">Current Stock vs Stock Out</p>
                      </div>
                      <div class="position-relative w-100 flex-grow-1 d-flex align-items-center justify-content-center" style="max-width:220px; min-height: 200px;">
                          <canvas id="distributionChart"></canvas>
                          <div class="position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center" style="pointer-events:none;">
                              <span class="fs-2 fw-black text-dark" style="font-weight:900;" id="donutTotal">0</span>
                              <span class="text-muted" style="font-size:10px;font-weight:600;text-transform:uppercase;">Total Logged</span>
                          </div>
                      </div>
                      <div class="w-100 mt-4 d-flex justify-content-center gap-4 small">
                          <div class="d-flex align-items-center gap-2">
                              <span class="rounded-circle" style="background-color:#14b8a6; width:12px;height:12px;"></span>
                              <span class="fw-medium text-secondary">Current (<span id="donutCurrent">0</span>)</span>
                          </div>
                          <div class="d-flex align-items-center gap-2">
                              <span class="rounded-circle" style="background-color:#fbbf24; width:12px;height:12px;"></span>
                              <span class="fw-medium text-secondary">Out (<span id="donutOut">0</span>)</span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          @if(!auth()->user()->isViewer())
          <!-- Bottom Row: Recent Activity Log -->
          <div class="chart-card p-0 overflow-hidden mb-4">
              <div class="p-4 border-bottom d-flex justify-content-between align-items-end" style="border-color:#f1f5f9!important;">
                  <div>
                      <h5 class="fw-bold text-dark mb-1">Recent Audit Actions</h5>
                      <p class="text-muted small mb-0">Live operational ledger logs</p>
                  </div>
                  <a href="#" class="fw-semibold text-decoration-none small text-teal-600" onclick="showPage('audit-trails'); return false;">View All</a>
              </div>
              <div class="table-responsive">
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
                          <tr>
                              <td class="ps-4 py-3">
                                  <div class="d-flex align-items-center gap-3">
                                      <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:12px;">
                                          {{ substr($log->user->name ?? '?', 0, 1) }}
                                      </div>
                                      <div>
                                          <p class="fw-semibold text-dark mb-0">{{ $log->user->name ?? 'System' }}</p>
                                          <p class="text-muted mb-0" style="font-size:11px;">{{ ucfirst($log->user->role ?? 'system') }}</p>
                                      </div>
                                  </div>
                              </td>
                              <td class="py-3">
                                  @if(str_contains(strtolower($log->action), 'in'))
                                      <span class="badge bg-emerald-50 text-emerald-500 border border-success border-opacity-25 px-2 py-1">
                                          <i class="bi bi-arrow-down-right"></i> {{ $log->action }}
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
          @endif
      </div>



    @include('InventoryDashboard.inventoryRegistry')
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        @include('InventoryDashboard.stockManagement')
    @endif
    @if(auth()->user()->isAdmin() || auth()->user()->isViewer())
        @include('InventoryDashboard.analytics')
    @endif
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        @include('InventoryDashboard.auditTrails')
    @endif
    
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <script>
    function showPage(pageId, clickedItem) {
      document.querySelectorAll('.page').forEach(function (page) {
        page.classList.remove('active-page');
      });

      document.querySelectorAll('.sidebar li').forEach(function (item) {
        item.classList.remove('active');
      });

      document.getElementById(pageId).classList.add('active-page');
      if (clickedItem) {
          clickedItem.classList.add('active');
      }
      
      // Update URL
      if (pageId === 'dashboard') {
          history.pushState(null, '', '/dashboard');
      } else {
          history.pushState(null, '', '/dashboard/' + pageId);
      }
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Load the correct tab based on the URL
        const pathSegments = window.location.pathname.split('/');
        const pageId = pathSegments[pathSegments.length - 1];
        const validPages = @json($validDashboardPages);
        
        if (validPages.includes(pageId)) {
            const sidebarItem = document.querySelector(`.sidebar li[onclick*="${pageId}"]`);
            if (sidebarItem) {
                showPage(pageId, sidebarItem);
            }
        }
    });

    // Set dynamic current date
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = new Date().toLocaleDateString('en-US', dateOptions);

    // Make charts globally accessible so inventoryRegistry.blade.php can update them
    let trendChartInstance = null;
    let distChartInstance = null;

    function initOrUpdateCharts(stockIn, stockOut, currentStock) {
        // Chart.js Configuration Defaults
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b'; // slate-500
        Chart.defaults.scale.grid.color = '#f1f5f9'; // slate-100

        // 1. Line Chart (Stock In vs Stock Out)
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        
        if (!trendChartInstance) {
            const gradientIn = ctxTrend.createLinearGradient(0, 0, 0, 300);
            gradientIn.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            gradientIn.addColorStop(1, 'rgba(16, 185, 129, 0)');

            const gradientOut = ctxTrend.createLinearGradient(0, 0, 0, 300);
            gradientOut.addColorStop(0, 'rgba(245, 158, 11, 0.2)');
            gradientOut.addColorStop(1, 'rgba(245, 158, 11, 0)');

            trendChartInstance = new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Stock In',
                            data: [0, 0, 0, 0, 0, stockIn || 0],
                            borderColor: '#10b981',
                            backgroundColor: gradientIn,
                            borderWidth: 2,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#10b981',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Stock Out',
                            data: [0, 0, 0, 0, 0, stockOut || 0],
                            borderColor: '#f59e0b',
                            backgroundColor: gradientOut,
                            borderWidth: 2,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#f59e0b',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { size: 13, family: "'Inter', sans-serif" },
                            bodyFont: { size: 13, family: "'Inter', sans-serif" },
                            padding: 10,
                            cornerRadius: 8,
                            boxPadding: 4
                        }
                    },
                    scales: {
                        x: { grid: { display: false, drawBorder: false } },
                        y: { beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9' } }
                    },
                    interaction: { mode: 'nearest', axis: 'x', intersect: false }
                }
            });
        } else {
            trendChartInstance.data.datasets[0].data[5] = stockIn;
            trendChartInstance.data.datasets[1].data[5] = stockOut;
            trendChartInstance.update();
        }

        // 2. Doughnut Chart (Inventory Distribution)
        const ctxDist = document.getElementById('distributionChart').getContext('2d');
        if (!distChartInstance) {
            distChartInstance = new Chart(ctxDist, {
                type: 'doughnut',
                data: {
                    labels: ['Current Stock', 'Stock Out'],
                    datasets: [{
                        data: [currentStock || 0, stockOut || 0],
                        backgroundColor: ['#14b8a6', '#fbbf24'],
                        hoverBackgroundColor: ['#0d9488', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 10,
                            cornerRadius: 8,
                            boxPadding: 4,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed !== null) label += context.parsed + ' units';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            distChartInstance.data.datasets[0].data = [currentStock, stockOut];
            distChartInstance.update();
        }

        document.getElementById('donutTotal').textContent = currentStock + stockOut;
        document.getElementById('donutCurrent').textContent = currentStock;
        document.getElementById('donutOut').textContent = stockOut;
    }
  </script>
</body>
</html>
