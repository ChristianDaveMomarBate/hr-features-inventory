<style>
@media print {
    body * {
        visibility: hidden;
    }
    #monthly-report-section, #monthly-report-section * {
        visibility: visible;
    }
    #monthly-report-section {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .chart-card {
        border: none !important;
        box-shadow: none !important;
    }
    .no-print {
        display: none !important;
    }
    .page.active-page {
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>

<div id="analytics" class="page">
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
                <div class="p-4 border-bottom border-light bg-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Monthly Item Activity Report</h5>
                    <div class="d-flex gap-3 align-items-center no-print">
                        <input type="month" id="reportMonthFilter" class="form-control form-control-sm" style="width: auto;">
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Data preparation
    const items = JSON.parse(document.getElementById('inventory-data').textContent);
    
    // Process Category Data
    const categories = {};
    items.forEach(item => {
        if(!categories[item.category]) categories[item.category] = 0;
        categories[item.category] += Number(item.stock);
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(categories),
            datasets: [{
                data: Object.values(categories),
                backgroundColor: ['#14b8a6', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: "'Inter', sans-serif" } } },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 10,
                    cornerRadius: 8,
                    bodyFont: { family: "'Inter', sans-serif" }
                }
            }
        }
    });

    // Monthly Data Processing
    const rawTx = JSON.parse(document.getElementById('transactions-data').textContent || '[]');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    let monthlyIn = Array(12).fill(0);
    let monthlyOut = Array(12).fill(0);

    rawTx.forEach(tx => {
        const d = new Date(tx.created_at);
        const m = d.getMonth(); // 0-11
        const type = String(tx.type || '').toLowerCase();

        if(type === 'in') {
            monthlyIn[m] += Number(tx.quantity);
        } else if(type === 'out') {
            monthlyOut[m] += Number(tx.quantity);
        }
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Stock In',
                    data: monthlyIn,
                    backgroundColor: '#10b981',
                    borderRadius: 4,
                    barPercentage: 0.75,
                    categoryPercentage: 0.7,
                    maxBarThickness: 28
                },
                {
                    label: 'Stock Out',
                    data: monthlyOut,
                    backgroundColor: '#f59e0b',
                    borderRadius: 4,
                    barPercentage: 0.75,
                    categoryPercentage: 0.7,
                    maxBarThickness: 28
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: "'Inter', sans-serif" } } },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 10,
                    cornerRadius: 8,
                    titleFont: { family: "'Inter', sans-serif" },
                    bodyFont: { family: "'Inter', sans-serif" }
                }
            },
            scales: {
                x: {
                    stacked: false,
                    grid: { display: false }
                },
                y: {
                    stacked: false,
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    border: { display: false }
                }
            }
        }
    });

    // Setup Monthly Report Filter and Table
    const reportMonthFilter = document.getElementById('reportMonthFilter');
    const reportTableBody = document.getElementById('reportTableBody');
    
    // Default to current month
    const today = new Date();
    const currentMonthStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
    reportMonthFilter.value = currentMonthStr;

    function renderMonthlyReport() {
        const selectedDate = new Date(reportMonthFilter.value + '-01');
        const year = selectedDate.getFullYear();
        const month = selectedDate.getMonth();

        // Group transactions by item for the selected month
        const itemStats = {};
        
        items.forEach(item => {
            itemStats[item.id] = {
                code: item.code,
                name: item.name,
                category: item.category,
                added: 0,
                used: 0
            };
        });

        rawTx.forEach(tx => {
            const txDate = new Date(tx.created_at);
            if(txDate.getFullYear() === year && txDate.getMonth() === month && tx.inventory_item_id) {
                const type = String(tx.type || '').toLowerCase();
                const qty = Number(tx.quantity);
                if(itemStats[tx.inventory_item_id]) {
                    if(type === 'in') {
                        itemStats[tx.inventory_item_id].added += qty;
                    } else if(type === 'out') {
                        itemStats[tx.inventory_item_id].used += qty;
                    }
                }
            }
        });

        let html = '';
        let hasData = false;
        
        Object.values(itemStats).forEach(stat => {
            if(stat.added > 0 || stat.used > 0) {
                hasData = true;
                html += `
                    <tr>
                        <td class="ps-4 py-3 fw-semibold text-dark">${stat.code}</td>
                        <td class="py-3 fw-bold text-dark">${stat.name}</td>
                        <td class="py-3"><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">${stat.category}</span></td>
                        <td class="py-3 text-center text-success fw-bold">+${stat.added}</td>
                        <td class="py-3 text-center text-warning fw-bold">-${stat.used}</td>
                    </tr>
                `;
            }
        });

        if(!hasData) {
            html = `<tr><td colspan="5" class="text-center py-5 text-muted">No activity found for the selected month.</td></tr>`;
        }
        
        reportTableBody.innerHTML = html;
    }

    reportMonthFilter.addEventListener('change', renderMonthlyReport);
    renderMonthlyReport(); // initial render
});
</script>
