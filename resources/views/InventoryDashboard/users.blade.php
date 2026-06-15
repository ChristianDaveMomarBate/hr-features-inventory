<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - PHRMDO Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-hri.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @include('style.style')
</head>
<body
    data-dashboard-url="{{ route('dashboard') }}"
    data-dashboard-base-url="{{ url('/dashboard') }}"
>
    <div class="dashboard-container">
        @include('InventoryDashboard.sidebar')

        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1 fw-bold" style="font-size: 32px; color: #111827;">User Management</h1>
                    <p class="text-muted mb-0">Manage registered admin account access.</p>
                </div>
            </div>


            <div class="chart-card p-0 overflow-hidden">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-dark mb-0">Registered Users</h5>
                </div>
                <div class="table-responsive bg-white">
                    <table class="table table-hover table-modern align-middle mb-0 border-0">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 border-0">User</th>
                                <th class="py-3 border-0">Email</th>
                                <th class="py-3 border-0">Access</th>
                                <th class="py-3 border-0">Status</th>
                                <th class="text-end pe-4 py-3 border-0">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:13px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <span class="fw-semibold text-dark">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-secondary">{{ $user->email }}</td>
                                    <td class="py-3">
                                        <form id="userForm{{ $user->id }}" method="POST" action="{{ route('users.update', $user) }}" class="d-flex gap-2 align-items-center justify-content-end">
                                            @csrf
                                            @method('PUT')
                                            <span class="badge role-badge role-badge-admin">Admin</span>
                                            <input type="hidden" name="is_active" value="{{ $user->is_active ? 1 : 0 }}">
                                        </form>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Deactivated' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button type="submit" form="userForm{{ $user->id }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-save me-1"></i>Save
                                            </button>
                                            <form method="POST" action="{{ route('users.update', $user) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No registered users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <!-- Toast Notification -->
    <div id="toastContainer" style="position:fixed;top:24px;right:24px;z-index:9999;min-width:320px;"></div>

    @if(session('success'))
    <div id="flashSuccess" data-message="{{ session('success') }}" hidden></div>
    @endif
    @if(session('error'))
    <div id="flashError" data-message="{{ session('error') }}" hidden></div>
    @endif

    @vite('resources/js/inventory/script.js')

</body>
</html>
