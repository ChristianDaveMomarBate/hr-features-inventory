<div id="property-transfer" class="page {{ (isset($activePageId) && $activePageId === 'property-transfer') ? 'active-page' : '' }}">
    <div class="analytics-header d-flex justify-content-between align-items-center mb-3 no-print">
        <div>
            <h1 class="dashboard-title mb-0">
                <span class="dashboard-title-badge">StockWise - Property Transfer Management</span>
            </h1>
        </div>
        @include('InventoryDashboard.navbar')
        
    </div>

    <div class="row g-3 w-100 m-0" style="height: calc(95vh - 100px);">
        <div class="col-12 col-lg-5 h-100">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 fw-semibold"><i class="bi bi-arrow-left-right me-1"></i> Transfer Property</h6>
                            <small class="text-muted">Select property and transfer information</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary">New Transfer</span>
                    </div>
                </div>
                <div class="card-body overflow-auto">
                    <div class="mb-3 position-relative">
                        <label class="form-label small fw-semibold">Search Property</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                            <input type="text" id="transferPropertySearch" class="form-control" autocomplete="off" placeholder="Property No., description or PAR No.">
                            <button type="button" class="btn btn-outline-secondary d-none" id="clearTransferPropertyBtn" title="Clear selected property"><i class="bi bi-x-lg"></i></button>
                            <button type="button" class="btn btn-primary" id="selectTransferPropertyBtn"><i class="bi bi-list-ul me-1"></i> Select</button>
                        </div>
                        <div id="transferPropertySuggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index:1050;display:none;"></div>
                    </div>
                    <input type="hidden" id="transferProperty">
                    <div id="selectedPropertyInfo" class="border rounded-3 p-3 bg-light mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-primary">Property Information</span>
                            <span id="transferPropertyStatus" class="badge bg-success-subtle text-success">Active</span>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-6">
                                <span class="text-muted d-block">Property No.</span>
                                <strong id="selectedPropertyNo">—</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">PAR / ICS No.</span>
                                <strong id="selectedPAR">—</strong>
                            </div>
                            <div class="col-12">
                                <span class="text-muted d-block">Description</span>
                                <strong id="selectedDescription">—</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Date Acquired</span>
                                <strong id="selectedDateAcquired">—</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Quantity</span>
                                <strong id="selectedQuantity">—</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Unit Value</span>
                                <strong id="selectedUnitValue">—</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Total Cost</span>
                                <strong id="selectedTotalCost">—</strong>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded-3 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-light border-bottom">
                            <span class="small fw-semibold"><i class="bi bi-file-earmark-pdf me-1 text-danger"></i> Property Attachment</span>
                            <button type="button" class="btn btn-sm btn-light border d-none" id="openPropertyAttachmentBtn"><i class="bi bi-box-arrow-up-right me-1"></i> Open</button>
                        </div>
                        <div id="propertyAttachmentContainer" class="d-flex align-items-center justify-content-center bg-white" style="height:350px;">
                            <div id="propertyAttachmentEmpty" class="text-center text-muted"><i class="bi bi-file-earmark-pdf fs-2 d-block mb-2"></i><small>Select a property to view its attachment</small></div>
                            <iframe id="propertyAttachmentViewer" src="" class="w-100 h-100 border-0 d-none"></iframe>
                        </div>
                    </div>
                </div>
               <div class="card-footer bg-white border-0">
                    <div class="d-flex justify-content-end">
                        <button style="margin-top:-30px;" type="button" class="btn btn-primary btn-sm px-3" style id="addTransferPropertyBtn" title="Add Property"><i class="bi bi-plus-lg me-1"></i>Add Property</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-7 h-100">
            <form id="propertyTransferForm" class="h-100"> 
             @csrf
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    <i class="bi bi-file-earmark-text me-1"></i>
                                    Transfer Details
                                </h6>
                                <small class="text-muted">Review and manage property transfer records</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button"
                                        class="btn btn-outline-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#transferHistoryModal">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Transfer Transaction History
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body overflow-auto">
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-2">
                                    <small class="text-muted d-block">Transfer No.</small>
                                    <input type="text"
                                        id="transferNo"
                                        name="transfer_no"
                                        class="form-control form-control-sm border-0 shadow-none p-0 fw-semibold"
                                        placeholder="Enter transfer no.">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-1" >
                                    <small style="padding-left:5px;" class="text-muted d-block">Transfer Date</small>
                                    <input style="padding-left:5px;" type="datetime-local" id="displayTransferDate" name="transfer_date" class="form-control form-control-sm border-0 shadow-none">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-2">
                                    <small class="text-muted d-block">Items</small>
                                    <strong id="transferItemCount">0</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-2">
                                    <small class="text-muted d-block">Status</small>
                                    <span id="transferStatus" class="badge bg-warning-subtle text-warning">Pending</span>
                                </div>
                            </div>
                        </div>
                        <div class="property-details-card mb-3">
                            <div class="property-details-header">
                                <div>
                                    <div class="property-details-title">
                                        <i class="bi bi-box-seam me-2"></i>
                                        Property Details
                                    </div>

                                    <div class="property-details-subtitle">
                                        Properties included in this transfer
                                    </div>
                                </div>

                                <div class="property-count" id="transferPropertyCount">
                                    0 Properties
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table property-details-table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th width="45">#</th>
                                            <th>Property No.</th>
                                            <th>Description</th>
                                            <th>PAR / ICS</th>
                                            <th>Date Acquired</th>
                                            <th class="text-end">Qty.</th>
                                            <th class="text-end">Unit Value</th>
                                            <th class="text-end">Total Cost</th>
                                            <th>Condition</th>
                                            <th width="45"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="transferPropertyTableBody">
                                        <tr id="noTransferPropertyRow">
                                            <td colspan="10" class="empty-property-state">
                                                <div class="empty-property-icon">
                                                    <i class="bi bi-box-seam"></i>
                                                </div>
                                                <div class="empty-property-title">
                                                    No property selected
                                                </div>
                                                <div class="empty-property-text">
                                                    Select a property above to add it to this transfer.
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="rounded-3 p-3 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="bg-light text-primary rounded-2 px-2 py-1">
                                            <i class="bi bi-person-check"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold small">Current Accountability</div>
                                            <div class="text-muted" style="font-size:11px;">
                                                Current property holder
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold mb-1">
                                            Accountable Officer
                                        </label>
                                        <input type="text"
                                            id="currentAccountableOfficer"
                                            name="current_accountable_officer"
                                            class="form-control"
                                            placeholder="Current accountable officer">
                                    </div>

                                    <div>
                                        <label class="form-label small fw-semibold mb-1">
                                            Office / Division
                                        </label>
                                        <select id="currentOffice"
                                                name="current_accountable_officer_office"
                                                class="form-select">
                                            <option value="">Select current office...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="rounded-3 p-3 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="bg-light text-success rounded-2 px-2 py-1">
                                            <i class="bi bi-person-plus"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold small">Transfer To</div>
                                            <div class="text-muted" style="font-size:11px;">
                                                New property accountable officer
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold mb-1">
                                            Receiving Officer
                                        </label>
                                        <input type="text"
                                            id="receivingOfficer"
                                            name="transferto_accountable_officer"
                                            class="form-control"
                                            placeholder="Enter receiving accountable officer..."
                                            autocomplete="off">
                                    </div>

                                    <div>
                                        <label class="form-label small fw-semibold mb-1">
                                            Receiving Office / Division
                                        </label>
                                        <select id="receivingOffice"
                                                name="transferto_accountable_officer_office"
                                                class="form-select">
                                            <option value="">Select receiving office...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3 p-3 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="bg-light text-primary rounded-2 px-2 py-1">
                                    <i class="bi bi-clipboard-check"></i>
                                </div>
                                <div>
                                    <div class="small fw-semibold">Transfer Details</div>
                                    <div class="text-muted" style="font-size:11px;">
                                        Transfer information and property condition
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold mb-1">Reason for Transfer</label>
                                    <select id="transferReason"  name="transfer_reason" class="form-select">
                                        <option value="">Select reason...</option>
                                        <option value="Donation">Donation</option>
                                        <option value="Reassignment/Recalled">Reassignment/Recalled</option>
                                        <option value="Recolate">Recolate</option>
                                        <option value="Retirement/Resignation">Retirement/Resignation</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold mb-1">Transfer Remarks</label>
                                    <textarea id="transferRemarks"  name="transfer_remarks" class="form-control" rows="5" placeholder="Enter additional transfer remarks..."></textarea>
                                </div>
                            </div>
                        </div>
                       <div class="p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="small fw-semibold">Transfer Documents</div>
                                    <small class="text-muted">PTR, acknowledgment and supporting documents</small>
                                </div>
                                <div>
                                    <input type="file" id="transferDocumentInput" name="transfer_documents[]" class="d-none" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="uploadTransferDocumentBtn">
                                        <i class="bi bi-paperclip me-1"></i>
                                        Attach
                                    </button>
                                </div>
                            </div>
                            <div id="transferDocuments">
                                <div id="noTransferDocuments" class="text-center text-muted small py-3">
                                    <i class="bi bi-file-earmark fs-4 d-block mb-1"></i>
                                    No documents attached
                                </div>
                                <div id="transferDocumentList" class="row g-3">
                                </div>
                            </div>
                        </div>
                        <div class="p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-pen me-2 text-primary"></i>
                                    <span class="small fw-semibold">Transfer Approval & Acknowledgment</span>
                                </div>

                                <div class="row g-0">
                                    <div class="col-md-6 pe-4">
                                        <div class="small fw-semibold text-muted text-uppercase mb-3">
                                            Prepared By
                                        </div>

                                        <div class="mb-3">
                                            <input type="text" id="preparedBy" name="prepared_by" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 shadow-none" placeholder="Enter name of preparer">
                                        </div>

                                        <div>
                                            <label class="form-label small text-muted mb-1">Prepared Date</label>
                                            <input type="date" id="preparedDate" name="prepared_date" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 shadow-none">
                                        </div>
                                    </div>

                                    <div class="col-md-6 ps-4 border-start">
                                        <div class="small fw-semibold text-muted text-uppercase mb-3">
                                            Approved By
                                        </div>

                                        <div class="mb-3">
                                            <input type="text" id="approvedBy" name="approved_by" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 shadow-none" placeholder="Enter approving authority">
                                        </div>

                                        <div>
                                            <label class="form-label small text-muted mb-1">Approval Date</label>
                                            <input type="date" id="approvalDate"  name="approval_date" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 shadow-none">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Transfer must be acknowledged by the receiving officer.
                                </small>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success btn-sm" id="acknowledgeTransferBtn">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Acknowledge Transfer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    {{-- modal --}}
        <div class="modal fade" id="transferHistoryModal" tabindex="-1" aria-labelledby="transferHistoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-semibold" id="transferHistoryModalLabel">
                                <i class="bi bi-clock-history me-2 text-primary"></i>
                                Property Transfer Transaction History
                            </h5>
                            <small class="text-muted">Review pending and approved property transfer transactions</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <small class="text-muted d-block">Pending Transfers</small>
                                            <strong class="fs-5" id="pendingTransferCount">0</strong>
                                        </div>
                                        <div class="text-warning fs-4">
                                            <i class="bi bi-hourglass-split"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <small class="text-muted d-block">Approved Transfers</small>
                                            <strong class="fs-5" id="approvedTransferCount">0</strong>
                                        </div>
                                        <div class="text-success fs-4">
                                            <i class="bi bi-check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <small class="text-muted d-block">Total Transactions</small>
                                            <strong class="fs-5" id="totalTransferCount">0</strong>
                                        </div>
                                        <div class="text-primary fs-4">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <div class="input-group" style="max-width: 350px;">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="transferHistorySearch" class="form-control" placeholder="Search transfer no., property or employee...">
                            </div>
                            <select id="transferHistoryStatus" class="form-select" style="max-width: 180px;">
                                <option value="">All Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                            </select>
                            <button type="button" class="btn btn-light border" id="refreshTransferHistory">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Refresh
                            </button>
                        </div>
                        <div class="table-responsive border rounded-3">
                            <table class="table table-hover table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-3">#</th>
                                        <th>Transfer No.</th>
                                        <th>Transfer Date</th>
                                        <th>Curr. Accountable Officer</th>
                                        <th>TRF Accountable Officer</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="transferHistoryTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">
                                            <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                                            No transfer transactions found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center p-2 border-top">
                                <small class="text-muted" id="transferHistoryPaginationInfo"></small>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0" id="transferHistoryPagination"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <small class="text-muted me-auto">
                            <i class="bi bi-info-circle me-1"></i>
                            Pending transfers require approval before the property accountability is transferred.
                        </small>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="transfer-action-popover" id="transferActionPopover">
            <div class="transfer-action-header">
                <span>Transfer Actions</span>
                <button type="button" id="closeTransferActionPopover">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="transfer-action-body">
                <button type="button" class="transfer-action-btn approve" id="approveTransfer">
                    <i class="bi bi-check-circle"></i>
                    <span>Approve</span>
                </button>
                <button type="button" class="transfer-action-btn disapprove" id="disapproveTransfer">
                    <i class="bi bi-x-circle"></i>
                    <span>Disapprove</span>
                </button>
                <button type="button" class="transfer-action-btn cancel" id="cancelTransfer">
                    <i class="bi bi-slash-circle"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="transferAttachmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-paperclip me-2 text-primary"></i>
                        Transfer Attachments
                    </h5>
                    <small class="text-muted" id="attachmentTransferNo"></small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <div id="attachmentLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="small text-muted mt-2">
                        Loading attachments...
                    </div>
                </div>

                <div id="attachmentEmpty"
                     class="text-center py-5 d-none">
                    <i class="bi bi-paperclip fs-1 text-muted"></i>
                    <div class="fw-semibold mt-2">
                        No attachments found
                    </div>
                    <small class="text-muted">
                        This transfer has no attached documents.
                    </small>
                </div>

                <div id="attachmentList"
                     class="list-group d-none">
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    // History with filter table
    let transferHistoryPage = 1;
    let transferHistorySearchTimer;
    let selectedTransferId = null;

    // Mao ni ang function para kuhaon ang transfer history gikan sa server.
    // Apil diri ang search, status filter, ug pagination.
    function loadTransferHistory(page = 1) {
        transferHistoryPage = page;

        const search = $('#transferHistorySearch').val().trim();
        const status = $('#transferHistoryStatus').val();
        const tbody = $('#transferHistoryTableBody');

        // Ipakita ang loading habang nagkuha pa og data.
        tbody.html(`
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    <div class="spinner-border spinner-border-sm text-primary mb-2"></div>
                    <div>Loading transfer transactions...</div>
                </td>
            </tr>
        `);

        // AJAX request para makuha ang transfer history.
        $.ajax({
            url: "{{ route('property-transfer.history') }}",
            type: 'GET',
            data: {
                search: search,
                status: status,
                page: page
            },
            cache: false,
            success: function(response) {
                // Kung failed ang response, ipakita ang error message.
                if (!response.success) {
                    showTransferHistoryError();
                    return;
                }

                // I-update ang summary counts sa ibabaw sa table.
                $('#pendingTransferCount').text(response.counts.pending);
                $('#approvedTransferCount').text(response.counts.approved);
                $('#totalTransferCount').text(response.counts.total);

                // I-display ang mga transfer records sa table.
                renderTransferHistory(response.transfers, response.current_page);

                // I-display ang pagination sa ubos sa table.
                renderTransferHistoryPagination(response);
            },
            error: function(xhr) {
                // I-log ang AJAX error para dali ma-debug.
                console.error('TRANSFER HISTORY ERROR:', xhr);

                // Ipakita ang error state sa table.
                showTransferHistoryError();
            }
        });
    }

    // Mao ni ang function para i-display ang mga transfer history sa table.
    function renderTransferHistory(transfers, currentPage = 1) {
        const tbody = $('#transferHistoryTableBody');

        // Kung walay transfer records, ipakita ang empty state.
        if (!transfers.length) {
            tbody.html(`
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                        No transfer transactions found.
                    </td>
                </tr>
            `);

            // Limpyohan ang pagination kung walay records.
            $('#transferHistoryPagination').empty();
            $('#transferHistoryPaginationInfo').text('');

            return;
        }

        // I-compute ang starting number base sa current page.
        const startIndex = (currentPage - 1) * 10;
        let html = '';

        // I-loop ang tanang transfer records ug himoon og table rows.
        transfers.forEach(function(transfer, index) {

            // Lahi nga badge color depende sa transfer status.
            const statusClass = transfer.status === 'Approved'
                ? 'bg-success-subtle text-success'
                : 'bg-warning-subtle text-warning-emphasis';

            // I-format ang transfer date para mas readable.
            const transferDate = transfer.transfer_date
                ? new Date(transfer.transfer_date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                })
                : '—';

            // I-build ang table row para sa matag transfer.
            html += `
                <tr>
                    <td class="px-3">${startIndex + index + 1}</td>
                    <td class="fw-semibold">
                        ${escapeHtml(transfer.transfer_no)}

                        <button type="button"
                                class="transfer-help-btn"
                                onclick="viewTransferAttachments(${transfer.id})"
                                title="View attachments">
                            <i class="bi bi-question-circle-fill"></i>
                        </button>
                    </td>
                    <td>${transferDate}</td>
                    <td>
                        <div class="fw-semibold">
                            ${escapeHtml(transfer.curent_accountable_officer)}
                        </div>
                        <small class="text-muted">
                            ${escapeHtml(transfer.curent_accountable_officer_office)}
                        </small>
                    </td>
                    <td>
                        <div class="fw-semibold">
                            ${escapeHtml(transfer.transferto_accountable_officer)}
                        </div>
                        <small class="text-muted">
                            ${escapeHtml(transfer.transferto_accountable_officer_office)}
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary">
                            ${transfer.items ?? 0}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${
                            transfer.status==='Approved'
                                ? 'bg-success text-white'
                                : transfer.status==='Disapproved'
                                    ? 'bg-danger text-white'
                                    : transfer.status==='Cancelled'
                                        ? 'bg-secondary text-white'
                                        : 'bg-warning text-white'}">
                            ${escapeHtml(transfer.status)}
                        </span>
                    </td>
                    <td class="text-center">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary transfer-history-action"
                                data-id="${transfer.id}"
                                data-transfer-no="${escapeHtml(transfer.transfer_no)}">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        // I-display ang nahimo nga rows sa table.
        tbody.html(html);
    }
    
    function viewTransferAttachments(transferId) {

        const modalElement = document.getElementById('transferAttachmentModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

        $('#attachmentLoading').removeClass('d-none');
        $('#attachmentEmpty').addClass('d-none');
        $('#attachmentList').addClass('d-none').empty();
        $('#attachmentTransferNo').text('');

        modal.show();

        $.ajax({
            type: 'POST',
            url: "{{ route('property-transfer.history.AttachmentView') }}",
            data: {
                _token: "{{ csrf_token() }}",
                id: transferId
            },
            success: function(response) {

                $('#attachmentLoading').addClass('d-none');

                if (!response.success || !response.attachments || response.attachments.length === 0) {
                    $('#attachmentEmpty').removeClass('d-none');
                    return;
                }

                if (response.transfer_no) {
                    $('#attachmentTransferNo').text(
                        'Transfer No. ' + response.transfer_no
                    );
                }

                let html = '';

                response.attachments.forEach(function(file) {

                    const fileName = escapeHtml(file.name);
                    const fileUrl = escapeHtml(file.url);

                    html += `
                        <div class="list-group-item d-flex align-items-center justify-content-between py-3">

                            <div class="d-flex align-items-center gap-3">
                                <div class="text-primary fs-4">
                                    <i class="${getAttachmentIcon(file.name)}"></i>
                                </div>

                                <div>
                                    <div class="fw-semibold">
                                        ${fileName}
                                    </div>
                                    <small class="text-muted">
                                        ${file.type ?? 'Attachment'}
                                    </small>
                                </div>
                            </div>

                            <div>
                                <a href="${fileUrl}"
                                target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>
                                    View
                                </a>
                            </div>

                        </div>
                    `;
                });

                $('#attachmentList')
                    .html(html)
                    .removeClass('d-none');
            },

            error: function(xhr) {

                $('#attachmentLoading').addClass('d-none');

                $('#attachmentList')
                    .html(`
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Unable to load the attachments.
                        </div>
                    `)
                    .removeClass('d-none');

                console.error(xhr.responseText);
            }
        });
    }

    function getAttachmentIcon(fileName) {

    const extension = fileName.split('.').pop().toLowerCase();

    if (extension === 'pdf') {
        return 'bi bi-file-earmark-pdf-fill';
    }

    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
        return 'bi bi-file-earmark-image-fill';
    }

    if (['doc', 'docx'].includes(extension)) {
        return 'bi bi-file-earmark-word-fill';
    }

    if (['xls', 'xlsx'].includes(extension)) {
        return 'bi bi-file-earmark-excel-fill';
    }

    return 'bi bi-file-earmark-fill';
}

    // Kuhaa ang ID sa transfer nga gi-click ug ipakita ang action popover.
    $(document).on('click','.transfer-history-action',function(e){
        e.stopPropagation();

        selectedTransferId=$(this).attr('data-id');

        const popover=$('#transferActionPopover');
        const button=$(this);
        const offset=button.offset();

        popover.css({
            top:offset.top+button.outerHeight()+6,
            left:offset.left-popover.outerWidth()+button.outerWidth()
        }).fadeIn(120);
    });

    // I-close ang action popover.
    $('#closeTransferActionPopover').on('click',function(){
        $('#transferActionPopover').fadeOut(100);
        selectedTransferId=null;
    });

    // I-close ang popover kung mo-click sa gawas.
    $(document).on('click',function(e){
        if(!$(e.target).closest('#transferActionPopover,.transfer-history-action').length){
            $('#transferActionPopover').fadeOut(100);
            selectedTransferId=null;
        }
    });

    // Function para sa Approve, Disapprove ug Cancel.
    function performTransferAction(action){
        const transferId=selectedTransferId;
        if(!transferId){
            Swal.fire({
                icon:'warning',
                title:'No Transfer Selected',
                text:'Please select a transfer first.'
            });
            return;
        }
        $.ajax({
            url:"{{ route('property-transfer.history.action') }}",
            type:'POST',
            data:{
                _token:"{{ csrf_token() }}",
                id:transferId,
                action:action
            },
            beforeSend:function(){
                $('#approveTransfer,#disapproveTransfer,#cancelTransfer').prop('disabled',true);
            },
            success:function(response){
                if(!response.success){
                    Swal.fire({
                        icon:'warning',
                        title:'Action Failed',
                        text:response.message||'Unable to update transfer status.'
                    });
                    return;
                }

                $('#transferActionPopover').fadeOut(100);
                selectedTransferId=null;

                Swal.fire({
                    icon:'success',
                    title:'Status Updated',
                    text:response.message,
                    timer:1500,
                    showConfirmButton:false
                });

                loadTransferHistory(transferHistoryPage);
            },
            error:function(xhr){
                console.error('TRANSFER ACTION ERROR:',xhr);

                const response=xhr.responseJSON||{};
                const errors=response.errors||{};
                const message=Object.values(errors).flat().join('<br>')||response.message||'Unable to update transfer status.';

                Swal.fire({
                    icon:xhr.status===422?'warning':'error',
                    title:xhr.status===422?'Action Not Allowed':'Action Failed',
                    html:message
                });
            },
            complete:function(){
                $('#approveTransfer,#disapproveTransfer,#cancelTransfer').prop('disabled',false);
            }
        });
    }
    // Approve sa selected transfer.
    $('#approveTransfer').on('click',function(){
        performTransferAction('approve');
    });
    // Disapprove sa selected transfer.
    $('#disapproveTransfer').on('click',function(){
        performTransferAction('disapprove');
    });
    // Cancel sa selected transfer.
    $('#cancelTransfer').on('click',function(){
        performTransferAction('cancel');
    });

    // Render Pagination
    function renderTransferHistoryPagination(response){
        const pagination=$('#transferHistoryPagination');
        const info=$('#transferHistoryPaginationInfo');
        const currentPage=response.current_page;
        const lastPage=response.last_page;
        const total=response.total;

        // Kung walay records, limpyohan ang pagination ug information.
        if(!total){
            pagination.empty();
            info.text('');
            return;
        }

        const from=((currentPage-1)*10)+1;
        const to=Math.min(currentPage*10,total);

        // Ipakita kung pila ka records ang currently gipakita.
        info.text(`Showing ${from}–${to} of ${total} transfers`);

        let html=`
            <li class="page-item ${currentPage===1?'disabled':''}">
                <button type="button" class="page-link transfer-history-page" data-page="${currentPage-1}">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </li>
        `;

        // I-generate ang page numbers.
        for(let page=1;page<=lastPage;page++){
            html+=`
                <li class="page-item ${page===currentPage?'active':''}">
                    <button type="button" class="page-link transfer-history-page" data-page="${page}">
                        ${page}
                    </button>
                </li>
            `;
        }

        html+=`
            <li class="page-item ${currentPage===lastPage?'disabled':''}">
                <button type="button" class="page-link transfer-history-page" data-page="${currentPage+1}">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </li>
        `;

        // I-display ang generated pagination.
        pagination.html(html);
    }

    // Ipakita ang error kung dili ma-load ang transfer history.
    function showTransferHistoryError(){
        $('#transferHistoryTableBody').html(`
            <tr>
                <td colspan="8" class="text-center text-danger py-5">
                    <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                    Unable to load transfer transactions.
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="retryTransferHistory">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Try Again
                    </button>
                </div>
            </td>
        </tr>
    `);

    // Limpyohan ang pagination kung adunay error.
    $('#transferHistoryPagination').empty();
    $('#transferHistoryPaginationInfo').text('');
    }
    // I-protect ang HTML output aron malikayan ang unwanted HTML injection.
    function escapeHtml(value){
        return $('<div>').text(value??'').html();
    }
    // I-load ang transfer history pag-open sa modal.
    $('#transferHistoryModal').on('shown.bs.modal',function(){
        loadTransferHistory(1);
    });
    // I-refresh ang transfer history gikan sa first page.
    $('#refreshTransferHistory').on('click',function(){
        loadTransferHistory(1);
    });
    // I-filter ang transfer history base sa status.
    $('#transferHistoryStatus').on('change',function(){
        loadTransferHistory(1);
    });
    // I-search ang transfer history gamit ang delay aron dili sige og AJAX request.
    $('#transferHistorySearch').on('input',function(){
        clearTimeout(transferHistorySearchTimer);

        transferHistorySearchTimer=setTimeout(function(){
            loadTransferHistory(1);
        },400);
    });
    // I-retry ang previous page kung naay loading error.
    $(document).on('click','#retryTransferHistory',function(){
        loadTransferHistory(transferHistoryPage);
    });

    // I-load ang selected page sa pagination.
    $(document).on('click','.transfer-history-page',function(){
        const page=Number($(this).data('page'));

        if(page>=1){
            loadTransferHistory(page);
        }
    });
    //perform sa pag display sa attachment files
    let transferDocuments=[];

    function initTransferDocuments(){
        $('#uploadTransferDocumentBtn').off('click').on('click',function(){
            $('#transferDocumentInput').click();
        });
        $('#transferDocumentInput').off('change').on('change',function(){
            const files=Array.from(this.files);
            if(!files.length)return;
            files.forEach(function(file){
                const exists=transferDocuments.some(function(existingFile){
                    return existingFile.name===file.name&&
                        existingFile.size===file.size&&
                        existingFile.lastModified===file.lastModified;
                });
                if(!exists){
                    transferDocuments.push(file);
                }
            });
            renderTransferDocuments();
            this.value='';
        });
        $(document).off('click','.remove-transfer-document').on('click','.remove-transfer-document',function(){
            const index=$(this).data('index');
            transferDocuments.splice(index,1);
            renderTransferDocuments();
        });
    }

    //perform sa pag render sa tanang selected attachment files
    function renderTransferDocuments(){
        const container=$('#transferDocumentList');
        const emptyState=$('#noTransferDocuments');

        container.empty();

        if(transferDocuments.length===0){
            emptyState.show();
            return;
        }
        emptyState.hide();
        transferDocuments.forEach(function(file,index){
            const extension=file.name.split('.').pop().toLowerCase();
            let icon='bi-file-earmark';
            let iconClass='text-secondary';

            if(extension==='pdf'){
                icon='bi-file-earmark-pdf';
                iconClass='text-danger';
            }else if(['doc','docx'].includes(extension)){
                icon='bi-file-earmark-word';
                iconClass='text-primary';
            }else if(['xls','xlsx'].includes(extension)){
                icon='bi-file-earmark-excel';
                iconClass='text-success';
            }else if(['jpg','jpeg','png'].includes(extension)){
                icon='bi-file-earmark-image';
                iconClass='text-info';
            }

            const size=formatTransferDocumentSize(file.size);

            container.append(`
                <div class="transfer-document-card">
                    <button type="button" class="transfer-document-remove remove-transfer-document" data-index="${index}" title="Remove">
                        <i class="bi bi-x"></i>
                    </button>
                    <div class="transfer-document-icon">
                        <i class="bi ${icon} ${iconClass}"></i>
                    </div>
                    <div class="transfer-document-name" title="${escapeTransferDocumentName(file.name)}">
                        ${escapeTransferDocumentName(file.name)}
                    </div>
                    <div class="transfer-document-size">
                        ${size}
                    </div>
                </div>
            `);
        });
    }

    //perform sa pag format sa file size aron mas readable
    function formatTransferDocumentSize(bytes){
        if(!bytes)return '0 Bytes';

        const units=['Bytes','KB','MB','GB'];
        const index=Math.floor(Math.log(bytes)/Math.log(1024));

        return `${parseFloat((bytes/Math.pow(1024,index)).toFixed(2))} ${units[index]}`;
    }

    //perform sa pag protect sa filename aron dili makasulod ang HTML
    function escapeTransferDocumentName(name){
        return $('<div>').text(name??'').html();
    }
    function formatTransferDocumentSize(bytes){
        if(bytes===0)return '0 Bytes';
        const units=['Bytes','KB','MB','GB'];
        const index=Math.floor(Math.log(bytes)/Math.log(1024));
        return (bytes/Math.pow(1024,index)).toFixed(index===0?0:1)+' '+units[index];
    }
    function escapeTransferDocumentName(name){
        return $('<div>').text(name).html();
    }
    initTransferDocuments();

    $('#acknowledgeTransferBtn').on('click',function(){
        const button=$(this);
        const form=document.getElementById('propertyTransferForm');
        const properties=[];
        const transferReason=$('#transferReason').val();

        // Siguraduhon nga valid ang napiling reason
        const validReasons=[
            'Donation',
            'Reassignment/Recalled',
            'Recolate',
            'Retirement/Resignation',
            'Other'
        ];

        if(!validReasons.includes(transferReason)){
            Swal.fire({
                icon:'warning',
                title:'Reason for Transfer Required',
                text:'Please select a valid reason for the transfer.'
            });
            return;
        }

        // Kolektahon ang tanang property nga naa sa transfer table
        $('#transferPropertyTableBody tr[data-property-id]').each(function(){
            const row=$(this);
            const quantityText=row.find('td:eq(5)').text().trim();
            const quantityMatch=quantityText.match(/^([\d,.]+)\s*(.*)$/);

            properties.push({
                property_id:row.data('property-id'),
                property_no:row.find('td:eq(1)').text().trim(),
                item_description:row.find('td:eq(2)').text().trim(),
                par_ics:row.find('td:eq(3)').text().trim(),

                // Kuhaon ang date acquired gikan sa row
                date_acquired:row.attr('data-date-acquired'),

                quantity:quantityMatch
                    ?parseFloat(quantityMatch[1].replace(/,/g,''))
                    :null,

                unit_of_measurement:quantityMatch
                    ?quantityMatch[2].trim()
                    :'',

                unit_value:parseFloat(
                    row.find('td:eq(6)').text().replace(/,/g,'').trim()
                )||0,

                // Kuhaon ang total cost gikan sa row
                total_cost:parseFloat(
                    row.attr('data-total-cost')
                )||0,

                // Gamiton ang reason sa transfer isip condition
                condition:transferReason
            });
        });

        // Dili pwede mag-save kung walay property
        if(!properties.length){
            Swal.fire({
                icon:'warning',
                title:'No Property Selected',
                text:'Please add at least one property to the transfer.'
            });
            return;
        }

        // Siguraduhon nga valid ang reason sa tanang property
        if(properties.some(property=>!validReasons.includes(property.condition))){
            Swal.fire({
                icon:'warning',
                title:'Invalid Transfer Reason',
                text:'Please select a valid reason for the transfer.'
            });
            return;
        }

        // Kuhaon ang tanang field gikan sa form
        const formData=new FormData(form);

        // Tangtangon ang existing attachment aron dili magdoble
        formData.delete('transfer_documents[]');

        // Ibutang ang tanang property data sa FormData
        properties.forEach((property,index)=>{
            Object.entries(property).forEach(([key,value])=>{
                formData.append(
                    `properties[${index}][${key}]`,
                    value??''
                );
            });
        });

        // I-check ang tanang gipiling attachment
        transferDocuments.forEach(function(file,index){
            console.log('Attachment '+(index+1)+':',{
                name:file.name,
                size:file.size,
                sizeMB:(file.size/1024/1024).toFixed(2),
                type:file.type
            });

            if(file instanceof File){
                if(file.size>10*1024*1024){
                    console.warn(
                        'File sobra sa 10MB:',
                        file.name,
                        (file.size/1024/1024).toFixed(2)+' MB'
                    );
                    return;
                }

                formData.append(
                    'transfer_documents[]',
                    file,
                    file.name
                );
            }
        });

        // Ipakita sa console ang data nga ipadala
        console.log('Transfer Data:');

        for(const [key,value] of formData.entries()){
            console.log(
                key,
                value instanceof File
                    ?{
                        name:value.name,
                        size:value.size,
                        type:value.type
                    }
                    :value
            );
        }

        button.prop('disabled',true);

        $.ajax({
            url:"{{ route('property-transfer.save') }}",
            type:'POST',
            data:formData,
            processData:false,
            contentType:false,
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },

            // Kung malampuson ang pag-save sa database
            success:function(response){
                console.log('TRANSFER SAVE RESPONSE:',response);

                if(response.success){
                    //Nasaya ini $('#transferNo').text(response.transfer_no);
                    $('#transferStatus').text(response.status);

                    Swal.fire({
                        icon:'success',
                        title:'Transfer Saved',
                        text:response.message,
                        timer:1500,
                        showConfirmButton:false
                    });
                }
            },

            // Kung adunay validation error o error gikan sa controller
            error:function(xhr){
                console.error('TRANSFER SAVE ERROR:',xhr);

                const response=xhr.responseJSON||{};
                const errors=response.errors||{};

                // Kuhaon ang tanang validation errors gikan sa Laravel
                const validationMessages=Object.values(errors).flat();

                // Gamiton ang custom message kung walay validation error
                const message=validationMessages.length
                    ?validationMessages.join('<br>')
                    :response.message||'Unable to save the transfer.';

                if(xhr.status===422 && response.type==='pending_transfer'){
                    Swal.fire({
                        icon:'warning',
                        title:'Pending Transfer Request',
                        html:`
                            <div class="text-start">
                                <div class="border rounded-3 p-3 mb-3 bg-light">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-warning fs-3">
                                            <i class="bi bi-hourglass-split"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">
                                                This property already has a pending transfer.
                                            </div>
                                            <small class="text-muted">
                                                A new transfer request cannot be created until the existing request is addressed.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-2 text-start">
                                    <div class="col-6">
                                        <div class="border rounded-3 p-2">
                                            <small class="text-muted d-block">
                                                Property No.
                                            </small>
                                            <span class="fw-semibold">
                                                ${escapeHtml(response.property_no)}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="border rounded-3 p-2">
                                            <small class="text-muted d-block">
                                                Transfer No.
                                            </small>
                                            <span class="fw-semibold text-primary">
                                                ${escapeHtml(response.transfer_no || '—')}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-3 mb-0 py-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Please review and address the pending transfer before submitting another request for this property.
                                </div>
                            </div>
                        `,
                        confirmButtonText:'Understood',
                        confirmButtonColor:'#0d6efd',
                        width:'520px'
                    });

                    return;
                }else{
                    Swal.fire({
                        icon:'error',
                        title:'Save Failed',
                        text:message
                    });
                }
            },
            // I-enable balik ang button pagkahuman sa request
            complete:function(){
                button.prop('disabled',false);
            }
        });
    });
    // Lista sa mga government offices nga gamiton sa current ug receiving office dropdown
    const governmentOffices = [
        { code: '001', name: "Provincial Governor's Office", acronym: 'PGO' },
        { code: '002', name: "Provincial Youth Development Office", acronym: 'PYDO' },
        { code: '003', name: "Provincial Legal Office", acronym: 'PLO' },
        { code: '004', name: "Provincial Planning and Development Office", acronym: 'PPDO' },
        { code: '005', name: "Provincial Budget Office", acronym: 'PBO' },
        { code: '006', name: "Provincial Accounting Office", acronym: 'PACCO' },
        { code: '007', name: "Provincial Treasurer's Office", acronym: 'PTO' },
        { code: '008', name: "Provincial Assessor's Office", acronym: 'PASSO' },
        { code: '009', name: "Provincial Human Resource Management and Development Office", acronym: 'PHRMDO' },
        { code: '010', name: "Provincial Economic Enterprise Development and Management Office", acronym: 'PEEDMO' },
        { code: '011', name: "Provincial General Services Office", acronym: 'PGSO' },
        { code: '012', name: "Provincial Social Welfare and Development Office", acronym: 'PSWDO' },
        { code: '013', name: "Provincial Agriculture Office", acronym: 'PAGGRI' },
        { code: '014', name: "Provincial Veterinary Office", acronym: 'PVET' },
        { code: '015', name: "Provincial Engineering Office", acronym: 'PEO' },
        { code: '016', name: "Provincial Health Office", acronym: 'PHO' },
        { code: '017', name: "Provincial Tourism Office", acronym: 'Tourism' },
        { code: '018', name: "Provincial Environment and Natural Resources Office", acronym: 'PENRO' },
        { code: '019', name: "Provincial Internal Audit Office", acronym: 'PIAO' },
        { code: '020', name: "Provincial Disaster Risk Reduction and Management Office", acronym: 'PDRRMO' },
        { code: '021', name: "Office of the SP Secretary", acronym: 'SP Sec' },
        { code: '022', name: "Provincial Public Employment Service Office", acronym: 'PPESO' },
        { code: '023', name: "Civil Security Unit", acronym: 'CSU-PGO' },
        { code: '024', name: "Multimedia Development Section", acronym: 'MDS-PGO' },
        { code: '025', name: "Provincial Media Affairs Office", acronym: 'PMAO-PGO' },
        { code: '026', name: "Provincial Governor Office - Culture and the Arts", acronym: 'PGO-Culture and the Arts' },
        { code: '027', name: "Provincial Information and Communications Technology Office", acronym: 'PICTO' },
        { code: '028', name: "Provincial Cooperative Development Office", acronym: 'PCDO-PGO' },
        { code: '029', name: "Office of the Provincial Administrator", acronym: 'PADMO' },
        { code: '030', name: "Provincial Governor Extension Office", acronym: 'PGEO' },
        { code: '031', name: "Bids and Awards Committee", acronym: 'BAC' },
        { code: '032', name: "Provincial Vice Governor Office", acronym: 'PVGO' },
        { code: '033', name: "Socorro District Hospital", acronym: 'SDH' },
        { code: '034', name: "Office of the SP Secretary", acronym: 'SP-SEC' },
        { code: '035', name: "Del Carmen District Hospital", acronym: 'DCDH' },
        { code: '036', name: "Surigao del Norte Provincial Hospital", acronym: 'SDNPH' },
        { code: '037', name: "Sangguniang Panlalawigan", acronym: 'SP' },
        { code: '038', name: "Malimono District Hospital", acronym: 'MDH' },
        { code: '039', name: "Sta. Monica District Hospital", acronym: 'SMDH' },
        { code: '040', name: "Commission on Audit", acronym: 'COA-PGO' },
        { code: '041', name: "Mainit Medicare Community Hospital", acronym: 'MMDH' },
        { code: '042', name: "Gigaquit Ambulatory Surgical Clinic", acronym: 'GASC' },
        { code: '4243', name: "Provincial Peace & Order Council", acronym: 'PPOC' },
        { code: '044', name: "Pilar District Hospital", acronym: 'PDH' }
    ];

    // Function para mapuno ang office dropdown gamit ang office list
    function populateOfficeSelect(selector, placeholder) {
        const select = $(selector);
        select.empty();
        select.append(` <option value="">${placeholder}</option> `);
        governmentOffices.forEach(function (office) {
            select.append(` <option value="${office.code}" data-acronym="${office.acronym}"> ${office.name} (${office.acronym}) </option>
            `);
        });
    }

    // Kuhaon ang current date ug i-adjust para sakto ang local timezone
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());

    // Ibutang ang current date ug time sa transfer date field
    $('#displayTransferDate').val(
        now.toISOString().slice(0, 16)
    );

    // I-load ang mga office sa current ug receiving office dropdown
    $(document).ready(function () {
        populateOfficeSelect('#currentOffice', 'Select current office...');
        populateOfficeSelect('#receivingOffice', 'Select receiving office...');
    });

    // I-prepare ang timer para dili sige-sige ug request kada type sa user
    $(document).ready(function () {
    let transferSearchTimer = null;

    // Function para mangita ug properties gikan sa database
    function fetchTransferProperties(search = '') {
        $.ajax({
            url: "{{ route('property-transfer.fetch') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                search: search
            },
            success: function (response) {
                // Kuhaon ang container diin ipakita ang search suggestions
                const suggestions = $('#transferPropertySuggestions');
                suggestions.empty();

                // Kuhaon ang properties gikan sa response
                const properties = response.data || [];

                // Kung walay property nga nakit-an, ipakita ang message
                if (!response.success || properties.length === 0) {
                    suggestions.append(`
                        <div class="list-group-item text-muted small py-3">
                            <i class="bi bi-info-circle me-1"></i>
                            No property found
                        </div>
                    `).show();

                    return;
                }

                // I-display ang matag property isip selectable suggestion
                properties.forEach(function (property) {
                    suggestions.append(`
                        <button type="button"
                                class="list-group-item list-group-item-action transfer-property-option"
                                data-id="${property.id}"
                                data-property-no="${property.property_no ?? ''}"
                                data-description="${property.item_description ?? ''}"
                                data-date-acquired="${property.date_acquired ? new Date(property.date_acquired).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}) : ''}"
                                data-par="${property.PAR_number ?? ''}"
                                data-quantity="${property.quantity ?? 0}"
                                data-unit="${property.unit_of_measurement ?? ''}"
                                data-unit-value="${property.unit_value ?? 0}"
                                data-total-cost="${property.total_cost ?? 0}"
                                data-current-user="${property.current_user ?? ''}"
                                data-status="${property.status ?? ''}"
                                data-attachment="${property.attachment ?? ''}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-start">
                                    <div class="fw-semibold">${property.property_no ?? ''}</div>
                                    <div class="text-muted text-truncate">${property.item_description ?? ''}</div>
                                </div>
                                <small class="text-muted ms-3">
                                    ${property.PAR_number ?? 'No PAR/ICS'}
                                </small>
                            </div>
                        </button>
                    `);
                });

                // Ipakita ang listahan sa suggestions
                suggestions.show();
            },
            error: function (xhr) {
                // I-log ang error para dali ma-debug kung naay problema sa request
                console.error('Property fetch error:', xhr.responseText);

                // Ipakita ang error message sa user
                $('#transferPropertySuggestions')
                    .empty()
                    .append(`
                        <div class="list-group-item text-danger small py-3">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Unable to load properties.
                        </div>
                    `)
                    .show();
            }
        });
    }

   // I-monitor ang pag-type sa property search field
    $('#transferPropertySearch').on('input', function () {
        const search = $(this).val().trim();

        // I-cancel ang previous timer para malikayan ang daghang AJAX request
        clearTimeout(transferSearchTimer);

        // I-hide ang clear button samtang nag-search
        $('#clearTransferPropertyBtn').addClass('d-none');

        // Kung empty ang search, i-reset ang property selection
        if (!search) {
            $('#transferPropertySuggestions').hide().empty();
            $('#transferProperty').val('');
            resetSelectedProperty();
            resetPropertyAttachment();
            return;
        }

        // Maghulat ug 300ms before mag-send ug search request
        transferSearchTimer = setTimeout(function () {
            fetchTransferProperties(search);
        }, 300);
    });

    // I-trigger ang property search manually kung i-click ang Select Property button
    $('#selectTransferPropertyBtn').on('click', function () {
        const search = $('#transferPropertySearch').val().trim();

        // I-cancel ang existing search timer
        clearTimeout(transferSearchTimer);

        // I-fetch dayon ang matching properties
        fetchTransferProperties(search);
    });

    // Pag-click sa property suggestion, kuhaon ang tanang details sa napiling property
    $(document).on('click', '.transfer-property-option', function () {
        const property = $(this);

        // Kuhaon ang property information gikan sa data attributes
        const propertyId = property.data('id');
        const propertyNo = property.data('property-no') || '';
        const description = property.data('description') || '';
        const par = property.data('par') || '';
        const quantity = property.data('quantity') || 0;
        const unit = property.data('unit') || '';
        const unitValue = property.data('unit-value') || 0;
        const currentUser = property.data('current-user') || '';
        const status = property.data('status') || 'Active';
        const attachment = property.data('attachment') || '';
        const dateAcquired=property.data('date-acquired')||'';
        const totalCost=property.data('total-cost')||0;

        // Ibutang ang selected property ID sa hidden field
        $('#transferProperty').val(propertyId);

        // Ibutang ang property number ug description sa search field
        $('#transferPropertySearch').val(
            `${propertyNo} - ${description}`
        );

        // I-display ang property details
        $('#selectedPropertyNo').text(propertyNo || '—');
        $('#selectedPAR').text(par || '—');
        $('#selectedDescription').text(description || '—');
        $('#selectedDateAcquired').text(dateAcquired||'—');
        $('#selectedQuantity').text(`${quantity} ${unit}`);

        $('#selectedTotalCost').text(Number(totalCost).toLocaleString('en-PH',{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        }));
        // I-format ang unit value gamit ang Philippine number format
        $('#selectedUnitValue').text(
            Number(unitValue).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        );

        // I-set ang current accountable officer base sa owner sa property
        $('#currentAccountableOfficer').val(currentUser);

        // I-update ang status badge depende sa current status sa property
        $('#transferPropertyStatus')
            .text(status)
            .removeClass(
                'bg-success-subtle bg-warning-subtle bg-danger-subtle text-success text-warning text-danger'
            )
            .addClass(
                status === 'Active'
                    ? 'bg-success-subtle text-success'
                    : status === 'Pending'
                        ? 'bg-warning-subtle text-warning'
                        : 'bg-danger-subtle text-danger'
            );

        // Ipakita ang attachment sa selected property kung naa
        displayPropertyAttachment(attachment);

        // I-hide ang suggestions human makapili ug property
        $('#transferPropertySuggestions')
            .hide()
            .empty();

        // Ipakita ang clear button para ma-reset ang selected property
        $('#clearTransferPropertyBtn').removeClass('d-none');
    });

    // Function para i-display ang attachment sa napiling property
    function displayPropertyAttachment(attachment) {
        const viewer = $('#propertyAttachmentViewer');
        const empty = $('#propertyAttachmentEmpty');
        const openButton = $('#openPropertyAttachmentBtn');

        // I-reset ang attachment viewer, empty state, ug open button
        viewer.addClass('d-none').attr('src', '');
        empty.removeClass('d-none');
        openButton.addClass('d-none').removeData('url');

        // Kung walay attachment, ipakita ang empty state
        if (!attachment) {
            empty.html(`
                <i class="bi bi-file-earmark-pdf fs-2 d-block mb-2"></i>
                <small>No attachment available</small>
            `);

            return;
        }

        // Himoa ang complete URL sa attachment
        const attachmentUrl = attachment.startsWith('http')
            ? attachment
            : `{{ asset('storage') }}/${attachment}`;

        // I-display ang attachment sa viewer
        viewer
            .attr('src', attachmentUrl)
            .removeClass('d-none');

        // I-hide ang empty state kay naa nay attachment
        empty.addClass('d-none');

        // I-enable ang button para ma-open ang attachment
        openButton
            .removeClass('d-none')
            .data('url', attachmentUrl);
    }
    // I-open ang property attachment sa bag-ong browser tab
    $('#openPropertyAttachmentBtn').on('click', function () {
        const url = $(this).data('url');

        if (url) {
            window.open(url, '_blank');
        }
    });

    // I-clear ang napiling property
    $('#clearTransferPropertyBtn').on('click', function () {
        clearTransferProperty();
    });

    // Function para i-reset ang tanang selected property details
    function clearTransferProperty() {
        // I-clear ang property search ug hidden property ID
        $('#transferPropertySearch').val('');
        $('#transferProperty').val('');

        // I-reset ang property details ug attachment viewer
        resetSelectedProperty();
        resetPropertyAttachment();

        // I-hide ug i-clear ang property suggestions
        $('#transferPropertySuggestions')
            .hide()
            .empty();

        // I-hide ang clear button kay wala nay selected property
        $('#clearTransferPropertyBtn').addClass('d-none');

        // Ibalik ang cursor sa property search field
        $('#transferPropertySearch').focus();
    }

    // I-reset ang tanang details sa selected property
    function resetSelectedProperty() {
        $('#selectedPropertyNo').text('—');
        $('#selectedPAR').text('—');
        $('#selectedDescription').text('—');
        $('#selectedQuantity').text('—');
        $('#selectedUnitValue').text('—');
        $('#currentAccountableOfficer').val('');
        $('#selectedDateAcquired').text('—');
        $('#selectedTotalCost').text('—');

        // Ibalik ang property status sa default nga Active
        $('#transferPropertyStatus')
            .text('Active')
            .removeClass(
                'bg-warning-subtle bg-danger-subtle text-warning text-danger'
            )
            .addClass('bg-success-subtle text-success');
    }

    // I-reset ang property attachment viewer
    function resetPropertyAttachment() {
        $('#propertyAttachmentViewer')
            .attr('src', '')
            .addClass('d-none');

        // Ipakita ang default message kung walay napiling property
        $('#propertyAttachmentEmpty')
            .removeClass('d-none')
            .html(`
                <i class="bi bi-file-earmark-pdf fs-2 d-block mb-2"></i>
                <small>Select a property to view its attachment</small>
            `);

        // I-disable ang open attachment button
        $('#openPropertyAttachmentBtn')
            .addClass('d-none')
            .removeData('url');
    }

    // Kung i-focus ang search field ug naay search value, i-load ang suggestions
    $('#transferPropertySearch').on('focus', function () {
        const search = $(this).val().trim();

        if (search && !$('#transferProperty').val()) {
            fetchTransferProperties(search);
        }
    });

    // I-hide ang suggestions kung mag-click sa gawas sa property search area
    $(document).on('click', function (e) {
        if (!$(e.target).closest(
            '#transferPropertySearch, #selectTransferPropertyBtn, #transferPropertySuggestions'
        ).length) {
            $('#transferPropertySuggestions').hide();
        }
    });

    // I-close ang property suggestions kung pinduton ang Escape key
    $('#transferPropertySearch').on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('#transferPropertySuggestions')
                .hide()
                .empty();
        }
    });

    // I-update ang total number sa selected properties ug items
    function updateTransferPropertyCount() {
        const count = $('#transferPropertyTableBody tr[data-property-id]').length;

        $('#transferPropertyCount').text(
            count === 1 ? '1 Property' : `${count} Properties`
        );

        $('#transferItemCount').text(
            count === 1 ? '1 Item' : `${count} Items`
        );
    }

    // I-add ang selected property sa transfer table
    $('#addTransferPropertyBtn').on('click',function(){
    const propertyId=$('#transferProperty').val();

    // Kung walay napiling property, dili ipadayon ang pag-add
    if(!propertyId){
        alert('Please select a property first.');
        return;
    }

    // Kuhaon ang details sa selected property
    const propertyNo=$('#selectedPropertyNo').text();
    const description=$('#selectedDescription').text();
    const par=$('#selectedPAR').text();
    const dateAcquired=$('#selectedDateAcquired').text();
    const quantityText=$('#selectedQuantity').text();
    const unitValue=$('#selectedUnitValue').text();
    const totalCost=$('#selectedTotalCost').text();

    // I-check kung naa na daan ang property sa transfer table
    if($(`#transferPropertyTableBody tr[data-property-id="${propertyId}"]`).length){
        alert('This property has already been added.');
        return;
    }

    // Tangtangon ang empty state row kung naa pa
    $('#transferPropertyTableBody tr').each(function(){
        if($(this).find('td').length===1){
            $(this).remove();
        }
    });

    // I-compute ang row number
    const rowNumber=$('#transferPropertyTableBody tr').length+1;

    // I-add ang property sa transfer table
    $('#transferPropertyTableBody').append(`
        <tr data-property-id="${propertyId}" data-date-acquired="${dateAcquired}" data-total-cost="${totalCost}" class="transfer-row-added">
            <td class="small py-2">${rowNumber}</td>
            <td class="small fw-semibold py-2">${propertyNo}</td>
            <td class="small py-2">${description}</td>
            <td class="small py-2">${par}</td>
            <td class="small py-2">${dateAcquired}</td>
            <td class="small text-end py-2">${quantityText}</td>
            <td class="small text-end py-2">${unitValue}</td>
            <td class="small text-end py-2">${totalCost}</td>
            <td class="py-2">
                <select class="form-select form-select-sm transfer-condition">
                    <option value="">Select</option>
                    <option value="Functional">Functional</option>
                    <option value="Not Functional">Not Functional</option>
                    <option value="For Repair">For Repair</option>
                </select>
            </td>
            <td class="text-center py-2">
                <button type="button"
                        class="remove-transfer-property"
                        title="Remove property">
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        </tr>
    `);

    // I-update ang property ug item count
    updateTransferPropertyCount();
});

    // I-remove ang property gikan sa transfer table
    $(document).on('click', '.remove-transfer-property', function () {
        // Kuhaon ug tangtangon ang row sa property
        $(this).closest('tr').remove();

        // I-renumber balik ang mga property rows
        $('#transferPropertyTableBody tr[data-property-id]').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });

        // Kung wala nay property, ibalik ang empty state
        if ($('#transferPropertyTableBody tr[data-property-id]').length === 0) {
            $('#transferPropertyTableBody').html(`
                <tr id="noTransferPropertyRow">
                    <td colspan="8" class="empty-property-state">
                        <div class="empty-property-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>

                        <div class="empty-property-title">
                            No property selected
                        </div>

                        <div class="empty-property-text">
                            Select a property above to add it to this transfer.
                        </div>
                    </td>
                </tr>
            `);
        }

        // I-update ang total property ug item count
        updateTransferPropertyCount();
    });
});
</script>
