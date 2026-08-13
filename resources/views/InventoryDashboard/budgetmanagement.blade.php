    <div id="budget-management" class="page {{ (isset($activePageId) && $activePageId === 'budget-management') ? 'active-page' : '' }}">
        <div class="analytics-header d-flex justify-content-between align-items-center mb-4 no-print">
            <div>
                <h1 class="dashboard-title mb-0">
                    <span class="dashboard-title-badge">StockWise - Budget Management </span>
                </h1>
            </div>
            @include('InventoryDashboard.navbar')
        </div>
        Budget Management
    </div>
