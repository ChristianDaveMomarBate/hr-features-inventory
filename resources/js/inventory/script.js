document.addEventListener('DOMContentLoaded', function () {
  initAuthTabs();
  initSidebar();
  initDashboardPage();
  initInventoryRegistry();
  initStockManagement();
  initAnalytics();
  initHourlyBibleVerse();
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
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
      if (prefersReducedMotion) {
        $('#' + target + '-section').show();
      } else {
        $('#' + target + '-section').fadeIn(300);
      }
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
    document.body.classList.toggle('request-mode-active', target === 'request');
    document.documentElement.classList.toggle('request-mode-active', target === 'request');
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
  const validTabs = ['home', 'about', 'login', 'register', 'kiosk', 'request'];
  const shouldReturnToKiosk = sessionStorage.getItem('kioskReturnTab') === 'true'
    || document.getElementById('kioskReceiptModal')
    || document.querySelector('.kiosk-alert-danger');
  const shouldReturnToRequest = document.getElementById('requestAutoTrackData') !== null
    || document.querySelector('#request-section .is-invalid') !== null
    || document.querySelector('#request-section .alert') !== null;
  const shouldRestoreKioskFullscreen = sessionStorage.getItem('kioskReturnFullscreen') === 'true';

  sessionStorage.removeItem('kioskReturnTab');
  sessionStorage.removeItem('kioskReturnFullscreen');

  if (shouldReturnToRequest) {
    showTab('request');
    const autoTrackData = document.getElementById('requestAutoTrackData');
    if (autoTrackData) {
      // Small delay to ensure functions are defined
      setTimeout(function() {
        if (typeof window.switchRequestTab === 'function') {
          window.switchRequestTab('track-request');
        }
        const trackInput = document.getElementById('track_request_id');
        if (trackInput) {
          trackInput.value = autoTrackData.dataset.requestId;
          if (typeof window.trackRequest === 'function') {
            window.trackRequest();
          }
        }
      }, 100);
    }
  } else if (shouldReturnToKiosk) {
    showTab('kiosk');
    if (shouldRestoreKioskFullscreen) {
      document.body.classList.add('kiosk-is-fullscreen');
      window.dispatchEvent(new Event('kiosk:layout-change'));
    }
  } else if (hasErrors && document.querySelector('#login-section .is-invalid, #login-section .lp-inp--err')) {
    showTab('login');
  } else if (validTabs.includes(initialHash)) {
    showTab(initialHash);
  } else {
    showTab('home');
  }
}

function initHourlyBibleVerse() {
  const verseElements = document.querySelectorAll('.hourlyBibleVerse');
  if (verseElements.length === 0) return;

  const verses = [
      'Psalm 23:1 - The Lord is my shepherd; I shall not want.',
      'Philippians 4:13 - I can do all things through Christ who strengthens me.',
      'Proverbs 3:5 - Trust in the Lord with all thine heart; and lean not unto thine own understanding.',
      'Isaiah 41:10 - Fear thou not; for I am with thee.',
      'Psalm 46:10 - Be still, and know that I am God.',
      'Matthew 5:16 - Let your light so shine before men.',
      'Romans 8:28 - And we know that all things work together for good to them that love God.',
      'Joshua 1:9 - Be strong and of a good courage; be not afraid.',
      'Psalm 118:24 - This is the day which the Lord hath made; we will rejoice and be glad in it.',
      'John 14:27 - Peace I leave with you, my peace I give unto you.',
      '1 Peter 5:7 - Casting all your care upon him; for he careth for you.',
      'Psalm 119:105 - Thy word is a lamp unto my feet, and a light unto my path.',
      'Jeremiah 29:11 - For I know the plans I have for you, saith the Lord.',
      'John 3:16 - For God so loved the world, that he gave his only begotten Son.',
      'Romans 12:12 - Rejoicing in hope; patient in tribulation; continuing instant in prayer.',
      'Psalm 27:1 - The Lord is my light and my salvation; whom shall I fear?',
      'Isaiah 40:31 - They that wait upon the Lord shall renew their strength.',
      'Matthew 11:28 - Come unto me, all ye that labour and are heavy laden, and I will give you rest.',
      '2 Timothy 1:7 - For God hath not given us the spirit of fear; but of power, and of love, and of a sound mind.',
      'Hebrews 11:1 - Now faith is the substance of things hoped for, the evidence of things not seen.',
      'Psalm 34:8 - O taste and see that the Lord is good.',
      'Lamentations 3:22-23 - His compassions fail not. They are new every morning.',
      'Romans 15:13 - Now the God of hope fill you with all joy and peace in believing.',
      'James 1:5 - If any of you lack wisdom, let him ask of God.',
      'Ephesians 2:8 - For by grace are ye saved through faith.',
      'Colossians 3:23 - And whatsoever ye do, do it heartily, as to the Lord.',
      'Psalm 37:4 - Delight thyself also in the Lord; and he shall give thee the desires of thine heart.',
      'Nahum 1:7 - The Lord is good, a strong hold in the day of trouble.',
      '1 Thessalonians 5:16-18 - Rejoice evermore. Pray without ceasing. In every thing give thanks.',
      'Micah 6:8 - What doth the Lord require of thee, but to do justly, and to love mercy, and to walk humbly with thy God?',
      'Deuteronomy 31:6 - Be strong and of a good courage; fear not, nor be afraid.',
      'Psalm 91:1 - He that dwelleth in the secret place of the most High shall abide under the shadow of the Almighty.',
      'Galatians 5:22-23 - The fruit of the Spirit is love, joy, peace, longsuffering, gentleness, goodness, faith, meekness, temperance.',
      'John 16:33 - In the world ye shall have tribulation: but be of good cheer; I have overcome the world.',
      'Psalm 121:1-2 - I will lift up mine eyes unto the hills, from whence cometh my help.',
      'Romans 10:9 - If thou shalt confess with thy mouth the Lord Jesus, and shalt believe in thine heart that God hath raised him from the dead, thou shalt be saved.',
      'Proverbs 16:3 - Commit thy works unto the Lord, and thy thoughts shall be established.',
      'Psalm 100:5 - For the Lord is good; his mercy is everlasting.',
      'Isaiah 26:3 - Thou wilt keep him in perfect peace, whose mind is stayed on thee.',
      'Hebrews 13:8 - Jesus Christ the same yesterday, and to day, and for ever.'
  ];

  function updateVerse() {
    const verse = verses[new Date().getHours() % verses.length];
    verseElements.forEach(function (element) {
      // Trigger animation
      if (element.parentElement && element.parentElement.classList.contains('bible-verse-pill')) {
        element.parentElement.classList.remove('verse-animating');
        void element.parentElement.offsetWidth; // trigger reflow
        element.parentElement.classList.add('verse-animating');
      }

      element.textContent = verse;
      if (element.parentElement) {
        element.parentElement.setAttribute('title', verse);
      }
    });
  }

  updateVerse();
  setInterval(updateVerse, 60 * 1000);
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
  const audio = new Audio('/sound/logout.mp3');
  audio.play().catch(function(e) { console.log("Audio play failed:", e); });

  const modal = document.getElementById('logoutModal');
  if (!modal || !window.bootstrap) return;

  new window.bootstrap.Modal(modal).show();
}

function initDashboardPage() {
  const dateDisplays = document.querySelectorAll('.currentDateDisplay');

  const pathSegments = window.location.pathname.split('/');
  const pageId = pathSegments[pathSegments.length - 1];
  const validPages = readJsonScript('valid-dashboard-pages-data', []);

  if (validPages.includes(pageId)) {
    const sidebarItem = document.querySelector('.sidebar li[data-page-target="' + pageId + '"]');
    if (sidebarItem) showPage(pageId, sidebarItem);
  }

  if (dateDisplays.length > 0) {
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = new Date().toLocaleDateString('en-US', dateOptions);
    dateDisplays.forEach(el => el.textContent = formattedDate);
  }

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

  // When stock_unit changes in the add/edit form, keep hidden issue_unit in sync
  document.addEventListener('change', function(e) {
    if (e.target && e.target.name === 'stock_unit' && e.target.form && e.target.form.id === 'inventoryForm') {
      var hiddenIssueUnit = e.target.form.querySelector('[name="issue_unit"]');
      if (hiddenIssueUnit) hiddenIssueUnit.value = e.target.value;
    }
  });
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

const TX_PER_PAGE = 5;
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
  const itemOptions = allInventoryItems.map(function (item) {
    const displayUnit = item.issue_unit || item.unit || 'pcs';
    const displayStock = item.display_stock || `${item.stock} ${displayUnit}`;
    const bulkText = item.bulk_equivalent ? `, ${item.bulk_equivalent}` : '';
    return `<option value="${item.id}">${item.code} - ${item.name} (${displayStock}${bulkText})</option>`;
  }).join('');

  const row = document.createElement('div');
  row.className = 'stock-item-card';
  row.id = `item-row-${idx}`;
  row.innerHTML = `
    <div class="stock-item-card-header">
      <span>Item Details</span>
      <button type="button" class="stock-item-remove" data-remove-row="${idx}" title="Remove" aria-label="Remove item row"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="stock-item-field stock-item-search">
      <select name="items[${idx}][inventory_item_id]" class="form-select" required>
        <option value="">Search Item...</option>
        ${itemOptions}
      </select>
    </div>

    <div class="stock-item-two-col">
      <div class="stock-item-field stock-item-type">
        <label class="form-label">Type</label>
        <input type="text" class="form-control bg-light text-muted fw-bold" value="STOCK IN" readonly>
        <input type="hidden" name="items[${idx}][type]" value="in">
      </div>
      <div class="stock-item-field stock-item-quantity">
        <label class="form-label">Quantity</label>
        <input type="number" name="items[${idx}][quantity]" min="1" class="form-control" required>
      </div>
    </div>

    <div class="stock-item-field stock-item-handled">
      <label class="form-label">Handled By:</label>
      <input type="text" name="items[${idx}][handled_by]" class="form-control bg-light text-muted fw-bold" required value="${stockManagementPage?.dataset.adminName || ''}" readonly>
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
      placeholder: "Search Item..."
    });
  }

  const totalCards = container.querySelectorAll('.stock-item-card').length;
  window.currentBatchPage = Math.ceil(totalCards / 5);
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
  const BATCH_ITEMS_PER_PAGE = 5;
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
        barPercentage: 0.8,
        categoryPercentage: 0.8,
        maxBarThickness: 40,
        borderRadius: 4,
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
        y: { display: true, grid: { display: false }, ticks: { font: { family: "'Inter', sans-serif" } } }
      }
    }
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

  // Compute top stock-out items for Most Consumed chart
  var itemStockOutMap = {};
  rawTx.forEach(function(tx) {
    if (String(tx.type || '').toLowerCase() !== 'out') return;
    var txId = tx.inventory_item_id;
    if (!itemStockOutMap[txId]) itemStockOutMap[txId] = 0;
    itemStockOutMap[txId] += Number(tx.quantity);
  });
  var itemNamesMap = {};
  items.forEach(function(item) { itemNamesMap[item.id] = item.name; });
  var consumedItems = Object.entries(itemStockOutMap)
    .sort(function(a, b) { return b[1] - a[1]; })
    .slice(0, 10)
    .map(function(entry) { return { name: itemNamesMap[entry[0]] || 'Unknown', qty: entry[1] }; });

  initMonthlyReport();

  // Most Consumed Items (Top 10 by Stock-Out) chart
  var mostConsumedCanvas = document.getElementById('mostConsumedChart');
  if (mostConsumedCanvas && consumedItems.length > 0) {
    new window.Chart(mostConsumedCanvas, {
      type: 'bar',
      data: {
        labels: consumedItems.map(function(d) { return d.name; }),
        datasets: [{
          data: consumedItems.map(function(d) { return d.qty; }),
          backgroundColor: ['#ef4444','#f97316','#eab308','#22c55e','#14b8a6','#3b82f6','#8b5cf6','#ec4899','#64748b','#f59e0b'].slice(0, consumedItems.length),
          borderWidth: 0,
          barPercentage: 0.8,
          categoryPercentage: 0.8,
          maxBarThickness: 40,
          borderRadius: 4,
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
            callbacks: {
              label: function(ctx) { return ' ' + ctx.parsed.x + ' units dispensed'; }
            }
          },
        },
        scales: {
          x: { display: true, beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false } },
          y: { display: true, grid: { display: false }, ticks: { font: { family: "'Inter', sans-serif" } } }
        }
      }
    });
  }

  document.querySelectorAll('[data-action="print-report"]').forEach(function (button) {
    button.addEventListener('click', function () {
      const printContainer = document.getElementById('printPagesContainer');
      const originalParent = printContainer ? printContainer.parentNode : null;
      const originalNextSibling = printContainer ? printContainer.nextSibling : null;

      // Move printPagesContainer directly under body so it escapes all
      // parent layout containers (sidebar, main-content, col padding, etc.)
      if (printContainer) {
        printContainer.style.position = 'absolute';
        printContainer.style.top = '0';
        printContainer.style.left = '0';
        printContainer.style.width = '100%';
        printContainer.style.margin = '0';
        printContainer.style.padding = '0';
        document.body.appendChild(printContainer);
      }

      window.print();

      // Restore the container to its original location after printing
      if (printContainer && originalParent) {
        if (originalNextSibling) {
          originalParent.insertBefore(printContainer, originalNextSibling);
        } else {
          originalParent.appendChild(printContainer);
        }
        printContainer.style.position = '';
        printContainer.style.top = '';
        printContainer.style.left = '';
        printContainer.style.width = '';
        printContainer.style.margin = '';
        printContainer.style.padding = '';
      }
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

async function initMonthlyReport() {
  const reportType = document.getElementById('reportType');
  const reportMonthFilter = document.getElementById('reportMonthFilter');
  const reportQuarterFilter = document.getElementById('reportQuarterFilter');
  const reportYearFilter = document.getElementById('reportYearFilter');
  const monthlyFilters = document.getElementById('monthlyFilters');
  const quarterlyFilters = document.getElementById('quarterlyFilters');
  const reportUITitle = document.getElementById('reportUITitle');
  const reportPrintTitle = document.getElementById('reportPrintTitle');

  const reportTableBody = document.getElementById('reportTableBody');
  const reportMonthPrintLabel = document.getElementById('reportMonthPrintLabel');
  if (!reportMonthFilter || !reportTableBody || !reportMonthPrintLabel) return;

  const today = new Date();
  reportMonthFilter.value = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');

  // Dynamically populate year dropdown: 2024 → current year + 5
  if (reportYearFilter) {
    const startYear = 2024;
    const endYear   = today.getFullYear() + 5;
    reportYearFilter.innerHTML = '';
    for (let y = endYear; y >= startYear; y--) {
      const opt = document.createElement('option');
      opt.value = y;
      opt.textContent = y;
      reportYearFilter.appendChild(opt);
    }
    reportYearFilter.value = today.getFullYear();
  }
  
  if (reportType) {
      const applyReportTypeUI = () => {
          if (reportType.value === 'quarterly') {
              monthlyFilters.style.display = 'none';
              quarterlyFilters.style.display = 'flex';
              if (reportUITitle) reportUITitle.textContent = 'Quarterly Item Activity Report';
              if (reportPrintTitle) reportPrintTitle.textContent = 'PHRMDO INVENTORY QUARTERLY REPORT';
          } else {
              monthlyFilters.style.display = 'block';
              quarterlyFilters.style.display = 'none';
              if (reportUITitle) reportUITitle.textContent = 'Monthly Item Activity Report';
              if (reportPrintTitle) reportPrintTitle.textContent = 'PHRMDO INVENTORY MONTHLY REPORT';
          }
      };

      reportType.addEventListener('change', () => {
          applyReportTypeUI();
          renderMonthlyReport();
      });

      // Apply correct initial state on page load
      applyReportTypeUI();

      if (reportQuarterFilter) reportQuarterFilter.addEventListener('change', renderMonthlyReport);
      if (reportYearFilter) reportYearFilter.addEventListener('change', renderMonthlyReport);
      var reportSortEl = document.getElementById('reportSortOrder');
      if (reportSortEl) reportSortEl.addEventListener('change', renderMonthlyReport);

      ['sigNotedName', 'sigNotedPos', 'sigPreparedName', 'sigPreparedPos', 'sigApprovedName', 'sigApprovedPos'].forEach(id => {
          const el = document.getElementById(id);
          if (el) el.addEventListener('input', renderMonthlyReport);
      });
  }

  let currentReportData = {};

  let items = [];
  let rawTx = [];
  try {
    // const response = await fetch('/storage/reports/monthly_report.json');
    if (response.ok) {
        const data = await response.json();
        items = data.items || [];
        rawTx = data.transactions || [];
    } else {
        throw new Error("Failed to fetch JSON");
    }
  } catch (err) {
    items = readJsonScript('inventory-data', []);
    rawTx = readJsonScript('transactions-data', []);
  }

  function renderMonthlyReport() {
    let year, startMonth, endMonth, periodLabel;
    
    if (reportType && reportType.value === 'quarterly') {
        year = parseInt(reportYearFilter.value, 10);
        const q = reportQuarterFilter.value;
        if (q === 'Q1') { startMonth = 0; endMonth = 2; periodLabel = '1st Semester ' + year; }
        else if (q === 'Q2') { startMonth = 3; endMonth = 5; periodLabel = '2nd Semester ' + year; }
        else if (q === 'Q3') { startMonth = 6; endMonth = 8; periodLabel = '3rd Semester ' + year; }
        else { startMonth = 9; endMonth = 11; periodLabel = '4th Semester ' + year; }
    } else {
        const selectedDate = new Date(reportMonthFilter.value + '-01');
        year = selectedDate.getFullYear();
        startMonth = selectedDate.getMonth();
        endMonth = selectedDate.getMonth();
        periodLabel = selectedDate.toLocaleDateString('en-US', {
            month: 'long',
            year: 'numeric',
        });
    }

    reportMonthPrintLabel.textContent = periodLabel;

    // Build a map of item id -> item for quick lookup
    const itemsById = {};
    items.forEach(function (item) {
      itemsById[item.id] = item;
    });

    const stockIn = [];   // Admin stock-in entries
    const stockOut = [];  // User-request stock-out entries
    const involvedItemIds = new Set(); // Items that had activity in this month

    rawTx.forEach(function (tx) {
      const txDate = new Date(tx.created_at);
      const type = String(tx.type || '').toLowerCase();
      const qty = Number(tx.quantity);
      const ref = String(tx.reference || '');

      if (txDate.getFullYear() !== year || txDate.getMonth() < startMonth || txDate.getMonth() > endMonth) return;

      const dateStr = (txDate.getMonth() + 1) + '/' + txDate.getDate() + '/' + txDate.getFullYear();
      const item = itemsById[tx.inventory_item_id];
      const name = item ? item.name : 'Unknown';

      // Stock In: admin-driven (type === 'in')
      if (type === 'in') {
        stockIn.push({ date: dateStr, name: name, qty: qty, itemId: tx.inventory_item_id });
        involvedItemIds.add(tx.inventory_item_id);
      }

      // Stock Out: all out transactions
      if (type === 'out') {
        stockOut.push({ date: dateStr, name: name, qty: qty, itemId: tx.inventory_item_id });
        involvedItemIds.add(tx.inventory_item_id);
      }
    });

    // Merge stockOut rows with same date + item name (sum their quantities)
    const mergeRows = (rows) => {
      const map = new Map();
      rows.forEach(row => {
        const key = row.date + '||' + row.name;
        if (map.has(key)) {
          map.get(key).qty += row.qty;
        } else {
          map.set(key, { ...row });
        }
      });
      return Array.from(map.values());
    };

    const mergedStockOut = mergeRows(stockOut);
    const mergedStockIn  = mergeRows(stockIn);

    // Stock Balance: only items involved in the above transactions, with current stock
    const stockBalance = [];
    involvedItemIds.forEach(function (id) {
      const item = itemsById[id];
      if (item) {
        stockBalance.push({ name: item.name, qty: item.stock });
      }
    });

    // Apply sort order before rendering
    var reportSortOrder = document.getElementById('reportSortOrder');
    var sortVal = reportSortOrder ? reportSortOrder.value : 'date';
    if (sortVal === 'alpha-asc') {
      mergedStockIn.sort(function(a, b) { return a.name.localeCompare(b.name); });
      mergedStockOut.sort(function(a, b) { return a.name.localeCompare(b.name); });
      stockBalance.sort(function(a, b) { return a.name.localeCompare(b.name); });
    } else if (sortVal === 'alpha-desc') {
      mergedStockIn.sort(function(a, b) { return b.name.localeCompare(a.name); });
      mergedStockOut.sort(function(a, b) { return b.name.localeCompare(a.name); });
      stockBalance.sort(function(a, b) { return b.name.localeCompare(a.name); });
    }

    const maxRows = Math.max(mergedStockIn.length, mergedStockOut.length, stockBalance.length);
    let html = '';
    
    // Build table rows for screen
    if (maxRows === 0) {
      html = '<tr><td colspan="8" class="text-center py-5 text-muted">No activity found for the selected month.</td></tr>';
    } else {
      for (let i = 0; i < maxRows; i++) {
        const inData  = mergedStockIn[i]  || { date: '', name: '', qty: '' };
        const outData = mergedStockOut[i] || { date: '', name: '', qty: '' };
        const balData = stockBalance[i] || { name: '', qty: '' };

        html += `
          <tr>
            <td class="report-cell report-cell-in">${inData.date}</td>
            <td class="report-cell report-cell-in">${inData.name}</td>
            <td class="report-cell report-cell-in text-center">${inData.qty !== '' ? inData.qty : ''}</td>
            <td class="report-cell report-cell-out">${outData.date}</td>
            <td class="report-cell report-cell-out">${outData.name}</td>
            <td class="report-cell report-cell-out text-center">${outData.qty !== '' ? outData.qty : ''}</td>
            <td class="report-cell report-cell-bal">${balData.name}</td>
            <td class="report-cell report-cell-bal text-center">${balData.qty !== '' ? balData.qty : ''}</td>
          </tr>
        `;
      }
    }
    reportTableBody.innerHTML = html;

    // --- Build Pagination for Print ---
    function buildPageHeaderHtml() {
      const isQuarterly = reportType && reportType.value === 'quarterly';
      const reportTitleStr = `PHRMDO INVENTORY ${isQuarterly ? 'QUARTERLY' : 'MONTHLY'} REPORT`;

      return `
          <div class="print-header-top" style="margin: 0; padding: 0; text-align: center;">
            <img src="/images/GovMail Header.png" alt="GovMail Header" style="width: 100%; height: auto; display: block; margin: 0; padding: 0;">
          </div>
          <div class="print-main-title" style="margin: 4px 0;">${reportTitleStr}</div>
          <div class="print-month-label" style="text-align: center; font-weight: bold; font-family: Arial, sans-serif; font-size: 13px; margin-bottom: 6px;">${periodLabel}</div>
        `;
    }

    currentReportData = {
      period: periodLabel,
      items: [
        ...mergedStockIn.map(i => ({ type: 'Stock In', ...i })),
        ...mergedStockOut.map(i => ({ type: 'Stock Out', ...i }))
      ]
    };
    const printContainer = document.getElementById('printPagesContainer');
    if (!printContainer) return;
    
    let printHtml = '';
    const itemsPerPage = 19;
    const totalPages = Math.max(1, Math.ceil(maxRows / itemsPerPage));

    for (let page = 0; page < totalPages; page++) {
      printHtml += `<div class="print-page" style="page-break-after: always; padding: 0; margin: 0; display: flex; flex-direction: column; min-height: 94vh; position: relative;">` ;
      printHtml += buildPageHeaderHtml();
      
      printHtml += `
        <div class="table-responsive print-page-table-wrap" style="overflow: visible;">
          <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
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
            <tbody>
      `;

      for (let r = 0; r < itemsPerPage; r++) {
        const i = page * itemsPerPage + r;
        if (i >= maxRows) break;
        
        const inData  = mergedStockIn[i]  || { date: '', name: '', qty: '' };
        const outData = mergedStockOut[i] || { date: '', name: '', qty: '' };
        const balData = stockBalance[i] || { name: '', qty: '' };

        printHtml += `
          <tr>
            <td class="report-cell report-cell-in" style="border: 1px solid #000; padding: 4px; font-size: 10px;">${inData.date}</td>
            <td class="report-cell report-cell-in" style="border: 1px solid #000; padding: 4px; font-size: 10px;">${inData.name}</td>
            <td class="report-cell report-cell-in text-center" style="border: 1px solid #000; padding: 4px; font-size: 10px;">${inData.qty !== '' ? inData.qty : ''}</td>
            <td class="report-cell report-cell-out" style="border: 1px solid #000; padding: 4px; font-size: 10px;">${outData.date}</td>
            <td class="report-cell report-cell-out" style="border: 1px solid #000; padding: 4px; font-size: 10px;">${outData.name}</td>
            <td class="report-cell report-cell-out text-center" style="border: 1px solid #000; padding: 4px; font-size: 10px;">${outData.qty !== '' ? outData.qty : ''}</td>
            <td class="report-cell report-cell-bal" style="border: 1px solid #000; padding: 4px; font-size: 10px;">${balData.name}</td>
            <td class="report-cell report-cell-bal text-center" style="border: 1px solid #000; padding: 4px; font-size: 10px;">${balData.qty !== '' ? balData.qty : ''}</td>
          </tr>
        `;
      }

      printHtml += `
            </tbody>
          </table>
        </div>
      `;

      if (page === totalPages - 1) {
          const sigNotedName = document.getElementById('sigNotedName') ? document.getElementById('sigNotedName').value : 'MAMARETO B. GESTA JR.';
          const sigNotedPos = document.getElementById('sigNotedPos') ? document.getElementById('sigNotedPos').value : 'Admin. Officer IV';
          const sigPreparedName = document.getElementById('sigPreparedName') ? document.getElementById('sigPreparedName').value : '';
          const sigPreparedPos = document.getElementById('sigPreparedPos') ? document.getElementById('sigPreparedPos').value : 'POSITION';
          const sigApprovedName = document.getElementById('sigApprovedName') ? document.getElementById('sigApprovedName').value : 'MILA B. LISONDRA';
          const sigApprovedPos = document.getElementById('sigApprovedPos') ? document.getElementById('sigApprovedPos').value : 'OIC - PHRMDO';

          printHtml += `
          <div style="display: flex; justify-content: space-between; margin-top: auto; padding-bottom: 20px; font-family: Arial, sans-serif; font-size: 13px; color: #000; padding: 20px 10px 0 10px;">
              <div style="text-align: center; width: 30%;">
                  <div style="text-align: left; margin-bottom: 25px; font-weight: bold;">Noted by:</div>
                  <div style="font-weight: bold; text-decoration: underline; text-transform: uppercase;">${sigNotedName}</div>
                  <div>${sigNotedPos}</div>
              </div>
              <div style="text-align: center; width: 30%;">
                  <div style="text-align: left; margin-bottom: 25px; font-weight: bold;">Prepared by:</div>
                  <div style="font-weight: bold; text-decoration: underline; text-transform: uppercase; min-height: 18px;">${sigPreparedName}</div>
                  <div>${sigPreparedPos}</div>
              </div>
              <div style="text-align: center; width: 30%;">
                  <div style="text-align: left; margin-bottom: 25px; font-weight: bold;">Approved by:</div>
                  <div style="font-weight: bold; text-decoration: underline; text-transform: uppercase;">${sigApprovedName}</div>
                  <div>${sigApprovedPos}</div>
              </div>
          </div>
          `;
      }

      printHtml += `</div>`;
    }
    
    printContainer.innerHTML = printHtml;


  }

  document.querySelectorAll('[data-action="export-json"]').forEach(function (button) {
    button.addEventListener('click', function () {
      const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(currentReportData, null, 2));
      const downloadAnchorNode = document.createElement('a');
      downloadAnchorNode.setAttribute("href", dataStr);
      downloadAnchorNode.setAttribute("download", "Activity_Report_" + currentReportData.period.replace(/\s+/g, '_') + ".json");
      document.body.appendChild(downloadAnchorNode);
      downloadAnchorNode.click();
      downloadAnchorNode.remove();
    });
  });

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
