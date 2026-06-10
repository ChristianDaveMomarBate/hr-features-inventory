<div id="stock-management" class="page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold" style="font-size: 32px; color: #111827;">Stock Management</h1>
            <p class="text-muted mb-0">Record stock ins, outs, and adjustments.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="chart-card p-0 h-100 d-flex flex-column overflow-hidden">
                <div class="p-4 border-bottom border-light bg-white">
                    <h5 class="fw-bold text-dark mb-0">New Transaction</h5>
                </div>
                <div class="p-4 flex-grow-1 bg-white">
                    <form method="POST" action="{{ route('stock.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Item</label>
                            <select name="inventory_item_id" class="form-select" required>
                                <option value="">Select an Item...</option>
                                @foreach($inventoryItems as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }} (Stock: {{ $item->stock }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select name="type" class="form-select" required>
                                <option value="in">Stock In (Add)</option>
                                <option value="out">Stock Out (Deduct)</option>
                                @if(auth()->user()->isAdmin())
                                    <option value="adjustment">Adjustment (Set Exact Amount)</option>
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" min="1" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reference (e.g. PO No., Request No.)</label>
                            <input type="text" name="reference" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Save Transaction</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="chart-card p-0 h-100 d-flex flex-column overflow-hidden">
                <div class="p-4 border-bottom border-light bg-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Recent Transactions</h5>
                </div>
                <div class="table-responsive flex-grow-1 bg-white">
                    <table class="table table-hover table-modern align-middle mb-0 border-0">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 border-0">Date</th>
                                    <th>Type</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Reference</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockTransactions as $tx)
                                    <tr>
                                        <td class="ps-4 text-nowrap py-3 fw-medium text-secondary" style="font-size:13px;">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="py-3">
                                            @if($tx->type == 'in')
                                                <span class="badge bg-emerald-50 text-emerald-500 border border-success border-opacity-25 px-2 py-1">IN</span>
                                            @elseif($tx->type == 'out')
                                                <span class="badge bg-amber-50 text-amber-500 border border-warning border-opacity-25 px-2 py-1">OUT</span>
                                            @else
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">ADJ</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($tx->inventoryItem)
                                                <span class="fw-semibold text-dark">{{ $tx->inventoryItem->code }}</span> - <span class="text-secondary">{{ $tx->inventoryItem->name }}</span>
                                            @else
                                                <span class="text-muted">Deleted Item</span>
                                            @endif
                                        </td>
                                        <td class="py-3 fw-bold text-dark">{{ $tx->quantity }}</td>
                                        <td class="py-3 text-secondary">{{ $tx->reference }}</td>
                                        <td class="py-3"><small class="text-muted">{{ $tx->remarks }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">No recent transactions.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
