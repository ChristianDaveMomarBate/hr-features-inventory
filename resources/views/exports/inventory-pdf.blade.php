<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PHRMDO Inventory List</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.04em;
        }

        .header p {
            margin: 4px 0 0;
            color: #4b5563;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 7px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-size: 10px;
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PHRMDO INVENTORY SYSTEM</h1>
        <p>Inventory Registry Report</p>
        <p>Generated: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Type</th>
                <th>Stock Unit</th>
                <th>Issue Unit</th>
                <th>Units / Stock Unit</th>
                <th>Current Stock</th>
                <th>Bulk Equivalent</th>
                <th>Minimum Stock</th>
                <th>Date Registered</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventoryItems as $item)
                <tr>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td>{{ $item->type ?? 'Consumable' }}</td>
                    <td>{{ $item->stock_unit }}</td>
                    <td>{{ $item->issue_unit }}</td>
                    <td>{{ $item->units_per_stock_unit }}</td>
                    <td>{{ $item->display_stock }}</td>
                    <td>{{ $item->bulk_equivalent ?? '' }}</td>
                    <td>{{ $item->minimum }}</td>
                    <td>{{ $item->date_registered ? \Carbon\Carbon::parse($item->date_registered)->format('M d, Y') : '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">No inventory items found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
