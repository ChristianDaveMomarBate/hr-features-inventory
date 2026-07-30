<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Request Receipt {{ $itemRequest->control_number ?? ('CN' . $itemRequest->created_at->format('mdY') . '-' . str_pad($itemRequest->id, 2, '0', STR_PAD_LEFT)) }} – PHRMDO Inventory System</title>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Century Gothic', 'Tw Cen MT', Arial, sans-serif;
            background: #e2e8f0;
            color: #000;
        }

        .no-print {
            text-align: center;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .btn-print {
            background: #10b981;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }

        .print-container {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: white;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .receipt-half {
            height: 50%;
            box-sizing: border-box;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .receipt-content {
            padding: 0 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header-img {
            width: 100%;
            height: auto;
            margin-bottom: 0px;
            display: block;
            flex-shrink: 0;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .meta-field {
            display: flex;
            align-items: flex-end;
        }

        .meta-label {
            margin-right: 5px;
        }

        .meta-value {
            border-bottom: 1px solid #000;
            padding: 0 10px;
            min-width: 150px;
            text-align: center;
            position: relative;
            font-weight: normal;
        }
        
        .signature-sub {
            font-size: 9px;
            text-align: center;
            font-weight: bold;
            margin-top: 1px;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: center;
        }

        .items-table th {
            font-weight: bold;
        }

        .items-table td {
            height: 14px;
        }

        .signatures {
    display: flex;
    margin-top: 5px;
    font-size: 11px;
    width: 100%;
}

.sign-block {
    width: 50%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    box-sizing: border-box;
}

.sign-block:first-child {
    padding-left: 0px;
}

.sign-block:last-child {
    padding-left: 0px;
}

.sign-label {
    font-weight: bold;
    margin-bottom: 15px;
}

.sign-name-container {
    text-align: center;
}

.sign-name {
    font-weight: bold;
    text-decoration: underline;
}

.sign-title {
    font-size: 10px;
}
        .footer-img {
            width: 100%;
            height: auto;
            max-height: 55px;
            object-fit: cover;
            position: absolute;
            bottom: 0;
            left: 0;
            z-index: 10;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body {
                background: none;
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
            .print-container {
                box-shadow: none;
                width: 100%;
                height: 100vh;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Print Document</button>
    </div>

    @php
        $items = [];
        $previewData = request('preview_data');
        
        if ($previewData) {
            $previewItems = json_decode($previewData, true) ?? [];
            foreach($previewItems as $pItem) {
                $items[] = [
                    'desc' => $pItem['desc'] ?? '',
                    'qty' => $pItem['qty'] ?? '',
                    'appr_qty' => '', // Receipt columns: No, Item, Qty, Remarks, Received. We don't have appr_qty column in the new layout anymore actually, we just use Remarks.
                    'remarks' => $pItem['remarks'] ?? ''
                ];
            }
        } else {
            if($itemRequest->requestItems && $itemRequest->requestItems->count() > 0) {
                foreach($itemRequest->requestItems as $reqItem) {
                    $unit = $reqItem->item->display_unit ?? 'pcs';
                    $qtyStr = $reqItem->requested_quantity . ' ' . $unit;
                    $items[] = [
                        'desc' => $reqItem->item->name ?? 'N/A',
                        'qty' => $qtyStr,
                        'remarks' => $reqItem->remarks ?? (in_array($itemRequest->status, ['Approved', 'Adjusted']) ? 'Approved: ' . $reqItem->approved_quantity . ' ' . $unit : '')
                    ];
                }
            } elseif($itemRequest->item_id) {
                $unit = $itemRequest->item->display_unit ?? 'pcs';
                $qtyStr = $itemRequest->requested_quantity . ' ' . $unit;
                $items[] = [
                    'desc' => $itemRequest->item->name ?? 'N/A',
                    'qty' => $qtyStr,
                    'remarks' => $itemRequest->remarks ?? (in_array($itemRequest->status, ['Approved', 'Adjusted']) ? 'Approved: ' . $itemRequest->approved_quantity . ' ' . $unit : '')
                ];
            }
        }
        
        $itemChunks = array_chunk($items, 8);
        if (count($itemChunks) == 0) {
             $itemChunks = [array_pad([], 8, ['desc' => '', 'qty' => '', 'remarks' => ''])];
        } else {
             $lastChunkIdx = count($itemChunks) - 1;
             $itemChunks[$lastChunkIdx] = array_pad($itemChunks[$lastChunkIdx], 8, ['desc' => '', 'qty' => '', 'remarks' => '']);
        }
    @endphp

    @foreach($itemChunks as $chunkIndex => $chunk)
    <div class="print-container" style="{{ $chunkIndex > 0 ? 'page-break-before: always;' : '' }}">
        @for($i = 0; $i < 2; $i++)
        <div class="receipt-half">
            <!-- Header Image -->
            <img src="{{ asset('images/GovMail Header.png') }}" class="header-img" alt="Header">

            <div class="receipt-content">
                <div class="meta-row">
                    <div class="meta-field">
                        <span class="meta-label">Date:</span>
                        <span class="meta-value" style="width: 30px;">{{ $itemRequest->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="meta-field">
                        <span class="meta-label">Control No.:</span>
                        <span class="meta-value" style="width: 50px; font-weight: bold;">{{ $itemRequest->control_number ?? ('CN' . $itemRequest->created_at->format('mdY') . '-' . str_pad($itemRequest->id, 2, '0', STR_PAD_LEFT)) }}</span>
                    </div>
                </div>

                <div class="meta-row" style="margin-bottom: 20px;">
                    <div class="meta-field" style="width: 50%;">
                        <span class="meta-label">Requested by:</span>
                        <span class="meta-value" style="flex: 1; text-align: left; padding-left: 20px;">
                            {{ $itemRequest->requester_name }} - {{ $itemRequest->department }}
                            <div class="signature-sub">Name &amp; Signature of Division Head</div>
                        </span>
                    </div>
                </div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th style="width: 50%;">Item Description</th>
                            <th style="width: 15%;">Quantity</th>
                            <th style="width: 15%;">Remarks</th>
                            <th style="width: 15%;">Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chunk as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="text-align: left;">{{ $item['desc'] }}</td>
                            <td><strong>{{ $item['qty'] }}</strong></td>
                            <td>{{ $item['remarks'] }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="signatures">
                    <div class="sign-block">
                        <div class="sign-label">Noted by:</div>
                        <div class="sign-name-container">
                            <div class="sign-name">MAMARETO B. GESTA JR.</div>
                            <div class="sign-title">Admin. Officer IV</div>
                        </div>
                    </div>
                    <div class="sign-block">
                        <div class="sign-label">Approved by:</div>
                        <div class="sign-name-container">
                            <div class="sign-name">MILA B. LISONDRA</div>
                            <div class="sign-title">OIC - PHRMDO</div>
                        </div>
                    </div>
                    </br>
                </div>
            </div>
            {{-- <img src="{{ asset('images/footer.png') }}" class="footer-img" alt="Footer"> --}}
        </div>
        @endfor
    </div>
    @endforeach
</body>
</html>
