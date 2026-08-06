<div id="property-management" class="page {{ (isset($activePageId) && $activePageId === 'property-management') ? 'active-page' : '' }}">
    <div class="analytics-header d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="dashboard-title mb-0">
                <span class="dashboard-title-badge">StockWise - Property Management</span>
            </h1>
        </div>
        @include('InventoryDashboard.navbar')
    </div>

    <!-- Property Management Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Property Inventory</h5>
            <button class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add Property
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" id="propertyTable">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Property No.</th>
                            <th>Item Description</th>
                            <th>Date Acquired</th>
                            <th>Unit of Measurement</th>
                            <th>Quantity</th>
                            <th>Unit Value</th>
                            <th>Total Cost</th>
                            <th>PAR Numver</th>
                            <th>Remarks</th>
                            <th>Current User</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data will be loaded here --}}
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                No property records found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>