@if($itemRequests->count() > 0)
  <div style="display: flex; flex-direction: column; gap: 1rem; max-height: 500px; overflow-y: auto; padding-right: 5px;">
  @foreach($itemRequests as $itemRequest)
  <div style="background: white; border-radius: 12px; padding: 1.5rem; text-align: left; color: #1e293b; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
      <h4 style="margin: 0; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-file-invoice text-primary"></i> {{ $itemRequest->control_number }}
      </h4>
      <div>
        @if($itemRequest->status === 'Pending')
          <span style="background: #fef08a; color: #854d0e; padding: 6px 12px; border-radius: 999px; font-size: 0.875rem; font-weight: 600;">Pending</span>
        @elseif($itemRequest->status === 'Approved')
          <span style="background: #bbf7d0; color: #166534; padding: 6px 12px; border-radius: 999px; font-size: 0.875rem; font-weight: 600;">Approved</span>
        @elseif($itemRequest->status === 'Adjusted')
          <span style="background: #bae6fd; color: #075985; padding: 6px 12px; border-radius: 999px; font-size: 0.875rem; font-weight: 600;">Adjusted</span>
        @elseif($itemRequest->status === 'Cancelled')
          <span style="background: #fecaca; color: #991b1b; padding: 6px 12px; border-radius: 999px; font-size: 0.875rem; font-weight: 600;">Cancelled</span>
        @endif
      </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; font-size: 0.95rem;">
      <div>
        <span style="color: #64748b; display: block; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Requester</span>
        <strong style="color: #334155;">{{ $itemRequest->requester_name }}</strong>
      </div>
      <div>
        <span style="color: #64748b; display: block; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Division</span>
        <strong style="color: #334155;">{{ $itemRequest->department }}</strong>
      </div>
      <div>
        <span style="color: #64748b; display: block; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Date Submitted</span>
        <strong style="color: #334155;">{{ $itemRequest->created_at->format('M d, Y h:i A') }}</strong>
      </div>
    </div>
    
    <div style="margin-bottom: 1.5rem;">
      <span style="color: #64748b; display: block; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Requested Items</span>
      <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem; margin: 0;">
          <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <tr>
              <th style="padding: 10px 12px; text-align: left; color: #475569; font-weight: 600;">Item Name</th>
              <th style="padding: 10px 12px; text-align: center; color: #475569; font-weight: 600; width: 80px;">Requested</th>
              @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
              <th style="padding: 10px 12px; text-align: center; color: #16a34a; font-weight: 600; width: 80px;">Approved</th>
              <th style="padding: 10px 12px; text-align: left; color: #475569; font-weight: 600;">Remarks</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @if($itemRequest->requestItems->count() > 0)
              @foreach($itemRequest->requestItems as $reqItem)
              <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 10px 12px; color: #334155;">{{ $reqItem->item->name ?? 'N/A' }}</td>
                <td style="padding: 10px 12px; text-align: center; font-weight: 500;">{{ $reqItem->requested_quantity }}</td>
                @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
                <td style="padding: 10px 12px; text-align: center; color: #16a34a; font-weight: 700;">{{ $reqItem->approved_quantity ?? '-' }}</td>
                <td style="padding: 10px 12px; color: #64748b; font-style: italic;">{{ $reqItem->remarks ?? '-' }}</td>
                @endif
              </tr>
              @endforeach
            @elseif($itemRequest->item_id)
              <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 10px 12px; color: #334155;">{{ $itemRequest->item->name ?? 'N/A' }}</td>
                <td style="padding: 10px 12px; text-align: center; font-weight: 500;">{{ $itemRequest->requested_quantity }}</td>
                @if(in_array($itemRequest->status, ['Approved', 'Adjusted']))
                <td style="padding: 10px 12px; text-align: center; color: #16a34a; font-weight: 700;">{{ $itemRequest->approved_quantity ?? '-' }}</td>
                <td style="padding: 10px 12px; color: #64748b; font-style: italic;">{{ $itemRequest->remarks ?? '-' }}</td>
                @endif
              </tr>
            @else
              <tr><td colspan="3" style="padding: 12px; text-align: center; color: #94a3b8;">No items found</td></tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
    
    @if($itemRequest->admin_note)
    <div style="background: #f8fafc; border-left: 4px solid #cbd5e1; padding: 12px 16px; border-radius: 0 8px 8px 0; margin-bottom: 1.5rem; font-size: 0.9rem;">
      <strong style="color: #475569; display: block; margin-bottom: 4px;"><i class="fas fa-comment-alt me-1"></i> Admin Note:</strong>
      <span style="color: #334155;">{{ $itemRequest->admin_note }}</span>
    </div>
    @endif
    
    <div style="text-align: right; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
      <button type="button" onclick="printReceiptIframe('{{ route('kiosk.request.receipt', $itemRequest->id) }}')" style="display: inline-block; background: #f1f5f9; color: #334155; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 0.9rem; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
        <i class="fas fa-print me-1"></i> Print Receipt
      </button>
    </div>

  </div>
  @endforeach
  </div>
@else
  <div style="background: #fef2f2; color: #991b1b; padding: 1rem; border-radius: 8px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-exclamation-triangle fa-lg"></i>
    <span>No request found for "<strong>{{ $searchTerm }}</strong>". Please check and try again.</span>
  </div>
@endif
