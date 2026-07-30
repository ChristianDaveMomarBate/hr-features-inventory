<div id="reports" class="page {{ (isset($activePageId) && $activePageId === 'reports') ? 'active-page' : '' }}">
    <div class="analytics-header d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="dashboard-title mb-0">
                <span class="dashboard-title-badge">StockWise - Reports</span>
            </h1>
        </div>
        @include('InventoryDashboard.navbar')
    </div>

    <div class="analytics-fit-grid row g-4">
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
                    <div class="print-main-title" id="reportPrintTitle">
                        PHRMDO INVENTORY MONTHLY REPORT
                    </div>

                    {{-- ===== SCREEN-ONLY CONTROLS ===== --}}
                    <div class="px-3 py-2 border-bottom border-light bg-white no-print d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-bold text-dark me-2" style="font-size:13px; white-space:nowrap;" id="reportUITitle">Monthly Item Activity Report</span>

                        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                            <select id="reportType" class="form-select form-select-sm" style="width:130px;">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                            </select>

                            {{-- Monthly filters --}}
                            <div id="monthlyFilters">
                                <input type="month" id="reportMonthFilter" class="form-control form-control-sm" style="width:150px;">
                            </div>

                            {{-- Quarterly filters --}}
                            <div id="quarterlyFilters" style="display: none;" class="gap-2">
                                <select id="reportQuarterFilter" class="form-select form-select-sm" style="width:150px;">
                                    <option value="Q1">1st Semester (Jan - Mar)</option>
                                    <option value="Q2">2nd Semester (Apr - Jun)</option>
                                    <option value="Q3">3rd Semester (Jul - Sep)</option>
                                    <option value="Q4">4th Semester (Oct - Dec)</option>
                                </select>
                                <select id="reportYearFilter" class="form-select form-select-sm" style="width:90px;">
                                    {{-- Populated dynamically by script.js --}}
                                </select>
                            </div>

                            <select id="reportSortOrder" class="form-select form-select-sm" style="width:160px;" title="Sort report items">
                                <option value="date">↕ Sort: By Date</option>
                                <option value="alpha-asc">A → Z (Item Name)</option>
                                <option value="alpha-desc">Z → A (Item Name)</option>
                            </select>

                            <button class="btn btn-sm btn-outline-success d-flex align-items-center gap-1" data-action="export-json" style="white-space:nowrap;">
                                <i class="bi bi-filetype-json"></i> <span>Export JSON</span>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" data-action="print-report" style="white-space:nowrap;">
                                <i class="bi bi-printer"></i> <span>Print</span>
                            </button>
                        </div>
                    </div>

                    {{-- ===== MODERN TABLE ===== --}}
                    <div class="analytics-table-card">
                        <div class="table-responsive analytics-table-wrap">
                            <table id="reportTable" class="modern-report-table">
                                <thead>
                                    <tr class="group-header">
                                        <th colspan="3" class="stock-in-header">
                                            <i class="bi bi-arrow-down-circle-fill"></i> Stock In
                                        </th>
                                        <th colspan="3" class="stock-out-header">
                                            <i class="bi bi-arrow-up-circle-fill"></i> Stock Out
                                        </th>
                                        <th colspan="2" class="balance-header">
                                            <i class="bi bi-box-seam-fill"></i> Stock Balance
                                        </th>
                                    </tr>

                                    <tr class="column-header">
                                        <th>Date</th>
                                        <th>Item Name</th>
                                        <th>In Quantity</th>

                                        <th>Date</th>
                                        <th>Item Name</th>
                                        <th>Out Quantity</th>

                                        <th>Item Name</th>
                                        <th>Balance Qty</th>
                                    </tr>
                                </thead>

                                <tbody id="reportTableBody">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ===== SIGNATURES UI (Screen only, for print injection) ===== --}}
                    <div class="row mt-3 mx-2 mb-3 no-print">
                        <div class="col-md-4">
                            <label class="fw-bold mb-1 text-secondary" style="font-size:12px;">Noted by:</label>
                            <input type="text" id="sigNotedName" class="form-control form-control-sm mb-1 fw-bold text-center" value="MAMARETO B. GESTA JR." placeholder="Name">
                            <input type="text" id="sigNotedPos" class="form-control form-control-sm text-center" value="Administrative Officer IV" placeholder="Position">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold mb-1 text-secondary" style="font-size:12px;">Prepared by:</label>
                            <input type="text" id="sigPreparedName" class="form-control form-control-sm mb-1 fw-bold text-center" placeholder="Name">
                            <input type="text" id="sigPreparedPos" class="form-control form-control-sm text-center" value="POSITION" placeholder="Position">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold mb-1 text-secondary" style="font-size:12px;">Approved by:</label>
                            <input type="text" id="sigApprovedName" class="form-control form-control-sm mb-1 fw-bold text-center" value="MILA B. LISONDRA" placeholder="Name">
                            <input type="text" id="sigApprovedPos" class="form-control form-control-sm text-center" value="OIC - PHRMDO" placeholder="Position">
                        </div>
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
