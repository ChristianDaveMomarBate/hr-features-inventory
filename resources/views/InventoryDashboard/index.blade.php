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

  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow">
              <div class="modal-header border-bottom border-light">
                  <h5 class="modal-title fw-bold" id="logoutModalLabel">Confirm Logout</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4">
                  <div class="d-flex align-items-start gap-3">
                      <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;">
                          <i class="bi bi-box-arrow-right fs-5"></i>
                      </div>
                      <div>
                          <h6 class="fw-bold text-dark mb-1">Are you sure you want to logout?</h6>
                          <p class="text-muted mb-0 small">You will need to sign in again to access the inventory dashboard.</p>
                      </div>
                  </div>
              </div>
              <div class="modal-footer border-top border-light bg-light">
                  <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-danger px-4" data-action="submit-logout">Logout</button>
              </div>
          </div>
      </div>
  </div>

  <!-- Edit Profile Modal -->
  <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
          <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="modal-content border-0 shadow">
              <div class="modal-header border-bottom border-light">
                  <h5 class="modal-title fw-bold">Edit Profile</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              @csrf
                  <div class="modal-body p-4">
                      @if($errors->profile->any())
                          <div class="alert alert-danger py-2 small">
                              Please check the highlighted profile fields.
                          </div>
                      @endif

                      <div class="mb-4 text-center">
                          <img src="{{ $currentUser->profile_picture ? asset('storage/' . $currentUser->profile_picture) : asset('images/default-avatar.png') }}" alt="Current Profile" class="rounded-circle border border-3 border-light shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                          <div>
                              <label for="profile_picture" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                  <i class="bi bi-camera me-1"></i> Change Picture
                              </label>
                              <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="if(this.files[0]) { let reader = new FileReader(); reader.onload = function(e) { document.querySelector('#profileModal img').src = e.target.result; }; reader.readAsDataURL(this.files[0]); }">
                          </div>
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label fw-bold text-secondary small">Name</label>
                          <input type="text" name="name" class="form-control rounded-3 @error('name', 'profile') is-invalid @enderror" value="{{ old('name', $currentUser->name) }}" required>
                          @error('name', 'profile')
                              <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                      </div>
                      <div class="mb-3">
                          <label class="form-label fw-bold text-secondary small">Email</label>
                          <input type="email" class="form-control rounded-3 bg-light" value="{{ $currentUser->email }}" readonly disabled>
                          <div class="form-text">Email cannot be changed here.</div>
                      </div>
                      <div class="border-top pt-3 mt-4">
                          <h6 class="fw-bold text-dark mb-3">Change Password</h6>
                          <div class="mb-3">
                              <label class="form-label fw-bold text-secondary small">Current Password</label>
                              <input type="password" name="current_password" class="form-control rounded-3 @error('current_password', 'profile') is-invalid @enderror" autocomplete="current-password">
                              @error('current_password', 'profile')
                                  <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                          </div>
                          <div class="mb-3">
                              <label class="form-label fw-bold text-secondary small">New Password</label>
                              <input type="password" name="password" class="form-control rounded-3 @error('password', 'profile') is-invalid @enderror" autocomplete="new-password">
                              @error('password', 'profile')
                                  <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                          </div>
                          <div class="mb-0">
                              <label class="form-label fw-bold text-secondary small">Confirm New Password</label>
                              <input type="password" name="password_confirmation" class="form-control rounded-3" autocomplete="new-password">
                              <div class="form-text">Leave password fields blank to keep your current password.</div>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer border-top border-light bg-light">
                      <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                  </div>
              </form>
      </div>
  </div>

  @if($errors->profile->any())
      <script>
          document.addEventListener('DOMContentLoaded', function () {
              const profileModal = document.getElementById('profileModal');

              if (profileModal && window.bootstrap) {
                  bootstrap.Modal.getOrCreateInstance(profileModal).show();
              }
          });
      </script>
  @endif

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

