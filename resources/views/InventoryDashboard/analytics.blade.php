<div id="analytics" class="page {{ (isset($activePageId) && $activePageId === 'analytics') ? 'active-page' : '' }}">
    <div class="analytics-header d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="mb-1 fw-bold animated-text" style="font-size: 32px;">ANALYTICS</h1>
            <p class="text-muted mb-0">Insights and reports on your inventory data.</p>
        </div>
        @include('InventoryDashboard.navbar')
    </div>

    <div class="analytics-fit-grid row g-4">
        <!-- Low Stock Alerts (Now half-width, Top Left) -->
        <div class="col-lg-6 no-print analytics-low-panel">
            <div class="chart-card p-0 overflow-hidden d-flex flex-column h-100">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-danger mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Critical Low Stock Items</h5>
                </div>
                <div class="table-responsive bg-white analytics-table-wrap flex-grow-1">
                    <table class="table table-hover table-modern mb-0 border-0">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 border-0">Code</th>
                                <th class="py-3 border-0">Item Name</th>
                                <th class="py-3 border-0">Category</th>
                                <th class="py-3 border-0">Current</th>
                                <th class="py-3 border-0">Min</th>
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
                                    <td class="py-3 text-danger fw-bold">{{ $item->display_stock }}</td>
                                    <td class="py-3 text-secondary">{{ number_format($item->minimum) }}</td>
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

        <!-- Monthly Transactions (Remains half-width, Top Right) -->
        <div class="col-lg-6 no-print analytics-monthly-panel">
            <div class="chart-card h-100 p-0 overflow-hidden d-flex flex-column">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-dark mb-0">Monthly Stock In vs Out</h5>
                </div>
                <div class="analytics-chart-area p-4 flex-grow-1 bg-white">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Stock by Category (Now full-width, Bottom) -->
        <div class="col-12 no-print analytics-category-panel">
            <div class="chart-card h-100 p-0 overflow-hidden d-flex flex-column">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-dark mb-0">Stock by Category</h5>
                </div>
                <div class="analytics-chart-area p-4 flex-grow-1 bg-white">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

