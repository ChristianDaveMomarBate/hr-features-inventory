@php
    $currentUser ??= auth()->user();
@endphp

<div class="sidebar-backdrop" data-action="close-sidebar" aria-hidden="true"></div>

<div class="sidebar">

    <button
        type="button"
        class="sidebar-toggle-btn"
        id="sidebarToggleBtn"
        aria-label="Hide sidebar"
        title="Hide sidebar"
    >
        <i class="bi bi-layout-sidebar-inset"></i>
    </button>

    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo-hri.png') }}" alt="PHRMDO Logo">
        </div>

        <h2 class="sidebar-title">
            PHRMDO<br>
            <span class="stock-logo">
                <span class="stock-white">Stock</span><span class="stock-blue">Wise</span>
            </span>
        </h2>
    </div>


    {{-- =====================================================
         MAIN NAVIGATION
    ====================================================== --}}

    <ul>

        <li class="{{ (!isset($activePageId) || $activePageId === 'dashboard') ? 'active' : '' }}"
            data-page-target="dashboard">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </li>

        <li class="{{ (isset($activePageId) && $activePageId === 'item-requests') ? 'active' : '' }}"
            data-page-target="item-requests">
            <i class="bi bi-hand-index-fill"></i>
            <span>Manage Request</span>
        </li>

        <li class="{{ (isset($activePageId) && $activePageId === 'inventory-registry') ? 'active' : '' }}"
            data-page-target="inventory-registry">
            <i class="bi bi-clipboard-data-fill"></i>
            <span>Inventory Registry</span>
        </li>

        <li class="{{ (isset($activePageId) && $activePageId === 'stock-management') ? 'active' : '' }}"
            data-page-target="stock-management">
            <i class="bi bi-arrow-left-right"></i>
            <span>Stock Management</span>
        </li>

        <li class="{{ (isset($activePageId) && $activePageId === 'analytics') ? 'active' : '' }}"
            data-page-target="analytics">
            <i class="bi bi-bar-chart-fill"></i>
            <span>Data Analytics</span>
        </li>

        <li class="{{ (isset($activePageId) && $activePageId === 'reports') ? 'active' : '' }}"
            data-page-target="reports">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            <span>Manage Reports</span>
        </li>

    </ul>
   {{-- =====================================================
        PROPERTY MANAGEMENT
    ====================================================== --}}

        <div class="sidebar-section-divider">
            <span>PROPERTY MANAGEMENT</span>
        </div>

        <ul>

        <li class="{{ (isset($activePageId) && $activePageId === 'property-management') ? 'active' : '' }}"
            data-page-target="property-management">
            <i class="bi bi-building"></i>
            <span>Property Management</span>
        </li>

            <li class="{{ (isset($activePageId) && $activePageId === 'property-return') ? 'active' : '' }}"
                data-page-target="property-return">
                <i class="bi bi-cash-coin"></i>
                <span>Budget Management</span>
            </li>

        <li class="{{ (isset($activePageId) && $activePageId === 'property-transfer') ? 'active' : '' }}"
            data-page-target="property-transfer">
            <i class="bi bi-recycle"></i>
            <span>Transfer History</span>
        </li>

        <li class="{{ (isset($activePageId) && $activePageId === 'property-reports') ? 'active' : '' }}"
            data-page-target="property-reports">
            <i class="bi bi-file-earmark-text-fill"></i>
            <span>Waste Disposal</span>
        </li>

    </ul>

    {{-- =====================================================
         SYSTEM
    ====================================================== --}}

    <div class="sidebar-section-divider">
        <span>SYSTEM</span>
    </div>

    <ul>

        <li class="{{ (isset($activePageId) && $activePageId === 'audit-trails') ? 'active' : '' }}"
            data-page-target="audit-trails">
            <i class="bi bi-clock-history"></i>
            <span>Activity Logs</span>
        </li>

        <li class="{{ (isset($activePageId) && $activePageId === 'users') ? 'active' : '' }}"
            data-navigate-url="{{ route('users.index') }}">
            <i class="bi bi-people-fill"></i>
            <span>Users</span>
        </li>

    </ul>


    {{-- =====================================================
         FOOTER
    ====================================================== --}}

    <div style="
        margin-top: auto;
        padding: 14px 16px;
        border-top: 1px solid rgba(255,255,255,0.1);
        text-align: center;
        color: rgba(255,255,255,0.5);
        font-size: 0.68rem;
        line-height: 1.6;
    ">
        <small>
            © 2026 All Rights Reserved </br>

            <strong style="color:rgba(255,255,255,0.85);">
                Provincial Human Resource Management and Development Office – StockWise
            </strong>

            &nbsp;&nbsp;

            <span style="color:yellow;">
                CDB.HR_v1r_wojt
            </span>
        </small>
    </div>

</div>