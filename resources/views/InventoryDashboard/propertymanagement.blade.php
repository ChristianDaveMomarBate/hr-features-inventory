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
        <div class="card shadow-sm border-0" >
            <div class="card-header bg-white" >
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0 fw-semibold">Property Inventory</h5>
                    </div>
                    <button type="button" class="btn-flat-primary" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
                        <i class="fas fa-plus me-2"></i>
                        Add Property
                    </button>
                </div>
                <div class="row g-2">
                    <!-- Search -->
                    <div class="col-lg-3">
                        <input type="text" class="flat-input" id="searchProperty" placeholder="Search property...">
                    </div>
                    <!-- Property Number -->
                    <div class="col-lg-2">
                        <input type="text" class="flat-input" id="propertyNoFilter" placeholder="Property No.">
                    </div>
                    <!-- Date Acquired -->
                    <div class="col-lg-2">
                        <input type="date" class="flat-input" id="dateFilter">
                    </div>
                    <!-- Unit -->
                    <div class="col-lg-2">
                        <select class="flat-input" id="unitFilter">
                            <option value="">All Units</option>
                            <option>Piece (pc)</option>
                            <option>Set</option>
                            <option>Unit</option>
                            <option>Pair</option>
                            <option>Pack</option>
                            <option>Box</option>
                            <option>Carton</option>
                            <option>Bundle</option>
                            <option>Roll</option>
                            <option>Ream</option>
                            <option>Book</option>
                            <option>Pad</option>
                            <option>Gram (g)</option>
                            <option>Kilogram (kg)</option>
                            <option>Liter (L)</option>
                            <option>Meter (m)</option>
                            <option>Bottle</option>
                            <option>Can</option>
                            <option>Jar</option>
                            <option>Tube</option>
                            <option>Sack</option>
                            <option>Dozen</option>
                        </select>
                    </div>
                    <!-- Current User -->
                    <div class="col-lg-2">
                        <input type="text" class="flat-input" id="currentUserFilter" placeholder="Current User">
                    </div>
                    <!-- Reset -->
                    <div class="col-lg-1">
                        <button type="button" class="btn-flat-light w-100" id="resetFilters">
                            Reset Filter
                            <i class="fas fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
            <div class="property-table-wrapper">
                <table class="property-table" id="propertyTable" >
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th>Property Number</th>
                            <th class="item-description">Item Description</th>
                            <th>Date Acquired</th>
                            <th>Base Unit</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Unit Value</th>
                            <th class="text-end">Total Cost</th>
                            <th>PAR Number</th>
                            <th>Remarks</th>
                            <th>Current User / Location</th>
                            <th class="col-actions text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="propertyTableBody">
                        <tr>
                            <td colspan="12" class="property-loading">
                                <div class="loading-content">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span>Loading properties...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="property-pagination">
                <div id="pagination"></div>
            </div>
        </div>
        </div>
        <div class="modal fade" id="addPropertyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content flat-modal">
                    <form id="propertyForm" action="{{ route('property.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flat-modal-header">
                            <div>
                                <h5 class="mb-1 fw-semibold">Add Property</h5>
                                <small class="text-muted">
                                    Enter the property information below.
                                </small>
                            </div>
                        </div>
                        <div class="flat-modal-body">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="flat-label">Property No.</label>
                                    <input type="text" class="flat-input" name="property_no">
                                </div>
                                <div class="col-md-4">
                                    <label class="flat-label">PAR Number</label>
                                    <input type="text" class="flat-input" name="PAR_number">
                                </div>
                                <div class="col-md-4">
                                    <label class="flat-label">Date Acquired</label>
                                    <input type="date" class="flat-input" name="date_acquired">
                                </div>
                                <div class="col-6">
                                    <label class="flat-label">Item Description</label>
                                    <textarea class="flat-input" rows="3" name="item_description"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="flat-label">Remarks</label>
                                    <textarea class="flat-input" rows="3" name="remarks"></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="flat-label">U/Measurement</label>
                                    <select class="flat-input" name="unit_of_measurement" required>
                                        <option value="">Select </option>
                                        <!-- Quantity -->
                                        <option value="Piece (pc)">Piece (pc)</option>
                                        <option value="Set">Set</option>
                                        <option value="Unit">Unit</option>
                                        <option value="Pair">Pair</option>
                                        <option value="Pack">Pack</option>
                                        <option value="Box">Box</option>
                                        <option value="Carton">Carton</option>
                                        <option value="Bundle">Bundle</option>
                                        <option value="Roll">Roll</option>
                                        <option value="Ream">Ream</option>
                                        <option value="Book">Book</option>
                                        <option value="Pad">Pad</option>
                                        <!-- Weight -->
                                        <option value="Gram (g)">Gram (g)</option>
                                        <option value="Kilogram (kg)">Kilogram (kg)</option>
                                        <option value="Metric Ton (MT)">Metric Ton (MT)</option>
                                        <!-- Volume -->
                                        <option value="Milliliter (mL)">Milliliter (mL)</option>
                                        <option value="Liter (L)">Liter (L)</option>
                                        <option value="Gallon">Gallon</option>
                                        <!-- Length -->
                                        <option value="Millimeter (mm)">Millimeter (mm)</option>
                                        <option value="Centimeter (cm)">Centimeter (cm)</option>
                                        <option value="Meter (m)">Meter (m)</option>
                                        <option value="Foot (ft)">Foot (ft)</option>
                                        <option value="Inch (in)">Inch (in)</option>
                                        <!-- Area -->
                                        <option value="Square Meter (sq.m)">Square Meter (sq.m)</option>
                                        <!-- Miscellaneous -->
                                        <option value="Bottle">Bottle</option>
                                        <option value="Can">Can</option>
                                        <option value="Jar">Jar</option>
                                        <option value="Tube">Tube</option>
                                        <option value="Sack">Sack</option>
                                        <option value="Dozen">Dozen</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="flat-label">Quantity</label>
                                    <input type="number" min="0" class="flat-input" id="quantity" name="quantity">
                                </div>
                                <div class="col-md-2">
                                    <label class="flat-label">Unit Value</label>
                                    <input type="number" min="0" step="0.01" class="flat-input" id="unit_value" name="unit_value">
                                </div>
                                <div class="col-md-2">
                                    <label class="flat-label">Total Cost</label>
                                    <input type="text" class="flat-input" id="total_cost" name="total_cost" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="flat-label">Current User</label>
                                    <input type="text" name="current_user" class="flat-input" placeholder="Enter employee name">
                                </div>
                            </div>
                        </div>
                        <div class="flat-modal-footer d-flex justify-content-end align-items-center">

                            <div class="d-flex align-items-center me-auto gap-2">

                                <label for="attachmentInput" class="btn-flat-attach mb-0">
                                    <i class="fas fa-paperclip me-2"></i>
                                    Upload & Attach PDF (ICS/PAR)
                                </label>

                                <span id="attachmentFileName" class="attachment-file-name">
                                    No file attached
                                </span>

                            </div>

                            <!-- Actual file input -->
                            <input type="file" id="attachmentInput" name="attachment" accept="application/pdf,.pdf" hidden>

                            <button type="button" class="btn-flat-light" data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <button type="submit" class="btn-flat-primary ms-2">
                                <i class="fas fa-save me-2"></i>
                                Save Property
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="attachmentModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <form id="attachmentForm" enctype="multipart/form-data">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title mb-0">Property Attachment</h5>
                                <small class="text-muted">View the property attachment PDF format.</small>
                            </div>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="property_id" name="property_id">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" readonly>
                                    Description
                                </label>
                                <input type="text" id="property_description" class="form-control" style="border:none; " disabled readonly>
                            </div>
                            <!-- Scan Preview -->
                            <div class="text-center">
                                <div id="pdfPreview" style=" width: 100%; height: 550px; border: 1px solid #d0d5dd; border-radius: 12px; background: #fafafa; overflow: hidden;">
                                    <div class="h-100 d-flex align-items-center justify-content-center">
                                        <div class="text-muted">
                                            <i class="fas fa-file-pdf fa-2x mb-2"></i>
                                            <div>No PDF document available.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-upload me-2"></i>
                                    Print
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        //File picker
        $('#attachmentInput').on('change', function() {
            const file = this.files[0];
            if (file) {
                $('#attachmentFileName')
                    .text(file.name)
                    .addClass('attached');
            } else {

                $('#attachmentFileName')
                    .text('No file attached')
                    .removeClass('attached');
            }
        });
        //attachment modal 
        $(document).on('click', '.attachmentBtn', function() {
            $('#property_id').val($(this).data('id'));
            $('#property_description').val(
                $(this).data('description')
            );
            $('#pdf_path').val(
                $(this).data('path')
            );
            $('#attachmentModal').modal('show');
        });
        //Display the PDF
        $(document).on('click', '.attachmentBtn', function() {
            const path = $(this).data('path');
            $('#property_id').val($(this).data('id'));
            $('#property_description').val(
                $(this).data('description')
            );
            $('#pdf_path').val(path);
            // Build storage URL
            const pdfUrl = '/storage/' + path;
            // Display PDF
            $('#pdfPreview').html(`
        <iframe src="${pdfUrl}" style="width: 100%;height: 100%;border: none;"></iframe>`);
            $('#pdfPreviewText').text(
                'PDF document'
            );
            $('#attachmentModal').modal('show');
        });
        //pag save sa database
        $('#propertyForm').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            // Debug - check if file is actually included
            const attachment = formData.get('attachment');
            console.log('Attachment:', attachment);
            if (attachment && attachment instanceof File) {
                console.log('File name:', attachment.name);
                console.log('File size:', attachment.size);
                console.log('File type:', attachment.type);
            }
            $.ajax({
                url: form.action,
                type: 'POST',
                data: formData,
                // IMPORTANT for file upload
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    // Reset form
                    form.reset();
                    // Reset attachment display
                    $('#attachmentFileName')
                        .text('No file attached')
                        .removeClass('attached');
                    // Close modal
                    const modalElement = document.getElementById(
                        'addPropertyModal'
                    );
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                    // Reload table
                    loadProperties();
                },
                error: function(xhr) {
                    let message = 'An unexpected error occurred.';
                    if (xhr.responseJSON?.errors) {
                        message = '';
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            message += value[0] + '<br>';
                        });
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Error',
                        html: message
                    });
                }
            });
        });

        //Pag load sa table
        function loadProperties(page = 1) {
            $.ajax({
                url: "{{ route('property.data') }}"
                , type: "GET"
                , dataType: "json"
                , data: {
                    page: page
                    , search: $('#searchProperty').val()
                    , property_no: $('#propertyNoFilter').val()
                    , date_acquired: $('#dateFilter').val()
                    , unit: $('#unitFilter').val()
                    , current_user: $('#currentUserFilter').val()
                }
                , success: function(response) {
                    let rows = '';
                    if (response.data.length === 0) {
                        rows = `
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
                            No property records found.
                        </td>
                    </tr>
                `;
                    } else {
                        $.each(response.data, function(index, item) {

                            rows += `
                    <tr>
                        <td>${response.from + index}</td>
                        <td>${item.property_no}</td>
                        <td class="item-description">
                            <a href="javascript:void(0)"
                            data-id="${item.id}"
                            data-description="${item.item_description}"
                            data-path="${item.attachment}"
                            class="attachmentBtn"
                            style="color:#4382DF; text-decoration:underline; font-weight:600; cursor:pointer;">
                                ${item.item_description}
                            </a>
                        </td>
                        <td>${new Date(item.date_acquired).toLocaleDateString()}</td>
                        <td>${item.unit_of_measurement}</td>
                        <td class="text-end">${item.quantity}</td>
                        <td class="text-end">${Number(item.unit_value).toLocaleString('en-US',{
                            minimumFractionDigits:2
                        })}</td>
                        <td class="text-end">${Number(item.total_cost).toLocaleString('en-US',{
                            minimumFractionDigits:2
                        })}</td>
                        <td>${item.PAR_number}</td>
                        <td>${item.remarks ?? ''}</td>
                        <td>${item.current_user}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary editProperty"
                                    data-id="${item.id}">
                               <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteProperty"
                                    data-id="${item.id}">
                               <i class="bi bi-trash3-fill"></i>
                            </button>
                        </td>
                    </tr>`;
                        });
                    }
                    $('#propertyTableBody').html(rows);
                    // Pagination
                    let pagination = '';
                    if (response.last_page > 1) {
                        pagination += `
                    <nav>
                        <ul class="pagination pagination-sm justify-content-end mb-0">
                `;
                        // Previous
                        pagination += `
                    <li class="page-item ${response.current_page == 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${response.current_page - 1}">
                            Previous
                        </a>
                    </li>
                `;
                        // Page Numbers
                        for (let i = 1; i <= response.last_page; i++) {

                            pagination += `
                        <li class="page-item ${i == response.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">
                                ${i}
                            </a>
                        </li>
                    `;
                        }
                        // Next
                        pagination += `
                    <li class="page-item ${response.current_page == response.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${response.current_page + 1}">
                            Next
                        </a>
                    </li>
                `;
                        pagination += `
                        </ul>
                    </nav>
                `;
                    }
                    $('#pagination').html(pagination);

                }
            });
        }
        $(document).ready(function() {
            loadProperties();
            $('#searchProperty, #propertyNoFilter, #currentUserFilter').on('keyup', function() {
                loadProperties();
            });
            $('#dateFilter, #unitFilter').on('change', function() {
                loadProperties();
            });
            $('#resetFilters').on('click', function() {
                $('#searchProperty').val('');
                $('#propertyNoFilter').val('');
                $('#dateFilter').val('');
                $('#unitFilter').val('');
                $('#currentUserFilter').val('');
                loadProperties();
            });
            // Pagination click
            $(document).on('click', '#pagination .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page) {
                    loadProperties(page);
                }
            });
        });
        const quantity = document.getElementById('quantity');
        const unitValue = document.getElementById('unit_value');
        const totalCost = document.getElementById('total_cost');

        function calculateTotalCost() {
            const qty = parseFloat(quantity.value) || 0;
            const unit = parseFloat(unitValue.value) || 0;
            const total = qty * unit;

            totalCost.value = total.toLocaleString('en-US', {
                minimumFractionDigits: 2
                , maximumFractionDigits: 2
            });
        }
        quantity.addEventListener('input', calculateTotalCost);
        unitValue.addEventListener('input', calculateTotalCost);

    </script>
