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
                @php $data = $notification->data; $isRequest = $notification->type === 'App\Notifications\NewItemRequest'; @endphp
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}"
                      id="notif-form-{{ $notification->id }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-wrap py-3 border-bottom px-3"
                            style="transition: background 0.15s;">
                        <div class="d-flex align-items-start gap-3">
                            {{-- Colored Icon --}}
                            <div class="flex-shrink-0 mt-1" style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center;
                                background: {{ $isRequest ? '#dbeafe' : '#fee2e2' }};">
                                <i class="{{ $isRequest ? 'bi bi-inbox-fill text-primary' : 'bi bi-exclamation-triangle-fill text-danger' }}" style="font-size:15px;"></i>
                            </div>
                            {{-- Content --}}
                            <div class="flex-grow-1 text-start">
                                @if($isRequest)
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge rounded-pill" style="background:#dbeafe; color:#1d4ed8; font-size:0.7rem;">New Request</span>
                                    </div>
                                    <div class="fw-semibold text-dark" style="font-size:0.875rem;">{{ $data['requester_name'] ?? 'Someone' }} submitted a request</div>
                                    <small class="text-muted">{{ $data['department'] ?? '' }} &middot; Qty: {{ $data['quantity'] ?? 0 }}</small>
                                @else
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge rounded-pill" style="background:#fee2e2; color:#dc2626; font-size:0.7rem;">Low Stock</span>
                                    </div>
                                    <div class="fw-semibold text-dark" style="font-size:0.875rem;">{{ $data['name'] ?? 'Unknown Item' }}</div>
                                    <small class="text-muted">Stock: <strong class="text-danger">{{ $data['current_stock_label'] ?? $data['current_stock'] ?? 0 }}</strong> &mdash; Min: {{ $data['minimum_stock_label'] ?? $data['minimum_stock'] ?? 0 }}</small>
                                @endif
                            </div>
                            {{-- Arrow hint --}}
                            <i class="bi bi-chevron-right text-muted flex-shrink-0 mt-2" style="font-size:11px;"></i>
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

<script>
if (!window._notifPollInitialized) {
    window._notifPollInitialized = true;
    
    (function () {
        const POLL_URL   = '{{ route("notifications.live") }}';
        const READ_BASE  = '{{ url("/notifications") }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const SOUND_URL  = '{{ asset("sound/notification.mp3") }}';
        const INTERVAL   = 10000; // Poll every 10 seconds

        let lastCount = {{ $unreadNotificationCount ?? 0 }};
        let notifAudio = null;
        let pollTimer  = null;

        function getAudio() {
            if (!notifAudio) notifAudio = new Audio(SOUND_URL);
            return notifAudio;
        }

        @if(session('login_success'))
        setTimeout(() => { if (lastCount > 0) getAudio().play().catch(() => {}); }, 3000);
        @else
        if (lastCount > 0) getAudio().play().catch(() => {});
        @endif

        /* ── Toast Popup ─────────────────────────────────────────────── */
        function showToast(n) {
            const isRequest  = n.type === 'request';
            const color      = isRequest ? '#1d4ed8' : '#dc2626';
            const bg         = isRequest ? '#dbeafe' : '#fee2e2';
            const icon       = isRequest ? 'bi-inbox-fill' : 'bi-exclamation-triangle-fill';
            const label      = isRequest ? 'New Request' : 'Low Stock';

            const toast = document.createElement('div');
            toast.innerHTML = `
                <div style="display:flex;align-items:flex-start;gap:12px;">
                    <div style="width:38px;height:38px;border-radius:50%;background:${bg};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi ${icon}" style="color:${color};font-size:16px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;">
                            <span style="background:${bg};color:${color};font-size:0.7rem;padding:2px 8px;border-radius:999px;font-weight:600;">${label}</span>
                        </div>
                        <div style="font-weight:600;font-size:0.875rem;color:#1e293b;">${n.title}</div>
                        <div style="font-size:0.78rem;color:#64748b;margin-top:2px;">${n.sub}</div>
                    </div>
                    <button onclick="this.closest('.notif-live-toast').remove()" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:18px;line-height:1;padding:0;">&times;</button>
                </div>`;
            toast.className = 'notif-live-toast';
            toast.style.cssText = `
                position:fixed; bottom:24px; right:24px; z-index:9999;
                background:white; border-radius:14px; padding:16px 18px;
                box-shadow:0 8px 30px rgba(0,0,0,0.15); border:1px solid #e2e8f0;
                min-width:300px; max-width:380px;
                animation: slideInToast 0.35s cubic-bezier(0.34,1.56,0.64,1);`;

            if (!document.getElementById('notif-toast-style')) {
                const style = document.createElement('style');
                style.id = 'notif-toast-style';
                style.textContent = `@keyframes slideInToast { from { opacity:0; transform:translateY(20px) scale(0.95); } to { opacity:1; transform:translateY(0) scale(1); } }`;
                document.head.appendChild(style);
            }

            document.body.appendChild(toast);
            setTimeout(() => { if (toast.parentNode) toast.remove(); }, 6000);
        }

        /* ── Build dropdown item HTML ────────────────────────────────── */
        function buildNotifItem(n) {
            const isRequest  = n.type === 'request';
            const iconClass  = isRequest ? 'bi bi-inbox-fill text-primary' : 'bi bi-exclamation-triangle-fill text-danger';
            const bgColor    = isRequest ? '#dbeafe' : '#fee2e2';
            const badgeStyle = isRequest ? 'background:#dbeafe;color:#1d4ed8' : 'background:#fee2e2;color:#dc2626';
            const badgeLabel = isRequest ? 'New Request' : 'Low Stock';
            return `
            <form method="POST" action="${READ_BASE}/${n.id}/read">
                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                <button type="submit" class="dropdown-item text-wrap py-3 border-bottom px-3" style="transition:background 0.15s;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0 mt-1" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:${bgColor};">
                            <i class="${iconClass}" style="font-size:15px;"></i>
                        </div>
                        <div class="flex-grow-1 text-start">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge rounded-pill" style="${badgeStyle};font-size:0.7rem;">${badgeLabel}</span>
                                <small class="text-muted">${n.created_at}</small>
                            </div>
                            <div class="fw-semibold text-dark" style="font-size:0.875rem;">${n.title}</div>
                            <small class="text-muted">${n.sub}</small>
                        </div>
                        <i class="bi bi-chevron-right text-muted flex-shrink-0 mt-2" style="font-size:11px;"></i>
                    </div>
                </button>
            </form>`;
        }

        /* ── Update bell badge ───────────────────────────────────────── */
        function updateBell(count) {
            const bellBtns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            bellBtns.forEach(bellBtn => {
                const existing = bellBtn.querySelector('.badge.bg-danger');
                if (existing) existing.remove();
                
                if (count > 0) {
                    const badge = document.createElement('span');
                    badge.className = 'position-absolute badge rounded-pill bg-danger border border-white';
                    badge.style.cssText = 'top:-2px;right:-2px;font-size:0.65rem;padding:0.35em 0.55em;';
                    badge.textContent = count;
                    bellBtn.appendChild(badge);
                }
            });
        }

        /* ── Rebuild dropdown list ───────────────────────────────────── */
        function updateDropdown(data) {
            const menus = document.querySelectorAll('.dropdown-menu.dropdown-menu-end');
            menus.forEach(menu => {
                const countEl = menu.querySelector('small.text-muted');
                if (countEl) countEl.textContent = data.count + ' unread notification(s)';
                
                const header = menu.querySelector('.px-3.py-3.border-bottom');
                let node = header ? header.nextSibling : null;
                while (node) { const next = node.nextSibling; menu.removeChild(node); node = next; }
                
                if (data.items.length === 0) {
                    menu.insertAdjacentHTML('beforeend', '<div class="px-3 py-4 text-center text-muted">No unread notifications.</div>');
                } else {
                    data.items.forEach(n => menu.insertAdjacentHTML('beforeend', buildNotifItem(n)));
                }
            });
        }

        /* ── Core poll function ──────────────────────────────────────── */
        function poll() {
            fetch(POLL_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    if (data.count > lastCount) {
                        getAudio().play().catch(() => {});
                        // Animate all bells
                        const bellIcons = document.querySelectorAll('[data-bs-toggle="dropdown"] .bi-bell');
                        bellIcons.forEach(bellIcon => {
                            bellIcon.style.transition = 'transform 0.15s';
                            bellIcon.style.transform = 'rotate(25deg)';
                            setTimeout(() => bellIcon.style.transform = 'rotate(-20deg)', 150);
                            setTimeout(() => bellIcon.style.transform = 'rotate(0deg)', 300);
                        });
                        
                        // Show toast for each new notification
                        const newItems = data.items.slice(0, data.count - lastCount);
                        newItems.forEach((n, i) => setTimeout(() => showToast(n), i * 400));
                    }
                    lastCount = data.count;
                    updateBell(data.count);
                    updateDropdown(data);
                })
                .catch(() => {});
        }

        /* ── Poll immediately when tab becomes visible ───────────────── */
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') poll();
        });
        window.addEventListener('focus', poll);

        /* ── Start interval polling ──────────────────────────────────── */
        setInterval(poll, INTERVAL);
    })();
}
</script>
