<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Request Receipt #{{ $itemRequest->id }} – PHRMDO Inventory System</title>
    <link href="{{ asset('images/favicon.ico') }}" rel="shortcut icon" type="image/x-icon">
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('fonts/fontawesome/css/all.min.css') }}" rel="stylesheet" type="text/css">
    
    <!-- Google Fonts -->
    <link href="{{ asset('vendor/@fontsource/inter/index.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/@fontsource/outfit/index.css') }}" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Inter', sans-serif;
            color: #212529;
            padding: 40px 0;
        }

        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid #eaeaea;
        }

        .receipt-header {
            background: #000000;
            color: #ffffff;
            text-align: center;
            padding: 25px 20px;
            border-bottom: 5px solid #10b981;
        }

        .receipt-header h4 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .receipt-header p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .receipt-body {
            padding: 30px;
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #eeeeee;
        }

        .receipt-item:last-child {
            border-bottom: none;
        }

        .receipt-label {
            font-weight: 600;
            color: #555555;
            flex-basis: 40%;
        }

        .receipt-value {
            font-weight: 500;
            color: #111111;
            flex-basis: 60%;
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.Approved { background: #d4edda; color: #155724; }
        .status-badge.Adjusted { background: #d1ecf1; color: #0c5460; }
        .status-badge.Cancelled { background: #f8d7da; color: #721c24; }

        .receipt-footer {
            background: #fafafa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eaeaea;
        }

        .btn-print {
            background: #10b981;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }

        .btn-print:hover {
            background: #0ea5e9;
            color: #ffffff;
        }

        .btn-back {
            background: #e2e8f0;
            color: #334155;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }

        .btn-back:hover {
            background: #cbd5e1;
            color: #0f172a;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                border: 1px solid #000;
                border-radius: 0;
            }
            .receipt-header {
                color: #000 !important;
                background: transparent !important;
                border-bottom: 2px solid #000;
            }
            .receipt-header * {
                color: #000 !important;
            }
            .receipt-footer, .no-print {
                display: none !important;
            }
            .receipt-item {
                border-bottom: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>

    @if(session('success'))
        <div class="container no-print mb-3" style="max-width: 600px;">
            <div class="alert alert-success rounded-3 alert-dismissible">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <div class="receipt-container">
        <div class="receipt-header">
            <h4>PHRMDO Inventory System</h4>
            <p>Official Item Request Slip</p>
        </div>
        
        <div class="receipt-body">
            <div class="receipt-item">
                <div class="receipt-label">Request ID</div>
                <div class="receipt-value">#{{ $itemRequest->id }}</div>
            </div>
            <div class="receipt-item">
                <div class="receipt-label">Status</div>
                <div class="receipt-value">
                    <span class="status-badge {{ $itemRequest->status }}">{{ $itemRequest->status }}</span>
                </div>
            </div>
            <div class="receipt-item">
                <div class="receipt-label">Date Submitted</div>
                <div class="receipt-value">{{ $itemRequest->created_at->format('M d, Y h:i A') }}</div>
            </div>
            <div class="receipt-item">
                <div class="receipt-label">Requester Name</div>
                <div class="receipt-value">{{ $itemRequest->requester_name }}</div>
            </div>
            <div class="receipt-item">
                <div class="receipt-label">Department / Division</div>
                <div class="receipt-value">{{ $itemRequest->department }}</div>
            </div>
            <div class="receipt-item">
                <div class="receipt-label">Requested Item</div>
                <div class="receipt-value">{{ $itemRequest->item->name ?? 'N/A' }}</div>
            </div>
            <div class="receipt-item">
                <div class="receipt-label">Quantity</div>
                <div class="receipt-value">{{ $itemRequest->requested_quantity }}</div>
            </div>
            @if($itemRequest->purpose)
            <div class="receipt-item">
                <div class="receipt-label">Purpose</div>
                <div class="receipt-value">{{ $itemRequest->purpose }}</div>
            </div>
            @endif
        </div>
        
        <div class="receipt-footer no-print">
            <button onclick="window.print()" class="btn-print"><i class="fas fa-print me-2"></i>Print Receipt</button>
            <a href="{{ route('login') }}#request" class="btn-back"><i class="fas fa-arrow-left me-2"></i>Back to Kiosk</a>
        </div>
    </div>
    
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
