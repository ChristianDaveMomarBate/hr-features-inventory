<div id="audit-trails" class="page {{ (isset($activePageId) && $activePageId === 'audit-trails') ? 'active-page' : '' }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold" style="font-size: 32px; color: #111827;">Audit Trails</h1>
            <p class="text-muted mb-0">Track all user activities and system changes.</p>
        </div>
    </div>

    <div class="chart-card p-0 overflow-hidden">
        <div class="p-4 border-bottom border-light bg-white">
            <h5 class="fw-bold text-dark mb-0">System Activity Logs</h5>
        </div>
        <div class="table-responsive bg-white">
            <table class="table table-hover table-modern align-middle mb-0 border-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 border-0">Date & Time</th>
                        <th class="border-0">User</th>
                        <th class="border-0">Action</th>
                        <th class="border-0">Module</th>
                        <th class="border-0">Reference</th>
                        <th class="border-0 pe-4">Details</th>
                    </tr>
                </thead>
                <tbody>
                        @forelse($auditTrails as $log)
                            <tr>
                                <td class="ps-4 text-nowrap">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ substr($log->user->name, 0, 1) }}
                                            </div>
                                            {{ $log->user->name }}
                                        </div>
                                    @else
                                        <span class="text-muted">System/Deleted User</span>
                                    @endif
                                </td>
                                <td><span class="fw-semibold">{{ $log->action }}</span></td>
                                <td><span class="badge bg-secondary">{{ $log->module }}</span></td>
                                <td>{{ $log->item_reference }}</td>
                                <td>
                                    @if($log->remarks)
                                        <div class="small text-muted">{{ $log->remarks }}</div>
                                    @endif
                                    @if($log->old_value || $log->new_value)
                                        <button class="btn btn-sm btn-link p-0 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#logDetails{{ $log->id }}">
                                            View Changes <i class="bi bi-chevron-down"></i>
                                        </button>
                                        <div class="collapse mt-2" id="logDetails{{ $log->id }}">
                                            <div class="mt-2">
                                                @php
                                                    $oldDecoded = is_string($log->old_value) ? json_decode($log->old_value, true) : null;
                                                    $isJsonOld = is_array($oldDecoded) && (json_last_error() == JSON_ERROR_NONE);
                                                    
                                                    $newDecoded = is_string($log->new_value) ? json_decode($log->new_value, true) : null;
                                                    $isJsonNew = is_array($newDecoded) && (json_last_error() == JSON_ERROR_NONE);
                                                @endphp

                                                @if($isJsonOld && $isJsonNew)
                                                    <div class="table-responsive bg-white rounded border border-1">
                                                        <table class="table table-sm table-hover mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="ps-3 text-muted" style="font-weight: 600; font-size: 0.8rem; text-transform: uppercase;">Field</th>
                                                                    <th class="text-muted" style="font-weight: 600; font-size: 0.8rem; text-transform: uppercase;">Previous</th>
                                                                    <th class="text-muted" style="font-weight: 600; font-size: 0.8rem; text-transform: uppercase;">Updated</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $allKeys = array_unique(array_merge(array_keys($oldDecoded), array_keys($newDecoded)));
                                                                    $hasChanges = false;
                                                                @endphp
                                                                @foreach($allKeys as $key)
                                                                    @if(in_array($key, ['created_at', 'updated_at'])) @continue @endif
                                                                    @php
                                                                        $oldVal = $oldDecoded[$key] ?? null;
                                                                        $newVal = $newDecoded[$key] ?? null;
                                                                        $isChanged = $oldVal != $newVal;
                                                                    @endphp
                                                                    @if($isChanged)
                                                                        @php $hasChanges = true; @endphp
                                                                        <tr>
                                                                            <td class="ps-3 text-capitalize fw-medium text-secondary" style="font-size: 0.85rem;">{{ str_replace('_', ' ', $key) }}</td>
                                                                            <td class="text-danger" style="font-size: 0.85rem;">
                                                                                <span class="text-decoration-line-through opacity-75">{{ $oldVal ?? '—' }}</span>
                                                                            </td>
                                                                            <td class="text-success fw-semibold" style="font-size: 0.85rem;">{{ $newVal ?? '—' }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                                @if(!$hasChanges)
                                                                    <tr>
                                                                        <td colspan="3" class="text-muted text-center py-3 small">No trackable fields changed.</td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @elseif($isJsonOld || $isJsonNew)
                                                    @php
                                                        $data = $isJsonOld ? $oldDecoded : $newDecoded;
                                                        $statusClass = $isJsonOld ? 'text-danger' : 'text-success';
                                                        $statusLabel = $isJsonOld ? 'Deleted Record' : 'New Record';
                                                    @endphp
                                                    <div class="card bg-white border border-1 shadow-sm">
                                                        <div class="card-header bg-light py-2 border-bottom">
                                                            <span class="fw-semibold {{ $statusClass }}" style="font-size: 0.85rem;">
                                                                <i class="bi {{ $isJsonOld ? 'bi-dash-circle' : 'bi-plus-circle' }} me-1"></i> {{ $statusLabel }}
                                                            </span>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <div class="row m-0">
                                                                @foreach($data as $key => $val)
                                                                    @if(in_array($key, ['created_at', 'updated_at'])) @continue @endif
                                                                    <div class="col-md-6 col-lg-4 border-bottom border-end p-2 px-3">
                                                                        <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600;">{{ str_replace('_', ' ', $key) }}</div>
                                                                        <div class="fw-medium text-dark text-truncate" style="font-size: 0.85rem;">{{ $val ?? '—' }}</div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="card card-body bg-light border-0 p-3 small rounded shadow-sm">
                                                        @if($log->old_value)
                                                            <div class="mb-2">
                                                                <span class="badge bg-secondary bg-opacity-25 text-secondary me-2">Before</span>
                                                                <span class="text-muted">{{ $log->old_value }}</span>
                                                            </div>
                                                        @endif
                                                        @if($log->new_value)
                                                            <div>
                                                                <span class="badge bg-primary bg-opacity-25 text-primary me-2">After</span>
                                                                <span class="fw-medium text-dark">{{ $log->new_value }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                    No audit logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $auditTrails->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
