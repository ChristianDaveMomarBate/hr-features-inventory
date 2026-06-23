document.addEventListener('DOMContentLoaded', function () {
  initAuthTabs();
  initSidebar();
  initDashboardPage();
  initInventoryRegistry();
  initStockManagement();
  initAnalytics();
  showFlashToasts();
});

let trendChartInstance = null;
let distChartInstance = null;
let inventoryItems = [];
let stockInTotalsMap = {};
let stockManagementRowCount = 0;

function readJsonScript(id, fallback) {
  const element = document.getElementById(id);
  if (!element) return fallback;

  try {
    return JSON.parse(element.textContent || '');
  } catch (error) {
    return fallback;
  }
}

function initAuthTabs() {
  if (!document.body.classList.contains('auth-page')) return;

  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }

  function showTab(target) {
    if (window.jQuery) {
      const $ = window.jQuery;
      $('.navbar-nav .nav-link').removeClass('active');
      $('.navbar-nav .nav-link[data-target="' + target + '"]').addClass('active');

      if ($('.navbar-collapse').hasClass('show')) {
        $('.navbar-toggler').click();
      }

      $('.tab-section').hide();
      $('#' + target + '-section').fadeIn(300);
    } else {
      document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
        link.classList.toggle('active', link.dataset.target === target);
      });
      document.querySelectorAll('.tab-section').forEach(function (section) {
        section.style.display = section.id === target + '-section' ? 'block' : 'none';
      });
    }

    document.body.classList.toggle('kiosk-mode-active', target === 'kiosk');
    document.documentElement.classList.toggle('kiosk-mode-active', target === 'kiosk');
    window.location.hash = target;
    requestAnimationFrame(function () {
      window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
    });
  }

  document.querySelectorAll('.nav-tab-btn').forEach(function (button) {
    button.addEventListener('click', function (event) {
      const target = button.dataset.target;
      if (!target) return;

      event.preventDefault();
      showTab(target);
    });
  });

  const initialHash = window.location.hash.substring(1);
  const hasErrors = document.body.dataset.authHasErrors === 'true';
  const validTabs = ['home', 'about', 'login', 'register', 'kiosk'];
  const shouldReturnToKiosk = sessionStorage.getItem('kioskReturnTab') === 'true'
    || document.getElementById('kioskReceiptModal')
    || document.querySelector('.kiosk-alert-danger');
  const shouldRestoreKioskFullscreen = sessionStorage.getItem('kioskReturnFullscreen') === 'true';

  sessionStorage.removeItem('kioskReturnTab');
  sessionStorage.removeItem('kioskReturnFullscreen');

  if (shouldReturnToKiosk) {
    showTab('kiosk');
    if (shouldRestoreKioskFullscreen) {
      document.body.classList.add('kiosk-is-fullscreen');
      window.dispatchEvent(new Event('kiosk:layout-change'));
    }
  } else if (validTabs.includes(initialHash)) {
    showTab(initialHash);
  } else if (hasErrors) {
    showTab('login');
  } else {
    showTab('home');
  }
}

function initSidebar() {
  const toggleBtn = document.getElementById('sidebarToggleBtn');
  const mobileToggleButtons = document.querySelectorAll('[data-action="toggle-sidebar"]');
  const closeButtons = document.querySelectorAll('[data-action="close-sidebar"]');
  const collapsedClass = 'sidebar-collapsed';
  const mobileOpenClass = 'sidebar-mobile-open';
  const mobileQuery = window.matchMedia('(max-width: 1023.98px)');

  function setMobileSidebar(open) {
    document.body.classList.toggle(mobileOpenClass, open);
    mobileToggleButtons.forEach(function (button) {
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  if (toggleBtn) {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    function setSidebarState(collapsed) {
      if (mobileQuery.matches) {
        setMobileSidebar(!document.body.classList.contains(mobileOpenClass));
        return;
      }

      document.body.classList.toggle(collapsedClass, collapsed);
      localStorage.setItem('sidebarCollapsed', collapsed ? 'true' : 'false');
      toggleBtn.setAttribute('aria-label', collapsed ? 'Show sidebar' : 'Hide sidebar');
      toggleBtn.setAttribute('title', collapsed ? 'Show sidebar' : 'Hide sidebar');
      toggleBtn.innerHTML = collapsed
        ? '<i class="bi bi-layout-sidebar-inset-reverse"></i>'
        : '<i class="bi bi-layout-sidebar-inset"></i>';
    }

    if (mobileQuery.matches) {
      setMobileSidebar(false);
    } else {
      setSidebarState(isCollapsed);
    }

    toggleBtn.addEventListener('click', function () {
      setSidebarState(!document.body.classList.contains(collapsedClass));
    });
  }

  mobileToggleButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      setMobileSidebar(!document.body.classList.contains(mobileOpenClass));
    });
  });

  closeButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      setMobileSidebar(false);
    });
  });

  window.addEventListener('resize', function () {
    if (!mobileQuery.matches) setMobileSidebar(false);
  });

  document.querySelectorAll('[data-page-target]').forEach(function (item) {
    item.addEventListener('click', function () {
      const target = item.dataset.pageTarget;
      if (!target) return;
      showPage(target, item);
      setMobileSidebar(false);
    });
  });

  document.querySelectorAll('[data-navigate-url]').forEach(function (item) {
    item.addEventListener('click', function () {
      const url = item.dataset.navigateUrl;
      if (!url) return;
      window.location = url;
    });
  });

  document.querySelectorAll('[data-action="confirm-logout"]').forEach(function (button) {
    button.addEventListener('click', confirmLogout);
  });

  document.querySelectorAll('[data-action="submit-logout"]').forEach(function (button) {
    button.addEventListener('click', function () {
      const form = document.getElementById('logoutForm');
      if (form) {
        // Ensure the form is actually submitted
        form.submit();
      }
    });
  });
}

function showPage(pageId, clickedItem) {
  if (!pageId) return;
  const page = document.getElementById(pageId);

  if (!page || !page.classList.contains('page')) {
    const dashboardUrl = document.body.dataset.dashboardUrl || '/dashboard';
    const dashboardBaseUrl = document.body.dataset.dashboardBaseUrl || '/dashboard';
    window.location = pageId === 'dashboard' ? dashboardUrl : `${dashboardBaseUrl}/${pageId}`;
    return;
  }

  document.querySelectorAll('.page').forEach(function (pageElement) {
    pageElement.classList.remove('active-page');
  });

  document.querySelectorAll('.sidebar li').forEach(function (item) {
    item.classList.remove('active');
  });

  page.classList.add('active-page');
  if (clickedItem) clickedItem.classList.add('active');

  const qs = window.location.search;
  if (pageId === 'dashboard') {
    history.pushState(null, '', '/dashboard' + qs);
  } else {
    history.pushState(null, '', '/dashboard/' + pageId + qs);
  }
}

function confirmLogout() {
  const modal = document.getElementById('logoutModal');
  if (!modal || !window.bootstrap) return;

  new window.bootstrap.Modal(modal).show();
}

function initDashboardPage() {
  const currentDate = document.getElementById('currentDate');
  if (!currentDate) return;

  const pathSegments = window.location.pathname.split('/');
  const pageId = pathSegments[pathSegments.length - 1];
  const validPages = readJsonScript('valid-dashboard-pages-data', []);

  if (validPages.includes(pageId)) {
    const sidebarItem = document.querySelector('.sidebar li[data-page-target="' + pageId + '"]');
    if (sidebarItem) showPage(pageId, sidebarItem);
  }

  const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  currentDate.textContent = new Date().toLocaleDateString('en-US', dateOptions);

  document.querySelectorAll('[data-page-link]').forEach(function (link) {
    link.addEventListener('click', function (event) {
      const page = link.dataset.pageLink;
      if (!page) return;
      event.preventDefault();
      showPage(page);
    });
  });
}

function initOrUpdateCharts(stockIn, stockOut, currentStock) {
  const trendCanvas = document.getElementById('trendChart');
  const distCanvas = document.getElementById('distributionChart');
  if (!trendCanvas || !distCanvas || !window.Chart) return;

  window.Chart.defaults.font.family = "'Inter', sans-serif";
  window.Chart.defaults.color = '#64748b';
  window.Chart.defaults.scale.grid.color = '#f1f5f9';

  const ctxTrend = trendCanvas.getContext('2d');

  if (!trendChartInstance) {
    const gradientIn = ctxTrend.createLinearGradient(0, 0, 0, 300);
    gradientIn.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
    gradientIn.addColorStop(1, 'rgba(16, 185, 129, 0)');

    const gradientOut = ctxTrend.createLinearGradient(0, 0, 0, 300);
    gradientOut.addColorStop(0, 'rgba(245, 158, 11, 0.2)');
    gradientOut.addColorStop(1, 'rgba(245, 158, 11, 0)');

    trendChartInstance = new window.Chart(ctxTrend, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
          chartLineDataset('Stock In', [0, 0, 0, 0, 0, stockIn || 0], '#10b981', gradientIn),
          chartLineDataset('Stock Out', [0, 0, 0, 0, 0, stockOut || 0], '#f59e0b', gradientOut),
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleFont: { size: 13, family: "'Inter', sans-serif" },
            bodyFont: { size: 13, family: "'Inter', sans-serif" },
            padding: 10,
            cornerRadius: 8,
            boxPadding: 4,
          },
        },
        scales: {
          x: { grid: { display: false, drawBorder: false } },
          y: { beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9' } },
        },
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
      },
    });
  } else {
    trendChartInstance.data.datasets[0].data[5] = stockIn;
    trendChartInstance.data.datasets[1].data[5] = stockOut;
    trendChartInstance.update();
  }

  const ctxDist = distCanvas.getContext('2d');
  if (!distChartInstance) {
    distChartInstance = new window.Chart(ctxDist, {
      type: 'doughnut',
      data: {
        labels: ['Current Stock', 'Stock Out'],
        datasets: [{
          data: [currentStock || 0, stockOut || 0],
          backgroundColor: ['#14b8a6', '#fbbf24'],
          hoverBackgroundColor: ['#0d9488', '#f59e0b'],
          borderWidth: 0,
          hoverOffset: 4,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            padding: 10,
            cornerRadius: 8,
            boxPadding: 4,
            callbacks: {
              label: function (context) {
                let label = context.label || '';
                if (label) label += ': ';
                if (context.parsed !== null) label += context.parsed + ' units';
                return label;
              },
            },
          },
        },
      },
    });
  } else {
    distChartInstance.data.datasets[0].data = [currentStock, stockOut];
    distChartInstance.update();
  }

  setText('donutTotal', currentStock + stockOut);
  setText('donutCurrent', currentStock);
  setText('donutOut', stockOut);
}

function chartLineDataset(label, data, color, backgroundColor) {
  return {
    label,
    data,
    borderColor: color,
    backgroundColor,
    borderWidth: 2,
    pointBackgroundColor: '#ffffff',
    pointBorderColor: color,
    pointBorderWidth: 2,
    pointRadius: 4,
    pointHoverRadius: 6,
    fill: true,
    tension: 0.3,
  };
}

function initInventoryRegistry() {
  const inventoryTable = document.getElementById('inventoryTable');
  if (!inventoryTable) return;

  inventoryItems = readJsonScript('inventory-data', []);
  stockInTotalsMap = readJsonScript('stock-in-totals', {});

  document.querySelectorAll('[data-action="open-add-item"]').forEach(function (button) {
    button.addEventListener('click', openAddItem);
  });

  inventoryTable.addEventListener('click', function (event) {
    const editButton = event.target.closest('[data-edit-item-id]');
    if (editButton) openEditItem(editButton.dataset.editItemId);
  });

  inventoryTable.addEventListener('submit', function (event) {
    const form = event.target.closest('[data-confirm]');
    if (form && !window.confirm(form.dataset.confirm)) event.preventDefault();
  });

  updateDashboardCards();
}

function canManageInventory() {
  return document.getElementById('inventory-registry')?.dataset.canManage === 'true';
}

function openAddItem() {
  if (!canManageInventory()) return;

  const form = document.getElementById('inventoryForm');
  if (!form) return;

  form.reset();
  form.action = document.getElementById('inventory-registry').dataset.storeUrl;
  document.getElementById('methodOverride').value = 'POST';
  document.getElementById('modalTitle').textContent = 'Add Inventory Item';
  form.elements.type.value = 'Consumable';
  form.elements.stock_unit.value = 'pcs';
  form.elements.issue_unit.value = 'pcs';
  form.elements.units_per_stock_unit.value = 1;
  new window.bootstrap.Modal(document.getElementById('itemModal')).show();
}

function openEditItem(id) {
  if (!canManageInventory()) return;

  const item = inventoryItems.find(function (entry) {
    return entry.id == id;
  });
  if (!item) return;

  const form = document.getElementById('inventoryForm');
  form.action = document.getElementById('inventory-registry').dataset.updateBaseUrl + '/' + id;
  document.getElementById('methodOverride').value = 'PUT';
  document.getElementById('modalTitle').textContent = 'Edit Inventory Item';
  const unitsPerStockUnit = Number(item.units_per_stock_unit || 1);
  const stockInStockUnits = unitsPerStockUnit > 0 ? Number(item.stock || 0) / unitsPerStockUnit : Number(item.stock || 0);

  form.elements.code.value = item.code;
  form.elements.name.value = item.name;
  form.elements.category.value = item.category;
  form.elements.type.value = item.type || 'Consumable';
  form.elements.stock_unit.value = item.stock_unit || item.unit || 'pcs';
  form.elements.issue_unit.value = item.issue_unit || item.unit || 'pcs';
  form.elements.units_per_stock_unit.value = unitsPerStockUnit;
  form.elements.stock.value = Number.isInteger(stockInStockUnits)
    ? stockInStockUnits
    : stockInStockUnits.toFixed(2).replace(/\.?0+$/, '');
  form.elements.minimum.value = item.minimum;
  form.elements.location.value = item.location || '';
  form.elements.date_registered.value = item.date_registered ? item.date_registered.split('T')[0] : '';

  new window.bootstrap.Modal(document.getElementById('itemModal')).show();
}

function updateDashboardCards() {
  const totalItems = inventoryItems.length;
  const rawTx = readJsonScript('transactions-data', []);

  let totalStockInTx = 0;
  let totalStockOut = 0;

  rawTx.forEach(function (tx) {
    if (tx.type === 'in') totalStockInTx += Number(tx.quantity);
    if (tx.type === 'out') totalStockOut += Number(tx.quantity);
  });

  const currentTotalStock = inventoryItems.reduce(function (total, item) {
    return total + Number(item.stock || 0);
  }, 0);

  const totalStockIn = Math.max(totalStockInTx, currentTotalStock + totalStockOut);
  const lowStockItems = inventoryItems.filter(function (item) {
    return item.stock <= item.minimum;
  }).length;

  setText('totalItems', totalItems);
  setText('currentStock', currentTotalStock);
  setText('totalStockIn', totalStockIn);
  setText('totalStockOut', totalStockOut);
  setText('lowStockItems', lowStockItems);

  initOrUpdateCharts(totalStockIn, totalStockOut, currentTotalStock);
}

function initStockManagement() {
  const container = document.getElementById('batchItemsContainer');
  if (!container) return;

  document.querySelectorAll('[data-action="add-stock-row"]').forEach(function (button) {
    button.addEventListener('click', addItemRow);
  });

  addItemRow();
  initTxPagination();
}

const TX_PER_PAGE = 4;
let currentTxPage = 1;

function initTxPagination() {
  const rows = document.querySelectorAll('#txTableBody .stock-table-row');
  if (rows.length === 0) return;

  updateTxPagination();
}

function updateTxPagination() {
  const rows = document.querySelectorAll('#txTableBody .stock-table-row');
  const total = rows.length;
  const totalPages = Math.ceil(total / TX_PER_PAGE) || 1;
  if (currentTxPage > totalPages) currentTxPage = totalPages;
  if (currentTxPage < 1) currentTxPage = 1;

  const start = (currentTxPage - 1) * TX_PER_PAGE;
  const end = start + TX_PER_PAGE;

  rows.forEach(function (row, i) {
    row.style.display = (i >= start && i < end) ? '' : 'none';
  });

  const footer = document.getElementById('txPaginationFooter');
  const prevBtn = document.getElementById('txPrevBtn');
  const nextBtn = document.getElementById('txNextBtn');
  const pageInfo = document.getElementById('txPageInfo');

  if (!footer) return;

  if (total <= TX_PER_PAGE) {
    footer.style.display = 'none';
    return;
  }

  footer.style.display = 'block';
  prevBtn.disabled = currentTxPage === 1;
  nextBtn.disabled = currentTxPage === totalPages;
  pageInfo.textContent = `Page ${currentTxPage} of ${totalPages} (${total} total)`;
}

window.changeTxPage = function (delta) {
  currentTxPage += delta;
  updateTxPagination();
};

function addItemRow() {
  stockManagementRowCount += 1;
  const idx = stockManagementRowCount;
  const container = document.getElementById('batchItemsContainer');
  if (!container) return;

  const stockManagementPage = document.getElementById('stock-management');
  const allInventoryItems = readJsonScript('stock-management-items-data', []);
  const isAdmin = stockManagementPage?.dataset.isAdmin === 'true';
  const typeOptions = isAdmin
    ? '<option value="in">Stock In</option><option value="out">Stock Out</option><option value="adjustment">Adjustment</option>'
    : '<option value="in">Stock In</option><option value="out">Stock Out</option>';
  const itemOptions = allInventoryItems.map(function (item) {
    const displayUnit = item.issue_unit || item.unit || 'pcs';
    const displayStock = item.display_stock || `${item.stock} ${displayUnit}`;
    const bulkText = item.bulk_equivalent ? `, ${item.bulk_equivalent}` : '';
    return `<option value="${item.id}">${item.code} - ${item.name} (${displayStock}${bulkText})</option>`;
  }).join('');

  const row = document.createElement('div');
  row.className = 'stock-item-card bg-white border border-light shadow-sm rounded-4 p-4 mb-3';
  row.id = `item-row-${idx}`;
  row.innerHTML = `
    <div class="mb-3">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <label class="form-label small fw-bold text-secondary mb-0">Item</label>
        <button type="button" class="btn-close bg-light border border-secondary border-opacity-25 rounded-circle" style="opacity: 1; padding: 0.4rem; font-size: 0.75rem;" data-remove-row="${idx}" title="Remove"></button>
      </div>
      <select name="items[${idx}][inventory_item_id]" class="form-select rounded-3" required>
        <option value="">Search item...</option>
        ${itemOptions}
      </select>
    </div>
    
    <div class="row g-2 mb-3">
      <div class="col-5">
        <label class="form-label small fw-bold text-secondary mb-1">Type</label>
        <select name="items[${idx}][type]" class="form-select rounded-3" required>${typeOptions}</select>
      </div>
      <div class="col-7">
        <label class="form-label small fw-bold text-secondary mb-1">Quantity <span class="fw-normal text-muted" style="font-size: 0.75rem;">(issue units)</span></label>
        <input type="number" name="items[${idx}][quantity]" min="1" class="form-control rounded-3" placeholder="0" required>
      </div>
    </div>
    
    <div class="mb-1">
      <label class="form-label small fw-bold text-secondary mb-1">Handled By</label>
      <input type="text" name="items[${idx}][handled_by]" class="form-control rounded-3" placeholder="Name of person who stocked in/ou" required>
    </div>
  `;

  row.querySelector('[data-remove-row]').addEventListener('click', function () {
    removeRow(idx);
  });
  container.appendChild(row);

  const selectEl = row.querySelector(`select[name="items[${idx}][inventory_item_id]"]`);
  if (window.TomSelect) {
    new window.TomSelect(selectEl, {
      create: false,
      sortField: {
        field: "text",
        direction: "asc"
      },
      placeholder: "Search item..."
    });
  }

  const totalCards = container.querySelectorAll('.stock-item-card').length;
  window.currentBatchPage = Math.ceil(totalCards / 4);
  updateBatchPagination();
}

function removeRow(idx) {
  const row = document.getElementById(`item-row-${idx}`);
  if (row) row.remove();
  updateBatchPagination();
}

function updateBatchPagination() {
  const container = document.getElementById('batchItemsContainer');
  if (!container) return;
  const cards = container.querySelectorAll('.stock-item-card');
  const totalCards = cards.length;
  const BATCH_ITEMS_PER_PAGE = 4;
  const totalPages = Math.ceil(totalCards / BATCH_ITEMS_PER_PAGE) || 1;

  if (window.currentBatchPage > totalPages || !window.currentBatchPage) {
    window.currentBatchPage = totalPages;
  }

  cards.forEach((card, index) => {
    const pageIndex = Math.floor(index / BATCH_ITEMS_PER_PAGE) + 1;
    if (pageIndex === window.currentBatchPage) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });

  let pagContainer = document.getElementById('batchPaginationContainer');
  if (!pagContainer) {
    pagContainer = document.createElement('div');
    pagContainer.id = 'batchPaginationContainer';
    pagContainer.className = 'd-flex justify-content-between align-items-center mt-3 mb-4';
    container.parentNode.insertBefore(pagContainer, container.nextSibling);
  }

  if (totalCards <= BATCH_ITEMS_PER_PAGE) {
    pagContainer.innerHTML = '';
    return;
  }

  pagContainer.innerHTML = `
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeBatchPage(-1)" ${window.currentBatchPage === 1 ? 'disabled' : ''}>
      <i class="bi bi-chevron-left"></i> Previous 4
    </button>
    <span class="badge bg-secondary">Showing ${((window.currentBatchPage - 1) * BATCH_ITEMS_PER_PAGE) + 1} - ${Math.min(window.currentBatchPage * BATCH_ITEMS_PER_PAGE, totalCards)} of ${totalCards} items</span>
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeBatchPage(1)" ${window.currentBatchPage === totalPages ? 'disabled' : ''}>
      Next 4 <i class="bi bi-chevron-right"></i>
    </button>
  `;
}

window.changeBatchPage = function (delta) {
  window.currentBatchPage += delta;
  updateBatchPagination();
};

function initAnalytics() {
  const categoryChart = document.getElementById('categoryChart');
  const monthlyChart = document.getElementById('monthlyChart');
  if (!categoryChart || !monthlyChart || !window.Chart) return;

  const items = readJsonScript('inventory-data', []);
  const categories = {};
  items.forEach(function (item) {
    if (!categories[item.category]) categories[item.category] = 0;
    categories[item.category] += Number(item.stock);
  });

  const sortedCategories = Object.entries(categories).sort((a, b) => a[1] - b[1]);
  const sortedLabels = sortedCategories.map(c => c[0]);
  const sortedData = sortedCategories.map(c => c[1]);

  new window.Chart(categoryChart, {
    type: 'bar',
    data: {
      labels: sortedLabels,
      datasets: [{
        data: sortedData,
        backgroundColor: ['#5bc0de', '#5cb85c', '#f0ad4e', '#d9534f', '#0275d8', '#292b2c', '#17a2b8', '#ffc107', '#28a745', '#dc3545'],
        borderWidth: 0,
        barPercentage: 0.9,
        categoryPercentage: 0.9,
      }],
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.9)',
          padding: 10,
          cornerRadius: 8,
          bodyFont: { family: "'Inter', sans-serif" },
        },
      },
      scales: {
        x: { display: true, beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false } },
        y: { display: false, grid: { display: false } }
      }
    },
    plugins: [{
      id: 'insideLabels',
      afterDatasetsDraw(chart) {
        const { ctx, data } = chart;
        ctx.save();
        chart.getDatasetMeta(0).data.forEach((datapoint, index) => {
          const label = data.labels[index];
          const val = data.datasets[0].data[index];
          const y = datapoint.y;
          const startX = datapoint.base;
          
          ctx.fillStyle = '#ffffff';
          ctx.font = 'bold 13px "Inter", sans-serif';
          ctx.textAlign = 'left';
          ctx.textBaseline = 'middle';
          
          ctx.shadowColor = 'rgba(0,0,0,0.6)';
          ctx.shadowBlur = 3;
          ctx.shadowOffsetX = 1;
          ctx.shadowOffsetY = 1;
          
          ctx.fillText(`${label}: ${val}`, startX + 10, y);
        });
        ctx.restore();
      }
    }]
  });

  const rawTx = readJsonScript('transactions-data', []);
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const monthlyIn = Array(12).fill(0);
  const monthlyOut = Array(12).fill(0);

  rawTx.forEach(function (tx) {
    const date = new Date(tx.created_at);
    const month = date.getMonth();
    const type = String(tx.type || '').toLowerCase();
    if (type === 'in') monthlyIn[month] += Number(tx.quantity);
    if (type === 'out') monthlyOut[month] += Number(tx.quantity);
  });

  new window.Chart(monthlyChart, {
    type: 'line',
    data: {
      labels: months,
      datasets: [
        monthlyDataset('Stock In', monthlyIn, '#10b981', 'rgba(16, 185, 129, 0.15)'),
        monthlyDataset('Stock Out', monthlyOut, '#f59e0b', 'rgba(245, 158, 11, 0.15)'),
      ],
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
          bodyFont: { family: "'Inter', sans-serif" },
        },
      },
      scales: {
        x: { stacked: false, grid: { display: false } },
        y: { stacked: false, beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false } },
      },
    },
  });

  initMonthlyReport(items, rawTx);

  document.querySelectorAll('[data-action="print-report"]').forEach(function (button) {
    button.addEventListener('click', function () {
      window.print();
    });
  });
}

function monthlyDataset(label, data, borderColor, backgroundColor) {
  return {
    label,
    data,
    borderColor: borderColor,
    backgroundColor: backgroundColor,
    borderWidth: 2,
    pointBackgroundColor: '#ffffff',
    pointBorderColor: borderColor,
    pointBorderWidth: 2,
    pointRadius: 3,
    pointHoverRadius: 5,
    fill: true,
    tension: 0.3,
  };
}

function initMonthlyReport(items, rawTx) {
  const reportMonthFilter = document.getElementById('reportMonthFilter');
  const reportTableBody = document.getElementById('reportTableBody');
  const reportMonthPrintLabel = document.getElementById('reportMonthPrintLabel');
  if (!reportMonthFilter || !reportTableBody || !reportMonthPrintLabel) return;

  const today = new Date();
  reportMonthFilter.value = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');

  function renderMonthlyReport() {
    const selectedDate = new Date(reportMonthFilter.value + '-01');
    const year = selectedDate.getFullYear();
    const month = selectedDate.getMonth();

    reportMonthPrintLabel.textContent = selectedDate.toLocaleDateString('en-US', {
      month: 'long',
      year: 'numeric',
    });

    const itemStats = {};
    items.forEach(function (item) {
      itemStats[item.id] = {
        code: item.code,
        name: item.name,
        category: item.category,
        added: 0,
        used: 0,
      };
    });

    rawTx.forEach(function (tx) {
      const txDate = new Date(tx.created_at);
      const type = String(tx.type || '').toLowerCase();
      const qty = Number(tx.quantity);
      const stat = itemStats[tx.inventory_item_id];

      if (txDate.getFullYear() !== year || txDate.getMonth() !== month || !stat) return;
      if (type === 'in') stat.added += qty;
      if (type === 'out') stat.used += qty;
    });

    let html = '';
    let hasData = false;

    Object.values(itemStats).forEach(function (stat) {
      if (stat.added <= 0 && stat.used <= 0) return;

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
    });

    if (!hasData) {
      html = '<tr><td colspan="5" class="text-center py-5 text-muted">No activity found for the selected month.</td></tr>';
    }

    reportTableBody.innerHTML = html;
  }

  reportMonthFilter.addEventListener('change', renderMonthlyReport);
  renderMonthlyReport();
}

function showFlashToasts() {
  const success = document.getElementById('flashSuccess');
  const error = document.getElementById('flashError');

  if (success) showToast('success', success.dataset.message);
  if (error) showToast('error', error.dataset.message);
}

function showToast(type, message) {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const id = 'toast-' + Date.now();
  const isSuccess = type === 'success';
  const toast = document.createElement('div');

  toast.id = id;
  toast.style.cssText = `display:flex;align-items:flex-start;gap:12px;background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.13);padding:16px 20px;margin-bottom:12px;border-left:4px solid ${isSuccess ? '#10b981' : '#ef4444'};opacity:0;transform:translateX(30px);transition:opacity 0.35s ease,transform 0.35s ease;`;
  toast.innerHTML = `<i class="bi ${isSuccess ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger'}" style="font-size:20px;line-height:1;margin-top:2px;"></i><div style="flex:1;"><div style="font-weight:600;font-size:14px;color:#111827;margin-bottom:2px;">${isSuccess ? 'Success' : 'Error'}</div><div style="font-size:13px;color:#6b7280;">${message}</div></div><button type="button" class="toast-close-btn" style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:18px;line-height:1;padding:0;">&times;</button>`;
  toast.querySelector('.toast-close-btn').addEventListener('click', function () {
    removeToast(id);
  });

  container.appendChild(toast);
  requestAnimationFrame(function () {
    requestAnimationFrame(function () {
      toast.style.opacity = '1';
      toast.style.transform = 'translateX(0)';
    });
  });
  setTimeout(function () {
    removeToast(id);
  }, 3000);
}

function removeToast(id) {
  const toast = document.getElementById(id);
  if (!toast) return;

  toast.style.opacity = '0';
  toast.style.transform = 'translateX(30px)';
  setTimeout(function () {
    toast.remove();
  }, 350);
}

function setText(id, value) {
  const element = document.getElementById(id);
  if (element) element.textContent = value;
}

window.showPage = showPage;
window.confirmLogout = confirmLogout;
window.openAddItem = openAddItem;
window.openEditItem = openEditItem;
window.addItemRow = addItemRow;
window.removeRow = removeRow;
window.initOrUpdateCharts = initOrUpdateCharts;
window.showToast = showToast;
window.removeToast = removeToast;
