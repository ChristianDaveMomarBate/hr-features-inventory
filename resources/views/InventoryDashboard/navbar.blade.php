{{-- Reusable top navbar: notification bell, date, logout, profile --}}
@php
    /** @var \App\Models\User $currentUser */
    $currentUser ??= auth()->user();
@endphp

<div class="d-flex align-items-center gap-3">
    {{-- Hourly Bible Verse --}}
    <div class="bible-verse-pill d-none d-xl-flex align-items-center gap-2" title="Hourly Bible verse">
        <i class="bi bi-book text-primary"></i>
        <span class="hourlyBibleVerse">Loading verse...</span>
    </div>

    {{-- Notification Bell --}}
    <div class="dropdown">
        <button class="btn btn-light position-relative rounded-circle shadow-sm border border-light d-flex align-items-center justify-content-center"
                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                style="width:44px;height:44px;">
            <i class="bi bi-bell fs-5 text-secondary"></i>
            @if(($unreadNotificationCount ?? 0) > 0)
                <span class="position-absolute badge rounded-pill bg-danger border border-white" style="top: -2px; right: -2px; font-size: 0.65rem; padding: 0.35em 0.55em;">
                    {{ $unreadNotificationCount }}
                </span>
            @endif
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0 shadow border-0"
             style="width:360px; max-height:420px; overflow-y:auto;">
            <div class="px-3 py-3 border-bottom bg-white">
                <h6 class="mb-0 fw-bold">Notifications</h6>
                <small class="text-muted">{{ ($unreadNotificationCount ?? 0) }} unread notification(s)</small>
            </div>
            @forelse(($dashboardNotifications ?? collect()) as $notification)
                @php $data = $notification->data; @endphp
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}"
                      id="notif-form-{{ $notification->id }}">
                    @csrf
                    <input type="hidden" name="page" id="notif-page-{{ $notification->id }}" value="">
                    <button type="submit" class="dropdown-item text-wrap py-3 border-bottom"
                            onclick="document.getElementById('notif-page-{{ $notification->id }}').value = window.location.pathname.split('/').pop() || 'dashboard'">
                        <div class="d-flex justify-content-between gap-3">
                            @if($notification->type === 'App\Notifications\NewItemRequest')
                                <div>
                                    <div class="fw-semibold text-dark">New Request: {{ $data['item_name'] ?? 'Item' }}</div>
                                    <small class="text-muted">By {{ $data['requester_name'] ?? 'Unknown' }} ({{ $data['department'] ?? 'Dept' }})</small>
                                </div>
                                <span class="badge bg-primary align-self-start">Qty: {{ $data['quantity'] ?? 0 }}</span>
                            @else
                                <div>
                                    <div class="fw-semibold text-dark">{{ $data['name'] ?? 'Unknown Item' }}</div>
                                    <small class="text-muted">{{ $data['code'] ?? '' }}</small>
                                </div>
                                <span class="badge bg-danger align-self-start">
                                    {{ $data['current_stock_label'] ?? ($data['current_stock'] ?? 0) }} /
                                    {{ $data['minimum_stock_label'] ?? ($data['minimum_stock'] ?? 0) }}
                                </span>
                            @endif
                        </div>
                    </button>
                </form>
            @empty
                <div class="px-3 py-4 text-center text-muted">
                    No unread notifications.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Date --}}
    <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border border-light">
        <span class="position-relative d-flex" style="width:10px;height:10px;">
            <span class="position-absolute w-100 h-100 rounded-circle bg-success opacity-75"
                  style="animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
            <span class="position-relative w-100 h-100 rounded-circle bg-success"></span>
        </span>
        <span class="currentDateDisplay fw-medium text-secondary" style="font-size:14px;"></span>
    </div>

    {{-- Logout --}}
    <form id="logoutForm" method="POST" action="{{ route('logout') }}" class="headerLogoutForm m-0">
        @csrf
        <button type="button"
                class="btn btn-light rounded-circle shadow-sm border border-light d-flex align-items-center justify-content-center text-danger"
                style="width:44px;height:44px;"
                data-action="confirm-logout"
                title="Logout">
            <i class="bi bi-box-arrow-right fs-5"></i>
        </button>
    </form>

    {{-- Profile --}}
    <div class="d-flex align-items-center gap-2"
         data-bs-toggle="modal" data-bs-target="#profileModal"
         title="Edit Profile" style="cursor: pointer;">
        <div class="text-end d-none d-md-block">
            <div class="fw-bold text-dark" style="font-size: 14px;">{{ $currentUser->name }}</div>
            <div class="text-muted" style="font-size: 12px;">Admin</div>
        </div>
        <img src="{{ $currentUser->profile_picture ? asset('storage/' . $currentUser->profile_picture) : asset('images/default-avatar.png') }}"
             alt="Profile"
             class="rounded-circle border border-2 border-white shadow-sm"
             style="width: 44px; height: 44px; object-fit: cover;">
    </div>
</div>

@if(($unreadNotificationCount ?? 0) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifAudio = new Audio('{{ asset("sound/notification.mp3") }}');
        
        function playNotif() {
            notifAudio.play().catch(function(e) {
                console.log("Notification audio blocked by browser autoplay policy:", e);
            });
        }

        @if(session('login_success'))
            // Delay notification sound by 3 seconds if we just logged in (so sounds don't overlap)
            setTimeout(playNotif, 3000);
        @else
            playNotif();
        @endif
    });
</script>
@endif
