<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PHRMDO Inventory Registry</title>
    <style>
        @page {
            margin: 210px 20px 60px 20px;
            size: a4 landscape;
        }

        body {
            font-family: Arial, sans-serif;
            color: #000;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        /* Fixed Header */
        header {
            position: fixed;
            top: -210px;
            left: -20px;
            right: -20px;
            height: auto;
            text-align: center;
        }

        header img {
            width: 100%;
            height: auto;
            max-height: 180px;
            object-fit: contain;
        }

        /* Fixed Footer */
        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
        }

        footer img {
            width: 100%;
            height: auto;
            max-height: 30px;
            object-fit: contain;
        }

        /* Content Title */
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #c8dff5; /* Light blue header */
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9px;
        }
        
        .data-table td {
            font-size: 10px;
        }

        .text-center {
            text-align: center !important;
        }

    </style>
</head>
<body>

    <!-- Header will repeat on every page -->
    <header>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/GovMail Header.png'))) }}" alt="GovMail Header" style="width: 100%; height: auto; display: block; margin: 0 auto;">
    </header>

    <!-- Footer will repeat on every page -->
    <footer>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/footer.png'))) }}" alt="Footer" style="width:100%; height:auto; max-height:30px; object-fit:contain;">
    </footer>

    <!-- Main Content -->
    <main>
        <div class="report-title">
            INVENTORY REGISTRY<br>
            <span style="font-size: 10px; font-weight: normal;">As of {{ \Carbon\Carbon::now()->format('F d, Y') }}</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Item Code</th>
                    <th style="width: 16%;">Item Name</th>
                    <th style="width: 12%;">Category</th>
                    <th style="width: 8%;">Type</th>
                    <th style="width: 6%;">Units</th>
                    <th style="width: 10%;">Location</th>
                    <th style="width: 9%;">Current Stock</th>
                    <th style="width: 9%;">Total Stock In</th>
                    <th style="width: 8%;">Minimum</th>
                    <th style="width: 12%;">Registered</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventoryItems as $item)
                <tr>
                    <td class="text-center">{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td class="text-center">{{ $item->type ?? 'Consumable' }}</td>
                    <td class="text-center">{{ $item->unit }}</td>
                    <td class="text-center">{{ $item->location ?? '--' }}</td>
                    <td class="text-center">{{ $item->stock }}</td>
                    <td class="text-center">{{ $stockInTotals[$item->id] ?? 0 }}</td>
                    <td class="text-center">{{ $item->minimum }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>

</body>
</html>
