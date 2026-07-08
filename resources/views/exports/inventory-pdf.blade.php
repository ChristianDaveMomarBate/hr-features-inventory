<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PHRMDO Inventory Monthly Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .header-container {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }

        .header-text {
            font-size: 11px;
            line-height: 1.3;
        }

        .header-text h1 {
            margin: 10px 0;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        th {
            font-weight: bold;
        }

        .bg-green { background-color: #c6e0b4; }
        .bg-orange { background-color: #f8cbad; }
        .bg-blue { background-color: #b4c6e7; }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: #002060;
            border-top: 2px solid #002060;
            padding-top: 5px;
        }

        .footer-tagline {
            color: #002060;
        }
        .footer-tagline .green { color: #00b050; }
        
    </style>
</head>
<body>
    <div class="header-container">
        <div class="header-text">
            Republic of The Philippines<br>
            Caraga Region XIII<br>
            PROVINCE OF SURIGAO DEL NORTE<br>
            Provincial Capitol<br>
            Governor Jose C. Sering Road, Surigao City
            <h1>PHRMDO INVENTORY MONTHLY REPORT</h1>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="3" class="bg-green">Stock In</th>
                <th colspan="3" class="bg-orange">Stock Out</th>
                <th colspan="2" class="bg-blue">Stock Balance</th>
            </tr>
            <tr>
                <!-- Stock In -->
                <th class="bg-green">Item Code</th>
                <th class="bg-green">Item Name</th>
                <th class="bg-green">Quantity In</th>
                <!-- Stock Out -->
                <th class="bg-orange">Item Code</th>
                <th class="bg-orange">Item Name</th>
                <th class="bg-orange">Quantity Out</th>
                <!-- Stock Balance -->
                <th class="bg-blue">Item Name</th>
                <th class="bg-blue">Balance Quantity</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventoryItems as $item)
                <tr>
                    <!-- Stock In -->
                    <td>{{ $item->monthly_in > 0 ? $item->code : '' }}</td>
                    <td>{{ $item->monthly_in > 0 ? $item->name : '' }}</td>
                    <td>{{ $item->monthly_in > 0 ? $item->monthly_in . ' ' . $item->display_unit : '' }}</td>
                    <!-- Stock Out -->
                    <td>{{ $item->monthly_out > 0 ? $item->code : '' }}</td>
                    <td>{{ $item->monthly_out > 0 ? $item->name : '' }}</td>
                    <td>{{ $item->monthly_out > 0 ? $item->monthly_out . ' ' . $item->display_unit : '' }}</td>
                    <!-- Stock Balance -->
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->display_stock }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No inventory items found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span class="footer-tagline">LIDERATONG: <span class="green">R</span>ESPONSABLE. <span class="green">L</span>IG-ON. <span class="green">S</span>INSERO. MAY <span class="green">B</span>ARUGANAN.</span>
    </div>
</body>
</html>
