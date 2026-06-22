const kioskCart = {};
let kioskBeepAudio = null;

function kioskPlayBeep() {
  const kioskSection = document.getElementById('kiosk-section');
  const beepUrl = kioskSection ? kioskSection.dataset.beepUrl : '';
  if (!beepUrl) return;

  if (!kioskBeepAudio) {
    kioskBeepAudio = new Audio(beepUrl);
    kioskBeepAudio.preload = 'auto';
  }

  kioskBeepAudio.pause();
  kioskBeepAudio.currentTime = 4;
  kioskBeepAudio.play().catch(function() {});
}

function kioskUpdateUI() {
  const body    = document.getElementById('kioskCartBody');
  const empty   = document.getElementById('kioskCartEmpty');
  const count   = document.getElementById('kioskCount');
  const submit  = document.getElementById('kioskSubmit');
  const nameEl  = document.getElementById('kioskName');
  const divEl   = document.getElementById('kioskDiv');
  const ids     = Object.keys(kioskCart);

  count.textContent = ids.length;

  // Remove old rows + hidden inputs
  body.querySelectorAll('.kiosk-row').forEach(r => r.remove());
  document.querySelectorAll('.kiosk-hi').forEach(i => i.remove());

  empty.style.display = ids.length === 0 ? '' : 'none';

  ids.forEach(function(id) {
    const e = kioskCart[id];
    const row = document.createElement('div');
    row.className = 'kiosk-row';
    row.innerHTML = `
      <div class="kiosk-row-name">
        ${e.name}
        <div class="kiosk-row-unit">${e.unit} &bull; max ${e.stock}</div>
      </div>
      <div class="kiosk-qty">
        <button type="button" class="kiosk-qty-btn" onclick="kioskQty('${id}',-1)">&#8722;</button>
        <span class="kiosk-qty-val" id="kqv_${id}">${e.qty}</span>
        <button type="button" class="kiosk-qty-btn" onclick="kioskQty('${id}',1)">&#43;</button>
      </div>
      <button type="button" class="kiosk-rem" onclick="kioskRem('${id}')" title="Remove">
        <i class="fas fa-times"></i>
      </button>
    `;
    body.appendChild(row);

    // Hidden inputs
    const hId = document.createElement('input');
    hId.type = 'hidden'; hId.name = `items[${id}][id]`; hId.value = id;
    hId.className = 'kiosk-hi';
    document.getElementById('kioskForm').appendChild(hId);

    const hQty = document.createElement('input');
    hQty.type = 'hidden'; hQty.name = `items[${id}][quantity]`; hQty.value = e.qty;
    hQty.id = `khq_${id}`; hQty.className = 'kiosk-hi';
    document.getElementById('kioskForm').appendChild(hQty);
  });

  const canSubmit = ids.length > 0
    && (nameEl ? nameEl.value.trim() : '').length > 0
    && (divEl  ? divEl.value.trim()  : '').length > 0;
  submit.disabled = !canSubmit;
}

function kioskCheckValid() {
  kioskUpdateUI();
}

function kioskAdd(btn) {
  kioskPlayBeep();

  const card  = btn.closest('.kiosk-card');
  const id    = card.dataset.id;
  const name  = card.dataset.name;
  const unit  = card.dataset.unit;
  const stock = parseInt(card.dataset.stock, 10);

  if (kioskCart[id]) {
    kioskCart[id].qty = Math.min(kioskCart[id].qty + 1, stock);
  } else {
    kioskCart[id] = { id, name, unit, stock, qty: 1 };
    card.classList.add('in-cart');
    btn.innerHTML = '<i class="fas fa-check"></i> Added';
  }
  kioskUpdateUI();
}

function kioskQty(id, delta) {
  if (!kioskCart[id]) return;
  kioskPlayBeep();
  const nq = kioskCart[id].qty + delta;
  if (nq < 1) { kioskRem(id, false); return; }
  kioskCart[id].qty = Math.min(nq, kioskCart[id].stock);
  const v = document.getElementById(`kqv_${id}`);
  const h = document.getElementById(`khq_${id}`);
  if (v) v.textContent = kioskCart[id].qty;
  if (h) h.value = kioskCart[id].qty;
}

function kioskRem(id, playSound = true) {
  if (!kioskCart[id]) return;
  if (playSound) kioskPlayBeep();
  delete kioskCart[id];
  const card = document.querySelector(`.kiosk-card[data-id="${id}"]`);
  if (card) {
    card.classList.remove('in-cart');
    const btn = card.querySelector('.kiosk-add-btn');
    if (btn) btn.innerHTML = '<i class="fas fa-plus"></i> Add';
  }
  kioskUpdateUI();
}

function kioskEscapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, function(char) {
    return {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    }[char];
  });
}

function kioskScheduleReceiptRemoval(receiptModal) {
  if (!receiptModal) return;

  setTimeout(function() {
    receiptModal.classList.add('is-hiding');
    setTimeout(function() {
      receiptModal.remove();
    }, 250);
  }, 5000);
}

function kioskRenderReceipt(receipt) {
  const oldReceipt = document.getElementById('kioskReceiptModal');
  if (oldReceipt) oldReceipt.remove();

  const receiptModal = document.createElement('div');
  receiptModal.className = 'kiosk-receipt-overlay';
  receiptModal.id = 'kioskReceiptModal';
  receiptModal.setAttribute('role', 'dialog');
  receiptModal.setAttribute('aria-modal', 'true');
  receiptModal.setAttribute('aria-labelledby', 'kioskReceiptTitle');

  const itemsHtml = (receipt.items || []).map(function(item) {
    return `
      <div class="kiosk-receipt-item">
        <span>${kioskEscapeHtml(item.name)}</span>
        <strong>${kioskEscapeHtml(item.quantity)} ${kioskEscapeHtml(item.unit)}</strong>
      </div>
    `;
  }).join('');

  receiptModal.innerHTML = `
    <div class="kiosk-receipt-card">
      <button type="button" class="kiosk-receipt-close" aria-label="Close receipt">
        <i class="fas fa-times"></i>
      </button>
      <div class="kiosk-receipt-head">
        <div class="kiosk-receipt-check"><i class="fas fa-check"></i></div>
        <div>
          <div class="kiosk-receipt-title" id="kioskReceiptTitle">Stock Out Receipt</div>
          <div class="kiosk-receipt-sub">Submitted successfully</div>
        </div>
      </div>
      <div class="kiosk-receipt-meta">
        <div><span>Receipt No.</span><strong>${kioskEscapeHtml(receipt.number)}</strong></div>
        <div><span>Date</span><strong>${kioskEscapeHtml(receipt.submitted_at)}</strong></div>
        <div><span>Name</span><strong>${kioskEscapeHtml(receipt.requester_name)}</strong></div>
        <div><span>Division</span><strong>${kioskEscapeHtml(receipt.division)}</strong></div>
      </div>
      <div class="kiosk-receipt-items">${itemsHtml}</div>
      <div class="kiosk-receipt-total">
        <span>Total Quantity</span>
        <strong>${kioskEscapeHtml(receipt.total_quantity)}</strong>
      </div>
    </div>
  `;

  document.body.appendChild(receiptModal);
  receiptModal.querySelector('.kiosk-receipt-close').addEventListener('click', function() {
    receiptModal.remove();
  });
  kioskScheduleReceiptRemoval(receiptModal);
}

function kioskUpdateSubmittedStocks(receipt) {
  (receipt.items || []).forEach(function(item) {
    const card = document.querySelector(`.kiosk-card[data-id="${item.id}"]`);
    if (!card) return;

    const remainingStock = parseInt(item.remaining_stock, 10);
    if (Number.isNaN(remainingStock)) return;

    card.dataset.stock = String(remainingStock);
    const stockText = card.querySelector('.kiosk-card-stock');
    if (stockText) {
      const dot = stockText.querySelector('.kiosk-stock-dot');
      stockText.textContent = '';
      if (dot) stockText.appendChild(dot);
      stockText.appendChild(document.createTextNode(` ${item.remaining_display_stock || remainingStock} available`));
    }

    let bulkText = card.querySelector('.kiosk-card-bulk');
    if (item.bulk_equivalent) {
      if (!bulkText) {
        bulkText = document.createElement('div');
        bulkText.className = 'kiosk-card-bulk';
        card.insertBefore(bulkText, card.querySelector('.kiosk-add-btn'));
      }
      bulkText.textContent = item.bulk_equivalent;
    } else if (bulkText) {
      bulkText.remove();
    }

    if (kioskCart[item.id]) {
      kioskCart[item.id].stock = remainingStock;
    }
    if (remainingStock <= 0) {
      delete kioskCart[item.id];
      card.style.display = 'none';
    }
  });
}

function kioskResetAfterSubmit() {
  Object.keys(kioskCart).forEach(function(id) {
    delete kioskCart[id];
  });

  document.querySelectorAll('.kiosk-card.in-cart').forEach(function(card) {
    card.classList.remove('in-cart');
    const btn = card.querySelector('.kiosk-add-btn');
    if (btn) btn.innerHTML = '<i class="fas fa-plus"></i> Add';
  });

  const nameEl = document.getElementById('kioskName');
  const divEl = document.getElementById('kioskDiv');
  if (nameEl) nameEl.value = '';
  if (divEl) divEl.value = '';

  kioskUpdateUI();
  window.dispatchEvent(new Event('kiosk:layout-change'));
}

function kioskShowSubmitErrors(errors) {
  const cleanErrors = Array.isArray(errors) ? errors : ['Stock out could not be submitted. Please try again.'];
  alert(cleanErrors.map(function(error) {
    const div = document.createElement('div');
    div.innerHTML = error;
    return div.textContent || div.innerText || error;
  }).join('\n'));
}

function kioskSubmitAjax(form) {
  const submit = document.getElementById('kioskSubmit');
  const originalSubmitHtml = submit ? submit.innerHTML : '';
  if (submit) {
    submit.disabled = true;
    submit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
  }

  fetch(form.action, {
    method: 'POST',
    body: new FormData(form),
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
    .then(function(response) {
      return response.json().then(function(data) {
        if (!response.ok) throw data;
        return data;
      });
    })
    .then(function(data) {
      if (!data.ok || !data.receipt) {
        kioskShowSubmitErrors(data.errors);
        return;
      }

      kioskUpdateSubmittedStocks(data.receipt);
      kioskResetAfterSubmit();
      kioskRenderReceipt(data.receipt);
    })
    .catch(function(error) {
      const validationErrors = error && error.errors && !Array.isArray(error.errors)
        ? Object.values(error.errors).flat()
        : error.errors;
      kioskShowSubmitErrors(validationErrors);
      kioskUpdateUI();
    })
    .finally(function() {
      if (submit) {
        submit.innerHTML = originalSubmitHtml;
      }
    });
}

function toggleKioskFullscreen() {
  const elem = document.documentElement;
  const btnIcon = document.querySelector('.kiosk-fullscreen-btn i');
  
  if (!document.fullscreenElement) {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) { /* Safari */
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) { /* IE11 */
      elem.msRequestFullscreen();
    }
    if(btnIcon) {
        btnIcon.classList.remove('fa-expand');
        btnIcon.classList.add('fa-compress');
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) { /* Safari */
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) { /* IE11 */
      document.msExitFullscreen();
    }
    if(btnIcon) {
        btnIcon.classList.remove('fa-compress');
        btnIcon.classList.add('fa-expand');
    }
  }
}

function handleFullscreenChange() {
  const isFS = document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement;
  if (isFS) {
    document.body.classList.add('kiosk-is-fullscreen');
    document.documentElement.classList.add('kiosk-is-fullscreen');
  } else {
    document.body.classList.remove('kiosk-is-fullscreen');
    document.documentElement.classList.remove('kiosk-is-fullscreen');
  }
  window.dispatchEvent(new Event('kiosk:layout-change'));
}

window.kioskAdd = kioskAdd;
window.kioskQty = kioskQty;
window.kioskRem = kioskRem;
window.kioskCheckValid = kioskCheckValid;
window.toggleKioskFullscreen = toggleKioskFullscreen;

document.addEventListener('fullscreenchange', handleFullscreenChange);
document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
document.addEventListener('msfullscreenchange', handleFullscreenChange);

document.addEventListener('DOMContentLoaded', function() {
  const kioskForm = document.getElementById('kioskForm');
  if (kioskForm) {
    kioskForm.addEventListener('submit', function(event) {
      event.preventDefault();
      kioskPlayBeep();
      sessionStorage.setItem('kioskReturnTab', 'true');
      sessionStorage.setItem(
        'kioskReturnFullscreen',
        document.body.classList.contains('kiosk-is-fullscreen') ? 'true' : 'false'
      );
      kioskSubmitAjax(kioskForm);
    });
  }

  const receiptModal = document.getElementById('kioskReceiptModal');
  if (receiptModal) {
    kioskScheduleReceiptRemoval(receiptModal);
  }

  ['kioskName','kioskDiv'].forEach(function(id) {
    const el = document.getElementById(id);
    if(el) {
      el.addEventListener('input', kioskCheckValid);
      el.addEventListener('change', kioskCheckValid);
    }
  });
  
  // Real-time clock for Ticker
  function updateTickerClock() {
    const clockEl = document.getElementById('tickerClock');
    if(clockEl) {
      clockEl.innerText = new Date().toLocaleTimeString('en-US');
    }
  }
  setInterval(updateTickerClock, 1000);
  updateTickerClock();

  // Fix mobile touch — make entire card tappable (fires Add button)
  document.addEventListener('touchend', function(e) {
    const card = e.target.closest('.kiosk-card');
    if (!card) return;
    // If the touch ended directly on the card (not on the button)
    if (!e.target.closest('.kiosk-add-btn')) {
      e.preventDefault();
      const btn = card.querySelector('.kiosk-add-btn');
      if (btn) kioskAdd(btn);
    }
  }, { passive: false });

  const si = document.getElementById('kioskSearch');
  const cf = document.getElementById('kioskCatFilter');
  let kioskCurrentPage = 1;
  const kioskItemsPerPage = 14;
  
  window.kioskChangePage = function(delta) {
    kioskCurrentPage += delta;
    doFilter();
  };

  function doFilter() {
    const t = si ? si.value.toLowerCase() : '';
    const c = cf ? cf.value.toLowerCase() : '';
    
    const allCards = Array.from(document.querySelectorAll('.kiosk-card'));
    const matchedCards = allCards.filter(function(card) {
      const nm = (card.dataset.name || '').toLowerCase();
      const ca = (card.dataset.category || '').toLowerCase();
      return (t === '' || nm.includes(t) || ca.includes(t)) && (c === '' || ca === c);
    });

    const totalPages = Math.ceil(matchedCards.length / kioskItemsPerPage) || 1;
    if (kioskCurrentPage > totalPages) kioskCurrentPage = totalPages;
    if (kioskCurrentPage < 1) kioskCurrentPage = 1;

    const startIndex = (kioskCurrentPage - 1) * kioskItemsPerPage;
    const endIndex = startIndex + kioskItemsPerPage;

    allCards.forEach(card => card.style.display = 'none');
    
    matchedCards.slice(startIndex, endIndex).forEach(function(card) {
      card.style.display = '';
    });

    // Update Pagination UI
    const pagContainer = document.getElementById('kioskPagination');
    if (pagContainer) {
        if(matchedCards.length > kioskItemsPerPage) {
            pagContainer.style.setProperty('display', 'flex', 'important');
            document.getElementById('kioskPageInfo').textContent = `Page ${kioskCurrentPage} of ${totalPages}`;
            document.getElementById('kioskPrevPage').disabled = kioskCurrentPage === 1;
            document.getElementById('kioskNextPage').disabled = kioskCurrentPage === totalPages;
        } else {
            pagContainer.style.setProperty('display', 'none', 'important');
        }
    }
  }

  // Reset to page 1 on search/filter change
  function handleFilterChange() {
      kioskCurrentPage = 1;
      doFilter();
  }

  if (si) si.addEventListener('input', handleFilterChange);
  if (cf) cf.addEventListener('change', handleFilterChange);
  window.addEventListener('kiosk:layout-change', handleFilterChange);
  window.addEventListener('resize', function() {
    if (document.body.classList.contains('kiosk-is-fullscreen')) handleFilterChange();
  });
  
  // Initial render
  doFilter();

  document.querySelectorAll('.kiosk-card').forEach(function(card) {
    card.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        const btn = card.querySelector('.kiosk-add-btn');
        if (btn) kioskAdd(btn);
      }
    });
    // Click on card (not button) also triggers add
    card.addEventListener('click', function(e) {
      if (!e.target.closest('.kiosk-add-btn')) {
        const btn = card.querySelector('.kiosk-add-btn');
        if (btn) kioskAdd(btn);
      }
    });
  });
});
