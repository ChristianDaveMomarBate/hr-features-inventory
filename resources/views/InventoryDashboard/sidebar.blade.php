<div class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo-hri.png') }}" alt="PHRMDO Logo">
        </div>
        <h2>PHRMDO INVENTORY SYSTEM</h2>
    </div>

  <ul>
    <li class="active" onclick="showPage('dashboard', this)">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Dashboard</span>
    </li>
    <li onclick="showPage('inventory-registry', this)">
        <i class="bi bi-clipboard-data-fill"></i>
        <span>Inventory Registry</span>
    </li>
    @if(!auth()->user()->isViewer())
    <li onclick="showPage('stock-management', this)">
        <i class="bi bi-arrow-left-right"></i>
        <span>Stock Management</span>
    </li>
    @endif
    @if(!auth()->user()->isStaff())
    <li onclick="showPage('analytics', this)">
        <i class="bi bi-bar-chart-fill"></i>
        <span>Analytics</span>
    </li>
    @endif
    @if(!auth()->user()->isViewer())
    <li onclick="showPage('audit-trails', this)">
        <i class="bi bi-clock-history"></i>
        <span>Audit Trails</span>
    </li>
    @endif
    @if(auth()->user()->isAdmin())
    <li onclick="window.location='{{ route('users.index') }}'">
        <i class="bi bi-people-fill"></i>
        <span>Users</span>
    </li>
    @endif
  </ul>

  <div class="sidebar-footer">
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
            <div class="sidebar-user-role">
                @php
                    $role = Auth::user()->role;
                    $roleClass = [
                        'admin' => 'role-badge-admin',
                        'staff' => 'role-badge-staff',
                        'viewer' => 'role-badge-viewer',
                    ][$role] ?? 'role-badge-viewer';
                @endphp
                <span class="role-badge {{ $roleClass }}">{{ ucfirst($role) }}</span>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
        @csrf
        <button type="button" class="sidebar-logout-btn" onclick="confirmLogout()">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </button>
    </form>
  </div>
</div>

{{-- Logout Confirmation Modal --}}
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
            <div class="modal-body text-center p-4">
                <div style="width:64px;height:64px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="bi bi-box-arrow-right" style="font-size:28px;color:#dc2626;"></i>
                </div>
                <h5 class="fw-bold mb-1">Sign Out</h5>
                <p class="text-muted mb-4" style="font-size:14px;">Are you sure you want to log out of the system?</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger flex-grow-1" onclick="document.getElementById('logoutForm').submit()">Yes, Logout</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmLogout() {
    new bootstrap.Modal(document.getElementById('logoutModal')).show();
}
</script>
