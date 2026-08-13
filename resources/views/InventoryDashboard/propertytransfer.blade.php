<div id="property-transfer" class="page {{ (isset($activePageId) && $activePageId === 'property-transfer') ? 'active-page' : '' }}">

    <div class="analytics-header d-flex justify-content-between align-items-center mb-3 no-print">
        <div>
            <h1 class="dashboard-title mb-0">
                <span class="dashboard-title-badge">
                    StockWise - Property Transfer Manage
                </span>
            </h1>
        </div>
        @include('InventoryDashboard.navbar')
    </div>
    <!-- Full Height 2 Columns -->
    <div class="row g-3 w-100 m-0"
         style="height: calc(100vh - 110px);">
        <!-- LEFT COLUMN -->
        <div class="col-12 col-lg-5 h-100">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    Column 1
                </div>
            </div>
        </div>
        <!-- RIGHT COLUMN -->
        <div class="col-12 col-lg-7 h-100">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    Column 2
                </div>
            </div>
        </div>
    </div>
</div>