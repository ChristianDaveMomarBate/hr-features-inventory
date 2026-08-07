{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>HRMIS PGSDN - Confidential Data</title>

    <link rel="icon" href="images/SDN.PNG" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        /* Compact table styling */
        .custom-table th,
        .custom-table td {
            font-size: 0.85rem;
            padding: 0.5rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        /* Dark header style */
        .custom-table thead th {
            background-color: #343a40;
            color: #fff;
            border-bottom: 2px solid #495057;
        }

        /* Alternating row colors */
        .custom-table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .custom-table tbody tr:nth-child(even) {
            background-color: #e9f2fb;
        }

        /* Hover effect */
        .custom-table tbody tr:hover {
            background-color: #d0ebff;
        }

        .custom-table th:nth-child(5),
        .custom-table td:nth-child(5) {
            max-width: 350px;
            white-space: normal;
            word-wrap: break-word;
        }

        .custom-table {
            font-size: 0.75rem;
        }

        .custom-table th,
        .custom-table td {
            padding: 0.25rem 0.4rem;
            white-space: nowrap;
        }

        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        /* Moving blue gradient background */
        .animated-bg-blue {
            background: linear-gradient(270deg, #1e3c72, #2a5298, #1e3c72);
            background-size: 600% 600%;
            animation: gradientMove 6s ease infinite;
            color: #fff;
        }

        /* Optional overlay for text readability */
        .card-body.bg-overlay {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 0.25rem;
            padding: 1rem;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Fixed Header -->
    <header class="fixed-top bg-white shadow-sm d-flex justify-content-between align-items-center px-3"
        style="height: 60px; z-index: 1030;">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 me-3">HRMIS PGSDN - Confidential Data</h5>

            <!-- Dropdown Menu -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="menuDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Menu
                </button>
                <ul class="dropdown-menu" aria-labelledby="menuDropdown">
                    <li><a class="dropdown-item" href="#" id="employeeLink">Employee</a></li>
                    <li><a class="dropdown-item" href="#" id="leaveAppLink">Leave Application</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                </ul>
            </div>
        </div>

        <!-- Sign Out Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn text-danger"
                style="width: 100px; height: 40px; font-size: 15px; padding: 0; border-radius: 4px;">
                Sign Out
            </button>
        </form>
    </header>

    <!-- Main content -->
    <div class="container-fluid py-5 mt-4">
        <!-- Employee Records Panel -->
        <div class="row gx-3" id="employeePanel">
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Employee Records</h5>
                            <button class="btn btn-dark btn-sm" id="addEmployeeBtn">
                                <i class="bx bx-plus"></i> Print PDF LIST
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable1"
                                class="table table-sm table-striped table-bordered align-middle mb-0 custom-table">
                                <thead class="table-dark text-white text-center align-middle">
                                    <tr class="text-nowrap">
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Gender</th>
                                        <th>Status</th>
                                        <th>Current Address</th>
                                        <th>Mobile</th>
                                        <th>Position ID</th>
                                        <th>Plantilla No</th>
                                        <th>Employment Status ID</th>
                                        <th>Date Employed</th>
                                        <th>Last Appointment</th>
                                        <th>Office ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Application Panel -->
        <div class="row gx-3" id="leaveAppPanel" style="display: none;">
            <div class="col-12 mb-4">
                <div class="col-12 mb-3 text-start">
                    <button class="btn btn-danger btn-sm" id="printLeaveSummary">
                        <i class="bx bx-printer"></i> Print Summary
                    </button>
                </div>

                <script>
                    document.getElementById('printLeaveSummary').addEventListener('click', function() {
                        window.open("{{ route('leaves.printPdf') }}", '_blank');
                    });
                </script>
                <div class="row gx-3 mb-3">
                    <div class="row gx-3 mb-3">
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm border-0 animate__animated animate__fadeInUp animated-bg-blue"
                                style="animation-duration: 1s;">
                                <div class="card-body bg-overlay">
                                    <h6 class="card-title text-white">Leave Counts per Leave Type</h6>
                                    <ul class="list-group list-group-flush" id="leaveTypeCounts"
                                        style="max-height: 300px; overflow-y: auto;">
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm border-0 animate__animated animate__fadeInUp animated-bg-blue"
                                style="animation-duration: 1s; animation-delay: 0.2s;">
                                <div class="card-body bg-overlay">
                                    <h6 class="card-title text-white">Total Leaves by Gender</h6>
                                    <ul class="list-group list-group-flush text-white">
                                        <li class="list-group-item d-flex justify-content-between bg-transparent">
                                            <span class="text-white">Male</span>
                                            <span class="text-white"id="totalMale">0</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between bg-transparent">
                                            <span class="text-white">Female</span>
                                            <span class="text-white" id="totalFemale">0</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="mb-3">Employee Leave Records</h5>
                            <div class="table-responsive">
                                <table id="datatableLeaves"
                                    class="table table-sm table-striped table-bordered align-middle mb-0 custom-table">
                                    <thead class="table-secondary text-center align-middle">
                                        <tr class="text-nowrap">
                                            <th>ID</th>
                                            <th>Series</th>
                                            <th>Employee ID</th>
                                            <th>Employee Name</th>
                                            <th>Gender</th>
                                            <th>Office</th>
                                            <th>Position</th>
                                            <th>Leave Type</th>
                                            <th>Date Filed</th>
                                            <th>Days</th>
                                            <th>Pay Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</body>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {

        $('#datatable1').DataTable({
            processing: true,
            serverSide: false,
            pageLength: 50,
            ajax: "{{ route('getEmployees') }}",
            columns: [{
                    data: 'empID'
                },
                {
                    data: 'lname',
                    render: function(data, type, row) {
                        return `${row.lname}, ${row.fname} ${row.mname ?? ''}`;
                    }
                },
                {
                    data: 'gender'
                },
                {
                    data: 'status'
                },
                {
                    data: 'currentAdd'
                },
                {
                    data: 'mobile'
                },
                {
                    data: 'positionID'
                },
                {
                    data: 'plantillaNo'
                },
                {
                    data: 'empStatusID'
                },
                {
                    data: 'dateEmployed'
                },
                {
                    data: 'last_appointment'
                },
                {
                    data: 'officeID'
                }
            ]
        });

        let leaveTable;

        $('#leaveAppLink').on('click', function(e) {
            e.preventDefault();
            $('#employeePanel').hide();
            $('#leaveAppPanel').show();

            $.ajax({
                url: "{{ route('getLeaves') }}",
                type: 'GET',
                success: function(response) {
                    const leaves = response.data;
                    const summary = response.summary;

                    const leaveTypeCounts = $('#leaveTypeCounts');
                    leaveTypeCounts.empty();

                    // Get both total and gender breakdowns
                    const leaveCountsByType = summary.leaveCountsByType;
                    const leaveCountsByTypeByGender = summary.leaveCountsByTypeByGender;

                    $.each(leaveCountsByType, function(leaveType, count) {
                        const genderData = leaveCountsByTypeByGender[leaveType] || {
                            Male: 0,
                            Female: 0
                        };

                        leaveTypeCounts.append(`
            <li class="list-group-item d-flex justify-content-between align-items-start flex-column">
                <div class="w-100 d-flex justify-content-between">
                    <span><strong>${leaveType}</strong></span>
                    <span class="badge bg-primary rounded-pill">${count}</span>
                </div>
                <div class="mt-1 w-100 d-flex justify-content-between small text-muted">
                    <span>👨 Male: ${genderData.Male}</span>
                    <span>👩 Female: ${genderData.Female}</span>
                </div>
            </li>
        `);
                    });

                    $('#totalMale').text(summary.totalMale);
                    $('#totalFemale').text(summary.totalFemale);

                    if ($.fn.DataTable.isDataTable('#datatableLeaves')) {
                        leaveTable.clear().rows.add(leaves).draw();
                    } else {
                        leaveTable = $('#datatableLeaves').DataTable({
                            data: leaves,
                            processing: true,
                            serverSide: false,
                            pageLength: 50,
                            columns: [{
                                    data: 'id'
                                },
                                {
                                    data: 'series'
                                },
                                {
                                    data: 'empID'
                                },
                                {
                                    data: 'employeeName'
                                },
                                {
                                    data: 'gender'
                                },
                                {
                                    data: 'officeID'
                                },
                                {
                                    data: 'positionID'
                                },
                                {
                                    data: 'leaveID'
                                },
                                {
                                    data: 'dateFiled'
                                },
                                {
                                    data: 'days'
                                },
                                {
                                    data: 'payType'
                                }
                            ]
                        });
                    }
                }
            });
        });

        $('#employeeLink').on('click', function(e) {
            e.preventDefault();
            $('#leaveAppPanel').hide();
            $('#employeePanel').show();
        });

        $('#addEmployeeBtn').on('click', function() {
            window.open("{{ route('employees.printPdf') }}", '_blank');
        });

        $('#leaveAppLink').on('click', function(e) {
            e.preventDefault();
            $('#employeePanel').hide();
            $('#leaveAppPanel').show();
        });

        $('#employeeLink').on('click', function(e) {
            e.preventDefault();
            $('#leaveAppPanel').hide();
            $('#employeePanel').show();
        });
    });
</script>

</html> --}}
