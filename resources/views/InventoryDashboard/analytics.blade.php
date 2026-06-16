<div id="analytics" class="page {{ (isset($activePageId) && $activePageId === 'analytics') ? 'active-page' : '' }}">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="mb-1 fw-bold" style="font-size: 32px; color: #111827;">Analytics</h1>
            <p class="text-muted mb-0">Insights and reports on your inventory data.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Stock by Category -->
        <div class="col-lg-6 no-print">
            <div class="chart-card h-100 p-0 overflow-hidden d-flex flex-column">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-dark mb-0">Stock by Category</h5>
                </div>
                <div class="p-4 flex-grow-1 bg-white" style="position: relative; height: 300px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Transactions -->
        <div class="col-lg-6 no-print">
            <div class="chart-card h-100 p-0 overflow-hidden d-flex flex-column">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-dark mb-0">Monthly Stock In vs Out</h5>
                </div>
                <div class="p-4 flex-grow-1 bg-white" style="position: relative; height: 300px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-12 no-print">
            <div class="chart-card p-0 overflow-hidden">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-danger mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Critical Low Stock Items</h5>
                </div>
                <div class="table-responsive bg-white">
                    <table class="table table-hover table-modern mb-0 border-0">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 border-0">Code</th>
                                <th class="py-3 border-0">Item Name</th>
                                <th class="py-3 border-0">Category</th>
                                <th class="py-3 border-0">Current Stock</th>
                                <th class="py-3 border-0">Minimum Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $lowStockItems = $inventoryItems->filter(function($item) {
                                    return $item->stock <= $item->minimum;
                                });
                            @endphp
                            @forelse($lowStockItems as $item)
                                <tr>
                                    <td class="ps-4 py-3 fw-semibold text-dark">{{ $item->code }}</td>
                                    <td class="py-3 fw-bold text-dark">{{ $item->name }}</td>
                                    <td class="py-3"><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">{{ $item->category }}</span></td>
                                    <td class="py-3 text-danger fw-bold">{{ $item->stock }}</td>
                                    <td class="py-3 text-secondary">{{ $item->minimum }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No items are low on stock.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Monthly Item Activity Report -->
        <div class="col-12" id="monthly-report-section">
            <div class="chart-card p-0 overflow-hidden">
                <div class="print-report-header">
                    <img src="{{ asset('images/logo-hri.png') }}" alt="Province Seal" class="print-report-logo">
                    <div>
                        <div class="print-report-office">
                            Provincial Human Resource Management<br>
                            and Development Office Inventory Reports
                        </div>
                        <div class="print-report-title">Monthly Item Activity Report</div>
                    </div>
                    <img src="{{ asset('images/Surigao_City_Seal.webp') }}" alt="Surigao Seal" class="print-report-logo">
                </div>

                <div class="print-report-month" id="reportMonthPrintLabel"></div>

                <div class="p-4 border-bottom border-light bg-white d-flex justify-content-between align-items-center no-print">
                    <h5 class="fw-bold text-dark mb-0">Monthly Item Activity Report</h5>
                    <div class="d-flex gap-3 align-items-center">
                        <input type="month" id="reportMonthFilter" class="form-control form-control-sm" style="width: auto;">
                        <button class="btn btn-sm btn-outline-secondary" data-action="print-report">
                            <i class="bi bi-printer me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="table-responsive bg-white">
                    <table class="table table-hover table-modern mb-0 border-0" id="reportTable">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 border-0">Code</th>
                                <th class="py-3 border-0">Item Name</th>
                                <th class="py-3 border-0">Category</th>
                                <th class="py-3 border-0 text-center">Stock Added (IN)</th>
                                <th class="py-3 border-0 text-center">Stock Used (OUT)</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
