<div id="analytics" class="page {{ (isset($activePageId) && $activePageId === 'analytics') ? 'active-page' : '' }}">
    <div class="analytics-header d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="mb-1 fw-bold animated-text" style="font-size: 32px;">ANALYTICS</h1>
            <p class="text-muted mb-0">Insights and reports on your inventory data.</p>
        </div>
        @include('InventoryDashboard.navbar')
    </div>

    <div class="analytics-fit-grid row g-4">
        <!-- Stock by Category -->
        <div class="col-lg-6 no-print analytics-category-panel">
            <div class="chart-card h-100 p-0 overflow-hidden d-flex flex-column">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-dark mb-0">Stock by Category</h5>
                </div>
                <div class="analytics-chart-area p-4 flex-grow-1 bg-white">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Transactions -->
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

        <!-- Low Stock Alerts -->
        <div class="col-12 no-print analytics-low-panel">
            <div class="chart-card p-0 overflow-hidden d-flex flex-column">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-danger mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Critical Low Stock Items</h5>
                </div>
                <div class="table-responsive bg-white analytics-table-wrap">
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
                                    <td class="py-3 text-danger fw-bold">{{ $item->display_stock }}</td>
                                    <td class="py-3 text-secondary">{{ number_format($item->minimum) }} {{ $item->display_unit }}</td>
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
        <div class="col-12 analytics-report-panel" id="monthly-report-section">
            <div class="chart-card p-0 overflow-hidden" style="background: white; border: none; box-shadow: none;">

                <div class="print-container">

                    {{-- ===== HEADER: GovMail Header image ===== --}}
                    <div class="print-header-top" style="text-align: center; margin-bottom: 6px;">
                        <img src="{{ asset('images/GovMail Header.png') }}" alt="GovMail Header" style="width: 100%; height: auto; display: block;">
                    </div>

                    {{-- Hidden span required by JS to set the month label --}}
                    <span id="reportMonthPrintLabel" style="display:none;"></span>
                    {{-- Hidden element exposing asset URLs to JS --}}
                    <span id="printAssetUrls"
                          data-logo-src="{{ asset('images/print-logo.png') }}"
                          data-footer-src="{{ asset('images/footer.png') }}"
                          style="display:none;"></span>

                    {{-- ===== REPORT TITLE below banner ===== --}}
                    <div class="print-main-title">
                        PHRMDO INVENTORY MONTHLY REPORT
                    </div>

                    {{-- ===== SCREEN-ONLY CONTROLS ===== --}}
                    <div class="p-3 border-bottom border-light bg-white d-flex justify-content-between align-items-center no-print">
                        <h5 class="fw-bold text-dark mb-0">Monthly Item Activity Report</h5>
                        <div class="d-flex gap-3 align-items-center">
                            <input type="month" id="reportMonthFilter" class="form-control form-control-sm" style="width: auto;">
                            <button class="btn btn-sm btn-outline-secondary" data-action="print-report">
                                <i class="bi bi-printer me-1"></i> Print
                            </button>
                        </div>
                    </div>

                    {{-- ===== TABLE ===== --}}
                    <div class="table-responsive analytics-table-wrap" style="overflow: visible;">
                        <table id="reportTable" style="width:100%; border-collapse:collapse; table-layout:fixed;">
                            <thead>
                                <tr>
                                    <th colspan="3" style="background-color:#a9d08e; color:#000; border:1px solid #000; text-align:center; font-size:11px; font-weight:bold; padding:4px;">Stock In</th>
                                    <th colspan="3" style="background-color:#f4b084; color:#000; border:1px solid #000; text-align:center; font-size:11px; font-weight:bold; padding:4px;">Stock Out</th>
                                    <th colspan="2" style="background-color:#9bc2e6; color:#000; border:1px solid #000; text-align:center; font-size:11px; font-weight:bold; padding:4px;">Stock Balance</th>
                                </tr>
                                <tr>
                                    <th style="background-color:#c6e0b4; color:#000; border:1px solid #000; text-align:center; font-size:10px; padding:3px; width:9%;">Date</th>
                                    <th style="background-color:#c6e0b4; color:#000; border:1px solid #000; text-align:center; font-size:10px; padding:3px; width:13%;">Item Name</th>
                                    <th style="background-color:#c6e0b4; color:#000; border:1px solid #000; text-align:center; font-size:10px; padding:3px; width:13%;">In Quantity</th>
                                    <th style="background-color:#f8cbad; color:#000; border:1px solid #000; text-align:center; font-size:10px; padding:3px; width:9%;">Date</th>
                                    <th style="background-color:#f8cbad; color:#000; border:1px solid #000; text-align:center; font-size:10px; padding:3px; width:13%;">Item Name</th>
                                    <th style="background-color:#f8cbad; color:#000; border:1px solid #000; text-align:center; font-size:10px; padding:3px; width:13%;">Out Quantity</th>
                                    <th style="background-color:#bdd7ee; color:#000; border:1px solid #000; text-align:center; font-size:10px; padding:3px; width:15%;">Item Name</th>
                                    <th style="background-color:#bdd7ee; color:#000; border:1px solid #000; text-align:center; font-size:10px; padding:3px; width:15%;">Balance Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>

                    {{-- ===== FOOTER IMAGE (screen reference, hidden in print via CSS) ===== --}}
                    <div class="print-report-footer">
                        <img src="{{ asset('images/footer.png') }}" alt="Footer">
                    </div>

                </div>{{-- end .print-container --}}

                {{-- ===== PRINT PAGES CONTAINER (populated by JS, visible only when printing) ===== --}}
                <div id="printPagesContainer" class="print-only"></div>

                {{-- ===== FIXED FOOTER: pinned to bottom of every printed page via CSS position:fixed ===== --}}
                <div id="printFixedFooter" class="print-only">
                    <img src="{{ asset('images/footer.png') }}" alt="Footer">
                </div>
            </div>
        </div>
    </div>
</div>

