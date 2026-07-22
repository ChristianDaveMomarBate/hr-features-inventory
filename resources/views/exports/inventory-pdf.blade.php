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

        /* ===== DomPDF-compatible table styles ===== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .header-table td {
            border: none;
            padding: 0;
        }

        /* Blue section (left + center) */
        .header-blue-cell {
            background-color: #2e6ad1;
            padding: 10px 8px 10px 12px;
            vertical-align: middle;
            text-align: center;
            width: 75%;
        }

        /* White fade section (right) */
        .header-white-cell {
            background-color: #c8dff5;
            width: 25%;
            vertical-align: middle;
        }

        /* Logo cell */
        .header-logo-cell {
            width: 90px;
            vertical-align: middle;
            text-align: left;
            padding-left: 8px;
        }

        .header-logo-cell img {
            width: 80px;
            height: 80px;
        }

        /* Address text */
        .header-address-cell {
            vertical-align: middle;
            text-align: center;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
        }

        .header-address-cell .province-name {
            font-size: 13px;
            font-weight: bold;
        }

        /* ===== REPORT TITLE ===== */
        .print-main-title {
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            color: #000;
            margin: 8px 0 2px 0;
        }

        .print-report-month {
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: bold;
            color: #000;
            margin: 0 0 10px 0;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        table.data-table th {
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

        .footer-tagline { color: #002060; }
        .footer-tagline .green { color: #00b050; }

    </style>
</head>
<body>

    {{-- ===== HEADER: Blue banner with logo + address ===== --}}
    {{-- Uses nested tables for DomPDF compatibility (no flexbox/gradient) --}}
    <table class="header-table">
        <tr>
            {{-- Blue zone: logo + address --}}
            <td style="background-color:#2e6ad1; padding:10px 0 10px 0; vertical-align:middle; width:80%;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        {{-- Logo --}}
                        <td style="width:90px; vertical-align:middle; text-align:center; padding-left:10px; border:none; padding-top:0; padding-bottom:0;">
                            <img src="{{ public_path('images/print-logo.png') }}" alt="Province Seal" style="width:80px; height:80px;">
                        </td>
                        {{-- Address text --}}
                        <td style="vertical-align:middle; text-align:center; font-size:11px; line-height:1.5; color:#000; border:none; padding:0;">
                            Republic Of The Philippines<br>
                            Caraga Region XIII<br>
                            <strong style="font-size:13px; font-weight:bold;">PROVINCE OF SURIGAO DEL NORTE</strong><br>
                            Provincial Capitol<br>
                            Governor Jose C. Sering Road, Surigao City
                        </td>
                    </tr>
                </table>
            </td>
            {{-- Fade zone: light blue to white --}}
            <td style="background-color:#c8dff5; width:20%; vertical-align:middle;">&nbsp;</td>
        </tr>
    </table>

    <p class="print-main-title">PHRMDO INVENTORY MONTHLY REPORT</p>
    <p class="print-report-month">{{ $reportMonth ?? '' }}</p>


    <table class="data-table">
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
