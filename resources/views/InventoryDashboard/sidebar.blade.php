@php
    /** @var \App\Models\User $currentUser */
    $currentUser ??= auth()->user();
@endphp

<div class="sidebar-backdrop" data-action="close-sidebar" aria-hidden="true"></div>
<div class="sidebar">
    <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Hide sidebar" title="Hide sidebar">
        <i class="bi bi-layout-sidebar-inset"></i>
    </button>

    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo-hri.png') }}" alt="PHRMDO Logo">
        </div>
        <h2>PHRMDO INVENTORY SYSTEM</h2>
    </div>

  <ul>
    <li class="{{ (!isset($activePageId) || $activePageId === 'dashboard') ? 'active' : '' }}" data-page-target="dashboard">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Dashboard</span>
    </li>
    <li class="{{ (isset($activePageId) && $activePageId === 'inventory-registry') ? 'active' : '' }}" data-page-target="inventory-registry">
        <i class="bi bi-clipboard-data-fill"></i>
        <span>Inventory Registry</span>
    </li>
    <li class="{{ (isset($activePageId) && $activePageId === 'stock-management') ? 'active' : '' }}" data-page-target="stock-management">
        <i class="bi bi-arrow-left-right"></i>
        <span>Stock Management</span>
    </li>
    <li class="{{ (isset($activePageId) && $activePageId === 'analytics') ? 'active' : '' }}" data-page-target="analytics">
        <i class="bi bi-bar-chart-fill"></i>
        <span>Analytics</span>
    </li>
    <li class="{{ (isset($activePageId) && $activePageId === 'audit-trails') ? 'active' : '' }}" data-page-target="audit-trails">
        <i class="bi bi-clock-history"></i>
        <span>Audit Trails</span>
    </li>
    <li class="{{ (isset($activePageId) && $activePageId === 'users') ? 'active' : '' }}" data-navigate-url="{{ route('users.index') }}">
        <i class="bi bi-people-fill"></i>
        <span>Users</span>
    </li>
  </ul>

  <div class="sidebar-footer">
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
            <div class="sidebar-user-role">
                @php
                    $role = 'admin';
                    $roleClass = 'role-badge-admin';
                @endphp
                <span class="role-badge {{ $roleClass }}">{{ ucfirst($role) }}</span>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('logout') }}" id="logoutForm" style="display: none;">
        @csrf
        @method('POST')
    </form>
    <button type="button" class="sidebar-logout-btn" data-action="confirm-logout">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </button>
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
                    <button type="button" class="btn btn-danger flex-grow-1" data-action="submit-logout">Yes, Logout</button>
                </div>
            </div>
        </div>
    </div>
</div>
